<?php
/****************************************************************************
CLASS email
-----------------------------------------------------------------------------
Task:
  Send emails
****************************************************************************/

class Email{

  /**************************************************************************
  send
  ---------------------------------------------------------------------------
  Task:
    Send email
  ---------------------------------------------------------------------------
  Parameters:
    $from             string            Sender
    $to               string            Receiver
    $subject          string            Email subject
    $body             string            Email body
  ---------------------------------------------------------------------------
  Return: --
  **************************************************************************/
  public function send($from_email = "", $from_name = "", $to = "", $subject = "", $body = "") {
    // Validate the recipient email
    if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
      return "Invalid recipient email address.";
    }

    // Validate the subject and body
    if (empty($subject) || empty($body)) {
      return "Subject and body cannot be empty.";
    }

    // Prepare headers
    $headers = "";

    // Format sender's email if name is provided
    if ($from_name) {
      $from = $from_name . " <" . $from_email . ">";
    } else {
      $from = $from_email;
    }

    // Add from and reply-to headers if email is provided
    if ($from_email) {
      $headers .= "From: $from" . PHP_EOL;
      $headers .= "Reply-To: $from" . PHP_EOL;
    }

    // Send email
    $mail_sent = mail($to, $subject, $body, $headers);

    // Return success or failure
    return $mail_sent ? true : "Failed to send email.";
  }
}
?>