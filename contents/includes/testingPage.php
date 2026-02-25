<?php
require_once("./php/_connect.php");

class question
{
    private $title;
    private $description;
    private $options;
    private $questionUUID;

    public function __construct()
    {
        $this->options = Array();
    }
    public function addOption($option)
    {
        array_push($this->options,$option);        
    }
    public function updateTitle($title)
    {
        $this->title=$title;
    }
    public function updateDescription($description)
    {
        $this->description=$description;
    }
        public function updateUUID($UUID)
    {
        $this->questionUUID=$UUID;
    }

    public function shuffleOptions()
    {
        for($i=0; $i < sizeof($this->options);$i++)
        {
            $target = random_int(0, (sizeof($this->options) -1)); // Cryptographically secure random generation
            $moveSpot = random_int(0, (sizeof($this->options) -1)); // Cryptographically secure random generation
            //Please pay attention that we remove 1 from the max to avoid
            //an off by one error :3
            $memory = $this->options[$target];
            $this->options[$target] = $this->options[$moveSpot];
            $this->options[$moveSpot] = $memory; // Now theoretically we could do a bit XOR so we don't have to use memory 
            //But this saves development time for abysmal memory cost.
            //Future efficiency can be implemented here in the case that we run into memory problems (we won't)
        }
    }

    public function getTitle()
    {
        return $this->title;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function getOptions()
    {
        return $this->options;
    }
    public function getUUID()
    {
        return $this->questionUUID;
    }
};
class quiz
{
    private $score;
    private $questions;
    private $quizUUID;
    public function __construct()
    {
        $this->questions =  Array();
    }

    public function addQuestion($question)
    {
        //Encapsulation baby
        array_push($this->questions,$question);
    }

    public function shuffleQuestions()
    {
        $spotArray = Array(); //List to hold numbers of spots, we can swap ints instead of moving or creating and deleting objects
        //  faster, and more efficient, at the end we only recreate the array once.

        for($n =0; $n < sizeof($this->questions); $n++)
        {
            array_push($spotArray,$n);
        }

        for($i =0; $i<5; $i++)//shuffle it 5 times to make sure it is properly randomised
        {

            for($n =0; $n < sizeof($this->questions); $n++)
            {
                $target = random_int(0, (sizeof($this->questions) -1)); // Cryptographically secure random generation
                $moveSpot = random_int(0, (sizeof($this->questions) -1)); // Cryptographically secure random generation
                //Please pay attention that we remove 1 from the max to avoid
                //an off by one error :3
                $temp = $spotArray[$target];
                $spotArray[$target] =$spotArray[$moveSpot];
                $spotArray[$moveSpot] = $temp;
                //swap complete

                $this->questions[$n]->shuffleOptions();
                //Also shuffle the order of the options 
                //This way we ensure that students can't memorise answer positions but the actual answer
            }    
        }
        //shuffle complete

        $newArrayOfQuestions = Array();
        for($n =0; $n < sizeof($this->questions); $n++) //sizeof $questions or $spotArray both fine
        {
            array_push($newArrayOfQuestions,$this->questions[$spotArray[$n]]);
            //Push the object in questions found at the position given in spotArray
        }

        //Finally swap old array with shuffled array
        $this->questions = $newArrayOfQuestions;
    }

    public function getQuestions()
    {
        //Encapsulation baby
        return $this->questions;
    }
}

$currentQuiz = new quiz();
$db= new inquizitiveDB ();

$quizUUID = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['quizID'])); // Unnecessary since we use prepared statements anyway but why not?
//UUID now also uses htmlspecialchars
//We query the questions for the given UUID
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
        <div id="quiz-timer-wrapper" class="shadow-sm">
            <div id="quiz-timer" class="animated-background"></div>
        </div>
        <div id="quizzes-wrapper">
            <?php
                for($n=0; $n < sizeof($currentQuiz->getQuestions());$n++)
                {
                    echo '
                    <div id="' . $currentQuiz->getQuestions()[$n]->getUUID() . '" class="question-wrapper hidden"> 
                        <div class="question-text shadow">
                            <div class="question-num">QUESTION ' . ($n+1) .'/' . sizeof($currentQuiz->getQuestions()) . '</div>
                            <div class="divider-small">
                                <div class="quiz-completion-divider"></div>
                            </div>
                            <h1>' . $currentQuiz->getQuestions()[$n]->getDescription() . '</h1>
                        </div>
                        <div class="question-option-wrapper shadow">
                            <div id="' . $n . 'A" class="question-option question-option-A shadow-sm" choice ="A">' . $currentQuiz->getQuestions()[$n]->getOptions()[0] . '</div>
                            <div id="' . $n . 'B" class="question-option question-option-B shadow-sm" choice ="B">' . $currentQuiz->getQuestions()[$n]->getOptions()[1] . '</div>
                            <div id="' . $n . 'C" class="question-option question-option-C shadow-sm" choice ="C">' . $currentQuiz->getQuestions()[$n]->getOptions()[2] . '</div>
                            <div id="' . $n . 'D" class="question-option question-option-D shadow-sm" choice ="D">' . $currentQuiz->getQuestions()[$n]->getOptions()[3] . '</div> 
                        </div>
                    </div>'; 
                }
            ?>
        </div>
    </section>
    <script>
        var questions = new Array();
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
            }
        }

        function quizTimer()
        {
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
                timeLeft = 20000;
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
        
        })
    </script>
</body>
</html>