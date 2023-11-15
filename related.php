<?php
/** Send an attached related image */
const DEBUG = false;
$imgName   = 'card.png';
$cid       = 'card';
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
    $fromEmail = $replyToEmail = 'from@mail.com';
    $ccEmail = '';
    $recipientEmail = 'to@mail.com';
    $senderName = 'Sender';
    $subject  = 'Test email with image';
    $message  = "<p>Code number: <b>$cardCode</b></p>";
    $message .= '<img src="cid:' . $cid . '">';
    $type = 'image/png';
    $encodedContent = chunk_split(base64_encode($imageData));
    $boundary = md5("random");
    //Header
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: $senderName <$fromEmail>\r\n";
    $headers .= "Cc: $ccEmail\r\n";
    $headers .= "Reply-To: $replyToEmail\r\n";
    $headers .= "Content-Type: multipart/related;"; // exactly this type!
    $headers .= "boundary = $boundary\r\n";
    // Html text
    $body = "--$boundary\r\n";
    $body .= "Content-Type: text/html; charset=utf-8\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $body .= chunk_split(base64_encode($message));
    // Attachment
    $body .= "--$boundary\r\n";
    $body .= "Content-Type: $type; name=$imgName\r\n";
    $body .= "Content-Disposition: attachment; filename=$imgName\r\n";
    $body .= "Content-ID: <$cid>\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "\r\n" . $encodedContent;
    echo mail($recipientEmail, $subject, $body, $headers) ? "OK" : "ERROR";
}
imagedestroy($pngImage);
