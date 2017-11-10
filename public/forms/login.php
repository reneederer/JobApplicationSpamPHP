<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if(!isset($_SESSION)) { session_start(); }
    require_once('file:///var/www/html/jobApplicationSpam/src/validate.php');
    require_once('file:///var/www/html/jobApplicationSpam/src/dbFunctions.php');
    require_once('file:///var/www/html/jobApplicationSpam/src/useCase.php');
    require_once('file:///var/www/html/jobApplicationSpam/src/config.php');
    $loginMsg = '';
    if(isset($_POST['loginEmail']))
    {
        $dbConn = new PDO('mysql:host=localhost;dbname=' . getConfig()['database']['database'], getConfig()['database']['username'], getConfig()['database']['password']);
        $taskResultAndUserId = ucLogin($dbConn, $_POST['loginEmail'], $_POST['loginPassword']);
        $taskResult = $taskResultAndUserId[0];
        $userId = $taskResultAndUserId[1];
        if($taskResult->isValid)
        {
            $_SESSION['userId'] = $userId;
            $_SESSION['userEmail'] = $_POST['loginEmail'];
            include('file:///var/www/html/jobApplicationSpam/public/forms/addEmployer.php');
        }
        else
        {
            $_SESSION['userId'] = $userId;
            $loginMsg = 'Entschuldigung, die Anfrage konnte nicht verarbeitet werden. ' . implode(". ", $taskResult->errors);
        }
    }
    echo $loginMsg;
?>

