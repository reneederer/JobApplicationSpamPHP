<?php

    class ValidityCheckResult
    {
        public $isValid;
        public $errors;
        public $warnings;
        function __construct()
        {
            $this->isValid = true;
            $this->errors = [];
            $this->warnings = [];
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
        $validityCheckResult = new ValidityCheckResult();
        if(trim($templateData->templateName === ''))
        {
            $validityCheckResult->isValid = false;
            $validityCheckResult->errors[] = 'Der Template-Name darf nicht leer sein.';
        }
        if(strlen(trim($templateData->emailSubject)) < 5)
        {
            $validityCheckResult->isValid = false;
            $validityCheckResult->errors[] = 'Der Email-Betreff muss mindestens 5 Buchstaben lang sein.';
        }
        if(trim($templateData->emailBody) === '')
        {
            $validityCheckResult->isValid = false;
            $validityCheckResult->errors[] = 'Der Email-Body darf nicht leer sein.';
        }
        if(!((strlen(trim($templateData->odtFileName)) >= 5) && substr_compare(strtolower($templateData->odtFileName), '.odt', -4) === 0))
        {
            $validityCheckResult->isValid = false;
            $validityCheckResult->errors[] = 'Die ODT-Datei darf nicht leer sein und muss auf ".odt" enden.';
        }
        for($i = 0; $i < count($templateData->pdfFileNames); ++$i)
        {
            if($templateData->pdfFileNames[$i] != '')
            {
                if(!(strlen(trim($templateData->pdfFileNames[$i])) >= 5 && substr_compare(strtolower($templateData->pdfFileNames[$i]), '.pdf', -4 ) === 0))
                {
                    $validityCheckResult->isValid = false;
                    $validityCheckResult->errors[] = 'PDF-Datei Nr. ' . ($i + 1) . ' ist kein PDF Format.';
                }
            }
        }
        return $validityCheckResult;
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
    $validityCheckResult = new ValidityCheckResult();
    if($userData->gender !== 'f' && $userData->gender !== 'm')
    {
        $validityCheckResult->isValid = false;
        $validityCheckResult->errors[] = 'Geschlecht muss "m" oder "f" sein.';
    }
    if(trim($userData->firstName) === '')
    {
        $validityCheckResult->isValid = false;
        $validityCheckResult->errors[] = 'Der Vorname darf nicht leer sein';
    }
    if(trim($userData->lastName) === '')
    {
        $validityCheckResult->isValid = false;
        $validityCheckResult->errors[] = 'Der Nachname darf nicht leer sein';
    }
    if(trim($userData->street) === '')
    {
        $validityCheckResult->isValid = false;
        $validityCheckResult->errors[] = 'Die Stra&szlig;e darf nicht leer sein';
    }
    if(trim($userData->postCode) === '')
    {
        $validityCheckResult->isValid = false;
        $validityCheckResult->errors[] = 'Die Postleitzahl darf nicht leer sein';
    }
    if(trim($userData->city) === '')
    {
        $validityCheckResult->isValid = false;
        $validityCheckResult->errors[] = 'Die Stadt darf nicht leer sein';
    }
    return $validityCheckResult;
}














?>
