<?php
    require_once('../vendor/phpmailer/phpmailer/src/Exception.php');
    require_once('../vendor/phpmailer/phpmailer/src/PHPMailer.php');
    require_once('../vendor/phpmailer/phpmailer/src/SMTP.php');

    use PHPMailer\PHPMailer\PHPMailer;


    function getNonExistingFileName($baseDir, $ext)
    {
        $fileName = "";
        do
        {
            $fileName = $baseDir . uniqid() . ($ext === '' ? '' : ".$ext");
        } while(file_exists($fileName));
        return $fileName;
    }

    function sendMail($from, $fromName, $subject, $body, $pdfAttachment, $to, $attachments)
    {
        try
        {
            $email = new PHPMailer(true);
            $email->CharSet = 'UTF-8';
            $email->Host = 'tls://smtp.gmail.com';
            $email->Port = 587;
            $email->SMTPAuth = true;
            $email->SMTPSecure = 'tls';
            $email->IsSMTP();
            $email->Username = 'rene.ederer.nbg@gmail.com';
            $email->Password = 'Steinmetzstr9';
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
            $email->AddAttachment($pdfAttachment, $pdfAttachment);
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
