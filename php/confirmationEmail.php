<?php
// REQUIRES EMAIL SERVER TO RUN !!

// If the page is accessed directly through the URL bar, block access. Only allows access if loaded via AJAX.
require_once("./php/blockDirectAccess.php");

// Sends confirmation email to new user.
function sendSignupEmail($email, $firstName, $lastName)
{
    // Sanitizing data to prevent XSS.
    $sanitizedFirstName = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
    $sanitizedLastName  = htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8');
    $sanitizedEmail     = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Check if email is valid.
    if (!filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL)) {
        error_log("Invalid email in sendSignupEmail(): $cleanEmail");
        return false;
    }

    // Create the email subject and body for the confirmation message.
    $emailSubject = "Welcome to Inquizitive!";
    $emailBody = "Hello $sanitizedFirstName $sanitizedLastName,\n\n"
               . "Your account has been successfully created!\n"
               . "Student created accounts require lecturer approval prior to accessing the platform.\n\n"
               . "This is an automated message — please do not reply.";

    // Prepare the headers for the email.
    $fromEmail = "accounts@Inquisitive.remote.ac";
    $headers  = "From: $fromEmail\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8";

   // Send the email to the user's email address.
    if (mail($sanitizedEmail, $emailSubject, $emailBody, $headers)) {
        error_log("Signup email sent to: $sanitizedEmail");
        return true;
    } else {
        error_log("Signup email FAILED for: $sanitizedEmail");
        return false;
    }
}