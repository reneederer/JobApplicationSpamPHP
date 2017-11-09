<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    session_start();
    require_once('/var/www/html/jobApplicationSpam/src/validate.php');
    require_once('/var/www/html/jobApplicationSpam/src/dbFunctions.php');
    require_once('/var/www/html/jobApplicationSpam/src/useCase.php');
    require_once('/var/www/html/jobApplicationSpam/src/config.php');
    require_once('/var/www/html/jobApplicationSpam/src/helperFunctions.php');
    $registerNewUserMsg = '';
    var_dump($_POST);
    if(isset($_POST['registerEmail']))
    {
        $dbConn = new PDO('mysql:host=localhost;dbname=' . getConfig()['database']['database'], getConfig()['database']['username'], getConfig()['database']['password']);
        $taskResult = ucRegisterNewUser($dbConn, $_POST['registerEmail'], $_POST['registerPassword'], $_POST['registerPasswordRepeated'], $sendMail);
        if($taskResult->isValid)
        {
            $registerNewUserMsg = 'Wir haben dir eine Email mit einem Link zugesandt. Bitte besuche diese Website, um deine Email-Adresse zu bestätigen.';
        }
        else
        {
            $registerNewUserMsg = 'Entschuldigung, die Anfrage konnte nicht verarbeitet werden. ' . implode(". ", $taskResult->errors);
        }
    }
    echo $registerNewUserMsg;
?>

