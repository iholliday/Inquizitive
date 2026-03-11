<?php
// Sending enquiry emails from the form on the landing page to the inquizitive email address.
// Added by Tom to fix broken enquiry form so the page works correctly.
// This was done for functionality; original page structure was left intact.
// REQUIRES EMAIL SERVER TO RUN.

require_once("./php/confirmationEmail.php");
require_once("./php/checkLocalHost.php");

$senderEmail = $_POST["emailAddress"];
$message = $_POST["message"];

// Send signup email, disabled for localhost development.
if (!isLocalHost()) 
{
    // Sanitizing data to prevent XSS, names are not needed so left blank.
    $sanitisedSenderEmail = htmlspecialchars($senderEmail, ENT_QUOTES, 'UTF-8');
    $sanitisedMessage  = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $sanitisedFirstName = "";
    $sanitisedLastName = "";
    $email = "accounts@Inquisitive.remote.ac";
                
    // Create the email subject and body for the confirmation message.
    $emailSubject = "Incoming enquiry";
    $emailBody = "From $sanitisedSenderEmail,\n\n"
                . "$sanitisedMessage";
    sendEmail($email, $sanitisedFirstName, $sanitisedLastName, $emailSubject, $emailBody);
}
?>