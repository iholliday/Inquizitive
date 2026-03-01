<?php
require_once("./php/_connect.php");
require_once("./php/_mtQuizClasses.php");
$quiz = new quiz();
$db = new inquizitiveDB();

$quizUUID = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['quizUUID'])); // Unnecessary since we use prepared statements anyway but why not?

$array = $_POST['answers'];
 $quizInstance =  htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['quizInstance']));
 echo $quizInstance;


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
            $question->addAnswer(htmlspecialchars($row['correctAnswer']));

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
    <title>Document</title>
    <link rel="stylesheet" href="./css/results.css" />
</head>
<body>
  <div id="results-page">
    <div class="accordion" id="accordionPanelsStayOpenExample">
      <?php
       for($i=0; $i < sizeof($array); $i++)
{
    $questionUUID =  htmlspecialchars($array[$i]["questionUUID"]);
    $answer =  htmlspecialchars($array[$i]["answer"]);
    $style = "";
    for($n=0; $n < sizeof($quiz->getQuestions()); $n++)
    {
      if($questionUUID == $quiz->getQuestions()[$n]->getUUID())
      {
        if($answer == $quiz->getQuestions()[$n]->getAnswer())
        {
          $style ="correct";
        }else
        {
          $style="wrong";
        }

            echo '  <div class="accordion-item">
    <h2 class="accordion-header ' . $style . '" id="panelsStayOpen-heading' . $i . '">
      <button class="accordion-button  ' . $style . '" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse' . $i . '" aria-expanded="true" aria-controls="panelsStayOpen-collapse' . $i . '">
        ' . $quiz->getQuestions()[$n]->getDescription() . '
      </button>
    </h2>
    <div id="panelsStayOpen-collapse' . $i . '" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-heading' . $i . '">
      <div class="accordion-body">
        <strong>(yours)' . $answer . '</strong><br>
        <strong>' . $quiz->getQuestions()[$n]->getAnswer() . '</strong><br>

      </div>
    </div>
  </div>';
      }
    }

}
?>

</div>
</div>
</body>
</html>