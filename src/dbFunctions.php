<?php
    function getJobApplicationTemplates($dbConn, $userId)
    {
        $statement = $dbConn->prepare('select templateName, userAppliesAs from jobApplicationTemplate where userId=:userId');
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

    function getJobApplicationTemplateODTFile($dbConn, $userId, $templateId)
    {
        $statement = $dbConn->prepare('select odtFile from jobApplicationTemplate where userId=:userId and id=:templateId');
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
            return $rows[0]['odtFile'];
        }
    }


    function getJobApplicationTemplate($dbConn, $userId, $templateId)
    {
        $statement = $dbConn->prepare('select userId, templateName, userAppliesAs, emailSubject, emailBody, odtFile from jobApplicationTemplate where userId=:userId and id=:templateId limit 1');
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
            throw new \Exception('Query failed to find job application template.');
        }
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
        if($result === false)
        {
            throw new \Exception('Query failed to add job application template.');
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

    function getTemplateIdByIndex($dbConn, $userId, $index)
    {
        $statement = $dbConn->prepare("select id from jobApplicationTemplate where userId=:userId limit :index,1");
        $statement->bindParam(':userId', $userId);
        --$index;
        $statement->bindParam(':index', $index);
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
        if(count($rows) < 1)
        {
            throw new \Exception('Failed to find row ' . $index);
        }
        return $rows[0]['id'];
    }

    function getPdfAppendices($dbConn, $templateId)
    {
        $statement = $dbConn->prepare('select pdfFile from jobApplicationPdfAppendix where jobApplicationTemplateId=:templateId');
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

    function addPdfAppendix($dbConn, $name, $templateId, $pdfFileName)
    {
        $statement = $dbConn->prepare('insert into jobApplicationPdfAppendix (name, jobApplicationTemplateId, pdfFile) values(:name, :templateId, :pdfFileName)');
        $statement->bindParam(':name', $name);
        $statement->bindParam(':templateId', $templateId);
        $statement->bindParam(':pdfFileName', $pdfFileName);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to insert PDF appendix.');
        }
    }

    function addUser($dbConn, $email, $password)
    {
        $statement = $dbConn->prepare('insert into user(name, password) values(:name, :password)');
        $statement->bindParam(':email', $email);
        $statement->bindParam(':password', $password);
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

    function getIdAndPasswordByEmail($dbConn, $email)
    {
        $statement = $dbConn->prepare('select id, password from user where email=:email limit 1');
        $statement->bindParam(':email', $email);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to get id and password.');
        }
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }
        $rows = $statement->fetchAll();
        if(count($rows) === 0)
        {
            return Array();
        }
        else
        {
            return Array('id' => $rows[0]['id'], 'password' => $rows[0]['password']);
        }
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


    function identifyUser($dbConn, $email)
    {
        $statement = $dbConn->prepare('select id from user where email=:email limit 1');
        $statement->bindParam(':email', $email);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to identify user.');
        }
        $result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
        if($result === false)
        {
            throw new \Exception('Failed to setFetchMode().');
        }

        $rows = $statement->fetchAll();
        if(count($rows) === 0)
        {
            throw new \Exception('Email or password wrong.');
        }
        else
        {
            return $rows[0]['id'];
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
            return new UserDetails(
                $rows[0]['gender'],
                $rows[0]['degree'],
                $rows[0]['firstName'],
                $rows[0]['lastName'],
                $rows[0]['street'],
                $rows[0]['postcode'],
                $rows[0]['city'],
                $rows[0]['phone'],
                $rows[0]['mobilePhone'],
                $rows[0]['birthday'],
                $rows[0]['birthplace'],
                $rows[0]['maritalStatus']);
        }
    }

    function updateUserValues($dbConn, $userId, $user)
    {
        $statement = $dbConn->prepare('update userDetails set
            gender = :gender,
            degree = :degree,
            firstName = :firstName,
            lastName = :lastName,
            street = :street,
            postcode = :postcode,
            city = :city,
            email = :email,
            mobilePhone = :mobilePhone,
            phone = :phone where userId = :userId');
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':gender', $user->gender);
        $statement->bindParam(':degree', $user->degree);
        $statement->bindParam(':firstName', $user->firstName);
        $statement->bindParam(':lastName', $user->lastName);
        $statement->bindParam(':street', $user->street);
        $statement->bindParam(':postcode', $user->postcode);
        $statement->bindParam(':city', $user->city);
        $statement->bindParam(':email', $user->email);
        $statement->bindParam(':mobilePhone', $user->mobilePhone);
        $statement->bindParam(':phone', $user->phone);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to update user values.');
        }
    }

    function getJobApplications($dbConn, $userId, $fromDate, $toDate)
    {
        $statement = $dbConn->prepare('select jobApplicationStatus.statusChangedOn, jobApplicationStatus.dueOn, jobApplicationStatus.statusValueId, employer.companyName, employer.degree, employer.firstName
            , employer.lastName, employer.email, employer.mobilePhone, employer.phone, employer.street, employer.postcode, employer.city
            from jobApplication
            join employer on jobApplication.employerId = employer.id and jobApplication.userId = :userId
            join jobApplicationStatus on jobApplicationStatus.jobApplicationId = jobApplication.id');
        $statement = $dbConn->prepare(
            'select    jobApplicationStatus.statusChangedOn as aa
                     , s1.statusChangedOn as "Beworben am"
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
        $statement = $dbConn->prepare('select companyName, street, postcode, city, email, mobilePhone, phone, gender, degree, firstName, lastName
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

    function getEmployerIndex($dbConn, $userId, $employerRowIndex)
    {
        $index = $employerRowIndex - 1;
        $statement = $dbConn->prepare("select id from employer where userId=:userId limit :index,1");
        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':index', $index);
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
        if(count($rows) < 1)
        {
            throw new \Exception("Employer with rowIndex $employerRowIndex not found.");
        }
        return $rows[0]['id'];
    }

    function addEmployer($dbConn, $userId, $employer)
    {
        $statement = $dbConn->prepare('insert into employer
            (userId, companyName, street, postcode, city, email, mobilePhone, phone, gender, degree, firstName, lastName)
            values (:userId, :companyName, :street, :postcode, :city, :email, :mobilePhone, :phone, :gender, :degree, :firstName, :lastName)');

        $statement->bindParam(':userId', $userId);
        $statement->bindParam(':companyName', $employer->company);
        $statement->bindParam(':street', $employer->street);
        $statement->bindParam(':postcode', $employer->postcode);
        $statement->bindParam(':city', $employer->city);
        $statement->bindParam(':gender', $employer->gender);
        $statement->bindParam(':degree', $employer->degree);
        $statement->bindParam(':firstName', $employer->firstName);
        $statement->bindParam(':lastName', $employer->lastName);
        $statement->bindParam(':email', $employer->email);
        $statement->bindParam(':mobilePhone', $employer->mobilePhone);
        $statement->bindParam(':phone', $employer->phone);
        $result = $statement->execute();
        if($result === false)
        {
            throw new \Exception('Query failed to add employer.');
        }
    }
?>
