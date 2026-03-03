<?php
class question
{
    private $title;
    private $description;
    private $options;
    private $questionUUID;
    private $answer;

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
        public function addAnswer($answer)
    {
        $this->answer = $answer;       
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
    public function getAnswer()
    {
        return $this->answer;        
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
    public function addQuizUUID($quizUUID)
    {
        //Encapsulation baby
        array_push($this->quizUUID,$quizUUID);
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
    public function getQuizUUID()
    {
        //Encapsulation baby
        return $this->quizUUID;
    }
    public function getQuestions()
    {
        //Encapsulation baby
        return $this->questions;
    }
}
?>