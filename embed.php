<?php
/** Send an embedded image */
const DEBUG = false;
$imgName = 'card.png';
$pngImage = imagecreatefrompng($imgName);
imagealphablending($pngImage, true);
imagesavealpha($pngImage, true);
$cardCode = mt_rand(100000000, 999999999);
$textColor = imagecolorallocate($pngImage, 0, 0, 10);
$fontPath = __DIR__ . '/font.ttf';
imagettftext($pngImage, 24, 0, 112, 180, $textColor, $fontPath, $cardCode);
if (DEBUG) {
    header('Content-Type: image/png');
    imagepng($pngImage);
} else {
    ob_start();
    imagepng($pngImage);
    $imageData = base64_encode(ob_get_clean());
    $fromEmail = 'from@email.com';
    $recipientEmail = 'recipientr@email.com';
    $subject  = 'Subject';
    $message  = "<p>Code number: <b>$cardCode</b></p>";
    $message .= "\r\n<img src='data:image/png;base64,$imageData'>\r\n";
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "From:" . $fromEmail . "\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    echo mail($recipientEmail, $subject, $message, $headers) ? "OK" : "ERROR";
}
imagedestroy($pngImage);
