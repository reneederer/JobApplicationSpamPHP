<?php

    class TaskResult
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
        function __construct($templateName, $emailSubject, $emailBody, $odtFile, $pdfFiles)
        {
            $odtMimeType = mime_content_type($odtFile['tmp_name']);
            if($odtMimeType !== 'application/vnd.oasis.opendocument.text' && $odtMimeType !== 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            {
                throw new \Exception('Template file should have type odt or docx.');
            }
            if($odtFile['size'] > 1000000)
            {
                throw new \Exception('Template file should be smaller than 1MB.');
            }
            for($i = 0; $i < min(count($pdfFiles['tmp_name']), 10); ++$i)
            {
                if(mime_content_type($pdfFiles['tmp_name'][$i]) !== 'application/pdf')
                {
                    throw new \Exception('PDF appendix should have type pdf.');
                }
                if($pdfFiles['size'][$i] > 1000000)
                {
                    throw new \Exception('PDF file ' . ($i + 1) . ' should be smaller than 1MB.');
                }
                $this->pdfFileNames[] = $pdfFiles['name'][$i];
            }
            $this->templateName = strip_tags($templateName);
            $this->emailSubject = strip_tags($emailSubject);
            $this->emailBody = strip_tags($emailBody);
            $this->odtFileName = strip_tags($odtFile['name']);
        }
    };
    function validateTemplateData($templateData)
    {
        $taskResult = new TaskResult(true, [], []);
        if(trim($templateData->templateName === ''))
        {
            $taskResult->isValid = false;
            $taskResult->errors[] = 'Der Template-Name darf nicht leer sein.';
        }
        if(strlen(trim($templateData->emailSubject)) < 5)
        {
            $taskResult->isValid = false;
            $taskResult->errors[] = 'Der Email-Betreff muss mindestens 5 Buchstaben lang sein.';
        }
        if(trim($templateData->emailBody) === '')
        {
            $taskResult->isValid = false;
            $taskResult->errors[] = 'Der Email-Body darf nicht leer sein.';
        }
        if(!((strlen(trim($templateData->odtFileName)) >= 5) && substr_compare(strtolower($templateData->odtFileName), '.odt', -4) === 0))
        {
            $taskResult->isValid = false;
            $taskResult->errors[] = 'Die ODT-Datei darf nicht leer sein und muss auf ".odt" enden.';
        }
        for($i = 0; $i < count($templateData->pdfFileNames); ++$i)
        {
            if($templateData->pdfFileNames[$i] != '')
            {
                if(!(strlen(trim($templateData->pdfFileNames[$i])) >= 5 && substr_compare(strtolower($templateData->pdfFileNames[$i]), '.pdf', -4 ) === 0))
                {
                    $taskResult->isValid = false;
                    $taskResult->errors[] = 'PDF-Datei Nr. ' . ($i + 1) . ' ist kein PDF Format.';
                }
            }
        }
        return $taskResult;
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
        $this->gender = strip_tags($gender);
        $this->degree = strip_tags($degree);
        $this->firstName = strip_tags($firstName);
        $this->lastName = strip_tags($lastName);
        $this->street = strip_tags($street);
        $this->postCode = strip_tags($postCode);
        $this->city = strip_tags($city);
        $this->email = strip_tags($email);
        $this->mobilePhone = strip_tags($mobilePhone);
        $this->phone = strip_tags($phone);
    }
};


function validateUserData($userData)
{
    $taskResult = new TaskResult(true, [], []);
    if($userData->gender !== 'f' && $userData->gender !== 'm')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Geschlecht muss "m" oder "f" sein.';
    }
    if(trim($userData->firstName) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Der Vorname darf nicht leer sein';
    }
    if(trim($userData->lastName) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Der Nachname darf nicht leer sein';
    }
    if(trim($userData->street) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Die Stra&szlig;e darf nicht leer sein';
    }
    if(trim($userData->postCode) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Die Postleitzahl darf nicht leer sein';
    }
    if(trim($userData->city) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Die Stadt darf nicht leer sein';
    }
    return $taskResult;
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
        $this->company = strip_tags($company);
        $this->gender = strip_tags($gender);
        $this->degree = strip_tags($degree);
        $this->firstName = strip_tags($firstName);
        $this->lastName = strip_tags($lastName);
        $this->street = strip_tags($street);
        $this->postCode = strip_tags($postCode);
        $this->city = strip_tags($city);
        $this->email = strip_tags($email);
        $this->mobilePhone = strip_tags($mobilePhone);
        $this->phone = strip_tags($phone);
    }
};


function validateEmployer($employer)
{
    $taskResult = new TaskResult(true, [], []);
    if(trim($employer->company) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Firmenname darf nicht leer sein';
    }
    if($employer->gender !== 'f' && $employer->gender !== 'm')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = "Geschlecht muss 'm' oder 'f' sein. Instead it is ." . $employer->gender . ".";
    }
    if(trim($employer->lastName) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Der Nachname darf nicht leer sein';
    }
    if(trim($employer->street) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Die Stra&szlig;e darf nicht leer sein';
    }
    if(trim($employer->postCode) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Die Postleitzahl darf nicht leer sein';
    }
    if(trim($employer->city) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Die Stadt darf nicht leer sein';
    }
    return $taskResult;
}
















?>
