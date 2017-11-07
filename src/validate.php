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
        function setInvalidWithErrorMsg($errorMsg)
        {
            $this->isValid = false;
            $this->errors[] = $errorMsg;
        }
    }



    function validateTemplate($template, $odt, $pdfs)
    {
        $taskResult = new TaskResult(true, [], []);
        if(trim($template['name'] === ''))
        {
            $taskResult->isValid = false;
            $taskResult->errors[] = 'Der Template-Name darf nicht leer sein.';
        }
        if(strlen(trim($template['emailSubject'])) < 5)
        {
            $taskResult->isValid = false;
            $taskResult->errors[] = 'Der Email-Betreff muss mindestens 5 Buchstaben lang sein.';
        }
        if(trim($template['emailBody']) === '')
        {
            $taskResult->isValid = false;
            $taskResult->errors[] = 'Der Email-Body darf nicht leer sein.';
        }
        $odtMimeType = mime_content_type($odt['tmp_name']);
        if($odtMimeType !== 'application/vnd.oasis.opendocument.text' && $odtMimeType !== 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
        {
            $taskResult->setInvalidWithErrorMsg('Template file should have type odt or docx.');
        }
        if($odt['size'] > 1000000)
        {
            $taskResult->setInvalidWithErrorMsg('Template file should be smaller than 1 MB.');
        }
        for($i = 0; $i < min(count($pdfs['tmp_name']), 10); ++$i)
        {
            if(mime_content_type($pdfs['tmp_name'][$i]) !== 'application/pdf')
            {
                $taskResult->setInvalidWithErrorMsg('PDF appendix should have type pdf.');
            }
            if($pdfs['size'][$i] > 1000000)
            {
                $taskResult->setInvalidWithErrorMsg('PDF file ' . ($i + 1) . ' should be smaller than 1 MB.');
            }
        }
        return $taskResult;
    }





function validateUserDetails($userDetails)
{
    $taskResult = new TaskResult(true, [], []);
    return $taskResult;
}


function validateEmployer($employer)
{
    $taskResult = new TaskResult(true, [], []);
    if(trim($employer['company']) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Firmenname darf nicht leer sein';
    }
    if($employer['gender'] !== 'f' && $employer['gender'] !== 'm')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = "Geschlecht muss 'm' oder 'f' sein. Instead it is ." . $employer['gender'] . ".";
    }
    if(trim($employer['lastName']) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Der Nachname darf nicht leer sein';
    }
    if(trim($employer['street']) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Die Stra&szlig;e darf nicht leer sein';
    }
    if(trim($employer['postcode']) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Die Postleitzahl darf nicht leer sein';
    }
    if(trim($employer['city']) === '')
    {
        $taskResult->isValid = false;
        $taskResult->errors[] = 'Die Stadt darf nicht leer sein';
    }
    return $taskResult;
}



?>
