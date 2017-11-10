<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if(!isset($_SESSION)) { session_start(); }
    require_once('file:///var/www/html/jobApplicationSpam/src/validate.php');
    require_once('file:///var/www/html/jobApplicationSpam/src/dbFunctions.php');
    require_once('file:///var/www/html/jobApplicationSpam/src/useCase.php');
    require_once('file:///var/www/html/jobApplicationSpam/src/config.php');
    require_once('file:///var/www/html/jobApplicationSpam/src/config.php');
    session_unset();
    include('file:///var/www/html/jobApplicationSpam/public/forms/addEmployer.php');
?>

