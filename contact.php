<?php
// See if we've had a form submitted to us
if ($_POST['submit']) {
 // form submitted - check for validity..?
 if ($_POST['name']=="" && $_POST['email']=="" && $_POST['subject']=="" && $_POST['howhear']=="0" && $_POST['enquiry']=="") {
  // Nothing filled in on the form!
  // No point in sending an email, might as well just give them a blank form back again!..
 } else {
  // all seems to be well, so deal with form
  $name = trim($_REQUEST['name']);
  $email = trim($_REQUEST['email']);
  $subject = trim($_REQUEST['subject']);
  $howhear = trim($_REQUEST['howhear']);
  $enquiry = trim($_REQUEST['enquiry']);
  if (get_magic_quotes_gpc()) {
   $name = stripslashes($name);
   $email = stripslashes($email);
   $subject = stripslashes($subject);
   $howhear = stripslashes($howhear);
   $enquiry = stripslashes($enquiry);
  }
  $body = "Subject: " . ($subject ? $subject : '<none>') . "\n";
  $body .= "Name: " . ($name ? $name : '<none>') . "\n";
  $body .= "Email Address: " . ($email ? $email : '<none>') . "\n";
  $body .= "How they heard about us: ";
  switch ($howhear) {
    case "":
    case "0":$body .= "<nothing selected>"; break;
    case "SawProduction":$body .= "Saw a Production"; break;
    case "SawPerformance":$body .= "Saw a Performance"; break;
    case "ReadReview":$body .= "Read a Review"; break;
    case "WOM":$body .= "Word of Mouth"; break;
    case "Ambassador":$body .= "Fail Better Ambassador"; break;
    case "ProdFlyer":$body .= "Production Flyer"; break;
    case "ProdPoster":$body .= "Production Poster"; break;
    case "ProdPublicity":$body .= "Production Publicity"; break;
    case "CAPITAL":$body .= "The CAPITAL Centre"; break;
    case "A7Comms":$body .= "A7 Communications Site"; break;
    case "EdEvent":$body .= "Education Event"; break;
    case "Facebook":$body .= "Facebook"; break;
    case "Accident":$body .= "Chance Find"; break;
    default:$body .= '"' . $howhear . '"';
  }
  // Don't really need this newline... not quite sure why it's here!
  $body .= "\n";
  $body .= "Enquiry:" . ($enquiry ? "\n".$enquiry : ' <none>') . "\n";
  // Ensure that lines are no longer than 70 characters
  $body = wordwrap ($body, 70);
  // $to = "info@failbetter.co.uk";
  // $to = "test@dave.thwaites.org.uk";
  $to = "failbetterproductions@hotmail.com";
  $safe_text = "/^[a-zA-Z0-9!£$%^*()\\-_+='~#,\\.? ]*$/";
  $email_subject = "[failbetter.co.uk] ";
  if ($subject!="" && preg_match($safe_text, $subject)) {
//      (strpos($subject, "\r")!==false) &&
//      (strpos($subject, '\r')!==false) &&
//      (strpos($subject, "\n")!==false) &&
//      (strpos($subject, '\n')!==false) &&
//      (stripos($subject, "Content-Transfer-Encoding")!==false) &&
//      (stripos($subject, "MIME-Version")!==false) &&
//      (strpos($subject, 'Content-Type')!==false)) {
   if (strlen($subject) > 60) $email_subject .= substr($subject,0,57) . "...";
   else $email_subject .= $subject;
  } else {
   $email_subject .= "Automated mail from FailBetter.co.uk";
  }
  $headers = "From: Fail Better Website <info@failbetter.co.uk>\r\n";
  // e-mail address validation
  $safe_email = "/^[-+\\.0-9=a-z_]+@([-0-9a-z]+\\.)+([0-9a-z]){2,4}$/i";
  if ($email != "" && preg_match($safe_email, $email) && strlen($email) <= 60) {
   if ($name != "" && preg_match($safe_text, $name) && strlen($name) <= 30) {
    $headers .= "Reply-To: \"" . $name . "\" <" . $email . ">\r\n";
   } else {
    $headers .= "Reply-To: " . $email . "\r\n";
   }
  } else {
   // 
   $headers .= "Reply-To: \"Change this address before hitting Send!..\" <" . $to . ">\r\n";
  }
  $headers .= "X-Mailer: PHP/" . phpversion();
  // finally, send the email
  mail($to, $email_subject, $body, $headers);

  // also send a similar email to Dave, for hacker-checking purposes!
  $body .= "\n\n";
  $body .= "Headers sent to Fail Better:\n";
  $body .= "To: " . $to . "\n";
  $body .= "Subject: " . $email_subject . "\n";
  // $body .= "Other headers\n";
  $body .= $headers . "\n";
  $warnings = "";
  if ($subject!="" && !preg_match($safe_text, $subject)) $warnings .= "Suspicious subject line - not used\n";
  if (strlen($subject) > 60) $warnings .= "Overly long subject line - truncated\n";
  if ($email!="" && !preg_match($safe_email, $email)) $warnings .= "Suspicious email address - not used\n";
  if (strlen($email) > 60) $warnings .= "Overly long email address - not used\n";
  if ($name!="" && !preg_match($safe_text, $name)) $warnings .= "Suspicious name - not used\n";
  if (strlen($name) > 30) $warnings .= "Overly long name - not used\n";
  if ($warnings) $body .= "\nWarnings:\n" . $warnings;
  $body .= "\n";
  $body .= "User Agent: " . $_SERVER["HTTP_USER_AGENT"] . "\n";
  $body .= "HTTP Host: " . $_SERVER["HTTP_HOST"] . "\n";
  $body .= "Server Name: " . $_SERVER["SERVER_NAME"] . "\n";
  $body .= "Remote Address: " . $_SERVER["REMOTE_ADDR"] . "\n";
  $body = wordwrap ($body, 70);
  mail("failbetter-web@dave.thwaites.org.uk", "[failbetter.co.uk] Automated mail from FailBetter.co.uk", $body, "From: Fail Better Website <info@failbetter.co.uk>\r\nReply-To: \"Change this address!..\" <failbetter-web@dave.thwaites.org.uk>\r\n");

  // and re-direct the user to the 'thank-you' page
  $host = $_SERVER['HTTP_HOST'];
  $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  $page = 'contact_done.html';
  // Default status code is 302 'Found', 303 'See Other' is slightly better (although maybe not so well supported?)
  header("Location: http://$host$uri/$page", true, 303);
  // don't need to send anything else, so...
  exit;
 }
}

// ...otherwise nothing has been submitted, so show the form!..
readfile("./contact.html");
?>