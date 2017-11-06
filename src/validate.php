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
        public $userAppliesAs;
        public $emailSubject;
        public $emailBody;
        public $odtFile;
        public $pdfFiles;
        function __construct($templateName, $userAppliesAs, $emailSubject, $emailBody, $odtFile, $pdfFiles)
        {
            $odtMimeType = mime_content_type($odtFile['tmp_name']);
            if($odtMimeType !== 'application/vnd.oasis.opendocument.text' && $odtMimeType !== 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            {
                throw new \Exception('Template file should have type odt or docx.');
            }
            if($odtFile['size'] > 1000000)
            {
                throw new \Exception('Template file should be smaller than 1 MB.');
            }
            $this->pdfFilePaths = [];
            for($i = 0; $i < min(count($pdfFiles['tmp_name']), 10); ++$i)
            {
                var_dump($pdfFiles['tmp_name'][$i]);
                if(mime_content_type($pdfFiles['tmp_name'][$i]) !== 'application/pdf')
                {
                    throw new \Exception('PDF appendix should have type pdf.');
                }
                if($pdfFiles['size'][$i] > 1000000)
                {
                    throw new \Exception('PDF file ' . ($i + 1) . ' should be smaller than 1 MB.');
                }
            }
            $this->templateName = $templateName;
            $this->emailSubject = $emailSubject;
            $this->emailBody = $emailBody;
            $this->odtFile = $odtFile;
            $this->pdfFiles = $pdfFiles;
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
        return $taskResult;
    }





function validateUserDetails($userData)
{
    $taskResult = new TaskResult(true, [], []);
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
    public $postcode;
    public $city;
    public $email;
    public $mobilePhone;
    public $phone;
    function __construct($company, $street, $postcode, $city, $gender, $degree, $firstName, $lastName, $email, $mobilePhone, $phone)
    {
        $this->company = $company;
        $this->gender = $gender;
        $this->degree = $degree;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->street = $street;
        $this->postcode = $postcode;
        $this->city = $city;
        $this->email = $email;
        $this->mobilePhone = $mobilePhone;
        $this->phone = $phone;
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
    if(trim($employer->postcode) === '')
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



class UserDetails
{
    public $gender;
    public $degree;
    public $firstName;
    public $lastName;
    public $street;
    public $postcode;
    public $city;
    public $phone;
    public $mobilePhone;
    public $birthday;
    public $birthplace;
    public $maritalStatus;
    function __construct($gender, $degree, $firstName, $lastName, $street, $postcode, $city, $mobilePhone, $phone, $birthday, $birthplace, $maritalStatus)
    {
        $this->gender = $gender;
        $this->degree = $degree;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->street = $street;
        $this->postcode = $postcode;
        $this->city = $city;
        $this->mobilePhone = $mobilePhone;
        $this->phone = $phone;
        $this->birthday = $birthday;
        $this->birthplace = $birthplace;
        $this->maritalStatus = $maritalStatus;
    }
}



?>
