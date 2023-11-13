<?php
/** Send an attached image */
const DEBUG = false;
$imgName   = 'card.png';
$pngImage  = imagecreatefrompng($imgName);
imagealphablending($pngImage, true);
imagesavealpha($pngImage, true);
$cardCode  = mt_rand(100000000, 999999999);
$textColor = imagecolorallocate($pngImage, 0, 0, 10);
$fontPath  = __DIR__ . '/font.ttf';
imagettftext($pngImage, 24, 0, 112, 180, $textColor, $fontPath, $cardCode);
if (DEBUG) {
    header('Content-Type: image/png');
    imagepng($pngImage);
} else {
    ob_start();
    imagepng($pngImage);
    $imageData = ob_get_clean();
    $fromEmail = $replyToEmail = 'from@email.com';
    $ccEmail = 'cc@email.com';
    $recipientEmail = 'recipientr@email.com';
    $senderName = 'Sender Name';
    $subject  = 'Subject';
    $message  = "<p>Code number: <b>$cardCode</b></p>";
    $type = 'image/png';
    $encodedContent = chunk_split(base64_encode($imageData));
    $boundary = md5("random"); // Define boundary with a md5 hashed value
    //Header
    $headers = "MIME-Version: 1.0\r\n"; // Defining the MIME version
    $headers .= "From: $senderName <$fromEmail>\r\n";
    $headers .= "Cc: $ccEmail\r\n";
    $headers .= "Reply-To: $replyToEmail\r\n";
    $headers .= "Content-Type: multipart/mixed;"; // Defining Content-Type
    $headers .= "boundary = $boundary\r\n"; //Defining the Boundary
    // Html text
    $body = "--$boundary\r\n";
    $body .= "Content-Type: text/html; charset=utf-8\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $body .= chunk_split(base64_encode($message));
    // Attachment
    $body .= "--$boundary\r\n";
    $body .= "Content-Type: $type; name=$imgName\r\n";
    $body .= "Content-Disposition: attachment; filename=$imgName\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "X-Attachment-Id: " . rand(1000, 99999) . "\r\n\r\n";
    $body .= $encodedContent; // Attaching the encoded file with email
    echo mail($recipientEmail, $subject, $body, $headers) ? "OK" : "ERROR";
}
imagedestroy($pngImage);
