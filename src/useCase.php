<?php

require_once('validate.php');
require_once('dbFunctions.php');
require_once('helperFunctions.php');

function ucLogin($dbConn, $email, $password)
{
    $userData = getUserDataByEmail($dbConn, $email);
    $taskResult = new TaskResult(true, [], []);
    if(count($userData) <= 0 || password_verify($password, $userData['password']) === false)
    {
        $taskResult->setInvalidWithErrorMsg('Email-Adresse oder Passwort ist falsch.');
    }
    else if(!is_null($userData['confirmationString']))
    {
        $taskResult->setInvalidWithErrorMsg('Bitte bestätige deine Email-Adresse. Wir haben dir einen Link per Email geschickt.');
    }
    return [$taskResult, $userData['userId'] ?? -1];
}

function ucLogout()
{
    //return a non-existing user id
    return -1;
}

function ucRegisterNewUser($dbConn, $email, $password, $passwordRepeated, $sendMailFunc)
{
    if($password !== $passwordRepeated)
    {
        return new TaskResult(false, ['Passworte stimmen nicht überein'], []);
    }
    $taskResult = new TaskResult(true, [], []);
    if(filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        if(isEmailRegistered($dbConn, $email))
        {
            return new TaskResult(false, ['Diese Email-Adresse ist schon registriert.'], []);
        }
        $confirmationString = bin2hex(random_bytes(16));
        $taskResult = $sendMailFunc('admin@bewerbungsspam.de'
            , 'www.bewerbungsspam.de'
            , 'Bitte bestätige deine Anmeldung bei www.bewerbungsspam.de'
            , "Hallo,\n\nbitte besuche diesen Link um deine Email-Addresse zu bestätigen: http://localhost/jobApplicationSpam/src/index.php?email=$email&confirmationString=$confirmationString\n\nViele Grüße\n\nDein Team von www.bewerbungsspam.de"
            , $email
            , null);
        addUser($dbConn, $email, password_hash($password, PASSWORD_DEFAULT), $confirmationString);
        return $taskResult;
    }
    else
    {
        $taskResult->setInvalidWithErrorMsg("Your email doesn't look valid.");
        return $taskResult;
    }
}

function ucConfirmEmailAddress($dbConn, $email, $userConfirmationString)
{
    $realConfirmationString = getConfirmationString($dbConn, $email);
    if($realConfirmationString === $userConfirmationString)
    {
        setEmailConfirmed($dbConn, $email);
        return new TaskResult(true, [], []);
    }
    else
    {
        return new TaskResult(false, ['Bestätigungs-Codes stimmen nicht überein'], []);
    }
}

function ucUploadJobApplicationTemplate($dbConn, $userId, $template, $odt, $pdfs)
{
    $templateValidationResult = new TaskResult(true, [], []);
    try
    {
        $templateValidationResult = validateTemplate($template, $odt, $pdfs);
        if(!$templateValidationResult->isValid)
        {
            return $templateValidationResult;
        }
        $baseDir = '/var/www/userFiles/';
        $odtPath = getNonExistingFileName($baseDir, 'odt');
        move_uploaded_file($odt['tmp_name'], $odtPath);

        $pdfPaths = [];
        for($i = 0; $i < count($pdfs['tmp_name']); ++$i)
        {
            if($pdfs['tmp_name'][$i] !== '')
            {
                $currentPdfPath = getNonExistingFileName($baseDir, 'pdf');
                move_uploaded_file($pdfs['tmp_name'][$i], $currentPdfPath);
                $pdfPaths[] = $currentPdfPath;
            }
        }
        addJobApplicationTemplate($dbConn , $userId , $template + [ 'odtPath' => $odtPath , 'pdfPaths' => $pdfPaths ]);
        return $templateValidationResult;
        //$currentMessage .= join('<br>', $templateValidationResult->errors);
    }
    catch(\Exception $e)
    {

        die($e->getMessage());
        $currentMessage .= $e->getMessage();
    }
}

function ucAddEmployer($dbConn, $userId, $employer)
{
    $validateResult = validateEmployer($employer);
    if($validateResult->isValid)
    {
        addEmployer($dbConn, $userId, $employer);
    }
    return $validateResult;
}


function ucSetUserDetails($dbConn, $userId, $userDetails)
{
    $validateUserDetailsResult = validateUserDetails($userDetails);
    if($validateUserDetailsResult->isValid)
    {
        setUserDetails($dbConn, $userId, $userDetails);
    }
    return $validateUserDetailsResult;
}

function ucApplyNow($dbConn, $userId, $employerId, $templateId, $mailToUserOnly)
{

    if($mailToUserOnly === false)
    {
        throw new \Exception('mail for real not implemented!');
    }
    $employerValuesDict = getEmployer($dbConn, $userId, $employerId);
    $userDetails = getUserDetails($dbConn, $userId);
    $userEmail = getEmailByUserId($dbConn, $userId);
    if(is_null($userDetails))
    {
        $currentMessage = 'Userdetails konnten nicht gefunden werden.';
        return;
    }
    else
    {
        $userDetailsDict =
            [ '$meinTitel' => $userDetails['degree']
            , '$meineAnrede' => $userDetails['gender']
            , '$meinVorname' => $userDetails['firstName']
            , '$meinNachname' => $userDetails['lastName']
            , '$meineStrasse' => $userDetails['street']
            , '$meinePlz' => $userDetails['postcode']
            , '$meineStadt' => $userDetails['city']
            , '$meineEmail' => $userEmail
            , '$meineTelefonnr' => $userDetails['phone']
            , '$meineMobilnr' => $userDetails['mobilePhone']
            , '$meinGeburtsdatum' => $userDetails['birthday']
            , '$meinGeburtsort' => $userDetails['birthplace']
            , '$meinFamilienstand' => $userDetails['maritalStatus'] ];
        $dict = $employerValuesDict + $userDetailsDict +
            [ "\$geehrter" => $employerValuesDict["\$chefAnrede"] === 'm' ? 'geehrter' : 'geehrte'
            , "\$chefAnredeBriefkopf" => $employerValuesDict["\$chefAnrede"] === 'm' ? 'Herrn' : 'Frau'
            , "\$datumHeute" => date('d.m.Y')];
        $dict["\$chefAnrede"] = $employerValuesDict["\$chefAnrede"] === 'm' ? 'Herr' : 'Frau';

        $jobApplicationTemplate = getJobApplicationTemplate($dbConn, $userId, $templateId);
        $pdfDirectoryAndFile = getPDF($jobApplicationTemplate['odtPath'], $dict, '/var/www/userFiles/tmp/', str_replace(' ', '_', mb_strtolower($userDetails['lastName'] . '_bewerbung')));
        addJobApplication($dbConn, $userId, $employerId, $templateId);


        $pdfAppendices = getPdfAppendices($dbConn, $templateId);
        $pdfUniteCommand = 'pdfunite ' . ($pdfDirectoryAndFile[0] . $pdfDirectoryAndFile[1]);
        foreach($pdfAppendices as $currentPdfAppendix)
        {
            $pdfUniteCommand .= ' ' . $currentPdfAppendix['pdfFile'];
        }
        $pdfFileName = $pdfDirectoryAndFile[0] . $pdfDirectoryAndFile[1];
        exec($pdfUniteCommand . ' ' . $pdfFileName . ' 2>1', $output);

        $taskResult = sendMail( $userEmail
            , $userDetails['firstName'] . ' ' . $userDetails['lastName']
            , replaceAllInString($jobApplicationTemplate['emailSubject'], $dict)
            , replaceAllInString($jobApplicationTemplate['emailBody'], $dict)
            , $mailToUserOnly ? $userEmail : $userEmail //TODO $employerValuesDict['$firmaEmail']
            , $pdfFileName);
        if($taskResult->isValid)
        {
            //$currentMessage .= 'Email wurde versandt.';
        }
        else
        {
            //$currentMessage .= join('<br>', $taskResult->errors);
        }
    }
}








