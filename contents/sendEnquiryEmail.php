<?php
//Sending enquiry emails from the form on the landing page to the 

require_once "emailclassthing.php";

$_POST["emailAddress"];
$_POST["message"];

//$emailTitle = "Enquiry received from email \"".$_POST["emailAddress"]."\"";

$emailBody = "Enquiry received at ".NOW().".\nMessage: \n";

print_r($_POST);
print_r(NOW());

?>