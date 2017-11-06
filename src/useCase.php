<?php

require_once('validate.php');
require_once('dbFunctions.php');

function ucLogin($dbConn, $email, $password)
{
    $userIdAndPassword = getUserIdAndPasswordByEmail($dbConn, $email);
    if(!empty($userIdAndPassword) && password_verify($password, $userIdAndPassword['password']))
    {
        $_SESSION['userId'] = $userIdAndPassword['userId'];
    }
    else
    {
        die("Failed to login");
        //TODO print error message
    }
}

function ucLogout()
{
    $_SESSION['userId'] = -1;
}

function ucRegisterNewUser($dbConn, $email, $password)
{
    //TODO send confirmation email
    if(filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        addUser($dbConn, $email, password_hash($password, PASSWORD_DEFAULT));
    }
    else
    {
        $currentMessage = 'Email doesn\'t look valid.';
    }
}

function ucUploadJobApplicationTemplate($dbConn, $userId, $templateName, $userAppliesAs, $templateEmailSubject, $templateEmailBody, $fileODT, $fileAppendices)
{
    try
    {
        $templateDataResult = validateTemplateData(
            new TemplateData($templateName,
                             $userAppliesAs,
                             $templateEmailSubject,
                             $templateEmailBody,
                             $fileODT,
                             $fileAppendices));
        if($templateDataResult->isValid){
            $baseDir = '/var/www/userFiles/';
            $odtPath = getNonExistingFileName($baseDir, 'odt');
            move_uploaded_file($fileODT['tmp_name'], $odtPath);

            $appendixPaths = [];
            for($i = 0; $i < count($fileAppendices['tmp_name']); ++$i)
            {
                if($fileAppendices['tmp_name'][$i] !== '')
                {
                    $pdfAppendixPath = getNonExistingFileName($baseDir, 'pdf');
                    move_uploaded_file($fileAppendices['tmp_name'][$i], $pdfAppendixPath);
                    $appendixPaths[] = $pdfAppendixPath;
                }
            }
            addJobApplicationTemplate( $dbConn
                                     , $userId
                                     , $templateName
                                     , $userAppliesAs
                                     , $templateEmailSubject
                                     , $templateEmailBody
                                     , $odtPath
                                     , $appendixPaths);
        }
        //$currentMessage .= join('<br>', $templateDataResult->errors);
    }
    catch(\Exception $e)
    {
        die($e->getMessage());
        $currentMessage .= $e->getMessage();
    }
}

function ucAddEmployer($dbConn, $userId,
                     $company,
                     $companyStreet,
                     $companyPostcode,
                     $companyCity,
                     $bossGender,
                     $bossDegree,
                     $bossFirstName,
                     $bossLastName,
                     $bossEmail,
                     $bossMobilePhone,
                     $bossPhone)
{
    $employer = new Employer(
                 $company,
                 $companyStreet,
                 $companyPostcode,
                 $companyCity,
                 $bossGender,
                 $bossDegree,
                 $bossFirstName,
                 $bossLastName,
                 $bossEmail,
                 $bossMobilePhone,
                 $bossPhone);
    $validateResult = validateEmployer($employer);
    if($validateResult->isValid)
    {
        addEmployer($dbConn, $userId, $employer);
    }
    else
    {
        $currentMessage .= join("<br>", $validateResult->errors);
    }
}


function ucSetUserDetails($dbConn, $userId,
                          $userGender,
                          $userDegree,
                          $userFirstName,
                          $userLastName,
                          $userStreet,
                          $userPostcode,
                          $userCity,
                          $userMobilePhone,
                          $userPhone,
                          $userBirthday,
                          $userBirthplace,
                          $userMaritalStatus)
{
    $userDetails = new UserDetails($userGender,
                                   $userDegree,
                                   $userFirstName,
                                   $userLastName,
                                   $userStreet,
                                   $userPostcode,
                                   $userCity,
                                   $userMobilePhone,
                                   $userPhone,
                                   $userBirthday,
                                   $userBirthplace,
                                   $userMaritalStatus);
    $checkUserDetailsResult = validateUserDetails($userDetails);
    if($checkUserDetailsResult->isValid)
    {
        setUserDetails($dbConn, $userId, $userDetails);
    }
    else
    {
        $currentMessage .= join("<br>", $checkUserDetailsResult->errors);
    }

}








