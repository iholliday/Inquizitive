<?php
require_once("./php/_connect.php");
require_once("./php/_mtQuizClasses.php");
$quiz = new quiz();
$db = new inquizitiveDB();
$score =0;
$array =[];
$quizInstance =  mysqli_real_escape_string($db->connect,$_POST['quizInstanceID']);

if ($result = $db->Query("CALL GetQuizQuestionsInQuizInstanceByUUID(?);",[$quizInstance])) {

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
            $question->updateUUID(htmlspecialchars($row['questionUUID']));
            $question->addAnswer(htmlspecialchars($row['correctAnswer']));
            $givenAnswer = htmlspecialchars($row['answerChosen']);
            array_push($array,$givenAnswer);
            $quiz->addQuestion($question);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attempt</title>
    <link rel="stylesheet" href="./css/results.css" />
</head>
<body>
  <div id="results-page">
    <div class="accordion shadow" id="accordionPanelsStayOpenExample">
      <?php
for($i=0; $i < sizeof($array); $i++)
{

    $style = "";
        if($array[$i] == $quiz->getQuestions()[$i]->getAnswer())
        {
          $style ="correct";
          $score = $score + 30;
        }else
        {
          $score =$score - 10;
          $style="wrong";
        }

            echo '  <div class="accordion-item">
    <h2 class="accordion-header ' . $style . '" id="panelsStayOpen-heading' . $i . '">
      <button class="accordion-button  ' . $style . '" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse' . $i . '" aria-expanded="true" aria-controls="panelsStayOpen-collapse' . $i . '">
        ' . $quiz->getQuestions()[$i]->getDescription() . '
      </button>
    </h2>
    <div id="panelsStayOpen-collapse' . $i . '" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading' . $i . '">
      <div class="accordion-body">
        <strong>Yours: ' . $array[$i] . '</strong><br>
        <strong>Correct: ' . $quiz->getQuestions()[$i]->getAnswer() . '</strong><br>

      </div>
    </div>
  </div>';
  }

?>

</div>
</div>
</body>
</html>