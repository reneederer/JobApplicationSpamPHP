<?php
    function getJobApplicationTemplates($dbConn, $userId)
    {
        $statement = $dbConn->prepare('select templateName, userAppliesAs from jobApplicationTemplate where userId=:userId');
        $statement->bindParam(':userId', $userId);
        $result = $statement->execute();
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        $rows = $statement->fetchAll();
        return $rows;
    }

    function getJobApplicationTemplateODTFile($dbConn, $userId, $templateId)
    {
        $statement = $dbConn->prepare('select odtFile from jobApplicationTemplate where userId=:userId and id=:templateId');
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':templateId', $templateId);
        $result = $statement->execute();
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        $rows = $statement->fetchAll();
        if(count($rows) === 0)
        {
            return '';
        }
        else
        {
            return $rows[0]['odtFile'];
        }
    }

    function getJobApplicationTemplate($dbConn, $userId, $templateId)
    {
        $statement = $dbConn->prepare('select userId, templateName, userAppliesAs, emailSubject, emailBody, odtFile from jobApplicationTemplate where userId=:userId and id=:templateId');
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':templateId', $templateId);
        $result = $statement->execute();
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        $rows = $statement->fetchAll();
        return $rows[0];
    }

    function addJobApplicationTemplate($dbConn, $userId, $templateName, $userAppliesAs, $emailSubject, $emailBody, $odtFile)
    {
        //TODO existing template with the same name should not be overwritten
        $statement = $dbConn->prepare('insert into jobApplicationTemplate(userId, templateName, userAppliesAs, emailSubject, emailBody, odtFile)
            values(:userId, :templateName, :userAppliesAs, :emailSubject, :emailBody, :odtFile)');
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':templateName', $templateName);
        $statement->bindParam(':userAppliesAs', $userAppliesAs);
        $statement->bindParam(':emailSubject', $emailSubject);
        $statement->bindParam(':emailBody', $emailBody);
        $statement->bindParam(':odtFile', $odtFile);
        $result = $statement->execute();
    }

    function getTemplateIdByName($dbConn, $userId, $templateName)
    {
        $statement = $dbConn->prepare("select id from jobApplicationTemplate where userId=:userId and templateName=:templateName");
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':templateName', $templateName);
        $result = $statement->execute();
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        $rows = $statement->fetchAll();
        return $rows[0]['id'];
    }

    function getTemplateIdByIndex($dbConn, $userId, $index)
    {
        $statement = $dbConn->prepare("select id from jobApplicationTemplate where userId=:userId limit :index,1");
        $statement->bindParam(':userId', $userId);
        --$index;
        $statement->bindParam(':index', $index);
        $result = $statement->execute();
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        $rows = $statement->fetchAll();
        return $rows[0]['id'];
    }

    function getPdfAppendices($dbConn, $templateId)
    {
        $statement = $dbConn->prepare('select pdfFile from jobApplicationPdfAppendix where jobApplicationTemplateId=:templateId');
        $statement->bindParam(':templateId', $templateId);
        $result = $statement->execute();
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        $rows = $statement->fetchAll();
        return $rows;
    }

    function addPdfAppendix($dbConn, $name, $templateId, $pdfFileName)
    {
        $statement = $dbConn->prepare('insert into jobApplicationPdfAppendix (name, jobApplicationTemplateId, pdfFile) values(:name, :templateId, :pdfFileName)');
        $statement->bindParam(':name', $name);
        $statement->bindParam(':templateId', $templateId);
        $statement->bindParam(':pdfFileName', $pdfFileName);
        $statement->execute();
    }

    function addUser($dbConn, $name, $password)
    {
        $statement = $dbConn->prepare('insert into user(name, password) values(:name, :password)');
        $statement->bindParam(':name', $name);
        $statement->bindParam(':password', $password);
        $statement->execute();
    }

    function addToDownloads($dbConn, $folder, $userId)
    {
        $statement = $dbConn->prepare('insert into userDownloads(folder, userId, downloadTime) values(:folder, :userId, now())');

        $statement->bindParam(':folder', $folder);
        $statement->bindParam(':userId', $userId);
        $statement->execute();
    }

    function identifyUser($dbConn, $userName, $password)
    {
        $statement = $dbConn->prepare('select id from user where name=:userName and password=:password');
        $statement->bindParam(':userName', $userName);
        $statement->bindParam(':password', $password);
        $result = $statement->execute();
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        $rows = $statement->fetchAll();
        if(count($rows) === 0)
        {
            return Array();
        }
        else
        {
            return Array('id' => $rows[0]['id'], 'name' => $userName);
        }
    }

    function getUserValues($dbConn, $userId)
    {
        $statement = $dbConn->prepare('select firstName, lastName, salutation, title, street, postCode, city, email, mobilePhone, phone, birthday, birthplace, maritalStatus from userAddress where userId=:userId');
        $statement->bindParam(':userId', $userId);
        $result = $statement->execute();
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
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

    function updateUserValues($dbConn, $userId, $firstName, $lastName, $salutation, $title, $street, $postCode, $city, $email, $mobilePhone, $phone)
    {
        $statement = $dbConn->prepare('update userAddress set
            firstName = :firstName,
            lastName = :lastName,
            title = :title,
            salutation = :salutation,
            street = :street,
            postCode = :postCode,
            city = :city,
            email = :email,
            mobilePhone = :mobilePhone,
            phone = :phone where userId = :userId');
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':firstName', $firstName);
        $statement->bindParam(':lastName', $lastName);
        $statement->bindParam(':title', $title);
        $statement->bindParam(':salutation', $salutation);
        $statement->bindParam(':street', $street);
        $statement->bindParam(':postCode', $postCode);
        $statement->bindParam(':city', $city);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':mobilePhone', $mobilePhone);
        $statement->bindParam(':phone', $phone);
        $statement->execute();
        $_SESSION['user']['id'] = $userId;
        $_SESSION['user']['firstName'] = $firstName;
        $_SESSION['user']['lastName'] = $lastName;
        $_SESSION['user']['title'] = $title;
        $_SESSION['user']['salutation'] = $salutation;
        $_SESSION['user']['street'] = $street;
        $_SESSION['user']['postCode'] = $postCode;
        $_SESSION['user']['city'] = $city;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['mobilePhone'] = $mobilePhone;
        $_SESSION['user']['phone'] = $phone;
    }

    function getJobApplications($dbConn, $userId, $fromDate, $toDate)
    {
        $statement = $dbConn->prepare('select jobApplicationStatus.statusChangedOn, jobApplicationStatus.dueOn, jobApplicationStatus.statusValueId, employer.companyName, employer.title, employer.firstName
            , employer.lastName, employer.email, employer.mobilePhone, employer.phone, employer.street, employer.postCode, employer.city
            from jobApplication
            join employer on jobApplication.employerId = employer.id and jobApplication.userId = :userId
            join jobApplicationStatus on jobApplicationStatus.jobApplicationId = jobApplication.id');
        $statement = $dbConn->prepare(
            'select    jobApplicationStatus.statusChangedOn as aa
                     , s1.statusChangedOn as "Beworben am"
                     , jobApplicationStatus.dueOn
                     , jobApplicationStatus.statusValueId
                     , employer.companyName
                     , employer.title
                     , employer.firstName 
                     , employer.lastName
                     , employer.email
                     , employer.mobilePhone
                     , employer.phone
                     , employer.street
                     , employer.postCode
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
                     , employer.title
                     , employer.firstName 
                     , employer.lastName
                     , employer.email
                     , employer.mobilePhone
                     , employer.phone
                     , employer.street
                     , employer.postCode
                     , employer.city');


        $statement->bindParam(':userId', $userId);
        $result = $statement->execute();
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
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
        $statement->execute();
    }

    function getEmployers($dbConn, $userId)
    {
        $statement = $dbConn->prepare('select companyName, street, postCode, city, email, mobilePhone, phone, salutation, title, firstName, lastName
            from employer where userId = :userId');
        $statement->bindParam(':userId', $userId);
        $result = $statement->execute();
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        $rows = $statement->fetchAll();
        if(count($rows) === 0)
        {
            return Array();
        }
        else
        {
            return $rows;
        }
    }

    function getEmployer($dbConn, $userId, $employerId)
    {
        $statement = $dbConn->prepare('select companyName as "\$firmaName", street as "\$firmaStrasse", postCode as "\$firmaPlz", city as "\$firmaStadt", email as "\$firmaEmail", mobilePhone as "\$firmaMobil", phone as "\$firmaTelefon", salutation as "\$chefAnrede", title as "\$chefTitel", firstName as "\$chefVorname", lastName as "\$chefNachname"
            from employer where userId = :userId and id = :employerId');
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':employerId', $employerId);
        $result = $statement->execute();
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        $rows = $statement->fetchAll();
        return $rows[0];
    }

    function getEmployerIndex($dbConn, $userId, $employerRowIndex)
    {
        $index = $employerRowIndex - 1;
        $statement = $dbConn->prepare("select id from employer where userId=:userId limit :index,1");
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':index', $index);
        $result = $statement->execute();
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        $rows = $statement->fetchAll();
        return $rows[0]['id'];
    }

    function addEmployer($dbConn, $userId, $companyName, $street, $postCode, $city, $salutation, $title, $firstName, $lastName, $email, $mobilePhone, $phone)
    {
        $statement = $dbConn->prepare('insert into employer
            (userId, companyName, street, postCode, city, email, mobilePhone, phone, salutation, title, firstName, lastName)
            values (:userId, :companyName, :street, :postCode, :city, :email, :mobilePhone, :phone, :salutation, :title, :firstName, :lastName)');

        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':companyName', $companyName);
        $statement->bindParam(':street', $street);
        $statement->bindParam(':postCode', $postCode);
        $statement->bindParam(':city', $city);
        $statement->bindParam(':salutation', $salutation);
        $statement->bindParam(':title', $title);
        $statement->bindParam(':firstName', $firstName);
        $statement->bindParam(':lastName', $lastName);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':mobilePhone', $mobilePhone);
        $statement->bindParam(':phone', $phone);
        $statement->execute();
    }
?>
