<?php
require_once("./php/_connect.php");

require_once("./php/_mtQuizClasses.php");


$currentQuiz = new quiz();
$db= new inquizitiveDB ();

$quizUUID = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['quizGrab']));


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

var_dump($currentQuiz);

?>