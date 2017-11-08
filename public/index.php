<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once('../src/config.php');
    require_once('../src/useCase.php');
    require_once('../src/odtFunctions.php');
    require_once('../src/websiteFunctions.php');
    require_once('../src/dbFunctions.php');
    require_once('../src/helperFunctions.php');
    require_once('../src/validate.php');

    session_start();

    $currentMessage = '';

    $dbConn = new PDO('mysql:host=localhost;dbname=' . $config['database']['database'], $config['database']['username'], $config['database']['password']);
    $dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbConn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbConn->exec("SET NAMES utf8");


    if(isset($_GET['email']) && isset($_GET['confirmationString']))
    {
        ucConfirmEmailAddress($dbConn, $_GET['email'], $_GET['confirmationString']);
    }
    if(isset($_POST['sbmLoginForm']))
    {
        $_SESSION['userId'] = ucLogin($dbConn, $_POST['txtLoginEmail'], $_POST['txtLoginPassword']);
    }
    else if(isset($_POST['sbmLogout']))
    {
        $_SESSION['userId'] = ucLogout();
    }
    else if(isset($_POST['sbmRegisterForm']))
        ucRegisterNewUser($dbConn, $_POST['txtRegisterEmail'], $_POST['txtRegisterPassword'], $_POST['txtRegisterPassworRepeated'], $sendMail);
    else if(isset($_POST['sbmSetUserDetails']))
        ucSetUserDetails($dbConn, $_SESSION['userId'], $_POST['userDetails']);
    else if(isset($_POST['sbmAddEmployer']))
        ucAddEmployer($dbConn, $_SESSION['userId'], $_POST['employer']);
    else if(isset($_POST['sbmDownloadPDF']))
    {
        //TODO FIX or remove this!
        //$dict = readEmployerFromWebsite('http://localhost/jobApplicationSpam/jobboerseArbeitsagentur.html');
        //$directoryAndFileName = getPDF($directory, $odtFile, $dict);
        //addToDownloads($dbConn, $directoryAndFileName[0], $_SESSION['userId']);
        //header('Content-type:application/pdf');
        //header("Content-Disposition:attachment;filename=jobApplication.pdf");
        //echo file_get_contents($directoryAndFileName[0] .  $directoryAndFileName[1]);
    }
    else if(isset($_POST['sbmUploadJobApplicationTemplate']))
    {
        $taskResult = ucUploadJobApplicationTemplate($dbConn, $_SESSION['userId'], $_POST['template'], $_FILES['templateFileOdt'], $_FILES['templateFilePdfs']);
        if($taskResult->isValid === false)
        {
            var_dump($taskResult->errors);
            die();
        }
    }
    else if(isset($_POST['sbmApplyNowForReal']) || isset($_POST['sbmApplyNowForTest']))
    {
        ucApplyNow($dbConn, $_SESSION['userId'], $_POST['hidEmployerIndex'], $_POST['hidTemplateIndex'], true);
    }
    else if(isset($_POST['sbmDownloadSentApplications']))
    {
        $jobApplications = getJobApplicationsForPrint($dbConn, $_SESSION['userId'], 0, 0);
        if(count($jobApplications) <= 0)
        {
            die("errrrrrrrrrrrrrr");
        }
        $html = '<table>';
        $html .= '<tr>';
        foreach($jobApplications[0] as $key => $value)
        {
            $html .= '<td style="margin-right:50px"">' . $key . '</td>';
        }
        $html .= '</tr>';
        foreach($jobApplications as $currentJobApplication)
        {
            $html .= '<tr>';
            foreach($currentJobApplication as $key => $value)
            {
                if($key == "status")
                {
                    $value = translateStatus($value);
                }
                $html .= "<td>$value</td>";
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        $folder = getNonExistingFileName('/var/www/userFiles/tmp/', '') . '/';
        $r = mkdir($folder, 0777, true);
        file_put_contents($folder . 'bewerbungen.html', $html);
        exec('unoconv ' . $folder . 'bewerbungen.html', $output);
        if(count($output) > 0)
        {
            var_dump($output);
            die();
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

    <title>Dein Bewerbungs-Spam!</title>


    <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/current.css">
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script type="text/javascript">
    selectedEmployerRowIndex = 0;
    lastEmployerBackgroundColor = "white";
    selectedTemplateRowIndex = 0;
    lastTemplateBackgroundColor = "white";
    function selectTemplateRowIndex(row, templateId)
    {
        document.getElementById("selectTemplateTable").getElementsByTagName("tr")[selectedTemplateRowIndex].style.backgroundColor = lastTemplateBackgroundColor;
        selectedTemplateRowIndex = row.rowIndex;
        document.getElementById('hidTemplateIndex').value = templateId;
        lastTemplateBackgroundColor = row.style.backgroundColor;
        row.style.backgroundColor = 'lightgreen';
    }
    function selectEmployerRowIndex(row, employerId)
    {
        document.getElementById("selectEmployerTable").getElementsByTagName("tr")[selectedEmployerRowIndex].style.backgroundColor = lastEmployerBackgroundColor;
        selectedEmployerRowIndex = row.rowIndex;
        document.getElementById('hidEmployerIndex').value = employerId;
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
                if(isset($_SESSION['userId']) && $_SESSION['userId'] >= 1)
                {
            ?>
                    <div id="loggedInDiv" style="position:absolute;top:0px;float:right;right:0px;">
                        Eingeloggt als <?php
    $email = getEmailByUserId($dbConn, $_SESSION['userId']);
    echo htmlspecialchars($email); ?>
                        <br />
                        <form action="" method="post"><input type="submit" value="Ausloggen" name="sbmLogout" /></form>
                    </div>
            <?php
                }
                else if((!isset($_SESSION['userId']) || $_SESSION['userId'] <= -1) && !isset($_POST['sbmShowRegisterForm']))
                {
            ?>
                    <div id="loginForm" class="page">
                        <form action="" method="post">
                            <table>
                                <tr>
                                    <td>Email:</td>
                                    <td><input type="text" value="" name="txtLoginEmail" /></td>
                                </tr>
                                <tr>
                                    <td>Password:</td>
                                    <td><input type="password" value="" name="txtLoginPassword" /></td>
                                </tr>
                                <tr>
                                    <td><input type="submit" name="sbmLoginForm" value="Einloggen" /></td>
                            </table>
                        </form>
                        <form action="" method="post"><input type="submit" value="Neu? Registrieren!" name="sbmShowRegisterForm" /></form>
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
                                    <td><input type="text" value="" name="txtRegisterEmail" /></td>
                                </tr>
                                <tr>
                                    <td>Password:</td>
                                    <td><input type="password" value="" name="txtRegisterPassword" /></td>
                                </tr>
                                <tr>
                                    <td>Password wiederholen:</td>
                                    <td><input type="password" value="" name="txtRegisterPassworRepeated" /></td>
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
                      <input type="text" class="form-control has-error" onInput="validateInput(this)" id="txtJobApplicationTemplateName" name="template[name]" value="<?php echo htmlspecialchars($_POST['template']['name'] ?? ''); ?>" />
                      <span class="help-block"></span>
                  </div>
                  <div class="form-group">
                      <label for="txtJobApplicationTemplateName">Bewerbung als</label>
                      <input type="text" class="form-control" id="txtUserAppliesAs" name="template[userAppliesAs]" value="<?php echo htmlspecialchars($_POST['template']['userAppliesAs'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtTemplateEmailSubject">Email-Betreff</label>
                      <input type="text" class="form-control" id="txtTemplateEmailSubject" name="template[emailSubject]" value="<?php echo htmlspecialchars($_POST['template']['emailSubject'] ?? ''); ?>" />
                  </div>
                      <label for="txtTemplateEmailBody">Email-Body</label>
                      <textarea name="template[emailBody]" class="form-control" id="txtTemplateEmailBody" cols="100" rows="15"><?php echo htmlspecialchars($_POST['template']['emailBody'] ?? ''); ?></textarea>
                  <div class="form-group">
                      <label for="fileOdt">Vorlage (*.odt oder *.docx)</label>
                      <input type="file" name="templateFileOdt" id="fileOdt" accept=".odt,.docx" />
                  </div>
                  <div class="form-group">
                      <label for="filePdf1">Pdf Anhang</label>
                      <input type="file" name="templateFilePdfs[]" id="filePdf1" Anhang" accept=".pdf" onChange="templateAppendixSelected(1);" />
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
                          <label class="radio-inline"><input type="radio" name="userDetails[gender]" value="m" <?php if(isset($_POST['rbUserGender']) && $_POST['rbUserGender'] === 'm') echo 'checked="checked"'; ?>/>M&auml;nnlich</label>
                          <label class="radio-inline"><input type="radio" name="userDetails[gender]" value="f" <?php if(isset($_POST['rbUserGender']) && $_POST['rbUserGender'] === 'f') echo 'checked="checked"'; ?>/>Weiblich</label>
                      </div>
                  </div>
                  <div class="form-group">
                      <label for="txtUserDegree">Titel</label>
                      <input class="form-control" type="text" name="userDetails[degree]" id="UserDegree" value="<?php echo htmlspecialchars($_POST['txtUserDegree'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserFirstName">Vorname</label>
                      <input class="form-control" type="text" name="userDetails[firstName]" id="txtUserFirstName" value="<?php echo htmlspecialchars($_POST['txtUserFirstName'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserLastName">Nachname</label>
                      <input class="form-control" type="text" name="userDetails[lastName]" id="txtUserLastName" value="<?php echo htmlspecialchars($_POST['txtUserLastName'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserStreet">Stra&szlig;e</label>
                      <input class="form-control" type="text" name="userDetails[street]" id="txtUserStreet" value="<?php echo htmlspecialchars($_POST['txtUserStreet'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserPostcode">Postleitzahl</label>
                      <input class="form-control" type="text" name="userDetails[postcode]" id="txtUserPostcode" value="<?php echo htmlspecialchars($_POST['txtUserPostcode'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserCity">Stadt</label>
                      <input class="form-control" type="text" name="userDetails[city]" id="txtUserCity" value="<?php echo htmlspecialchars($_POST['txtUserCity'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserEmail">Email</label>
                      <input class="form-control" type="text" name="userDetails[email]" id="txtUserEmail" value="<?php echo htmlspecialchars($_POST['txtUserEmail'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserMobilePhone">Telefon mobil</label>
                      <input class="form-control" type="text" name="userDetails[mobilePhone]" id="txtUserMobilePhone" value="<?php echo htmlspecialchars($_POST['txtUserMobilePhone'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserPhone">Telefon fest</label>
                      <input class="form-control" type="text" name="userDetails[phone]" id="txtUserPhone" value="<?php echo htmlspecialchars($_POST['txtUserPhone'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserBirthday">Geburtstag</label>
                      <input class="form-control" type="text" name="userDetails[birthday]" id="txtUserBirthday" value="<?php echo htmlspecialchars($_POST['txtUserBirthday'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserBirthplace">Geburtsort</label>
                      <input class="form-control" type="text" name="userDetails[birthplace]" id="txtUserBirthplace" value="<?php echo htmlspecialchars($_POST['txtUserBirthplace'] ?? ''); ?>" />
                  </div>
                  <div class="form-group">
                      <label for="txtUserMaritalStatus">Familienstand</label>
                      <input class="form-control" type="text" name="userDetails[maritalStatus]" id="txtUserMaritalStatus" value="<?php echo htmlspecialchars($_POST['txtUserMaritalStatus'] ?? ''); ?>" />
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
                            <input class="form-control" type="text" name="employer[company]" value="<?php echo htmlspecialchars($currentEmployer['$firmaName'] ?? ''); ?>" />
                            <label for="txtCompanyStreet">Stra&szlig;e</label>
                            <input class="form-control" type="text" name="employer[street]" value="<?php echo htmlspecialchars($currentEmployer['$firmaStrasse'] ?? ''); ?>"/>
                            <label for="txtCompanyPostcode">Postleitzahl</label>
                            <input class="form-control" type="text" name="employer[postcode]" value="<?php echo htmlspecialchars($currentEmployer['$firmaPlz'] ?? ''); ?>"/>
                            <label for="txtCompanyCity">Stadt</label>
                            <input class="form-control" type="text" name="employer[city]" value="<?php echo htmlspecialchars($currentEmployer['$firmaStadt'] ?? ''); ?>"/>
                            <div class="form-group">
                                <label>Chef-Geschlecht</label>
                                <div class="form-control">
                                    <input type="hidden" name="rbBossGender" value="x" />
                                    <label class="radio-inline"><input type="radio" name="employer[gender]" value="m" <?php if(isset($_POST['rbBossGender']) && $_POST['rbBossGender'] === 'm') echo 'checked="checked"'; ?>/>M&auml;nnlich</label>
                                    <label class="radio-inline"><input type="radio" name="employer[gender]" value="f" <?php if(isset($_POST['rbBossGender']) && $_POST['rbBossGender'] === 'f') echo 'checked="checked"'; ?>/>Weiblich</label>
                                </div>
                            </div>
                            <label for="txtBossDegree">Chef-Titel</label>
                            <input class="form-control" type="text" name="employer[degree]" value="<?php echo htmlspecialchars($currentEmployer['$chefTitel'] ?? ''); ?>"/>
                            <label for="txtBossFirstName">Chef-Vorname</label>
                            <input class="form-control" type="text" name="employer[firstName]" value="<?php echo htmlspecialchars($currentEmployer['$chefVorname'] ?? ''); ?>"/>
                            <label for="txtBossLastName">Chef-Nachname</label>
                            <input class="form-control" type="text" name="employer[lastName]" value="<?php echo htmlspecialchars($currentEmployer['$chefNachname'] ?? ''); ?>"/>
                            <label for="txtBossEmail">Email</label>
                            <input class="form-control" type="text" name="employer[email]" value="<?php echo htmlspecialchars($currentEmployer['$firmaEmail'] ?? ''); ?>"/>
                            <label for="txtBossMobilePhone">Telefon mobil</label>
                            <input class="form-control" type="text" name="employer[mobilePhone]" value="<?php echo htmlspecialchars($currentEmployer['$firmaMobil'] ?? ''); ?>"/>
                            <label for="txtBossPhone">Telefon fest</label>
                            <input class="form-control" type="text" name="employer[phone]" value="<?php echo htmlspecialchars($currentEmployer['$firmaTelefon'] ?? ''); ?>"/>
                            <input type="submit" name="sbmAddEmployer" value="Arbeitgeber hinzuf&uuml;gen" />
                </form>
            </div>








                <div id="divApplyNow" class="distanced-div">
                    <h1 id="applyNow" class="undecorated-anchor">Jetzt bewerben</h1>
                    <table id="selectEmployerTable" class="table table-hover table-border table-sm">
                    <?php
                        $employers = [];
                        if(isset($_SESSION['userId']))
                        {
                            $employers = getEmployers($dbConn, $_SESSION['userId']);
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
                                echo '<tr onClick="selectEmployerRowIndex(this, ' . htmlspecialchars($employer['id']) . ')">';
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
                        if(isset($_SESSION['userId']))
                        {
                            $jobApplicationTemplates = getJobApplicationTemplates($dbConn, $_SESSION['userId']);
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
                                echo '<tr onClick="selectTemplateRowIndex(this, ' . htmlspecialchars($jobApplicationTemplate['id']) . ')">';
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
                        if(isset($_SESSION['userId']))
                        {
                            $sentApplications = getJobApplications($dbConn, $_SESSION['userId'], 0, 0); //TODO Fix parameters
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
