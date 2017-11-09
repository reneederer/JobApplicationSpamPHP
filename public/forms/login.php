<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('/var/www/html/jobApplicationSpam/src/validate.php');
    require_once('/var/www/html/jobApplicationSpam/src/dbFunctions.php');
    require_once('/var/www/html/jobApplicationSpam/src/useCase.php');
    require_once('/var/www/html/jobApplicationSpam/src/config.php');
    $loginMsg = '';
    if(isset($_POST['loginEmail']))
    {
        $dbConn = new PDO('mysql:host=localhost;dbname=' . getConfig()['database']['database'], getConfig()['database']['username'], getConfig()['database']['password']);
        $taskResult = ucLogin($dbConn, $_POST['loginEmail'], $_POST['loginPassword']);
        if($taskResult->isValid)
        {
            include('/var/www/html/jobApplicationSpam/public/forms/addEmployer.php');
        }
        else
        {
            $loginMsg = 'Entschuldigung, die Anfrage konnte nicht verarbeitet werden. ' . implode(". ", $taskResult->errors);
        }
    }
    echo $loginMsg;
?>

