<?php
    session_start();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require_once('/var/www/html/jobApplicationSpam/src/validate.php');
    require_once('/var/www/html/jobApplicationSpam/src/dbFunctions.php');
    require_once('/var/www/html/jobApplicationSpam/src/useCase.php');
    require_once('/var/www/html/jobApplicationSpam/src/config.php');

    $uploadTemplateMsg = '';
    if(isset($_POST['template']) && isset($_POST['template']['name']))
    {
        $dbConn = new PDO('mysql:host=localhost;dbname=' . getConfig()['database']['database'], getConfig()['database']['username'], getConfig()['database']['password']);
        $taskResult = ucUploadJobApplicationTemplate($dbConn, $_SESSION['userId'], $_POST['template'], $_FILES['fileOdt'], $_FILES['filePdfs']);
        if($taskResult->isValid)
        {
            $uploadTemplateMsg = 'Bewerbungsvorlage wurde hinzugefügt!';
        }
        else
        {
            $uploadTemplateMsg = 'Entschuldigung, die Anfrage konnte nicht verarbeitet werden.';
        }
    }
?>
 <div>
    <h1>Bewerbungsvorlage hochladen</h1>
    <form id="formUploadTemplate" onSubmit="submitForm('forms/uploadTemplate.php', this);return false;" enctype="multipart/form-data">
        <fieldset>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtJobApplicationTemplateName">Name der Vorlage</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" class="form-control has-error" onInput="validateInput(this)" id="txtJobApplicationTemplateName" name="template[name]" value="<?php if(isset($_POST['template'])) echo htmlspecialchars($_POST['template']['name']) ?? ''; ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="template[userAppliesAs]">Bewerbung als</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" class="form-control" id="template[userAppliesAs]" name="template[userAppliesAs]" value="<?php if(isset($_POST['template'])) echo htmlspecialchars($_POST['template']['userAppliesAs']) ?? ''; ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtTemplateEmailSubject">Email-Betreff</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" class="form-control" id="txtTemplateEmailSubject" name="template[emailSubject]" value="<?php if(isset($_POST['template'])) echo htmlspecialchars($_POST['template']['emailSubject']) ?? ''; ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtTemplateEmailBody">Email-Body</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <textarea name="template[emailBody]" class="form-control" id="txtTemplateEmailBody" cols="100" rows="15"><?php if(isset($_POST['template'])) echo htmlspecialchars($_POST['template']['emailBody']) ?? ''; ?></textarea>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="fileOdt">Vorlage</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="file" name="fileOdt" id="fileOdt" accept=".odt,.docx" onChange="document.querySelector('#lblFileOdt').innerHTML=this.value" />
                    <label id="lblFileOdt" role="button" for="fileOdt" style="min-width:100%">*.odt oder *.docx</label>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="filePdf1">Anhang</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="file" id="filePdf1" name="filePdfs[]" accept=".pdf" onChange="templatePdfSelected(1);" />
                    <label id="lblFilePdf1" for="filePdf1" role="button" style="min-width:100%">*.pdf</label>
                </div>
            </div>
            <div class="row">
                <input type="submit" name="sbmUploadJobApplicationTemplate" value="Vorlage hochladen" />
                <label><?php echo htmlspecialchars($uploadTemplateMsg) ?? ''; ?></label>
            </div>
        </fieldset>
     </form>
 </div>










