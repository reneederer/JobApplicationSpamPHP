<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('/var/www/html/jobApplicationSpam/src/config.php');
require_once('/var/www/html/jobApplicationSpam/src/useCase.php');
require_once('/var/www/html/jobApplicationSpam/src/odtFunctions.php');
require_once('/var/www/html/jobApplicationSpam/src/websiteFunctions.php');
require_once('/var/www/html/jobApplicationSpam/src/dbFunctions.php');
require_once('/var/www/html/jobApplicationSpam/src/helperFunctions.php');
require_once('/var/www/html/jobApplicationSpam/src/validate.php');

if(!isset($_SESSION)) { session_start(); }

$dbConn = new PDO('mysql:host=localhost;dbname=' . getConfig()['database']['database'], getConfig()['database']['username'], getConfig()['database']['password']);
$dbConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbConn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


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
    session_unset();
}
else if(isset($_POST['sbmRegisterForm']))
    ucRegisterNewUser($dbConn, $_POST['txtRegisterEmail'], $_POST['txtRegisterPassword'], $_POST['txtRegisterPassworRepeated'], $sendMail);
else if(isset($_POST['sbmSetUserDetails']))
    ucSetUserDetails($dbConn, $_SESSION['userId'], $_POST['userDetails']);
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
else if(isset($_POST['sbmApplyNowForReal']) || isset($_POST['sbmApplyNowForTest']))
{
    ucApplyNow($dbConn, $_SESSION['userId'], $_POST['hidEmployerIndex'], $_POST['hidTemplateIndex'], true);
}
else if(isset($_POST['sbmDownloadSentApplications']))
{
    $jobApplications = getJobApplicationsForPrint($dbConn, $_SESSION['userId'], 0, 0);
    if(count($jobApplications) <= 0)
    {
        die('errrrrrrrrrrrrrr');
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
            if($key == 'status')
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

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>www.bewerbungsspam.de</title>


    <link rel="stylesheet" href="css/current.css" />
    <link rel="stylesheet" href="https://gitcdn.link/repo/Chalarangelo/mini.css/master/dist/mini-default.min.css" />
    <style>
        .responsive-label {align-items: center;}
    </style>
<script>
function templatePdfSelected(pdfFormIndex)
{
    document.querySelector("#lblFilePdf" + (+pdfFormIndex)).innerHTML = document.querySelector("#filePdf" + (+pdfFormIndex)).value;
    lastDiv = document.querySelector("#formUploadTemplate div:last-child");
    filePdfs = document.querySelectorAll("#formUploadTemplate [name = 'filePdfs[]']");
    if(filePdfs.length == pdfFormIndex)
    {
        lastFilePdfDiv = filePdfs[filePdfs.length - 1].parentNode.parentNode;
        label1 = document.createElement("label");
        label1.setAttribute("for", "filePdf" + (+pdfFormIndex + 1));
        label1.appendChild(document.createTextNode("Pdf Anhang"));
        divInner1 = document.createElement("div");
        divInner1.setAttribute("class", "col-sm-12 col-md-1");
        divInner1.setAttribute("style", "min-width:130px");
        divInner1.appendChild(label1);


        fileInput = document.createElement("input");
        fileInput.setAttribute("type", "file");
        fileInput.setAttribute("name", "filePdfs[]");
        fileInput.setAttribute("value", "PDF Anhang");
        fileInput.setAttribute("onChange", "templatePdfSelected(" + (+pdfFormIndex + 1) + ");");;
        fileInput.setAttribute("accept", ".pdf");
        fileInput.setAttribute("id", "filePdf" + (+pdfFormIndex + 1));
        label2 = document.createElement("label");
        label2.appendChild(document.createTextNode("*.pdf"));
        label2.setAttribute("id", "lblFilePdf" + (+pdfFormIndex + 1));
        label2.setAttribute("role", "button");
        label2.setAttribute("for", "filePdf" + (+pdfFormIndex + 1));
        label2.setAttribute("min-width", "100%");
        divInner2 = document.createElement("div");
        divInner2.setAttribute("class", "col-sm-12 col-md-1");
        divInner2.appendChild(fileInput);
        divInner2.appendChild(label2);
        divOuter = document.createElement("div");
        divOuter.setAttribute("class", "row responsive-label");
        divOuter.appendChild(divInner1);
        divOuter.appendChild(divInner2);
        lastFilePdfDiv.insertAdjacentElement("afterend", divOuter);
    }
}
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


function submitForm(url, form)
{
    var request = new XMLHttpRequest();
    request.open("POST", url, true);
    request.onreadystatechange = function() {
        if (this.readyState == 4)
        {
            document.body.style.cursor = "pointer";
            if(this.status === 200)
            {
                document.querySelector("#mainDiv").innerHTML = this.responseText;
            }
        }
        else if(this.readyState == 4)
        {
            document.body.style.cursor = "pointer";
        }

    };
    if(form == null)
    {
        request.send();
    }
    else
    {
        document.body.style.cursor = "wait";
        request.send(new FormData(form));
    }
}
function closeWindowOnEscape(el, ev)
{
    if(ev.keyCode == 27)
    {
        el.checked = false;
    }
}

</script>
</head>
<body>



<header class="sticky" id="header-toggle">
    <label class="drawer-toggle button" style="position:sticky" for="navigation-toggle"></label>
    <a href="" class="logo">www.bewerbungsspam.de</span></a>
</header>
<div class="container" style="padding-left: 0.25rem;">
    <div class="row"> <input type="checkbox" id="navigation-toggle">
        <nav class="sticky drawer col-md-4 col-lg-2" style="" id="real-drawer">
            <label class="close" for="navigation-toggle"></label>
            <a href="" onClick="submitForm('forms/uploadTemplate.php', null);return false;">Bewerbungsvorlage hochladen</a>
            <a href="" onClick="submitForm('forms/setUserDetails.php', null);return false;">Deine Werte ändern</a>
            <a href="" onClick="submitForm('forms/addEmployer.php', null); return false">Arbeitgeber hinzufügen</a>
            <a href="" onClick="submitForm('forms/applyNow.php', null);return false">Jetzt bewerben</a></h3>
            <a href="">Abgeschickte Bewerbungen anzeigen</a>
            <a href="">Termine</a>
        </nav>
        <div class="col-sm-12 col-md-8 col-lg-10">
            <main>
                <div class="row" style="padding-top: 40px;" id="navigation-title">
                    <div id="mainDiv" class="col-sm-12">
                        <label for="loginDialog" role="button">Einloggen</label>
                        <input id="loginDialog" type="checkbox" onChange="document.querySelector('#loginEmail').focus();"/>
                        <div class="modal" tabIndex="1000" onKeyDown="closeWindowOnEscape(document.querySelector('#loginDialog'), event);" />
                            <div class="card" tabIndex="1001" onKeyDown="closeWindowOnEscape(document.querySelector('#loginDialog'), event);" />
                                <label for="loginDialog" class="close"></label>
                                <h3 class="section">Login</h3>
                                <form onSubmit="document.querySelector('#loginDialog').checked=false;submitForm('forms/login.php', this);return false;">
                                    <label for="loginEmail">Email</label>
                                    <input type="text" id="loginEmail" name="loginEmail" onKeyDown="closeWindowOnEscape(document.querySelector('#loginDialog'), event);" />
                                    <label for="loginPassword">Password</label>
                                    <input type="password" id="loginPassword"  name="loginPassword" onKeyDown="closeWindowOnEscape(document.querySelector('#loginDialog'), event);" />
                                    <input type="submit" value="Anmelden" />
                                </form>
                            </div>
                        </div>



                        <label for="registerDialog" role="button">Registrieren</label>
                        <input id="registerDialog" type="checkbox" onChange="document.querySelector('#registerEmail').focus();"/>
                        <div class="modal">
                            <div class="card">
                                <label for="registerDialog" class="close"></label>
                                <h3 class="section">Register</h3>
                                <form onSubmit="document.querySelector('#registerDialog').checked=false;submitForm('forms/registerNewUser.php', this);return false;">
                                    <label for="registerEmail">Email</label>
                                    <input type="text" id="registerEmail" name="registerEmail" onKeyDown="closeWindowOnEscape(document.querySelector('#registerDialog'), event);" />
                                    <label for="registerPassword">Passwort</label>
                                    <input type="password" id="registerPasswort" name="registerPassword" onKeyDown="closeWindowOnEscape(document.querySelector('#registerDialog'), event);" />
                                    <label for="registerPasswordRepeated">Passwort wiederholen</label>
                                    <input type="password" id="registerPasswordRepeated" name="registerPasswordRepeated" onKeyDown="closeWindowOnEscape(document.querySelector('#registerDialog'), event);" />
                                    <input type="submit" value="Registrieren" />
                                </form>
                            </div>
                        </div>
<?php
if(isset($_SESSION['userId']) && $_SESSION['userId'] >= 1)
{
?>
                    <div id="loggedInDiv">
                    Eingeloggt als <?php
    $email = getEmailByUserId($dbConn, $_SESSION['userId']);
    echo htmlspecialchars($email); ?>
                        <br />
                        <form action="" method="post"><input type="submit" value="Ausloggen" name="sbmLogout" /></form>
                    </div>

<?php
}
?>
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
    </main>
</div>
</div>
</div>

</body>

</html>
