<?php
include_once 'Mail.php';
include_once 'Mail/mime.php';
require_once 'Mail/IMAPv2.php';

class qMail {
    /**
     * Enter description here...
     *
     * @param unknown_type $message
     * @param unknown_type $email
     * @param unknown_type $subject
     * @param unknown_type $from
     * @param array $attachments
     * $attachment['body']   -  имя файла или содержимое файла
     * $attachment['ftype']  -  mime-type изображение или файл ('application/octet-stream')
     * $attachment['name']   -  если body - это содержимое, иначе ''
     * $attachment['isFile'] -  файл (true) или содержимое
     */
    public static function mail($message, $email, $subject = '', $from = '', $attachments = array(), $images = array(),$hdrsin = array()) {
        if (String::email($email)) {
            $crlf = "\n";
            //$hdrs .= $hdrsin;
            $hdrs = array(
                'From'    => $from,
                'Subject' => $subject,
                'Content-Type' => 'text/html; charset="windows-1251"',
            );
            $mime = new Mail_mime($crlf);
            //$mime->setTxtBody($message);
            $mime->setTxtBody(strip_tags($message));
            $mime->setHTMLBody($message);
            if ($attachments) {
                foreach ($attachments as $id => $attachment) {
                    $isFile = (isset($attachment['isFile'])) ? $attachment['isFile'] : true;
                    $mime->addAttachment($attachment['body'], $attachment['ftype'], $attachment['name'], $isFile);
                }
            }
            if ($images) {
                foreach ($images as $image) {
                    $mime->addHTMLImage($image);
                }
            }
            $body = $mime->get();
            $hdrs = $mime->headers($hdrs);
            $mail = Mail::factory('mail');
            $mail->send($email, $hdrs, $body);
        }
    }

    public static function send($message, $email, $subject = '', $from = '', $attachments = array(), $images = array(),$hdrsin = array()) {
        self::mail($message, $email, $subject, $from, $attachments, $images, $hdrsin);
    }
}