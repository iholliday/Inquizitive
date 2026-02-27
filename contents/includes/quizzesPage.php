<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzes</title>
</head>
<body>
    <section  id="quizzesPage">
<?php

require_once("./php/_connect.php");
$db= new inquizitiveDB ();

if ($result = $db->Query("CALL GetAllQuizzes();",[])) {

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)){
            echo'<div>
                <div>Quiz ' . htmlspecialchars($row['quizUUID']) . '</div>
                <button id="' . htmlspecialchars($row['quizUUID']) . '" class ="take-quiz-btn" >Take quiz</button>
            </div>';
        }
    }
}

?>

    </section>

    <script> // Move to scriptName.js
        $(".take-quiz-btn").ready(function(){
            $(".take-quiz-btn").click(function(){
                $.ajax({
                    url: './testing', 
                    type: 'POST', 
                    data: { quizID: $(this).attr("id").toString(), key2: 'test' },
                    success: function(response) {
                        //console.log('Success:', response);
                        $("#content").html(response);
                    },
                    error: function(xhr, status, error) {
                        //console.log('Error:', error);
                    }
                });
            })
        })

    </script>
</body>
</html>