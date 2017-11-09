<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('/var/www/html/jobApplicationSpam/vendor/phpmailer/phpmailer/src/Exception.php');
    require_once('/var/www/html/jobApplicationSpam/vendor/phpmailer/phpmailer/src/PHPMailer.php');
    require_once('/var/www/html/jobApplicationSpam/vendor/phpmailer/phpmailer/src/SMTP.php');
    require_once('/var/www/html/jobApplicationSpam/src/config.php');

    use PHPMailer\PHPMailer\PHPMailer;


    function getNonExistingFileName($baseDir, $ext)
    {
        $fileName = '';
        do
        {
            $fileName = $baseDir . uniqid() . ($ext === '' ? '' : ".$ext");
        } while(file_exists($fileName));
        return $fileName;
    }

    $sendMail = function($from, $fromName, $subject, $body, $to, $pdfAttachment)
    {
        if(is_null($from))
        {
            return new TaskResult(false, ['Sender email was null'], []);
        }
        if(is_null($to))
        {
            return new TaskResult(false, ['Receiver email was null'], []);
        }
        try
        {
            $email = new PHPMailer(true);
            $email->CharSet = 'UTF-8';
            $email->Host = 'tls://smtp.gmail.com';
            $email->Port = 587;
            $email->SMTPAuth = true;
            $email->SMTPSecure = 'tls';
            $email->IsSMTP();
            $email->Username = getConfig()['email']['username'];
            $email->Password = getConfig()['email']['password'];
            $email->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true));
            $email->From = $from;
            $email->FromName = $fromName;
            $email->Subject = $subject;
            $email->Body = $body;
            $email->AddBCC($from);
            $email->AddAddress($to);
            if(!is_null($pdfAttachment)) { $email->AddAttachment($pdfAttachment, $pdfAttachment); }
            $email->Send();
        }
        catch(phpmailerException $e)
        {
            die($e->errorMessage());
            return new TaskResult(false, ['Email konnte nicht versandt werden'], []);
        }
        catch(\Exception $e)
        {
            die($e->errorMessage());
            return new TaskResult(false, ['Email konnte nicht versandt werden'], []);
        }
        return new TaskResult(true, [], []);
    }

?>
