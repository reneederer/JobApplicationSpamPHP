<?php

    class ErrorResult
    {
        public $isValid;
        public $errors;
        public $warnings;
        function __construct($isValid, $errors, $warnings)
        {
            $this->isValid = $isValid;
            $this->errors = $errors;
            $this->warnings = $warnings;
        }
        function append($isValid, $errors, $warnings)
        {
            $this->isValid = $this->isValid && $isValid;
            $this->errors = array_merge($this->errors, $errors);
            $this->warnings = array_merge($this->warnings, $warnings);
        }
        function appendErrors($errors)
        {
            $this->isValid = $this->isValid && empty($errors);
            $this->errors = array_merge($this->errors, $errors);
        }
    }


    class TemplateData
    {
        public $templateName;
        public $emailSubject;
        public $emailBody;
        public $odtFileName;
        public $pdfFileNames;
        function __construct($templateName, $emailSubject, $emailBody, $odtFileName, $pdfFileNames)
        {
            $this->templateName = $templateName;
            $this->emailSubject = $emailSubject;
            $this->emailBody = $emailBody;
            $this->odtFileName = $odtFileName;
            $this->pdfFileNames = $pdfFileNames;
        }
    };
    function validateTemplateData($templateData)
    {
        $errorResult = new ErrorResult(true, [], []);
        if(trim($templateData->templateName === ''))
        {
            $errorResult->isValid = false;
            $errorResult->errors[] = 'Der Template-Name darf nicht leer sein.';
        }
        if(strlen(trim($templateData->emailSubject)) < 5)
        {
            $errorResult->isValid = false;
            $errorResult->errors[] = 'Der Email-Betreff muss mindestens 5 Buchstaben lang sein.';
        }
        if(trim($templateData->emailBody) === '')
        {
            $errorResult->isValid = false;
            $errorResult->errors[] = 'Der Email-Body darf nicht leer sein.';
        }
        if(!((strlen(trim($templateData->odtFileName)) >= 5) && substr_compare(strtolower($templateData->odtFileName), '.odt', -4) === 0))
        {
            $errorResult->isValid = false;
            $errorResult->errors[] = 'Die ODT-Datei darf nicht leer sein und muss auf ".odt" enden.';
        }
        for($i = 0; $i < count($templateData->pdfFileNames); ++$i)
        {
            if($templateData->pdfFileNames[$i] != '')
            {
                if(!(strlen(trim($templateData->pdfFileNames[$i])) >= 5 && substr_compare(strtolower($templateData->pdfFileNames[$i]), '.pdf', -4 ) === 0))
                {
                    $errorResult->isValid = false;
                    $errorResult->errors[] = 'PDF-Datei Nr. ' . ($i + 1) . ' ist kein PDF Format.';
                }
            }
        }
        return $errorResult;
    }



class User
{
    public $gender;
    public $degree;
    public $firstName;
    public $lastName;
    public $street;
    public $postCode;
    public $city;
    public $email;
    public $mobilePhone;
    public $phone;
    function __construct($gender, $degree, $firstName, $lastName, $street, $postCode, $city, $email, $mobilePhone, $phone)
    {
        $this->gender = $gender;
        $this->degree = $degree;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->street = $street;
        $this->postCode = $postCode;
        $this->city = $city;
        $this->email = $email;
        $this->mobilePhone = $mobilePhone;
        $this->phone = $phone;
    }
};


function validateUserData($userData)
{
    $errorResult = new ErrorResult(true, [], []);
    if($userData->gender !== 'f' && $userData->gender !== 'm')
    {
        $errorResult->isValid = false;
        $errorResult->errors[] = 'Geschlecht muss "m" oder "f" sein.';
    }
    if(trim($userData->firstName) === '')
    {
        $errorResult->isValid = false;
        $errorResult->errors[] = 'Der Vorname darf nicht leer sein';
    }
    if(trim($userData->lastName) === '')
    {
        $errorResult->isValid = false;
        $errorResult->errors[] = 'Der Nachname darf nicht leer sein';
    }
    if(trim($userData->street) === '')
    {
        $errorResult->isValid = false;
        $errorResult->errors[] = 'Die Stra&szlig;e darf nicht leer sein';
    }
    if(trim($userData->postCode) === '')
    {
        $errorResult->isValid = false;
        $errorResult->errors[] = 'Die Postleitzahl darf nicht leer sein';
    }
    if(trim($userData->city) === '')
    {
        $errorResult->isValid = false;
        $errorResult->errors[] = 'Die Stadt darf nicht leer sein';
    }
    return $errorResult;
}





class Employer
{
    public $company;
    public $gender;
    public $degree;
    public $firstName;
    public $lastName;
    public $street;
    public $postCode;
    public $city;
    public $email;
    public $mobilePhone;
    public $phone;
    function __construct($company, $street, $postCode, $city, $gender, $degree, $firstName, $lastName, $email, $mobilePhone, $phone)
    {
        $this->company = $company;
        $this->gender = $gender;
        $this->degree = $degree;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->street = $street;
        $this->postCode = $postCode;
        $this->city = $city;
        $this->email = $email;
        $this->mobilePhone = $mobilePhone;
        $this->phone = $phone;
    }
};


function validateEmployer($employer)
{
    $errorResult = new ErrorResult(true, [], []);
    if(trim($employer->company) === '')
    {
        $errorResult->isValid = false;
        $errorResult->errors[] = 'Firmenname darf nicht leer sein';
    }
    if($employer->gender !== 'f' && $employer->gender !== 'm')
    {
        $errorResult->isValid = false;
        $errorResult->errors[] = "Geschlecht muss 'm' oder 'f' sein. Instead it is ." . $employer->gender . ".";
    }
    if(trim($employer->lastName) === '')
    {
        $errorResult->isValid = false;
        $errorResult->errors[] = 'Der Nachname darf nicht leer sein';
    }
    if(trim($employer->street) === '')
    {
        $errorResult->isValid = false;
        $errorResult->errors[] = 'Die Stra&szlig;e darf nicht leer sein';
    }
    if(trim($employer->postCode) === '')
    {
        $errorResult->isValid = false;
        $errorResult->errors[] = 'Die Postleitzahl darf nicht leer sein';
    }
    if(trim($employer->city) === '')
    {
        $errorResult->isValid = false;
        $errorResult->errors[] = 'Die Stadt darf nicht leer sein';
    }
    return $errorResult;
}
















?>
