<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $dbConn = new PDO('mysql:host=localhost;dbname=jobApplication', 'root', '1234');
    $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbConn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbConn->exec("SET NAMES utf8");

    $renePassword = password_hash("1234", PASSWORD_DEFAULT);
    $helmutPassword = password_hash("1234", PASSWORD_DEFAULT);
    $str = file_get_contents('/var/www/html/jobApplicationSpam/jobApplication.sql');
    $str = str_replace("\$renePassword", $renePassword, $str);
    $str = str_replace("\$helmutPassword", $helmutPassword, $str);
    file_put_contents('/var/www/html/jobApplicationSpam/jobApplication1.sql', $str);

    $handle = fopen("/var/www/html/jobApplicationSpam/jobApplication1.sql", "r");
    if($handle)
    {
        while(($line = fgets($handle)) !== false)
        {
            if(trim($line) === '')
            {
                continue;
            }
            echo $line . "<br>";
            $dbConn->exec($line);
        }
        fclose($handle);
    }
    else
    {
        echo "Couldn't open /var/www/html/jobApplicationSpam/jobApplication1.sql";
    }

    unlink('/var/www/html/jobApplicationSpam/jobApplication1.sql');





