<?php
// REQUIRES EMAIL SERVER TO RUN !!

// If the page is accessed directly through the URL bar, block access. Only allows access if loaded via AJAX.
require_once("./php/blockDirectAccess.php");

// Sends confirmation email to new user.
function sendSignupEmail($email, $sanitisedFirstName, $sanitisedLastName, $emailSubject, $emailBody)
{
    // Sanitizing data to prevent XSS.
    $sanitisedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Check if email is valid.
    if (!filter_var($sanitisedEmail, FILTER_VALIDATE_EMAIL)) {
        error_log("Invalid email in sendSignupEmail(): $sanitisedEmail");
        return false;
    }

    // Prepare the headers for the email.
    $fromEmail = "accounts@Inquisitive.remote.ac";
    $headers  = "From: $fromEmail\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8";

   // Send the email to the user's email address.
    if (mail($sanitisedEmail, $emailSubject, $emailBody, $headers)) {
        return true;
    } else {
        error_log("Signup email FAILED for: $sanitizsdEmail");
        return false;
    }
}
?>