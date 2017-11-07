<?php
    function getJobApplicationTemplates($dbConn, $userId)
    {
        $statement = $dbConn->prepare('select id, templateName, userAppliesAs from jobApplicationTemplate where userId=:userId');
        $statement->bindParam(':userId', $userId);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to get job application templates.');
        }
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }
        $rows = $statement->fetchAll();
        return $rows;
    }

    function getJobApplicationTemplateODTPath($dbConn, $userId, $templateId)
    {
        $statement = $dbConn->prepare('select odtPath from jobApplicationTemplate where userId=:userId and id=:templateId');
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':templateId', $templateId);
        $result = $statement->execute();
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to get job application templates.');
        }
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }
        $rows = $statement->fetchAll();
        if(count($rows) === 0)
        {
            return '';
        }
        else
        {
            return $rows[0]['odtPath'];
        }
    }


    function getJobApplicationTemplate($dbConn, $userId, $templateId)
    {
        $statement = $dbConn->prepare('select userId, templateName, userAppliesAs, emailSubject, emailBody, odtPath from jobApplicationTemplate where userId=:userId and id=:templateId limit 1');
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':templateId', $templateId);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to get job application templates.');
        }
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }
        $rows = $statement->fetchAll();
        if(count($rows) === 1)
        {
            return $rows[0];
        }
        else
        {
            throw new \Exception("Query failed to find job application template. userId: $userId, templateId: $templateId");
        }
    }

    function addJobApplicationTemplate($dbConn, $userId, $templateData)
    {
        //TODO existing template with the same name should not be overwritten
        $statement = $dbConn->prepare('insert into jobApplicationTemplate(userId, templateName, userAppliesAs, emailSubject, emailBody, odtPath)
            values(:userId, :templateName, :userAppliesAs, :emailSubject, :emailBody, :odtPath)');
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':templateName', $templateData['name']);
        $statement->bindParam(':userAppliesAs', $templateData['userAppliesAs']);
        $statement->bindParam(':emailSubject', $templateData['emailSubject']);
        $statement->bindParam(':emailBody', $templateData['emailBody']);
        $statement->bindParam(':odtPath', $templateData['odtPath']);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to add job application template.');
        }
        foreach($templateData['pdfPaths'] as $currentPdfPath)
        {
            $statement = $dbConn->prepare('insert into jobApplicationPdfAppendix(jobApplicationTemplateId, pdfPath) values(last_insert_id(), :pdfPath)');
            $statement->bindParam(':pdfPath', $currentPdfPath);
            $result = $statement->execute();
            if($result === false)
            {
                throw new \Exception('Query failed to insert PDF pdf.');
            }
        }

    }


    function getTemplateIdByName($dbConn, $userId, $templateName)
    {
        $statement = $dbConn->prepare("select id from jobApplicationTemplate where userId=:userId and templateName=:templateName");
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':templateName', $templateName);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to get job application templates.');
        }
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }
        $rows = $statement->fetchAll();
        return $rows[0]['id'];
    }


    function getPdfAppendices($dbConn, $templateId)
    {
        $statement = $dbConn->prepare('select pdfPath from jobApplicationPdfAppendix where jobApplicationTemplateId=:templateId');
        $statement->bindParam(':templateId', $templateId);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to get job application templates.');
        }
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }
        $rows = $statement->fetchAll();
        return $rows;
    }


    function addUser($dbConn, $email, $password, $confirmationString)
    {
        $statement = $dbConn->prepare('insert into user(email, password, confirmationString, created) values(:email, :password, :confirmationString, curdate())');
        $statement->bindParam(':email', $email);
        $statement->bindParam(':password', $password);
        $statement->bindParam(':confirmationString', $confirmationString);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to add user.');
        }
        $statement = $dbConn->prepare("insert into userDetails(userId, degree, gender, firstName, lastName, street, postcode, city, phone, mobilePhone, birthday, birthplace, maritalStatus)
            values(last_insert_id(), '', '', '', '', '', '', '', '', '', '', '', '')");
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to add user address.');
        }
    }

    function setEmailConfirmed($dbConn, $email)
    {
        $statement = $dbConn->prepare('update user set confirmationString = null where email=:email');
        $statement->bindParam(':email', $email);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to add user.');
        }
    }

    function getConfirmationString($dbConn, $email)
    {
        $statement = $dbConn->prepare('select confirmationString from user where email=:email');
        $statement->bindParam(':email', $email);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to add user.');
        }
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }
        $rows = $statement->fetchAll();
        if(count($rows) === 0)
        {
            return null;
        }
        else
        {
            return $rows[0]['confirmationString'];
        }
    }

    function getUserDataByEmail($dbConn, $email)
    {
        $statement = $dbConn->prepare('select id as userId, password, confirmationString from user where email=:email limit 1');
        $statement->bindParam(':email', $email);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to get userId and password.');
        }
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }
        $rows = $statement->fetchAll();
        if(count($rows) !== 1)
        {
            return Array();
        }
        else
        {
            return $rows[0];
        }
    }

    function isEmailRegistered($dbConn, $email)
    {
        $statement = $dbConn->prepare('select id from user where email=:email limit 1');
        $statement->bindParam(':email', $email);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to execute: isEmailRegistered (' . $email . '.');
        }
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }
        $rows = $statement->fetchAll();
        return count($rows) === 1;
    }

    function getEmailByUserId($dbConn, $userId)
    {
        $statement = $dbConn->prepare('select email from user where id = :userId limit 1');
        $statement->bindParam(':userId', $userId);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to find user.');
        }
        $rows = $statement->fetchAll();
        if(count($rows) !== 1)
        {
            throw new \Exception('Email not found.');
        }
        else
        {
            return $rows[0]['email'];
        }
    }


    function getUserDetails($dbConn, $userId)
    {
        $statement = $dbConn->prepare('select gender, degree, firstName, lastName, street, postcode, city, phone, mobilePhone, birthday, birthplace, maritalStatus from userDetails where userId=:userId limit 1');
        $statement->bindParam(':userId', $userId);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to get user values.');
        }
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }

        $rows = $statement->fetchAll();
        if(count($rows) !== 1)
        {
            throw new \Exception('No user details found');
        }
        else
        {
                return $rows[0];
        }
    }

    function setUserDetails($dbConn, $userId, $userDetails)
    {
        $statement = $dbConn->prepare('update userDetails set
            gender = :gender,
            degree = :degree,
            firstName = :firstName,
            lastName = :lastName,
            street = :street,
            postcode = :postcode,
            city = :city,
            mobilePhone = :mobilePhone,
            phone = :phone,
            birthday = :birthday,
            birthplace = :birthplace,
            maritalStatus = :maritalStatus
            where userId = :userId');
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':gender', $userDetails['gender']);
        $statement->bindParam(':degree', $userDetails['degree']);
        $statement->bindParam(':firstName', $userDetails['firstName']);
        $statement->bindParam(':lastName', $userDetails['lastName']);
        $statement->bindParam(':street', $userDetails['street']);
        $statement->bindParam(':postcode', $userDetails['postcode']);
        $statement->bindParam(':city', $userDetails['city']);
        $statement->bindParam(':mobilePhone', $userDetails['mobilePhone']);
        $statement->bindParam(':phone', $userDetails['phone']);
        $statement->bindParam(':birthday', $userDetails['birthday']);
        $statement->bindParam(':birthplace', $userDetails['birthplace']);
        $statement->bindParam(':maritalStatus', $userDetails['maritalStatus']);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to update user values.');
        }
    }

    function translateStatus($s)
    {
        $translations =
            [ "Waiting for reply after sending job application" => "Wartend nach Bewerbung",
                "Appointment for job interview" => "Vorstellungstermin",
                "Job application rejected without an interview" => "Abgelehnt ohne Einladung",
                "Waiting for reply after job interview" => "Wartend nach Vorstellungsgespräch",
                "Job application rejected after interview" => "Bewerbung abgelehnt nach Vorstellungsgespräch", 
                "Job application accepted after interview" => "Bewerbung akzeptiert"];
        return $translations[$s] ?? $s;
    }

    function getJobApplicationsForPrint($dbConn, $userId, $fromDate, $toDate)
    {
        $statement = $dbConn->prepare(
            'select    s1.statusChangedOn as "Beworben am"
                     , jobApplicationStatus.statusChangedOn as "Status vom"
                     , jobApplicationStatusValue.status
                     , employer.companyName
                from jobApplication
                join employer on jobApplication.employerId = employer.id and jobApplication.userId = :userId
                join jobApplicationStatus
                    on jobApplicationStatus.jobApplicationId = jobApplication.id
                    and statusChangedOn = (select max(statusChangedOn) from jobApplicationStatus where jobApplicationId = jobApplication.id)
                join jobApplicationStatus s1
                    on s1.jobApplicationId = jobApplication.id
                    and s1.statusChangedOn = (select s2.statusChangedOn from jobApplicationStatus s2 where jobApplicationId = jobApplication.id and statusValueId = 1)
                join jobApplicationStatusValue on jobApplicationStatus.statusValueId = jobApplicationStatusValue.id
                group by
                     employer.companyName');
        $statement->bindParam(':userId', $userId);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to get job applications.');
        }
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }
        $rows = $statement->fetchAll();
        return $rows;

    }

    function getJobApplications($dbConn, $userId, $fromDate, $toDate)
    {
        $statement = $dbConn->prepare(
            'select    jobApplicationStatus.statusChangedOn
                     , s1.statusChangedOn
                     , jobApplicationStatus.dueOn
                     , jobApplicationStatus.statusValueId
                     , employer.companyName
                     , employer.degree
                     , employer.firstName 
                     , employer.lastName
                     , employer.email
                     , employer.mobilePhone
                     , employer.phone
                     , employer.street
                     , employer.postcode
                     , employer.city
                from jobApplication
                join employer on jobApplication.employerId = employer.id and jobApplication.userId = :userId
                join jobApplicationStatus
                    on jobApplicationStatus.jobApplicationId = jobApplication.id
                    and statusChangedOn = (select max(statusChangedOn) from jobApplicationStatus where jobApplicationId = jobApplication.id)
                join jobApplicationStatus s1
                    on s1.jobApplicationId = jobApplication.id
                    and s1.statusChangedOn = (select s2.statusChangedOn from jobApplicationStatus s2 where jobApplicationId = jobApplication.id and statusValueId = 1)
                group by
                       jobApplicationStatus.dueOn
                     , employer.companyName
                     , employer.degree
                     , employer.firstName 
                     , employer.lastName
                     , employer.email
                     , employer.mobilePhone
                     , employer.phone
                     , employer.street
                     , employer.postcode
                     , employer.city');


        $statement->bindParam(':userId', $userId);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to get job applications.');
        }
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }
        $rows = $statement->fetchAll();
        return $rows;
    }

    function addJobApplication($dbConn, $userId, $employerId, $templateId)
    {
        $statement = $dbConn->prepare('select max(id) + 1 as id from jobApplication');
        $statement->execute();
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        $rows = $statement->fetchAll();
        $nextJobApplicationId = $rows[0]['id'];


        $statement = $dbConn->prepare('insert into jobApplication(id, userId, employerId, jobApplicationTemplateId)
            values(:jobApplicationId, :userId, :employerId, :templateId)');
        $statement->bindParam(':jobApplicationId', $jobApplicationId);
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':employerId', $employerId);
        $statement->bindParam(':templateId', $templateId);
        $statement->execute();

        $statement = $dbConn->prepare('insert into jobApplicationStatus(jobApplicationId, statusChangedOn, dueOn, statusValueId, statusMessage)
            values(:jobApplicationId, curdate(), null, 1, "")');
        $statement->bindParam(':jobApplicationId', $jobApplicationId);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to add job application.');
        }
    }

    function getEmployers($dbConn, $userId)
    {
        $statement = $dbConn->prepare('select id, companyName, street, postcode, city, email, mobilePhone, phone, gender, degree, firstName, lastName
            from employer where userId = :userId');
        $statement->bindParam(':userId', $userId);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to get get employers.');
        }
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }
        $rows = $statement->fetchAll();
        return $rows;
    }

    function getEmployer($dbConn, $userId, $employerId)
    {
        $statement = $dbConn->prepare('select companyName as "\$firmaName", street as "\$firmaStrasse", postcode as "\$firmaPlz", city as "\$firmaStadt", email as "\$firmaEmail", mobilePhone as "\$firmaMobil", phone as "\$firmaTelefon", gender as "\$chefAnrede", degree as "\$chefTitel", firstName as "\$chefVorname", lastName as "\$chefNachname"
            from employer where userId = :userId and id = :employerId');
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':employerId', $employerId);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to get employer with id ' . $employerId);
        }
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }
        $rows = $statement->fetchAll();
        return $rows[0];
    }


    function addEmployer($dbConn, $userId, $employer)
    {
        $statement = $dbConn->prepare('insert into employer
            (userId, companyName, street, postcode, city, email, mobilePhone, phone, gender, degree, firstName, lastName)
            values (:userId, :companyName, :street, :postcode, :city, :email, :mobilePhone, :phone, :gender, :degree, :firstName, :lastName)');

        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':companyName', $employer['company']);
        $statement->bindParam(':street', $employer['street']);
        $statement->bindParam(':postcode', $employer['postcode']);
        $statement->bindParam(':city', $employer['city']);
        $statement->bindParam(':gender', $employer['gender']);
        $statement->bindParam(':degree', $employer['degree']);
        $statement->bindParam(':firstName', $employer['firstName']);
        $statement->bindParam(':lastName', $employer['lastName']);
        $statement->bindParam(':email', $employer['email']);
        $statement->bindParam(':mobilePhone', $employer['mobilePhone']);
        $statement->bindParam(':phone', $employer['phone']);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to add employer.');
        }
    }
?>
