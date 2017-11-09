<?php
    session_start();
    require_once('../src/validate.php');
    require_once('../src/dbFunctions.php');
    require_once('../src/useCase.php');
    require_once('../src/config.php');

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
     <form onSubmit="submitForm('uploadTemplate.php', this);return false;" enctype="multipart/form-data">
         <label for="txtJobApplicationTemplateName">Name der Vorlage</label>
         <input type="text" class="form-control has-error" onInput="validateInput(this)" id="txtJobApplicationTemplateName" name="template[name]" value="<?php echo htmlspecialchars($_POST['template']['name'] ?? ''); ?>" />
         <span class="help-block"></span>
         <label for="txtJobApplicationTemplateName">Bewerbung als</label>
         <input type="text" class="form-control" id="txtUserAppliesAs" name="template[userAppliesAs]" value="<?php echo htmlspecialchars($_POST['template']['userAppliesAs'] ?? ''); ?>" />
         <label for="txtTemplateEmailSubject">Email-Betreff</label>
         <input type="text" class="form-control" id="txtTemplateEmailSubject" name="template[emailSubject]" value="<?php echo htmlspecialchars($_POST['template']['emailSubject'] ?? ''); ?>" />
         <label for="txtTemplateEmailBody">Email-Body</label>
         <textarea name="template[emailBody]" class="form-control" id="txtTemplateEmailBody" cols="100" rows="15"><?php echo htmlspecialchars($_POST['template']['emailBody'] ?? ''); ?></textarea>
<br />
         <label for="fileOdt">Vorlage (*.odt oder *.docx)</label>
<br />
         <input type="file" name="fileOdt" id="fileOdt" onChange="alert('hallo');" accept=".odt,.docx" />
<br />
         <label for="filePdf1">Pdf Anhang</label>
<br />
         <input type="file" name="filePdfs[]" id="filePdf1" accept=".pdf" onChange="templateAppendixSelected(1);" />
<br />
         <input type="submit" name="sbmUploadJobApplicationTemplate" value="Vorlage hochladen" />
<br />
         <label><?php echo htmlspecialchars($uploadTemplateMsg) ?? ''; ?>
     </form>
 </div>
