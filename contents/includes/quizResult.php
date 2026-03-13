<?php
require_once("./php/_connect.php");
require_once("./php/_mtQuizClasses.php");
$quiz = new quiz();
$db = new inquizitiveDB();
$score =0;
$quizUUID = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['quizUUID'])); // Unnecessary since we use prepared statements anyway but why not?
$userUUID = htmlspecialchars(mysqli_real_escape_string($db->connect,$_SESSION['userUUID']));
$array = $_POST['answers'];
$quizInstance =  mysqli_real_escape_string($db->connect,$_POST['quizInstance']);
// echo $quizInstance;


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
    <title>Quiz Result</title>
    <link rel="stylesheet" href="./css/results.css" />
</head>
<body>
  <div id="results-page">
    <div class="accordion shadow" id="accordionPanelsStayOpenExample">
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
          $score = $score + 30;
        }else
        {
          $score =$score - 10;
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
        <strong>Yours: ' . $answer . '</strong><br>
        <strong>Correct: ' . $quiz->getQuestions()[$n]->getAnswer() . '</strong><br>

      </div>
    </div>
  </div>';
      }
    }

}

if($score > 0){
  $db= new inquizitiveDB ();
  $currencyItemUUIDResult = $db->Query("CALL GetCurrencyItemUUID()");
  $currencyItemUUID;
  $rowCount = mysqli_num_rows($currencyItemUUIDResult);
  if ($rowCount > 0) {
          $row = mysqli_fetch_assoc($currencyItemUUIDResult);
          $currencyItemUUID = htmlspecialchars($row['itemUUID']);
      }
  $db= new inquizitiveDB ();
  if ($result = $db->Query("CALL GetInquizitiveCurrencyByUserUUID(?)", [$userUUID])) {

      $rowCount = mysqli_num_rows($result);
      if ($rowCount > 0) {
          $row = mysqli_fetch_assoc($result);
          $userCurrency = htmlspecialchars($row['quantity']);
      }else
      {
        // No item found therefore base currency is set to 0
        $db= new inquizitiveDB ();
        $userCurrency =0;
        $result = $db->Query("CALL AddItemToGivenAccountInventory(?,?,?);", [$userUUID,$currencyItemUUID,$userCurrency]);

      }
  }

  $moneyLeft =  (int)$userCurrency + (int)$score;
  if(!is_null($currencyItemUUID) && !is_null($userUUID) && !is_null($userCurrency))
  {
    $db= new inquizitiveDB ();
    $b = $db->Query("CALL SetUserInventoryItemQuantity(?,?,?);", [$userUUID,$currencyItemUUID,$moneyLeft]); 
    echo '<div class="score">you gained ' . $score . " iq points</div>";
  }
}

if($score <0){$score = 0;};
$updateQuizInstanceResult = $db->Query("CALL UpdateQuizInstanceByUUID(?,?);", [$quizInstance,$score]); 


for($i=0; $i < sizeof($array); $i++)
{
    $questionUUID =  htmlspecialchars($array[$i]["questionUUID"]);
    $answer =  htmlspecialchars($array[$i]["answer"]);
    $style = "";
    for($n=0; $n < sizeof($quiz->getQuestions()); $n++)
    {

      if($questionUUID == $quiz->getQuestions()[$n]->getUUID())
      {
        $qAnswer = $array[$i]["answer"];
        $qUUID = $quiz->getQuestions()[$n]->getUUID();
        $db = new inquizitiveDB();
        $createQuizInstance =$db->Query("CALL CreateQuestionInstance(?,?,?,?)",[$quizInstance,$qUUID,$i,$qAnswer]);

      }
    }
}

?>

</div>
</div>
</body>
</html>