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

$quizUUID = mysqli_real_escape_string($db->connect,$_POST['quizzID']); // Unnecessary since we use prepared statements anyway but why not?
//We query the questions for the given UUID
if ($result = $db->Query("CALL GetQuizzQuestionsByID(?);",[$quizUUID])) {

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
    <title>Quizz Title</title>
    <link rel = "stylesheet" href="./css/testingPage.css" /> 
</head>
<body>
    <section id="testingPage" class="">
        <div id="quizTimerWrapper">
            <div id="quizTimer" class="animatedBackground"></div>
        </div>
        <div id="quizzesWrapper">
            <?php
                for($n=0; $n < sizeof($currentQuiz->getQuestions());$n++)
                {
                    echo '
                    <div id="' . $currentQuiz->getQuestions()[$n]->getUUID() . '" class="questionWrapper hidden"> 
                        <div class="questionText ">
                            <h1>Question: ' . $currentQuiz->getQuestions()[$n]->getDescription() . '</h1>
                        </div>
                        <div class="questionOptionWrapper">
                            <div id="' . $n . 'A" class="questionOption" choice ="A">A: ' . $currentQuiz->getQuestions()[$n]->getOptions()[0] . '</div>
                            <div id="' . $n . 'B" class="questionOption" choice ="B">B: ' . $currentQuiz->getQuestions()[$n]->getOptions()[1] . '</div>
                            <div id="' . $n . 'C" class="questionOption" choice ="C">C: ' . $currentQuiz->getQuestions()[$n]->getOptions()[2] . '</div>
                            <div id="' . $n . 'D" class="questionOption" choice ="D">D: ' . $currentQuiz->getQuestions()[$n]->getOptions()[3] . '</div> 
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
        timeLeft =20000;
        timeSpent =0;
        function sendPayload()
        {
            if(!submitted)
            {
                clearTimeout(quizTimeout);
                console.log(answered);
                submitted=true;
            }
        }
        function quizTimer()
        {
            percent = ((timeLeft-timeSpent) / timeLeft) * 100;
            $("#quizTimer").css("width",percent+"%");
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
                $(".questionWrapper").addClass("hidden");
                $("#"+questions[currentQuestion]).removeClass("hidden");
                timeLeft = 20000;
                timeSpent =0;
                currentQuestion+=1;
                if( typeof quizTimeout !== 'undefined')
                {
                    clearTimeout(quizTimeout);
                }
                quizTimer();
            }else
            {
                //send data to results page
                sendPayload();
            }
        }


        
        $("#testingPage").ready(function(){
            $(".questionWrapper").each(function(index, element){
                questions.push($(this).attr("id").toString());
            })
            console.log(questions);


 
        $(".questionOption").ready(function(){
            $(".questionOption").click(function(){
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
        $(".questionWrapper").addClass("hidden");
        $("#"+questions[currentQuestion]).removeClass("hidden");
        quizTimer();

        })
    </script>
</body>
</html>