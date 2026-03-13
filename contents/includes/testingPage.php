<?php
require_once("./php/_connect.php");

require_once("./php/_mtQuizClasses.php");


$currentQuiz = new quiz();
$db= new inquizitiveDB ();

$quizUUID = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['quizID'])); // Unnecessary since we use prepared statements anyway but why not?
$userUUID = htmlspecialchars(mysqli_real_escape_string($db->connect,$_SESSION['userUUID']));
$quizInstance;
//UUID now also uses htmlspecialchars
//We query the questions for the given UUID
if($result = $db->Query("CALL AddQuizInstanceForQuizUUIDAndUserUUID(?,?);",[$quizUUID,$userUUID]))
{
    $questionCount = mysqli_num_rows($result);
    if ($questionCount > 0) {
        $row = mysqli_fetch_assoc($result);
        $quizInstance = htmlspecialchars($row['quizInstanceUUID']);
    }
}

if ($result = $db->Query("CALL GetQuizQuestionsByID(?);",[$quizUUID])) {

    $questionCount = mysqli_num_rows($result);
    if ($questionCount > 0) {
        $counter =1;
        while($row = mysqli_fetch_assoc($result)){
            //Populate object with question data
            $question = new question();
            $title = "Question " . $counter . "/" . $questionCount . " :";
            $counter++;
            $question->updateTitle($title);
            $question->updateDescription(htmlspecialchars($row['questionText']));
            $question->addOption(htmlspecialchars($row['answerA']));
            $question->addOption(htmlspecialchars($row['answerB']));
            $question->addOption(htmlspecialchars($row['answerC']));
            $question->addOption(htmlspecialchars($row['answerD']));
            $question->updateUUID(htmlspecialchars($row['questionUUID']));
            $question->updateTimer(htmlspecialchars($row['difficultyPoints']));

            $currentQuiz->addQuestion($question);
        }
    }
}
//We shuffle the questions
$currentQuiz->shuffleQuestions();
?>


<html lang="en" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Title</title>
    <link rel = "stylesheet" href="./css/testingPage.css" /> 
</head>
<body>
    <section id="testing-page" class="">
        <div id="quiz-instance" class="hidden" quiz-instance=<?php echo '"' . $quizInstance . '"' ;?>><?= $quizInstance?></div>
        <div id="quiz-uuid" class="hidden" quiz-uuid=<?php echo '"' . $quizUUID . '"' ;?>><?= $quizUUID?></div>
        <div id="quiz-timer-wrapper" class="shadow-sm">
            <div id="quiz-timer" class="animated-background"></div>
        </div>
        <div id="quizzes-wrapper">
            <?php
                for($n=0; $n < sizeof($currentQuiz->getQuestions());$n++)
                {
                    echo '
                    <div id="' . $currentQuiz->getQuestions()[$n]->getUUID() . '" class="question-wrapper hidden" data-timer = "' . $currentQuiz->getQuestions()[$n]->getTimer() . '"> 
                        <div class="question-text shadow">
                            <div class="question-num">QUESTION ' . ($n+1) .'/' . sizeof($currentQuiz->getQuestions()) . '</div>
                            <div class="divider-small">
                                <div class="quiz-completion-divider"></div>
                            </div>
                            <h1>' . $currentQuiz->getQuestions()[$n]->getDescription() . '</h1>
                        </div>
                        <div class="question-option-wrapper shadow">
                            <div id="' . $n . 'A" class="question-option question-option-A shadow-sm" choice ="' . $currentQuiz->getQuestions()[$n]->getOptions()[0] . '">' . $currentQuiz->getQuestions()[$n]->getOptions()[0] . '</div>
                            <div id="' . $n . 'B" class="question-option question-option-B shadow-sm" choice ="' . $currentQuiz->getQuestions()[$n]->getOptions()[1] . '">' . $currentQuiz->getQuestions()[$n]->getOptions()[1] . '</div>
                            <div id="' . $n . 'C" class="question-option question-option-C shadow-sm" choice ="' . $currentQuiz->getQuestions()[$n]->getOptions()[2] . '">' . $currentQuiz->getQuestions()[$n]->getOptions()[2] . '</div>
                            <div id="' . $n . 'D" class="question-option question-option-D shadow-sm" choice ="' . $currentQuiz->getQuestions()[$n]->getOptions()[3] . '">' . $currentQuiz->getQuestions()[$n]->getOptions()[3] . '</div> 
                        </div>
                    </div>'; 
                }
            ?>
        </div>
    </section>
    <script>
        var questions = new Array();
        var timers = new Array();
        var answered = new Array();
        var currentQuestion =0;
        var quizTimeout =setTimeout(() => {}, 10);
        var submitted = false;
        var completion =0;
        timeLeft =20000;
        timeSpent =0;
        function completionBarUpdate()
        {
            if( currentQuestion !== 0 && currentQuestion <= questions.length)
            {
                completion = (currentQuestion / questions.length) *100;
                $(".quiz-completion-divider").css("width",completion+"%");
        
            }else if( currentQuestion ==0 )
            {
                completion =0; //Avoid division by 0 or computer very angry :c
                $(".quiz-completion-divider").css("width",completion+"%");
            }
        }
        function sendPayload()
        {
            if(!submitted)
            {
                clearTimeout(quizTimeout);
                completionBarUpdate();
                console.log(answered);
                submitted=true;

                $.ajax({
                    url: './quiz-result', 
                    type: 'POST', 
                    data: 
                    { 
                    quizInstance:$("#quiz-instance").attr("quiz-instance").toString(),       
                    quizUUID:$("#quiz-uuid").attr("quiz-uuid").toString(), 
                    answers:answered 
                    },
                    success: function(response) {
                        //console.log('Success:', response);
                        $("#content").html(response);
                    },
                    error: function(xhr, status, error) {
                        //console.log('Error:', error);
                    }
                });
            }
        }

        function quizTimer()
        {
            if(timers[currentQuestion] <= 0)
            {
                timeLeft = 100000000000000;
                $("#quiz-timer-wrapper").addClass("hidden");
            }else
            {
                timeLeft = timers[currentQuestion] * 1000;
                $("#quiz-timer-wrapper").removeClass("hidden");

            }
            percent = ((timeLeft-timeSpent) / timeLeft) * 100;
            $("#quiz-timer").css("width",percent+"%");
            if(timeLeft-(timeSpent+100)>0)
            {
                if(answered.length < questions.length){
                    quizTimeout = setTimeout(quizTimer, 100);
                    timeSpent+=100;
                }else
                {
                    sendPayload();
                }
            }else
            {
                if(answered.length < questions.length)
                {
                    var payload ={
                        questionUUID: questions[currentQuestion].toString(),
                        answer: "Time ran out!"
                    }
                    console.log(currentQuestion);
                    answered.push(payload);
                    nextQuestion();
                }else
                {
                    sendPayload();
                }
            }
        }
        function nextQuestion()
        {
            if(currentQuestion < questions.length)
            {
                currentQuestion+=1;
                $(".question-wrapper").addClass("hidden");
                $("#"+questions[currentQuestion]).removeClass("hidden");
                timeSpent =0;
                if( typeof quizTimeout !== 'undefined')
                {
                    clearTimeout(quizTimeout);
                }
                quizTimer();
                completionBarUpdate();

            }else
            {
                //send data to results page
                sendPayload();
            }
        }


        
        $("#testing-page").ready(function(){
            $(".question-wrapper").each(function(index, element){
                questions.push($(this).attr("id").toString());
                timers.push($(this).attr("data-timer").toString());
            })
            console.log(questions);


 
        $(".question-option").ready(function(){
            $(".question-option").click(function(){
                if(answered.length<questions.length){
                    var payload ={
                        questionUUID: questions[currentQuestion],
                        answer: $(this).attr("choice").toString()
                    }
                    console.log(currentQuestion);
                    answered.push(payload);
                    nextQuestion();
                }else
                {
                    sendPayload();
                }
            })
        })

        //Show the first and start counting
        $(".question-wrapper").addClass("hidden");
        $("#"+questions[currentQuestion]).removeClass("hidden");
        quizTimer();
        completionBarUpdate();
        console.log(timers);
        
        })
    </script>
</body>
</html>