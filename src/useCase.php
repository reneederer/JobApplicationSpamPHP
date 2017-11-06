<?php

require_once('src/validate.php');
require_once('src/dbFunctions.php');

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








