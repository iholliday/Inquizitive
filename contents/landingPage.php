<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquizitive - educational quizzes</title>
    <link rel="stylesheet" href="../css/landingPage.css">
    <link rel="stylesheet" href="../css/colours.css">
    <link rel="stylesheet" href="../css/global.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Bungee&family=Mogra&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Pacifico&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>

<body class="body">
    <div id="header">
        <div id="logo"><div class="l1">i</div><div class="l2">Q</div></div>
        <div>
            <button class="sm-hide">Login</button>
            <button class="bold-button">Create an account</button>
        </div>
    </div>
    
    <!-- First row - welcome to Inquizitive -->
    <section id="first-row" class="flex-row lp-row">
        <div class="lp-row-internal halves-row 100w md-collapse">
            <div class="textbox-lg">
                <h1>Welcome to Inquizitive!</h1>
                <p>UCW's own reinforcement testing platform.</p>
                <button>Find out more</button>
            </div>
            <div>
                <img class="" src="../images/sampleimage.png" alt="sample">
            </div>
        </div>
    </section>
    
    <!-- Second row -->
    <section id="second-row" class="flex-row lp-row">
        <div class="lp-row-internal halves-row 100w md-collapse">
            <div>
                <img class="" src="../images/sampleimage.png" alt="sample">
            </div>
            <div class="textbox-lg">
                <h3>Who It’s For</h3>
                <p>Inquizitive is designed for university learners and the educators who support them, offering an engaging way to reinforce knowledge beyond traditional lectures.</p>

                <h3>What It Does</h3>
                <p>The platform gives teachers greater control over quiz creation and clear visibility into student progress, while providing students with a flexible space to practise and improve at their own pace.</p>

                <h3>Why It Stands Out</h3>
                <p>Inquizitive blends structured assessment with enjoyable gamified elements, striking a balance between formal testing and fun to motivate students even when no exam is approaching.</p>
            </div>
        </div>
    </section>
    
    <!-- Third row - how to use Inquizitive -->
    <section id="guide-row" class="flex-row lp-row 100vw">
        <div class="lp-row-internal thirds-row md-collapse">
            <div class="flex-col">
                <img src="../images/sampleimage.png" alt="sample">
                <div class="textbox">
                    <h2>1: Create an account</h2>
                    <p>Click <a class="a" href="">Create an Account</a> in the top right to start!</p>
                </div>
                
            </div>
            <div class="flex-col">
                <img src="../images/sampleimage.png" alt="sample">
                <div class="textbox">
                    <h2>2: Join a course</h2>
                    <p>Teachers can create classes for their students, or you can join a class created by your teacher.</p>
                </div>
            </div>
            <div class="flex-col">
                <img src="../images/sampleimage.png" alt="sample">
                <div class="textbox">
                    <h2>3: Start quizzing!</h2>
                    <p>Learn in a fun environment and track your progress.</p>
                </div>
            </div>
        </div>
    </section>
    
    <div id="bottom-row">
        <!-- to be email form -->
        <form action="post">
            <label for="email">Enter your email:</label>
            <input type="email" id="email" name="email">
            <input type="text" name="" id="">
            <input type="submit" value=""><i>></i></input>
        </form>
    </div>
</body>
</html>