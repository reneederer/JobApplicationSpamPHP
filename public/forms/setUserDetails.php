<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    session_start();
    require_once('/var/www/html/jobApplicationSpam/src/validate.php');
    require_once('/var/www/html/jobApplicationSpam/src/dbFunctions.php');
    require_once('/var/www/html/jobApplicationSpam/src/useCase.php');
    require_once('/var/www/html/jobApplicationSpam/src/config.php');

    $setUserDetailsMsg = 'Deine Daten wurden geändert!';
    if(isset($_POST['userDetails']) && isset($_POST['userDetails']['lastName']))
    {
        $dbConn = new PDO('mysql:host=localhost;dbname=' . getConfig()['database']['database'], getConfig()['database']['username'], getConfig()['database']['password']);
        $taskResult = ucSetUserDetails($dbConn, $_SESSION['userId'], $_POST['userDetails']);
        if($taskResult->isValid)
        {
            $setUserDetailsMsg = 'Deine Daten wurden geändert!';
        }
        else
        {
            $setUserDetailsMsg = 'Entschuldigung, die Anfrage konnte nicht verarbeitet werden.';
        }
    }
?>

<div>
    <h1>Deine Werte ändern</h1>
    <form onSubmit="submitForm('forms/setUserDetails.php', this);return false;">
        <fieldset>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Geschlecht</label>
                </div>
                <div class="input-group">
                    <input type="hidden" name="rbUserGender" value="x" />
                    <input id="rbUserMale" type="radio" name="userDetails[gender]" value="m" <?php if(isset($_POST['rbUserGender']) && $_POST['rbUserGender'] === 'm') echo 'checked="checked"'; ?> /><label for="rbUserMale">Männlich</label>
                    <input id="rbUserFemale" type="radio" name="userDetails[gender]" value="f" <?php if(isset($_POST['rbUserGender']) && $_POST['rbUserGender'] === 'f') echo 'checked="checked"'; ?> /><label for="rbUserFemale">Weiblich</label>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="userDetailsDegree">Titel</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input id="userDetailsDegree" type="text" name="userDetails[degree]" value="<?php if(isset($_POST['userDetails'])) echo htmlspecialchars($_POST['userDetails']['degree']) ?? ''; ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Vorname</label>
                </div>
                <div class="col-sm-12 col-md">
                    <input type="text" name="userDetails[firstName]" value="<?php if(isset($_POST['userDetails'])) echo htmlspecialchars($_POST['userDetails']['firstName']) ?? ''; ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Nachname</label>
                </div>
                <div>
                    <input type="text" name="userDetails[lastName]" value="<?php if(isset($_POST['userDetails'])) echo htmlspecialchars($_POST['userDetails']['lastName']) ?? ''; ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Stra&szlig;e</label>
                </div>
                <div>
                    <input type="text" name="userDetails[street]" value="<?php if(isset($_POST['userDetails'])) echo htmlspecialchars($_POST['userDetails']['street']) ?? ''; ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Postleitzahl</label>
                </div>
                <div>
                    <input type="text" name="userDetails[postcode]" value="<?php if(isset($_POST['userDetails'])) echo htmlspecialchars($_POST['userDetails']['postcode']) ?? ''; ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Stadt</label>
                </div>
                <div>
                    <input type="text" name="userDetails[city]" value="<?php if(isset($_POST['userDetails'])) echo htmlspecialchars($_POST['userDetails']['city']) ?? ''; ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Email</label>
                </div>
                <div>
                    <input type="text" name="userDetails[email]" value="<?php if(isset($_POST['userDetails'])) echo htmlspecialchars($_POST['userDetails']['email']) ?? ''; ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Telefon mobil</label>
                </div>
                <div>
                    <input type="text" name="userDetails[mobilePhone]" value="<?php if(isset($_POST['userDetails'])) echo htmlspecialchars($_POST['userDetails']['mobilePhone']) ?? ''; ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Telefon fest</label>
                </div>
                <div>
                    <input type="text" name="userDetails[phone]" value="<?php if(isset($_POST['userDetails'])) echo htmlspecialchars($_POST['userDetails']['phone']) ?? ''; ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Geburtstag</label>
                </div>
                <div>
                    <input type="text" name="userDetails[birthday]" value="<?php if(isset($_POST['userDetails'])) echo htmlspecialchars($_POST['userDetails']['birthday']) ?? ''; ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Geburtsort</label>
                </div>
                <div>
                    <input type="text" name="userDetails[birthplace]" value="<?php if(isset($_POST['userDetails'])) echo htmlspecialchars($_POST['userDetails']['birthplace']) ?? ''; ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Familienstand</label>
                </div>
                <div>
                    <input type="text" name="userDetails[maritalStatus]" value="<?php if(isset($_POST['userDetails'])) echo htmlspecialchars($_POST['userDetails']['maritalStatus']) ?? ''; ?>" />
                </div>
            </div>
            <input type="submit" name="sbmSetUserDetails" value="Deine Werte &auml;ndern"/>
            <label><?php echo htmlspecialchars($setUserDetailsMsg) ?? ''; ?></label>
        </fieldset>
    </form>
</div>
