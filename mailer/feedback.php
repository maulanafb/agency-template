<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'inc/Exception.php';
require 'inc/PHPMailer.php';
require 'inc/SMTP.php';

require 'forms_settings.php'; // recipient data

if ( !empty($_POST) ) {

	$name = $_POST['name'];
	$tel = $_POST['tel'];
	$email = $_POST['email'];
	$message = $_POST['message'];

	$messages  = "<h3>New message from the site " .$fromName. "</h3> \r\n";
	$messages .= "<ul>";
	$messages .= "<li><strong>Name: </strong>" .$name."</li>";
	$messages .= "<li><strong>Email: </strong>" .$email."</li>";
	$messages .= "<li><strong>Phone: </strong>" .$tel."</li>";
	$messages .= "<li><strong>Message: </strong>" .$message."</li>";
	$messages .= "</ul> \r\n";

    $mail = new PHPMailer;

    // Configure the PHPMailer instance
    //$mail->isSMTP();
    //$mail->Host = 'your_smtp_host'; // Your SMTP Host
    //$mail->SMTPAuth = true;
    //$mail->Username = 'your_email_address'; // Your Email Address
    //$mail->Password = 'your_password'; // Your Email Password
    //$mail->Port = 465; // SMTP Port

    //Recipients
    $mail->From = $from;
    $mail->FromName = $fromName;

    $mail->addAddress($to, 'Admin'); //Add a recipient

    //Attachments
    $ext = PHPMailer::mb_pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
    $uploadfile = tempnam(sys_get_temp_dir(), hash('sha256', $_FILES['userfile']['name'])) . '.' . $ext;
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        if (!$mail->addAttachment($uploadfile, $_FILES['userfile']['name'])) {
            $rfile = 'Failed to attach file ' . $_FILES['userfile']['name'];
        }
    } else {
        $rfile = 'Failed to move file to ' . $uploadfile;
    }

    //Content
    $mail->isHTML(true); //Set email format to HTML
    $mail->CharSet = $charset;
    $mail->Subject = $subj;
    $mail->Body    = $messages;

    //send the message, check for errors
    if (!$mail->send()) {
        //mailer error
        $result = "error";
        $status = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    } else {
        //message sent
        $result = "success";
    }

    echo json_encode(["result" => $result, "resultfile" => $rfile, "status" => $status]);
    exit;
} else {
    echo json_encode(["result" => $result, "resultfile" => $rfile, "status" => $status]);
    exit;
}

?>