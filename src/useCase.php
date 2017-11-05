<?php
    function ucAddEmployer($employer)
    {
        $validateResult = validateEmployer($employer);
        if($validateResult->isValid)
        {
            addEmployer(getDBConn(), $_SESSION['user']['id'], $employer);
        }
        else
        {
            $currentMessage .= join("<br>", $validateResult->errors);
        }
    }
