<?php
    session_start();
    require_once('../src/validate.php');
    require_once('../src/dbFunctions.php');
    require_once('../src/useCase.php');
    require_once('../src/config.php');

    if(isset($_POST['userDetails']) && isset($_POST['userDetails']['lastName']))
    {
        $dbConn = new PDO('mysql:host=localhost;dbname=' . getConfig()['database']['database'], getConfig()['database']['username'], getConfig()['database']['password']);
        $taskResult = ucSetUserDetails($dbConn, $_SESSION['userId'], $_POST['userDetails']);
        if($taskResult->isValid)
        {
            $addUserDetailsMsg = 'Deine Daten wurden geändert!';
        }
        else
        {
            $addUserDetailsMsg = 'Entschuldigung, die Anfrage konnte nicht verarbeitet werden.';
        }
    }
?>

<div>
    <h1>Deine Werte ändern</h1>
    <form onSubmit="submitForm('setUserValues.php', this);return false;">
        <label for="">Geschlecht</label>
        <input type="hidden" name="rbUserGender" value="x" />
        <label><input type="radio" name="userDetails[gender]" value="m" <?php if(isset($_POST['rbUserGender']) && $_POST['rbUserGender'] === 'm') echo 'checked="checked"'; ?> />Männlich</label>
        <label><input type="radio" name="userDetails[gender]" value="f" <?php if(isset($_POST['rbUserGender']) && $_POST['rbUserGender'] === 'f') echo 'checked="checked"'; ?> />Weiblich</label>
        <label for="txtUserDegree">Titel</label>
        <input type="text" name="userDetails[degree]" id="txtUserDegree" value="<?php echo htmlspecialchars($_POST['txtUserDegree'] ?? ''); ?>" />
        <label for="txtUserFirstName">Vorname</label>
        <input type="text" name="userDetails[firstName]" id="txtUserFirstName" value="<?php echo htmlspecialchars($_POST['txtUserFirstName'] ?? ''); ?>" />
        <label for="txtUserLastName">Nachname</label>
        <input type="text" name="userDetails[lastName]" id="txtUserLastName" value="<?php echo htmlspecialchars($_POST['txtUserLastName'] ?? ''); ?>" />
        <label for="txtUserStreet">Stra&szlig;e</label>
        <input type="text" name="userDetails[street]" id="txtUserStreet" value="<?php echo htmlspecialchars($_POST['txtUserStreet'] ?? ''); ?>" />
        <label for="txtUserPostcode">Postleitzahl</label>
        <input type="text" name="userDetails[postcode]" id="txtUserPostcode" value="<?php echo htmlspecialchars($_POST['txtUserPostcode'] ?? ''); ?>" />
        <label for="txtUserCity">Stadt</label>
        <input type="text" name="userDetails[city]" id="txtUserCity" value="<?php echo htmlspecialchars($_POST['txtUserCity'] ?? ''); ?>" />
        <label for="txtUserEmail">Email</label>
        <input type="text" name="userDetails[email]" id="txtUserEmail" value="<?php echo htmlspecialchars($_POST['txtUserEmail'] ?? ''); ?>" />
        <label for="txtUserMobilePhone">Telefon mobil</label>
        <input type="text" name="userDetails[mobilePhone]" id="txtUserMobilePhone" value="<?php echo htmlspecialchars($_POST['txtUserMobilePhone'] ?? ''); ?>" />
        <label for="txtUserPhone">Telefon fest</label>
        <input type="text" name="userDetails[phone]" id="txtUserPhone" value="<?php echo htmlspecialchars($_POST['txtUserPhone'] ?? ''); ?>" />
        <label for="txtUserBirthday">Geburtstag</label>
        <input type="text" name="userDetails[birthday]" id="txtUserBirthday" value="<?php echo htmlspecialchars($_POST['txtUserBirthday'] ?? ''); ?>" />
        <label for="txtUserBirthplace">Geburtsort</label>
        <input type="text" name="userDetails[birthplace]" id="txtUserBirthplace" value="<?php echo htmlspecialchars($_POST['txtUserBirthplace'] ?? ''); ?>" />
        <label for="txtUserMaritalStatus">Familienstand</label>
        <input type="text" name="userDetails[maritalStatus]" id="txtUserMaritalStatus" value="<?php echo htmlspecialchars($_POST['txtUserMaritalStatus'] ?? ''); ?>" />
        <input type="submit" name="sbmSetUserDetails" value="Deine Werte &auml;ndern"/>
        <label><?php echo htmlspecialchars($addUserDetailsMsg) ?? ''; ?></label>
    </form>
</div>
