<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('../vendor/phpmailer/phpmailer/src/Exception.php');
    require_once('../vendor/phpmailer/phpmailer/src/PHPMailer.php');
    require_once('../vendor/phpmailer/phpmailer/src/SMTP.php');

    require_once('config.php');
    require_once('useCase.php');
    require_once('odtFunctions.php');
    require_once('websiteFunctions.php');
    require_once('dbFunctions.php');
    require_once('helperFunctions.php');
    require_once('validate.php');

    use PHPMailer\PHPMailer\PHPMailer;

    session_start();

    $currentMessage = '';


    $dbConn = new PDO('mysql:host=localhost;dbname=' . $config['database']['database'], $config['database']['username'], $config['database']['password']);
    $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbConn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbConn->exec("SET NAMES utf8");


    if(isset($_POST['sbmLoginForm']))
    {
        $userIdAndPassword = getIdAndPasswordByEmail($dbConn, $_POST['txtUserEmail']);
        if(!empty($userIdAndPassword) && password_verify($_POST['txtPassword'], $userIdAndPassword['password']))
        {
            $_SESSION['user']['id'] = identifyUser($dbConn, $_POST['txtUserEmail']);
        }
    }
    else if(isset($_POST['sbmLogout']))
    {
        logout();
    }
    else if(isset($_POST['sbmRegisterForm']))
    {
        if(filter_var($_POST['txtUserEmail'], FILTER_VALIDATE_EMAIL))
        {
            addUser($dbConn, $_POST['txtUserEmail'], password_hash($_POST['txtPassword'], PASSWORD_DEFAULT));
        }
        else
        {
            $currentMessage = 'Email doesn\'t look valid.';
        }
    }
    else if(isset($_POST['sbmSetUserDetails']))
    {
        ucSetUserDetails($dbConn, $_SESSION['user']['id'], 
                         $_POST['rbUserGender'],
                         $_POST['txtUserDegree'],
                         $_POST['txtUserFirstName'],
                         $_POST['txtUserLastName'],
                         $_POST['txtUserStreet'],
                         $_POST['txtUserPostcode'],
                         $_POST['txtUserCity'],
                         $_POST['txtUserMobilePhone'],
                         $_POST['txtUserPhone'],
                         $_POST['txtUserBirthday'],
                         $_POST['txtUserBirthplace'],
                         $_POST['txtUserMaritalStatus']);
    }
    else if(isset($_POST['sbmAddEmployer']))
    {
        ucAddEmployer($dbConn, $_SESSION['user']['id'],
                     $_POST['txtCompany'],
                     $_POST['txtCompanyStreet'],
                     $_POST['txtCompanyPostcode'],
                     $_POST['txtCompanyCity'],
                     $_POST['rbBossGender'],
                     $_POST['txtBossDegree'],
                     $_POST['txtBossFirstName'],
                     $_POST['txtBossLastName'],
                     $_POST['txtBossEmail'],
                     $_POST['txtBossMobilePhone'],
                     $_POST['txtBossPhone']);
    }
    else if(isset($_POST['sbmDownloadPDF']))
    {
        //TODO FIX or remove this!
        //$dict = readEmployerFromWebsite('http://localhost/jobApplicationSpam/jobboerseArbeitsagentur.html');
        //$directoryAndFileName = getPDF($directory, $odtFile, $dict);
        //addToDownloads($dbConn, $directoryAndFileName[0], $_SESSION['user']['id']);
        //header('Content-type:application/pdf');
        //header("Content-Disposition:attachment;filename=jobApplication.pdf");
        //echo file_get_contents($directoryAndFileName[0] .  $directoryAndFileName[1]);
    }
    else if(isset($_POST['sbmUploadJobApplicationTemplate']))
    {
        try
        {
            $templateDataResult = validateTemplateData(
                new TemplateData($_POST['txtJobApplicationTemplateName'],
                                 $_POST['txtUserEmailSubject'], $_POST['txtUserEmailBody'],
                                 $_FILES['fileODT'],
                                 $_FILES['fileAppendices']));
            if($templateDataResult->isValid){
                $baseDir = '/var/www/userFiles/';
                $odtFileName = getNonExistingFileName($baseDir, 'odt');
                move_uploaded_file($_FILES['fileODT']['tmp_name'], $odtFileName);
                addJobApplicationTemplate( $dbConn
                                         , $_SESSION['user']['id']
                                         , $_POST['txtJobApplicationTemplateName']
                                         , $_POST['txtUserAppliesAs']
                                         , $_POST['txtUserEmailSubject']
                                         , $_POST['txtUserEmailBody']
                                         , $odtFileName);
                $templateId = getTemplateIdByName($dbConn, $_SESSION['user']['id'], $_POST['txtJobApplicationTemplateName']);
                for($i = 0; $i < count($_FILES['fileAppendices']['tmp_name']); ++$i)
                {
                    if($_FILES['fileAppendices']['tmp_name'][$i] !== '')
                    {
                        $pdfAppendixFileName = getNonExistingFileName($baseDir, 'pdf');
                        move_uploaded_file($_FILES['fileAppendices']['tmp_name'][$i], $pdfAppendixFileName);
                        addPdfAppendix( $dbConn
                                      , $_POST['txtJobApplicationTemplateName']
                                      , $templateId
                                      , $pdfAppendixFileName);
                    }
                }
            }
            $currentMessage .= join('<br>', $templateDataResult->errors);
        }
        catch(\Exception $e)
        {
            $currentMessage .= $e->errorMessage;
        }
    }
    else if(isset($_POST['sbmApplyNowForReal']) || isset($_POST['sbmApplyNowForTest']))
    {
        $employerIndex = getEmployerIndex($dbConn, $_SESSION['user']['id'], $_POST['hidEmployerIndex']);
        $employerValuesDict = getEmployer($dbConn, $_SESSION['user']['id'], $employerIndex);
        $userDetails = getUserDetails($dbConn, $_SESSION['user']['id']);
        $userEmail = getEmailByUserId($dbConn, $_SESSION['user']['id']);
        if(is_null($userDetails))
        {
            $currentMessage = 'Userdetails konnten nicht gefunden werden.';
        }
        else
        {
            $userDetailsDict =
                [ '$meinTitel' => $userDetails->degree
                , '$meineAnrede' => $userDetails->gender
                , '$meinVorname' => $userDetails->firstName
                , '$meinNachname' => $userDetails->lastName
                , '$meineStrasse' => $userDetails->street
                , '$meinePlz' => $userDetails->postcode
                , '$meineStadt' => $userDetails->city
                , '$meineEmail' => $userEmail
                , '$meineTelefonnr' => $userDetails->phone
                , '$meineMobilnr' => $userDetails->mobilePhone
                , '$meinGeburtsdatum' => $userDetails->birthday
                , '$meinGeburtsort' => $userDetails->birthplace
                , '$meinFamilienstand' => $userDetails->maritalStatus]; 
            $dict = $employerValuesDict + $userDetailsDict +
                [ "\$geehrter" => $employerValuesDict["\$chefAnrede"] === 'm' ? 'geehrter' : 'geehrte'
                , "\$chefAnredeBriefkopf" => $employerValuesDict["\$chefAnrede"] === 'm' ? 'Herrn' : 'Frau'
                , "\$datumHeute" => date('d.m.Y')];
            $dict["\$chefAnrede"] = $employerValuesDict["\$chefAnrede"] === 'm' ? 'Herr' : 'Frau';

            $jobApplicationTemplateId = getTemplateIdByIndex($dbConn, $_SESSION['user']['id'], $_POST['hidTemplateIndex']);
            $jobApplicationTemplate = getJobApplicationTemplate($dbConn, $_SESSION['user']['id'], $jobApplicationTemplateId);
            $pdfDirectoryAndFile = getPDF($jobApplicationTemplate['odtFile'], $dict, '/var/www/userFiles/tmp/');
            $templateId = getTemplateIdByIndex($dbConn, $_SESSION['user']['id'], $_POST['hidTemplateIndex']);
            addJobApplication($dbConn, $_SESSION['user']['id'], $employerIndex, $templateId);


            $pdfAppendices = getPdfAppendices($dbConn, $templateId);
            $pdfUniteCommand = $config['pdfunite'] . ' ' . ($pdfDirectoryAndFile[0] . $pdfDirectoryAndFile[1]);
            foreach($pdfAppendices as $currentPdfAppendix)
            {
                $pdfUniteCommand .= ' ' . $currentPdfAppendix['pdfFile'];
            }
            $pdfFileName = $pdfDirectoryAndFile[0] . str_replace(' ', '_', mb_strtolower($userDetails->lastName . '_bewerbung_als_' . $jobApplicationTemplate['userAppliesAs'])) . '.pdf';
            exec($pdfUniteCommand . ' ' . $pdfFileName . ' 2>1', $output);

            $taskResult = sendMail( $userEmail
                , $userDetails->firstName . ' ' . $userDetails->lastName
                , replaceAllInString($jobApplicationTemplate['emailSubject'], $dict)
                , replaceAllInString($jobApplicationTemplate['emailBody'], $dict)
                , $pdfFileName
                , isset($_POST['sbmApplyNowForReal']) ? $employerValuesDict['$firmaEmail'] : $userEmail
                , [$pdfFileName]);
            if($taskResult->isValid)
            {
                $currentMessage .= 'Email wurde versandt.';
            }
            else
            {
                $currentMessage .= join('<br>', $taskResult->errors);
            }
        }
    }
    else if(isset($_POST['sbmDownloadSentApplications']))
    {
        $data = getJobApplications($dbConn, $_SESSION['user']['id'], 0, 0);
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B', 15);
        $pdf->MultiCell(0, 10, 'Rene Ederer');
        $pdf->MultiCell(0, 10, 'Bewerbungen 01.10.2017 - 24.10.2017\n');
        $pdf->MultiCell(0, 15, '');
        $pdf->SetFont('Arial','', 13);
        $w = [50, 135];
        foreach($data as $row)
        {
            $i = 0;
            foreach($row as $col)
            {
                $pdf->Cell($w[$i],6,$col,1);
                ++$i;
                if($i >= 2){break;}
            }
            $pdf->Ln();
        }
        $pdf->Output();
    }


    function logout()
    {
        $_SESSION['user'] = Array();
    }

    function sendMail($from, $fromName, $subject, $body, $pdfAttachment, $to, $attachments)
    {
        try
        {
            $email = new PHPMailer(true);
            $email->CharSet = 'UTF-8';
            $email->Host = 'tls://smtp.gmail.com';
            $email->Port = 587;
            $email->SMTPAuth = true;
            $email->SMTPSecure = 'tls';
            $email->IsSMTP();
            $email->Username = 'rene.ederer.nbg@gmail.com';
            $email->Password = 'Steinmetzstr9';
            $email->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true));
            $email->From = $from;
            $email->FromName = $fromName;
            $email->Subject = $subject;
            $email->Body = $body;
            $email->AddBCC($from);
            $email->AddAddress($to);
            $email->AddAttachment($pdfAttachment, $pdfAttachment);
            $email->Send();
        }
        catch(phpmailerException $e)
        {
            die($e->errorMessage());
            return new TaskResult(false, ['Email konnte nicht versandt werden'], []);
        }
        catch(\Exception $e)
        {
            die($e->errorMessage());
            return new TaskResult(false, ['Email konnte nicht versandt werden'], []);
        }
        return new TaskResult(true, [], []);
    }


?>




<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dein Bewerbungs-Spam!</title>


    <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/current.css">
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script type="text/javascript">
    selectedEmployerRowIndex = 0;
    lastEmployerBackgroundColor = "white";
    selectedTemplateRowIndex = 0;
    lastTemplateBackgroundColor = "white";
    function selectTemplateRowIndex(row)
    {
        document.getElementById("selectTemplateTable").getElementsByTagName("tr")[selectedTemplateRowIndex].style.backgroundColor = lastTemplateBackgroundColor;
        selectedTemplateRowIndex = row.rowIndex;
        document.getElementById('hidTemplateIndex').value = row.rowIndex;
        lastTemplateBackgroundColor = row.style.backgroundColor;
        row.style.backgroundColor = 'lightgreen';
    }
    function selectEmployerRowIndex(row)
    {
        document.getElementById("selectEmployerTable").getElementsByTagName("tr")[selectedEmployerRowIndex].style.backgroundColor = lastEmployerBackgroundColor;
        selectedEmployerRowIndex = row.rowIndex;
        document.getElementById('hidEmployerIndex').value = row.rowIndex;
        lastEmployerBackgroundColor = row.style.backgroundColor;
        row.style.backgroundColor = 'lightgreen';
    }


    function templateAppendixSelected(fileNr)
    {
        lastTableRow = $("#tblUploadJobApplicationTemplate tr").eq(-1);
        fileAppendices = $("#tblUploadJobApplicationTemplate [name = 'fileAppendices[]'");
        if(fileAppendices.length == fileNr)
        {
            td1 = $("<td />").text("PDF Anhang");
            fileInput = $("<input></input>")
                    .attr("type", "file")
                    .attr("name", "fileAppendices[]")
                    .attr("value", "PDF Anhang")
                    .attr("onChange", "templateAppendixSelected(" + (fileNr + 1) + ");");
            td2 = $("<td />").append(fileInput);
            tr = $("<tr />");
            tr.append(td1);
            tr.append(td2);
            tr.insertBefore(lastTableRow);
        }


    }

    function validateInput(el)
    {
        if(el.value != "hallo" && false)
        {
            el.parentNode.classList.add("has-error");
            el.setCustomValidity("Everything fine");
        }
        else
        {
            el.parentNode.classList.remove("has-error");
            el.setCustomValidity("");
        }
    }

</script>



<script>
    $(document).ready(function() {
          $('[data-toggle=offcanvas]').click(function() {
                  $('.row-offcanvas').toggleClass('active');
                    });
          });
</script>

</head>


<body>


<!--
              <li class="active"><h4><a href="#">Deine Daten</a></h4></li>
              <li><h4><a href="#">Bewerbungsvorlage hochladen</a></h4></li>
              <li><h4><a href="#">Arbeitgeber hinzuf&uuml;gen</a></h4></li>
              <li><h4><a href="#">Jetzt bewerben</a></h4></li> 
              <li><h4><a href="#">Abgeschickte Bewerbungen</a></h4></li> 
              <li><h4><a href="#">Termin festlegen</a></h4></li> 
-->
    
        <!-- main area -->


<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Bewerbungs-Spam!</a>
    </div>
    <div class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">Home</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
    </div><!--/.nav-collapse -->
</div><!--/.navbar -->

<div class="row-offcanvas row-offcanvas-left">
  <div id="sidebar" class="sidebar-offcanvas">
      <div class="col-md-12">
        <ul class="nav nav-pills nav-stacked">
          <li class="active"><h3><a href="#uploadTemplate">Bewerbungsvorlage hochladen</a></h3></li>
          <li><h3><a href="#setYourValues">Deine Werte &auml;ndern</a></h3></li>
          <li><h3><a href="#addEmployer">Arbeitgeber hinzuf&uuml;gen</a></h3></li>
          <li><h3><a href="#applyNow">Jetzt bewerben</a></h3></li>
          <li><h3><a href="#sentApplications">Abgeschickte Bewerbungen anzeigen</a></h3></li>
          <li><h3><a href="#dates">Termine</a></h3></li>
        </ul>
      </div>
  </div>
  <div id="main">
    
      <div class="col-md-12">
          <p class="visible-xs">
              <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas">&gt;</button>
          </p>
            <?php
                if(isset($_SESSION['user']) && isset($_SESSION['user']['id']) && $_SESSION['user']['id'] >= 1)
                {
            ?>
                    <div id="loggedInDiv" style="position:absolute;top:0px;float:right;right:0px;">
                        Eingeloggt als <?php
    $email = getEmailByUserId($dbConn, $_SESSION['user']['id']);
    echo htmlspecialchars($email); ?>
                        <br />
                        <form action="" method="post"><input type="submit" value="Ausloggen" name="sbmLogout" /></form>
                    </div>
            <?php
                }
                else if(!isset($_POST['sbmShowRegisterForm']))
                {
            ?>
                    <div id="loginForm" class="page">
                        <form action="" method="post">
                            <table>
                                <tr>
                                    <td>Email:</td>
                                    <td><input type="text" value="" name="txtUserEmail" /></td>
                                </tr>
                                <tr>
                                    <td>Password:</td>
                                    <td><input type="password" value="" name="txtPassword" /></td>
                                </tr>
                                <tr>
                                    <td><input type="submit" name="sbmLoginForm" value="Einloggen" /></td>
                            </table>
                        </form>
                        <form action="" method="post">Neu?<input type="submit" value="Registrieren" name="sbmShowRegisterForm" /></form>
                    </div>
            <?php
                } else if(isset($_POST['sbmShowRegisterForm']))
                {
            ?>
                    <div id="registerForm" class="page">
                        <form action="" method="post">
                            <table>
                                <tr>
                                    <td>Email:</td>
                                    <td><input type="text" value="" name="txtUserEmail" /></td>
                                </tr>
                                <tr>
                                    <td>Password:</td>
                                    <td><input type="password" value="" name="txtPassword" /></td>
                                </tr>
                                <tr>
                                    <td>Password wiederholen:</td>
                                    <td><input type="password" value="" name="txtPassworRepeated" /></td>
                                </tr>
                                    <td></td>
                                    <td><input type="submit" name="sbmRegisterForm" value="Registrieren" /></td>
                                </tr>
                            </table>
                        </form>
                    </div>

            <?php
                }
            ?>









          <div style="background-color:yellow;position:fixed;top:350px;left:300px;">
              <?php echo htmlspecialchars($currentMessage); ?>...
          </div>
          <div class="distanced-div">
              <h1 id="uploadTemplate" class="undecorated-anchor">Bewerbungsvorlage hochladen</h1>
              <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group has-error">
                      <label for="txtJobApplicationTemplateName">Name der Vorlage</label>
                      <input type="text" class="form-control has-error" onInput="validateInput(this)" id="txtJobApplicationTemplateName" name="txtJobApplicationTemplateName" value="<?php echo htmlspecialchars($_POST['txtJobApplicationTemplateName'] ?? ''); ?>" />
                      <span class="help-block"></span>
                  </div>
                  <div class="form-group">
                      <label for="txtJobApplicationTemplateName">Bewerbung als</label>
                      <input type="text" class="form-control" id="txtUserAppliesAs" name="txtUserAppliesAs" value="<?php echo htmlspecialchars($_POST['txtUserAppliesAs'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserEmailSubject">Email-Betreff</label>
                      <input type="text" class="form-control" id="txtUserEmailSubject" name="txtUserEmailSubject" value="<?php echo htmlspecialchars($_POST['txtUserEmailSubject'] ?? ''); ?>" />
                  </div>
                      <label for="txtUserEmailBody">Email-Body</label>
                      <textarea name="txtUserEmailBody" class="form-control" id="txtUserEmailBody" cols="100" rows="15"><?php echo htmlspecialchars($_POST['txtUserEmailBody'] ?? ''); ?></textarea>
                  <div class="form-group">
                      <label for="fileODT">Vorlage (*.odt oder *.docx)</label>
                      <input type="file" name="fileODT" id="fileODT" accept=".odt,.docx" />
                  </div>
                  <div class="form-group">
                      <label for="filePdf1">Pdf Anhang</label>
                      <input type="file" name="fileAppendices[]" id="filePdf1" Anhang" accept=".pdf" onChange="templateAppendixSelected(1);" />
                  </div>
                  <div class="form-group">
                      <input type="submit" name="sbmUploadJobApplicationTemplate" value="Vorlage hochladen" />
                  </div>
              </form>
          </div>



          <div class="distanced-div">
              <h1 id="setYourValues" class="undecorated-anchor">Deine Werte &auml;ndern</h1>
              <form action="" method="post">
                  <div class="form-group">
                      <label for="">Geschlecht</label>
                      <div class="form-control">
                          <input type="hidden" name="rbUserGender" value="x" />
                          <label class="radio-inline"><input type="radio" name="rbUserGender" value="m" <?php if(isset($_POST['rbUserGender']) && $_POST['rbUserGender'] === 'm') echo 'checked="checked"'; ?>/>M&auml;nnlich</label>
                          <label class="radio-inline"><input type="radio" name="rbUserGender" value="f" <?php if(isset($_POST['rbUserGender']) && $_POST['rbUserGender'] === 'f') echo 'checked="checked"'; ?>/>Weiblich</label>
                      </div>
                  </div>
                  <div class="form-group">
                      <label for="txtUserDegree">Titel</label>
                      <input class="form-control" type="text" name="txtUserDegree" id="txtUserDegree" value="<?php echo htmlspecialchars($_POST['txtUserDegree'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserFirstName">Vorname</label>
                      <input class="form-control" type="text" name="txtUserFirstName" id="txtUserFirstName" value="<?php echo htmlspecialchars($_POST['txtUserFirstName'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserLastName">Nachname</label>
                      <input class="form-control" type="text" name="txtUserLastName" id="txtUserLastName" value="<?php echo htmlspecialchars($_POST['txtUserLastName'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserStreet">Stra&szlig;e</label>
                      <input class="form-control" type="text" name="txtUserStreet" id="txtUserStreet" value="<?php echo htmlspecialchars($_POST['txtUserStreet'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserPostcode">Postleitzahl</label>
                      <input class="form-control" type="text" name="txtUserPostcode" id="txtUserPostcode" value="<?php echo htmlspecialchars($_POST['txtUserPostcode'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserCity">Stadt</label>
                      <input class="form-control" type="text" name="txtUserCity" id="txtUserCity" value="<?php echo htmlspecialchars($_POST['txtUserCity'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserEmail">Email</label>
                      <input class="form-control" type="text" name="txtUserEmail" id="txtUserEmail" value="<?php echo htmlspecialchars($_POST['txtUserEmail'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserMobilePhone">Telefon mobil</label>
                      <input class="form-control" type="text" name="txtUserMobilePhone" id="txtUserMobilePhone" value="<?php echo htmlspecialchars($_POST['txtUserMobilePhone'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserPhone">Telefon fest</label>
                      <input class="form-control" type="text" name="txtUserPhone" id="txtUserPhone" value="<?php echo htmlspecialchars($_POST['txtUserPhone'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserBirthday">Geburtstag</label>
                      <input class="form-control" type="text" name="txtUserBirthday" id="txtUserBirthday" value="<?php echo htmlspecialchars($_POST['txtUserBirthday'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserBirthplace">Geburtsort</label>
                      <input class="form-control" type="text" name="txtUserBirthplace" id="txtUserBirthplace" value="<?php echo htmlspecialchars($_POST['txtUserBirthplace'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserMaritalStatus">Familienstand</label>
                      <input class="form-control" type="text" name="txtUserMaritalStatus" id="txtUserMaritalStatus" value="<?php echo htmlspecialchars($_POST['txtUserMaritalStatus'] ?? ''); ?>" />
                  </div>
                      <input type="submit" name="sbmSetUserDetails" value="Deine Werte &auml;ndern"/>
              </form>
          </div>




            <div class="distanced-div">
                <h1 id="addEmployer" class="undecorated-anchor">Arbeitgeber hinzuf&uuml;gen</h1>
                <form action="" method="post">
                    <input type="text" name="txtReadEmployerValuesFromWebSite" />
                    <input type="submit" name="sbmReadEmployerValuesFromWebSite" value="Werte von Website einlesen" />
                </form>
                <form action="" method="post">
                    <?php
                        $currentEmployer = ['$chefAnredeBriefkopf' => ''
                                           , '$geehrter' => ''
                                           , '$chefAnrede' => ''
                                           , '$chefVorname' => ''
                                           , '$chefNachname' => ''
                                           , '$firmaName' => ''
                                           , '$firmaStrasse' => ''
                                           , '$firmaPlz' => ''
                                           , '$firmaStadt' => ''
                                           , '$firmaTelefon' => ''
                                           , '$firmaEmail' => '' ];
                        if(isset($_POST['sbmReadEmployerValuesFromWebSite']))
                        {
                            $currentEmployer = readEmployerFromWebsite($_POST['txtReadEmployerValuesFromWebSite']);
                        }
                    ?>
                            <label for="txtCompany">Firma</label>
                            <input class="form-control" type="text" name="txtCompany" value="<?php echo htmlspecialchars($currentEmployer['$firmaName'] ?? ''); ?>" />
                            <label for="txtCompanyStreet">Stra&szlig;e</label>
                            <input class="form-control" type="text" name="txtCompanyStreet" value="<?php echo htmlspecialchars($currentEmployer['$firmaStrasse'] ?? ''); ?>"/>
                            <label for="txtCompanyPostcode">Postleitzahl</label>
                            <input class="form-control" type="text" name="txtCompanyPostcode" value="<?php echo htmlspecialchars($currentEmployer['$firmaPlz'] ?? ''); ?>"/>
                            <label for="txtCompanyCity">Stadt</label>
                            <input class="form-control" type="text" name="txtCompanyCity" value="<?php echo htmlspecialchars($currentEmployer['$firmaStadt'] ?? ''); ?>"/>
                            <div class="form-group">
                                <label>Chef-Geschlecht</label>
                                <div class="form-control">
                                    <input type="hidden" name="rbBossGender" value="x" />
                                    <label class="radio-inline"><input type="radio" name="rbBossGender" value="m" <?php if(isset($_POST['rbBossGender']) && $_POST['rbBossGender'] === 'm') echo 'checked="checked"'; ?>/>M&auml;nnlich</label>
                                    <label class="radio-inline"><input type="radio" name="rbBossGender" value="f" <?php if(isset($_POST['rbBossGender']) && $_POST['rbBossGender'] === 'f') echo 'checked="checked"'; ?>/>Weiblich</label>
                                </div>
                            </div>
                            <label for="txtBossDegree">Chef-Titel</label>
                            <input class="form-control" type="text" name="txtBossDegree" value="<?php echo htmlspecialchars($currentEmployer['$chefTitel'] ?? ''); ?>"/>
                            <label for="txtBossFirstName">Chef-Vorname</label>
                            <input class="form-control" type="text" name="txtBossFirstName" value="<?php echo htmlspecialchars($currentEmployer['$chefVorname'] ?? ''); ?>"/>
                            <label for="txtBossLastName">Chef-Nachname</label>
                            <input class="form-control" type="text" name="txtBossLastName" value="<?php echo htmlspecialchars($currentEmployer['$chefNachname'] ?? ''); ?>"/>
                            <label for="txtBossEmail">Email</label>
                            <input class="form-control" type="text" name="txtBossEmail" value="<?php echo htmlspecialchars($currentEmployer['$firmaEmail'] ?? ''); ?>"/>
                            <label for="txtBossMobilePhone">Telefon mobil</label>
                            <input class="form-control" type="text" name="txtBossMobilePhone" value="<?php echo htmlspecialchars($currentEmployer['$firmaMobil'] ?? ''); ?>"/>
                            <label for="txtBossPhone">Telefon fest</label>
                            <input class="form-control" type="text" name="txtBossPhone" value="<?php echo htmlspecialchars($currentEmployer['$firmaTelefon'] ?? ''); ?>"/>
                            <input type="submit" name="sbmAddEmployer" value="Arbeitgeber hinzuf&uuml;gen" />
                </form>
            </div>








                <div id="divApplyNow" class="distanced-div">
                    <h1 id="applyNow" class="undecorated-anchor">Jetzt bewerben</h1>
                    <table id="selectEmployerTable" class="table table-hover table-border table-sm">
                    <?php
                        $employers = [];
                        if(isset($_SESSION['user']) && isset($_SESSION['user']['id']))
                        {
                            $employers = getEmployers($dbConn, $_SESSION['user']['id']);
                        }
                        if(count($employers) > 0)
                        {
                            echo '<tr>';
                            foreach($employers[0] as $key => $value)
                            {
                                echo '<td>' . htmlspecialchars($key) . '</td>';
                            }
                            echo '</tr>';
                            foreach($employers as $employer)
                            {
                                echo '<tr onClick="selectEmployerRowIndex(this)">';
                                foreach($employer as $key => $value)
                                {
                                        echo '<td>';
                                            echo htmlspecialchars($value);
                                        echo '</td>';
                                }
                                echo '</tr>';
                            }
                        }
                    ?>
                    </table>
                    <table id="selectTemplateTable" class="selectableTable">
                    <?php
                        $jobApplicationTemplates = [];
                        if(isset($_SESSION['user']) && isset($_SESSION['user']['id']))
                        {
                            $jobApplicationTemplates = getJobApplicationTemplates($dbConn, $_SESSION['user']['id']);
                        }
                        if(count($jobApplicationTemplates) > 0)
                        {
                            echo '<tr>';
                            foreach($jobApplicationTemplates[0] as $key => $value)
                            {
                                echo '<td>' . htmlspecialchars($key) . '</td>';
                            }
                            echo '</tr>';
                            foreach($jobApplicationTemplates as $jobApplicationTemplate)
                            {
                                echo '<tr onClick="selectTemplateRowIndex(this)">';
                                foreach($jobApplicationTemplate as $key => $value)
                                {
                                        echo '<td>';
                                            echo htmlspecialchars($value);
                                        echo '</td>';
                                }
                                echo '</tr>';
                            }
                        }
                    ?>
                    </table>
                    <form action="" method="post">
                        <input type="hidden" id="hidEmployerIndex" name="hidEmployerIndex" value="" />
                        <input type="hidden" id="hidTemplateIndex" name="hidTemplateIndex" value="" />
                        <table>
                            <tr>
                                <td><input type="submit" name="sbmApplyNowForReal" value="Bewerbung abschicken" /><td>
                            <tr>
                                <td><input type="submit" name="sbmApplyNowForTest" value="Bewerbung zum Testen an mich selbst schicken" /></td>
                            </tr>
                        </table>
                    </form>
                </div>





                <div id="divSentApplications" class="distanced-div">
                    <h1 id="sentApplications" class="undecorated-anchor">Abgeschickte Bewerbungen</h1>
                    <table class="table table-hover table-border">
                    <?php
                        $sentApplications = [];
                        if(isset($_SESSION['user']) && isset($_SESSION['user']['id']))
                        {
                            $sentApplications = getJobApplications($dbConn, $_SESSION['user']['id'], 0, 0); //TODO Fix parameters
                        }
                        if(count($sentApplications) > 0)
                        {
                            echo '<tr>';
                            foreach($sentApplications[0] as $key => $value)
                            {
                                echo '<td>' . htmlspecialchars($key) . '</td>';
                            }
                            echo '</tr>';
                            foreach($sentApplications as $currentApplication)
                            {
                                echo '<tr>';
                                foreach($currentApplication as $key => $value)
                                {
                                        echo '<td>';
                                            echo htmlspecialchars($value);
                                        echo '</td>';
                                }
                                echo '</tr>';
                            }
                        }
                    ?>
                    </table>
                    <form action="" method="post" enctype="multipart/form-data">
                        <table>
                            <tr>
                                <td>Von Datum:</td>
                                <td><input type="date" value="<?php 
                                    $firstOfMonth = strtotime('-' . (date('d') - 1) . ' days', time());
                                    echo date('Y-m-d', $firstOfMonth);
                                ?>" name="dateDownloadSentApplicationsFromDate" /></td>
                            </tr>
                            <tr>
                                <td>Bis Datum:</td>
                                <td><input type="date" value="<?php echo date('Y-m-d'); ?>" name="dateDownloadSentApplicationsToDate" /></td>
                            </tr>
                            <tr>
                                <td><input type="submit" name="sbmDownloadSentApplications" value="Liste als PDF downloaden" /></td>
                            </tr>
                        </table>
                    </form>
                </div>











    </div>
  </div>
</div>








</body>

</html>
