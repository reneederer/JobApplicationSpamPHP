<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('config.php');
    require_once('odtFunctions.php');
    require_once('websiteFunctions.php');
    require_once('dbFunctions.php');
    require_once('helperFunctions.php');
    require_once('validate.php');

    session_start();

    $currentMessage = "";


    $dbConn = new PDO('mysql:host=localhost;dbname=' . $config["database"]["database"], $config['database']['username'], $config['database']['password']);
    $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbConn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbConn->exec("SET NAMES utf8");


    if(isset($_POST['sbmLoginForm']))
    {
        $_SESSION['user'] = identifyUser($dbConn, $_POST['txtName'], $_POST['txtPassword']);
        if(count($_SESSION['user']) > 0)
        {
            $userValues = getUserValues($dbConn, $_SESSION['user']['id']);
            foreach($userValues as $key => $value)
            {
                $_SESSION['user'][$key] = $value;
            }
            $_SESSION['user']['userName'] = $_POST['txtName'];
        }
    }
    else if(isset($_POST['sbmLogout']))
    {
        logout();
    }
    else if(isset($_POST['sbmSetUserValues']))
    {
        $userValues = new User($_POST['rbGender'],
                               $_POST['txtDegree'],
                               $_POST['txtFirstName'],
                               $_POST['txtLastName'],
                               $_POST['txtStreet'],
                               $_POST['txtPostCode'],
                               $_POST['txtCity'],
                               $_POST['txtEmail'],
                               $_POST['txtMobilePhone'],
                               $_POST['txtPhone']);
        $checkUserValuesResult = validateUserData($userValues);
        if(!$checkUserValuesResult->isValid)
        {
            $currentMessage = join("<br>", $checkUserValuesResult->errors);
        }
        else
        {
            updateUserValues($dbConn, $_SESSION['user']['id'], $userValues);
        }
    }
    else if(isset($_POST['sbmAddEmployer']))
    {
        addEmployer($dbConn, $_SESSION['user']['id'], $_POST['txtCompany'], $_POST['txtStreet'], $_POST['txtPostCode'], $_POST['txtCity'], $_POST['txtGender'], $_POST['txtDegree'], $_POST['txtFirstName'], $_POST['txtLastName'], $_POST['txtEmail'], $_POST['txtMobilePhone'], $_POST['txtPhone']);
    }
    else if(isset($_POST['sbmDownloadPDF']))
    {
        $dict = readEmployerFromWebsite('http://localhost/jobApplicationSpam/jobboerseArbeitsagentur.html');
        $directoryAndFileName = getPDF($directory, $odtFile, $dict);
        addToDownloads($dbConn, $directoryAndFileName[0], $_SESSION['user']['id']);
        header('Content-type:application/pdf');
        header("Content-Disposition:attachment;filename=jobApplication.pdf");
        echo file_get_contents($directoryAndFileName[0] .  $directoryAndFileName[1]);
    }
    else if(isset($_POST['sbmUploadJobApplicationTemplate']))
    {
        $templateDataResult = validateTemplateData(
            new TemplateData($_POST['txtJobApplicationTemplateName'],
                             $_POST['txtEmailSubject'], $_POST['txtEmailBody'],
                             $_FILES['fileODT']['name'],
                             $_FILES['fileAppendices']['name']));
        if($templateDataResult->isValid){
            $baseDir = "/var/www/user/" . $_SESSION['user']['userName'] . '/';
            if (!file_exists($baseDir)) {
                mkdir($baseDir, 0777, true);
            }
            $odtFileName = getNonExistingFileName($baseDir);
            move_uploaded_file($_FILES['fileODT']['tmp_name'], $odtFileName);
            addJobApplicationTemplate( $dbConn
                                     , $_SESSION['user']['id']
                                     , $_POST['txtJobApplicationTemplateName']
                                     , $_POST['txtUserAppliesAs']
                                     , $_POST['txtEmailSubject']
                                     , $_POST['txtEmailBody']
                                     , $odtFileName);
            $templateId = getTemplateIdByName($dbConn, $_SESSION['user']['id'], $_POST['txtJobApplicationTemplateName']);
            for($i = 0; $i < count($_FILES['fileAppendices']['tmp_name']); ++$i)
            {
                if($_FILES['fileAppendices']['tmp_name'][$i] !== '')
                {
                    $pdfAppendixFileName = getNonExistingFileName($baseDir);
                    move_uploaded_file($_FILES['fileAppendices']['tmp_name'][$i], $pdfAppendixFileName);
                    addPdfAppendix( $dbConn
                                  , $_POST['txtJobApplicationTemplateName']
                                  , $templateId
                                  , $pdfAppendixFileName);
                }
            }
        }
        $currentMessage = join("<br>", $templateDataResult->errors);
    }
    else if(isset($_POST['sbmApplyNowForReal']) || isset($_POST['sbmApplyNowForTest']))
    {
        $employerIndex = getEmployerIndex($dbConn, $_SESSION['user']['id'], $_POST['hidEmployerIndex']);
        $employerValuesDict = getEmployer($dbConn, $_SESSION['user']['id'], $employerIndex);
        $userValuesDict =
            [ '$meinTitel' => $_SESSION['user']['title']
            , '$meineAnrede' => $_SESSION['user']['gender']
            , '$meinVorname' => $_SESSION['user']['firstName']
            , '$meinNachname' => $_SESSION['user']['lastName']
            , '$meineStrasse' => $_SESSION['user']['street']
            , '$meinePlz' => $_SESSION['user']['postCode']
            , '$meineStadt' => $_SESSION['user']['city']
            , '$meineEmail' => $_SESSION['user']['email']
            , '$meineTelefonnr' => $_SESSION['user']['phone']
            , '$meineMobilnr' => $_SESSION['user']['mobilePhone']
            , '$meinGeburtsdatum' => $_SESSION['user']['birthday']
            , '$meinGeburtsort' => $_SESSION['user']['birthplace']
            , '$meinFamilienstand' => $_SESSION['user']['maritalStatus'] ];
        $dict = $employerValuesDict + $userValuesDict +
            [ "\$geehrter" => $employerValuesDict["\$chefAnrede"] === "Herr" ? 'geehrter' : 'geehrte'
            , "\$chefAnredeBriefkopf" => $employerValuesDict["\$chefAnrede"] === "Herr" ? 'Herrn' : 'Frau'
            , "\$datumHeute" => date('d.m.Y')];

        $jobApplicationTemplate = getJobApplicationTemplate($dbConn, $_SESSION['user']['id'], $_POST['hidTemplateIndex']);
        $pdfDirectoryAndFile = getPDF(file_get_contents($jobApplicationTemplate['odtFile']), $dict);
        addToDownloads($dbConn, $pdfDirectoryAndFile[0], $_SESSION['user']['id']);
        $templateId = getTemplateIdByIndex($dbConn, $_SESSION['user']['id'], $_POST['hidTemplateIndex']);
        addJobApplication($dbConn, $_SESSION['user']['id'], $employerIndex, $templateId);
        $pdfAppendices = getPdfAppendices($dbConn, $templateId);
        $m = new Merger();
        $m->addFromFile($pdfDirectoryAndFile[0] . $pdfDirectoryAndFile[1]);
        foreach($pdfAppendices as $currentPdfAppendix)
        {
            $m->addFromFile($currentPdfAppendix['pdfFile']);
        }
        $pdfFileName = $pdfDirectoryAndFile[0] . str_replace(" ", "_", mb_strtolower($_SESSION['user']['lastName'] . '_bewerbung_als_' . $jobApplicationTemplate['userAppliesAs'])) . '.pdf';
        file_put_contents($pdfFileName, $m->merge());
        sendMail( $_SESSION['user']['email']
            , $_SESSION['user']['firstName'] . ' ' . $_SESSION['user']['lastName']
            , replaceAllInStringIgnoreTags($jobApplicationTemplate['emailSubject'], $dict)
            , replaceAllInStringIgnoreTags($jobApplicationTemplate['emailBody'], $dict)
            , isset($_POST['sbmApplyNowForReal']) ? $employerValuesDict['$firmaEmail'] : $_SESSION['user']['email']
            , [$pdfFileName]);
    }
    else if(isset($_POST['sbmDownloadSentApplications']))
    {
        $data = getJobApplications($dbConn, $_SESSION['user']['id'], 0, 0);
        $pdf = new FPDF();
        //    $pdf->AddPage();
         //   $pdf->SetFont('Arial', '', 13);
          //  $pdf->Cell(40, 10, 'hello world');
        // Colors, line width and bold font
        $pdf->AddPage();
        $pdf->SetFont('Arial','B', 15);
        //foreach($header as $col)
        //{
         //   $pdf->Cell(40,7,$col,1);
          //  $pdf->Ln();
        //}
        $pdf->MultiCell(0, 10, "Rene Ederer");
        $pdf->MultiCell(0, 10, "Bewerbungen 01.10.2017 - 24.10.2017\n");
        $pdf->MultiCell(0, 15, "");
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

    function sendMail($from, $fromName, $subject, $body, $to, $attachments)
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
            $email->Username = $config['email']['username'];
            $email->Password = $config['email']['password'];
            $email->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true));
            //$email->SMTPDebug = 4;
            //echo "<br>" . $email->Username . "," . $email->Password . ".";
            $email->From = $from;
            $email->FromName = $fromName;
            $email->Subject = $subject;
            $email->Body = $body;
            $email->AddBCC($_SESSION['user']['email'], $_SESSION['user']['firstName'] . ' ' . $_SESSION['user']['lastName']);
            $email->AddAddress($to);
            $email->AddAttachment($attachments[0], $attachments[0]);
            $email->Send();
        }
        catch(phpmailerException $e)
        {
            echo $e->errorMessage();
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
        }
    }


?>




<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Simple Sidebar - Start Bootstrap Template</title>


    <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/current.css">
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>



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
      <a class="navbar-brand" href="#">Brand</a>
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
        <h3>Sidebar (fixed)</h3>
        <ul class="nav nav-pills nav-stacked">
          <li class="active"><a href="#uploadTemplate">Bewerbungsvorlage hochladen</a></li>
          <li><a href="#setYourValues">Deine Werte &auml;ndern</a></li>
          <li><a href="#addEmployer">Arbeitgeber hinzuf&uuml;gen</a></li>
          <li><a href="#applyNow">Jetzt bewerben</a></li>
          <li><a href="#sentApplications">Abgeschickte Bewerbungen anzeigen</a></li>
          <li><a href="#dates">Termine</a></li>
        </ul>
      </div>
  </div>
  <div id="main">
    
      <div class="col-md-12">
          <p class="visible-xs">
              <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><h3>&gt;</h3></button>
          </p>
            <?php
                if(isset($_SESSION['user']) && isset($_SESSION['user']['id']) && $_SESSION['user']['id'] >= 1)
                {
            ?>
                    <div id="loggedInDiv" style="position:absolute;top:0px;float:right;right:0px;">
                        Eingeloggt als <?php echo $_SESSION['user']['name']; ?>
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
                                    <td>Benutzername:</td>
                                    <td><input type="text" value="" name="txtName" /></td>
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
                                    <td>Benutzername:</td>
                                    <td><input type="text" value="" name="txtName" /></td>
                                </tr>
                                <tr>
                                    <td>Password:</td>
                                    <td><input type="password" value="" name="txtPassword" /></td>
                                </tr>
                                <tr>
                                    <td>Password wiederholen:</td>
                                    <td><input type="password" value="" name="txtPassworRepeated" /></td>
                                </tr>
                                <tr>
                                    <td>Email:</td>
                                    <td><input type="text" value="" name="txtEmail" /></td>
                                </tr>
                                <tr>
                                    <td><input type="submit" name="sbmRegisterForm" value="Registrieren" /></td>
                                </tr>
                            </table>
                        </form>
                    </div>

            <?php
                }
            ?>









          <div>
          <p><?php echo $currentMessage; ?></p>
          </div>
          <div class="distanced-div">
              <h1><a name="uploadTemplate">Bewerbungsvorlage hochladen</a></h1>
              <form action="" method="post" enctype="multipart/form-data">
                  <div class="form-group has-error">
                      <label for="txtJobApplicationTemplateName">Name der Vorlage</label>
                      <input type="text" class="form-control has-error" onInput="validateInput(this)" id="txtJobApplicationTemplateName" name="txtJobApplicationTemplateName" value="<?php if(isset($_POST['txtJobApplicationTemplateName'])) echo $_POST['txtJobApplicationTemplateName']; ?>" />
                      <span class="help-block"></span>
                  </div>
                  <div class="form-group">
                      <label for="txtJobApplicationTemplateName">Bewerbung als</label>
                      <input type="text" class="form-control" id="txtUserAppliesAs" name="txtUserAppliesAs" value="<?php if(isset($_POST['txtUserAppliesAs'])) echo $_POST['txtUserAppliesAs']; ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtEmailSubject">Email-Betreff</label>
                      <input type="text" class="form-control" id="txtEmailSubject" name="txtEmailSubject" value="<?php if(isset($_POST['txtEmailSubject'])) echo $_POST['txtEmailSubject']; ?>" />
                  </div>
                      <label for="txtEmailBody">Email-Body</label>
                      <textarea name="txtEmailBody" class="form-control" id="txtEmailBody" cols="100" rows="15"><?php if(isset($_POST['txtEmailBody'])) echo $_POST['txtEmailBody']; ?></textarea>
                  <div class="form-group">
                      <label for="fileODT">Vorlage (*.odt oder *.docx)</label>
                      <input type="file" name="fileODT" id="fileODT" />
                  </div>
                  <div class="form-group">
                      <label for="filePdf1">Pdf Anhang</label>
                      <input type="file" name="fileAppendices[]" id="filePdf1" value="PDF Anhang" onChange="templateAppendixSelected(1);" />
                  </div>
                  <div class="form-group">
                      <input type="submit" name="sbmUploadJobApplicationTemplate" value="Vorlage hochladen" />
                  </div>
              </form>
          </div>



          <div class="distanced-div">
              <h1><a name="setYourValues">Deine Werte &auml;ndern</a></h1>
              <form action="" method="post">
                  <div class="form-group">
                      <label for="">Geschlecht</label>
                      <div class="form-control">
                          <input type="hidden" name="rbGender" value="x" />
                          <label class="radio-inline"><input type="radio" name="rbGender" value="m" <?php if(isset($_POST['rbGender']) && $_POST['rbGender'] === 'm') echo 'checked="checked"'; ?>/>M&auml;nnlich</label>
                          <label class="radio-inline"><input type="radio" name="rbGender" value="f" <?php if(isset($_POST['rbGender']) && $_POST['rbGender'] === 'f') echo 'checked="checked"'; ?>/>Weiblich</label>
                      </div>
                  </div>
                  <div class="form-group">
                      <label for="txtDegree">Titel</label>
                      <input class="form-control" type="text" name="txtDegree" id="txtDegree" value="<?php if(isset($_POST['txtDegree'])) echo $_POST['txtDegree']; ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtFirstName">Vorname</label>
                      <input class="form-control" type="text" name="txtFirstName" id="txtFirstName" value="<?php if(isset($_POST['txtFirstName'])) echo $_POST['txtFirstName']; ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtLastName">Nachname</label>
                      <input class="form-control" type="text" name="txtLastName" id="txtLastName" value="<?php if(isset($_POST['txtLastName'])) echo $_POST['txtLastName']; ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtStreet">Stra&szlig;e</label>
                      <input class="form-control" type="text" name="txtStreet" id="txtStreet" value="<?php if(isset($_POST['txtStreet'])) echo $_POST['txtStreet']; ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtPostCode">Postleitzahl</label>
                      <input class="form-control" type="text" name="txtPostCode" id="txtPostCode" value="<?php if(isset($_POST['txtPostCode'])) echo $_POST['txtPostCode']; ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtCity">Stadt</label>
                      <input class="form-control" type="text" name="txtCity" id="txtCity" value="<?php if(isset($_POST['txtCity'])) echo $_POST['txtCity']; ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtEmail">Email</label>
                      <input class="form-control" type="text" name="txtEmail" id="txtEmail" value="<?php if(isset($_POST['txtEmail'])) echo $_POST['txtEmail']; ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtMobilePhone">Telefon mobil</label>
                      <input class="form-control" type="text" name="txtMobilePhone" id="txtMobilePhone" value="<?php if(isset($_POST['txtMobilePhone'])) echo $_POST['txtMobilePhone']; ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtPhone">Telefon fest</label>
                      <input class="form-control" type="text" name="txtPhone" id="txtPhone" value="<?php if(isset($_POST['txtPhone'])) echo $_POST['txtPhone']; ?>" />
                  </div>
                      <input type="submit" name="sbmSetUserValues" value="Deine Werte &auml;ndern"/>
              </form>
          </div>




            <div class="distanced-div">
                <h1><a name="addEmployer">Arbeitgeber hinzuf&uuml;gen</a></h1>
                <form action="" method="post">
                    <input type="text" name="txtReadEmployerValuesFromWebSite" />
                    <input type="submit" name="sbmReadEmployerValuesFromWebSite" value="Werte von Website einlesen" />
                </form>
                <form action="" method="post">
                    <table>
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
                            <input class="form-control" type="text" id="txtCompany" name="txtCompany" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaName']; ?>" />
                            <label for="txtStreet">Stra&szlig;e</label>
                            <input class="form-control" type="text" id="txtStreet" name="txtStreet" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaStrasse']; ?>"/>
                            <label for="txtPostCode">Postleitzahl</label>
                            <input class="form-control" type="text" id="txtPostCode" name="txtPostCode" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaPlz']; ?>"/>
                            <label for="txtCity">Stadt</label>
                            <input class="form-control" type="text" id="txtCity" name="txtCity" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaStadt']; ?>"/>
                            <label for="txtGender">Chef-Anrede</label>
                            <input class="form-control" type="text" id="txtGender" name="txtGender" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$chefAnrede']; ?>"/>
                            <label for="txtDegree">Chef-Titel</label>
                            <input class="form-control" type="text" id="txtDegree" name="txtDegree" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$chefTitel']; ?>"/>
                            <label for="txtFirstName">Chef-Vorname</label>
                            <input class="form-control" type="text" id="txtFirstName" name="txtFirstName" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$chefVorname']; ?>"/>
                            <label for="txtLastName">Chef-Nachname</label>
                            <input class="form-control" type="text" id="txtLastName" name="txtLastName" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$chefNachname']; ?>"/>
                            <label for="txtEmail">Email</label>
                            <input class="form-control" type="text" id="txtEmail" name="txtEmail" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaEmail']; ?>"/>
                            <label for="txtMobilePhone">Telefon mobil</label>
                            <input class="form-control" type="text" id="txtMobilePhone" name="txtMobilePhone" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaMobil']; ?>"/>
                            <label for="txtPhone">Telefon fest</label>
                            <input class="form-control" type="text" id="txtPhone" name="txtPhone" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaTelefon']; ?>"/>
                            <input type="submit" name="sbmAddEmployer" value="Arbeitgeber hinzuf&uuml;gen" />
                </form>
            </div>








                <div id="divApplyNow" class="distanced-div">
                    <h1><a name="applyNow">Jetzt bewerben</a></h1>
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
                            echo "\n";
                            foreach($employers[0] as $key => $value)
                            {
                                echo "<td>$key</td>";
                                echo "\n";
                            }
                            echo '</tr>';
                            echo "\n";
                            foreach($employers as $employer)
                            {
                                echo '<tr onClick="selectEmployerRowIndex(this)">';
                                echo "\n";
                                foreach($employer as $key => $value)
                                {
                                        echo '<td>';
                                            echo $value;
                                        echo '</td>';
                                        echo "\n";
                                }
                                echo '</tr>';
                                echo "\n";
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
                        if(count($employers) > 0 && count($jobApplicationTemplates) > 0)
                        {
                            echo '<tr>';
                            echo "\n";
                            foreach($jobApplicationTemplates[0] as $key => $value)
                            {
                                echo "<td>$key</td>";
                                echo "\n";
                            }
                            echo '</tr>';
                            echo "\n";
                            foreach($jobApplicationTemplates as $jobApplicationTemplate)
                            {
                                echo '<tr onClick="selectTemplateRowIndex(this)">';
                                echo "\n";
                                foreach($jobApplicationTemplate as $key => $value)
                                {
                                        echo '<td>';
                                            echo $value;
                                        echo '</td>';
                                        echo "\n";
                                }
                                echo '</tr>';
                                echo "\n";
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
                    <h1><a name="sentApplications">Abgeschickte Bewerbungen</a></h1>
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
                            echo "\n";
                            foreach($sentApplications[0] as $key => $value)
                            {
                                echo "<td>$key</td>";
                                echo "\n";
                            }
                            echo '</tr>';
                            echo "\n";
                            foreach($sentApplications as $currentApplication)
                            {
                                echo '<tr>';
                                echo "\n";
                                foreach($currentApplication as $key => $value)
                                {
                                        echo '<td>';
                                            echo $value;
                                        echo '</td>';
                                        echo "\n";
                                }
                                echo '</tr>';
                                echo "\n";
                            }
                        }
                    ?>
                    </table>
                    <form action="" method="post" enctype="multipart/form-data">
                        <table>
                            <tr>
                                <td>Von Datum:</td>
                                <td><input type="date" value="<?php 
                                    $firstOfMonth = strtotime("-" . (date('d') - 1) . " days", time());
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











        </div><!-- /.col-xs-12 main -->
    </div><!--/.row-->
  </div><!--/.container-->
</div><!--/.page-container-->








</body>
<script>
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
        alert("hallo!");
        if(el.value != "hallo")
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

</html>
