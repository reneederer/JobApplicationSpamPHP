<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    if(!isset($_SESSION)) { session_start(); }
    require_once('/var/www/html/jobApplicationSpam/src/validate.php');
    require_once('/var/www/html/jobApplicationSpam/src/dbFunctions.php');
    require_once('/var/www/html/jobApplicationSpam/src/useCase.php');
    require_once('/var/www/html/jobApplicationSpam/src/config.php');

    if(!isset($_SESSION['userId']))
    {
        include('/var/www/html/jobApplicationSpam/public/forms/howto.php');
        die();
    }

    $dbConn = new PDO('mysql:host=localhost;dbname=' . getConfig()['database']['database'], getConfig()['database']['username'], getConfig()['database']['password']);
    $setUserDetailsMsg = '';
    if(isset($_POST['userDetails']) && isset($_POST['userDetails']['lastName']))
    {
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
<?php
    $userDetails = ['gender' => '',
        'degree' => '',
        'firstName' => '',
        'lastName' => '',
        'street' => '',
        'postcode' => '',
        'city' => '',
        'email' => '',
        'phone' => '',
        'mobilePhone' => '',
        'birthday' => '',
        'birthplace' => '',
        'maritalStatus' => ''];
    if(isset($_POST['userDetails']))
    {
        $userDetails = $_POST['userDetails'];
    }
    else if(($_SESSION['userId'] ?? -1) >= 1)
    {
            $userDetails = getUserDetails($dbConn, $_SESSION['userId']);
    }
?>
    <form onSubmit="submitForm('forms/setUserDetails.php', this);return false;">
        <fieldset>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Geschlecht</label>
                </div>
                <div class="input-group">
                    <input type="hidden" name="userDetails[gender]" value="" />
                    <input id="rbUserMale" type="radio" name="userDetails[gender]" value="m" <?php if($userDetails['gender'] === 'm') echo 'checked="checked"'; ?> /><label for="rbUserMale">Männlich</label>
                    <input id="rbUserFemale" type="radio" name="userDetails[gender]" value="f" <?php  if($userDetails['gender'] == 'f') echo 'checked="checked"'; ?> /><label for="rbUserFemale">Weiblich</label>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="userDetailsDegree">Titel</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input id="userDetailsDegree" type="text" name="userDetails[degree]" value="<?php echo htmlspecialchars($userDetails['degree']); ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Vorname</label>
                </div>
                <div class="col-sm-12 col-md">
                    <input type="text" name="userDetails[firstName]" value="<?php echo htmlspecialchars($userDetails['firstName']); ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Nachname</label>
                </div>
                <div>
                    <input type="text" name="userDetails[lastName]" value="<?php echo htmlspecialchars($userDetails['lastName']); ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Stra&szlig;e</label>
                </div>
                <div>
                    <input type="text" name="userDetails[street]" value="<?php echo htmlspecialchars($userDetails['street']); ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Postleitzahl</label>
                </div>
                <div>
                    <input type="text" name="userDetails[postcode]" value="<?php echo htmlspecialchars($userDetails['postcode']); ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Stadt</label>
                </div>
                <div>
                    <input type="text" name="userDetails[city]" value="<?php echo htmlspecialchars($userDetails['city']); ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Email</label>
                </div>
                <div>
                    <input type="text" name="userDetails[email]" value="<?php echo htmlspecialchars($userDetails['email']); ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Telefon mobil</label>
                </div>
                <div>
                    <input type="text" name="userDetails[mobilePhone]" value="<?php echo htmlspecialchars($userDetails['mobilePhone']); ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Telefon fest</label>
                </div>
                <div>
                    <input type="text" name="userDetails[phone]" value="<?php echo htmlspecialchars($userDetails['phone']); ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Geburtstag</label>
                </div>
                <div>
                    <input type="text" name="userDetails[birthday]" value="<?php echo htmlspecialchars($userDetails['birthday']); ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Geburtsort</label>
                </div>
                <div>
                    <input type="text" name="userDetails[birthplace]" value="<?php echo htmlspecialchars($userDetails['birthplace']); ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Familienstand</label>
                </div>
                <div>
                    <input type="text" name="userDetails[maritalStatus]" value="<?php echo htmlspecialchars($userDetails['maritalStatus']); ?>" />
                </div>
            </div>
            <input type="submit" name="sbmSetUserDetails" value="Deine Werte &auml;ndern"/>
            <label><?php echo htmlspecialchars($setUserDetailsMsg) ?? ''; ?></label>
        </fieldset>
    </form>
</div>
