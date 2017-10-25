<?php
    use iio\libmergepdf\Merger;
    use iio\libmergepdf\Pages;


    require_once('../app/odtFunctions.php');
    require_once('../app/websiteFunctions.php');
    require_once('../app/dbFunctions.php');
    require_once('../app/helperFunctions.php');

    session_start();
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);



$kernel->terminate($request, $response);





    $dbConn = new PDO('mysql:host=localhost;dbname=jobApplication', 'root', 'Steinmetzstr9!@#$');
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
        updateUserValues($dbConn, $_SESSION['user']['id'], $_POST['txtFirstName'], $_POST['txtLastName'], $_POST['txtSalutation'], $_POST['txtTitle'], $_POST['txtStreet'], $_POST['txtPostCode'], $_POST['txtCity'], $_POST['txtEmail'], $_POST['txtMobilePhone'], $_POST['txtPhone']);
    }
    else if(isset($_POST['sbmAddEmployer']))
    {
        addEmployer($dbConn, $_SESSION['user']['id'], $_POST['txtCompany'], $_POST['txtStreet'], $_POST['txtPostCode'], $_POST['txtCity'], $_POST['txtSalutation'], $_POST['txtTitle'], $_POST['txtFirstName'], $_POST['txtLastName'], $_POST['txtEmail'], $_POST['txtMobilePhone'], $_POST['txtPhone']);
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
        $baseDir = "c:/uniserverz/user/" . $_SESSION['user']['userName'] . '/';
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
    else if(isset($_POST['sbmSendJobApplication']) || isset($_POST['sbmSendTestJobApplication']))
    {
        $employerIndex = getEmployerIndex($dbConn, $_SESSION['user']['id'], $_POST['hidEmployerIndex']);
        $employerValuesDict = getEmployer($dbConn, $_SESSION['user']['id'], $employerIndex);
        $userValuesDict =
            [ '$meinTitel' => $_SESSION['user']['title']
            , '$meineAnrede' => $_SESSION['user']['salutation']
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
            , replaceAllInString($jobApplicationTemplate['emailSubject'], $dict)
            , replaceAllInString($jobApplicationTemplate['emailBody'], $dict)
            , isset($_POST['sbmSendJobApplication']) ? $employerValuesDict['$firmaEmail'] : $_SESSION['user']['email']
            , [$pdfFileName]);
        echo "DONE!";
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
            $credentials = explode("\r\n", file_get_contents('mailCredentials.txt'));
            $email->Username = $credentials[0];
            $email->Password = $credentials[1];
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
<html>
<head>
<style>
.selectableTable {
    border-collapse: collapse;
    width: 100%;
}

th, td {
    text-align: left;
    padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2}



.tabs {
  position: relative;   
  min-height: 1200px; // This part sucks
  clear: both;
  margin: 70px 70px;
}
.tab {
  float: left;
}
.tab label {
  background: #eee; 
  padding: 10px; 
  border: 1px solid #ccc; 
  margin-left: -1px; 
  position: relative;
  left: 1px; 
}
.tab [type=radio] {
  display: none;   
}
.content {
  position: absolute;
  top: 28px;
  left: 0;
  background: white;
  right: 0;
  bottom: 0;
  padding: 20px;
  border: 1px solid #ccc; 
}
[type=radio]:checked ~ label {
  background: white;
  border-bottom: 1px solid white;
  z-index: 2;
}
[type=radio]:checked ~ label ~ .content {
  z-index: 1;
}
</style>
<title>Meine Bewerbung</title>
<style>
div
{
    margin-bottom:40px;
}
</style>
</head>
<body>
<?php
?>

<!-- create loginDiv

-->
<?php
    if(isset($_SESSION['user']) && isset($_SESSION['user']['id']) && $_SESSION['user']['id'] >= 1)
    {
?>
        <div id="loggedInDiv" style="position:absolute;top:20;right:20;">
            Eingeloggt als
<?php
        echo $_SESSION['user']['name'];
?>
        <br />
        <form action="" method="post"><input type="submit" value="Ausloggen" name="sbmLogout" /></form>
        </div>
<?php
    }
    else if(!isset($_POST['sbmShowRegisterForm']))
    {
?>
        <div id="loginForm">
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
            Neu?<form action="" method="post"><input type="submit" value="Registrieren" name="sbmShowRegisterForm" /></form>
        </div>
<?php
    } else if(isset($_POST['sbmShowRegisterForm']))
    {
?>
        <div id="registerForm">
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
?>

<?php
    }
?>



<!-- uploadApplicationTemplate()

-->
<div class="tabs">
    <div id="uploadJobApplicationTemplateDiv" class="tab">
        <input type="radio" id="tab-1" name="tab-group-1" checked>
        <label for="tab-1">Bewerbungsvorlage hochladen</label>
        <div class="content">
            Bewerbungsvorlage hochladen
            <form action="" method="post" enctype="multipart/form-data">
            <table>
                <tr>
                    <td>Name der Vorlage</td>
                    <td><input type="text" name="txtJobApplicationTemplateName" /></td>
                </tr>
                <tr>
                    <td>Bewerbung als</td>
                    <td><input type="text" name="txtUserAppliesAs" />
                </tr>
                <tr>
                    <td>Email-Betreff</td>
                    <td><input type="text" name="txtEmailSubject" />
                </tr>
                <tr>
                    <td>Email-Body</td>
                    <td><textarea name="txtEmailBody" cols="100" rows="15"></textarea>
                </tr>
                <tr>
                    <td>Vorlage (*.odt oder *.docx)</td>
                    <td><input type="file" name="fileODT" id="fileODT" /></td>
                </tr>
                <tr>
                    <td>PDF Anhang</td>
                    <td><input type="file" name="fileAppendices[]" value="PDF Anhang" /></td>
                </tr>
                <tr>
                    <td>PDF Anhang</td>
                    <td><input type="file" name="fileAppendices[]" value="PDF Anhang" /></td>
                </tr>
                <tr>
                    <td>PDF Anhang</td>
                    <td><input type="file" name="fileAppendices[]" value="PDF Anhang" /></td>
                </tr>
                <tr>
                    <td>PDF Anhang</td>
                    <td><input type="file" name="fileAppendices[]" value="PDF Anhang" /></td>
                </tr>
                <tr>
                    <td>PDF Anhang</td>
                    <td><input type="file" name="fileAppendices[]" value="PDF Anhang" /></td>
                </tr>
                <tr>
                    <td>PDF Anhang</td>
                    <td><input type="file" name="fileAppendices[]" value="PDF Anhang" /></td>
                </tr>
                <tr>
                    <td>PDF Anhang</td>
                    <td><input type="file" name="fileAppendices[]" value="PDF Anhang" /></td>
                </tr>
                <tr>
                    <td>PDF Anhang</td>
                    <td><input type="file" name="fileAppendices[]" value="PDF Anhang" /></td>
                </tr>
                <tr>
                    <td>PDF Anhang</td>
                    <td><input type="file" name="fileAppendices[]" value="PDF Anhang" /></td>
                </tr>
                <tr>
                    <td>PDF Anhang</td>
                    <td><input type="file" name="fileAppendices[]" value="PDF Anhang" /></td>
                </tr>
                <tr>
                    <td><input type="submit" name="sbmUploadJobApplicationTemplate" value="Vorlage hochladen" /></td>
                </tr>
            </table>
            </form>
        </div>
    </div>

    <!-- setUserValues()
    -->
    <div id="setUserValuesDiv" class="tab">
    <input type="radio" id="tab-2" name="tab-group-1" <?php if(isset($_POST['sbmSetUserValues'])) { echo "checked"; }?>>
        <label for="tab-2">Benutzer bearbeiten</label>
        <div class="content">
            <form action="" method="post">
            <table>
                <tr>
                    <td>Anrede</td>
                    <td><input type="text" name="txtSalutation" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['salutation'])) echo $_SESSION['user']['salutation']; ?>" /></td>
                </tr>
                <tr>
                    <td>Titel</td>
                    <td><input type="text" name="txtTitle" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['title'])) echo $_SESSION['user']['title']; ?>" /></td>
                </tr>
                <tr>
                    <td>Vorname</td>
                    <td><input type="text" name="txtFirstName" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['firstName'])) echo $_SESSION['user']['firstName']; ?>" /></td>
                </tr>
                <tr>
                    <td>Nachname</td>
                    <td><input type="text" name="txtLastName" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['lastName'])) echo $_SESSION['user']['lastName']; ?>" /></td>
                </tr>
                <tr>
                    <td>Stra&szlig;e</td>
                    <td><input type="text" name="txtStreet" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['street'])) echo $_SESSION['user']['street']; ?>" /></td>
                </tr>
                <tr>
                    <td>Postleitzahl</td>
                    <td><input type="text" name="txtPostCode" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['postCode'])) echo $_SESSION['user']['postCode']; ?>" /></td>
                </tr>
                <tr>
                    <td>Stadt</td>
                    <td><input type="text" name="txtCity" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['city'])) echo $_SESSION['user']['city']; ?>" /></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><input type="text" name="txtEmail" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['email'])) echo $_SESSION['user']['email']; ?>" /></td>
                </tr>
                <tr>
                    <td>Telefon mobil</td>
                    <td><input type="text" name="txtMobilePhone" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['mobilePhone'])) echo $_SESSION['user']['mobilePhone']; ?>" /></td>
                </tr>
                <tr>
                    <td>Telefon fest</td>
                    <td><input type="text" name="txtPhone" value="<?php if(isset($_SESSION['user']) && isset($_SESSION['user']['phone'])) echo $_SESSION['user']['phone']; ?>" /></td>
                </tr>
                <tr>
                    <td><input type="submit" name="sbmSetUserValues" value="Deine Werte &auml;ndern"/></td>
                </tr>
            </table>
            </form>
        </div>
    </div>



<!-- addEmployer()

-->
    <div id="addEmployerDiv" class="tab">
        <input type="radio" id="tab-3" name="tab-group-1" <?php if(isset($_POST['sbmReadEmployerValuesFromWebSite']) || isset($_POST['sbmAddEmployer'])) { echo "checked"; } ?>>
        <label for="tab-3">Arbeitgeber hinzuf&uuml;gen</label>
        <div class="content">
            <form action="" method="post">
            <table>
            <tr>
                <td><input type="text" name="txtReadEmployerValuesFromWebSite" /></td>
            </tr>
            <tr>
                <td><input type="submit" name="sbmReadEmployerValuesFromWebSite" value="Werte von Website einlesen" /></td>
            </tr>
            </table>
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
                <tr>
                    <td>Firma</td>
                    <td><input type="text" name="txtCompany" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaName']; ?>" /></td>
                </tr>
                <tr>
                    <td>Stra&szlig;e</td>
                    <td><input type="text" name="txtStreet" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaStrasse']; ?>"/></td>
                </tr>
                <tr>
                    <td>Postleitzahl</td>
                    <td><input type="text" name="txtPostCode" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaPlz']; ?>"/></td>
                </tr>
                <tr>
                    <td>Stadt</td>
                    <td><input type="text" name="txtCity" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaStadt']; ?>"/></td>
                </tr>
                <tr>
                    <td>Chef-Anrede</td>
                    <td><input type="text" name="txtSalutation" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$chefAnrede']; ?>"/></td>
                </tr>
                <tr>
                    <td>Chef-Titel</td>
                    <td><input type="text" name="txtTitle" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$chefTitel']; ?>"/></td>
                </tr>
                <tr>
                    <td>Chef-Vorname</td>
                    <td><input type="text" name="txtFirstName" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$chefVorname']; ?>"/></td>
                </tr>
                <tr>
                    <td>Chef-Nachname</td>
                    <td><input type="text" name="txtLastName" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$chefNachname']; ?>"/></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><input type="text" name="txtEmail" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaEmail']; ?>"/></td>
                </tr>
                <tr>
                    <td>Telefon mobil</td>
                    <td><input type="text" name="txtMobilePhone" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaMobil']; ?>"/></td>
                </tr>
                <tr>
                    <td>Telefon fest</td>
                    <td><input type="text" name="txtPhone" value="<?php if(isset($_POST['sbmReadEmployerValuesFromWebSite'])) echo $currentEmployer['$firmaTelefon']; ?>"/></td>
                </tr>
                <tr>
                    <td><input type="submit" name="sbmAddEmployer" value="Arbeitgeber hinzuf&uuml;gen" /></td>
                    <td></td>
                </tr>
            </table>
            </form>
        </div>
    </div>




    <div id="applyNowDiv" class="tab">
        <input type="radio" id="tab-4" name="tab-group-1" <?php if(isset($_POST['sbmSendJobApplication'])) { echo "checked"; }?>>
        <label for="tab-4">Jetzt bewerben</label>
        <div class="content">
            <table id="selectEmployerTable" class="selectableTable">
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
                    <td><input type="submit" name="sbmSendJobApplication" value="Bewerbung abschicken" /><td>
                <tr>
                    <td><input type="submit" name="sbmSendTestJobApplication" value="Bewerbung zum Testen an mich selbst schicken" /></td>
                </tr>
            </table>
        </form>
        </div>
    </div>

<!-- Sent applications

-->
    <div id="sentApplicationsDiv" class="tab">
        <input type="radio" id="tab-5" name="tab-group-1" <?php if(isset($_POST['sbmDownloadSentApplications'])) { echo "checked"; }?>>
        <label for="tab-5">Abgeschickte Bewerbungen</label>
        <div class="content">
            <table>
            <?php
                $sentApplications = [];
                if(isset($_SESSION['user']) && isset($_SESSION['user']['id']))
                {
                    getJobApplications($dbConn, $_SESSION['user']['id'], 0, 0); //TODO Fix parameters
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
    </div>
<!--
<form action="" method="post">
<input type="submit" name="sbmDownloadPDF" value="PDF downloaden" />
</form>

-->


<!-- sentApplications
-->

<!--
<div id="sentApplications">
<table>
<php
    if(isset($_SESSION['user']['id']))
    {
        $sentApplications = getJobApplications($dbConn, $_SESSION['user']['id'], 0, 0);
        foreach($sentApplications as $application)
        {
            echo "<tr>";
                echo "<td>";
                    echo $application['date'];
                echo "</td>";
                echo "<td>";
                    echo $application['companyName'];
                echo "</td>";
            echo "</tr>";
        }
    }
?>
</table>
</div>
-->
</div>


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

</script>
</html>
















