<?php

/**
*@desc
*/
class Mail
{
     const SMTP_MAILPOSTMASTER_SERVER = 'loop.vn';
     const CONTENT_TYPE_TEXT_PLAIN = 'text/plain';
     const CONTENT_TYPE_TEXT_HTML = 'text/html';

    /**
    *@desc sendmail though sendmail on nix system.
    * send mail is OK if mail send successfully to unix MailTransferAgent (it means your task is completed, the rest task of sending mail is of MTA).
    * send mail false if unix server config fail, can not handle your send mail request, it is because of your problem in your code
    * @param string mail send from which address, it will set the reply-to
    * @param string mail send to which address?
    * @param string subject
    * @param string content body of message
    * @return true if ok. false if fail
    */
    public static function sendmailUNIX($from, $to, $subject, $content, $contentType = Mail::CONTENT_TYPE_TEXT_PLAIN)
    {
        if(empty($from) || empty($to) || empty($content) || empty($subject))
        {
            return false;
        }

        $nRandom = rand(500, 1000);
        $contentType = ($contentType === Mail::CONTENT_TYPE_TEXT_HTML) ?
                        Mail::CONTENT_TYPE_TEXT_HTML : Mail::CONTENT_TYPE_TEXT_PLAIN;
        //Header is the portinn where we will set the from address, reply to address, cc address, etc....
        //The display of address we see on the top (from,to,replyto) portion of the composer will be based on the header we set
        $headers =  "From: $from\r\n" .
                    //"To: $to\r\n" .
                    "Reply-To: $from\r\n" .
                    'X-DKIM: Sendmail DKIM Filter v2.2.1 '.SMTP_MAILPOSTMASTER_SERVER  ." $nRandom\r\n" .
                    'X-DomainKeys: Sendmail DomainKeys Filter v0.6.0 '.SMTP_MAILPOSTMASTER_SERVER. " $nRandom\r\n" .
                    "X-Campaignid: gurucoreunix$nRandom\r\n" .
                    "MIME-Version: 1.0\r\n" .
                    "Content-type: $contentType; charset=utf-8\r\n";

        $mailsendresult = mail($to, $subject, $content, $headers);
        return $mailsendresult;
    }
}
?>