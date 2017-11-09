<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    if(!isset($_SESSION)) { session_start(); }
    require_once('/var/www/html/jobApplicationSpam/src/validate.php');
    require_once('/var/www/html/jobApplicationSpam/src/dbFunctions.php');
    require_once('/var/www/html/jobApplicationSpam/src/useCase.php');
    require_once('/var/www/html/jobApplicationSpam/src/config.php');
    require_once('/var/www/html/jobApplicationSpam/src/websiteFunctions.php');

    $addEmployerMsg = '';
    $currentEmployer =
        [ 'companyName' => ''
        , 'street' => ''
        , 'postcode' => ''
        , 'city' => ''
        , 'gender' => ''
        , 'degree' => ''
        , 'firstName' => ''
        , 'lastName' => ''
        , 'email' => ''
        , 'mobilePhone' => ''
        , 'phone' => '' ];
    if(isset($_SESSION['userId']) && $_SESSION['userId'] >= 1)
    {
        if(isset($_POST['employer']) && isset($_POST['employer']['lastName']))
        {
            $dbConn = new PDO('mysql:host=localhost;dbname=' . getConfig()['database']['database'], getConfig()['database']['username'], getConfig()['database']['password']);
            $taskResult = ucAddEmployer($dbConn, $_SESSION['userId'], $_POST['employer']);
            if($taskResult->isValid)
            {
                $addEmployerMsg = 'Arbeitgeber wurde hinzugefügt!';
            }
            else
            {
                $addEmployerMsg = 'Entschuldigung, die Anfrage konnte nicht verarbeitet werden.';
            }
        }
        else if(isset($_POST['txtReadEmployerValuesFromWebSite']))
        {
            $currentEmployer = readEmployerFromWebsite($_POST['txtReadEmployerValuesFromWebSite']);
        }
    }
?>
<div>
    <h1>Arbeitgeber hinzuf&uuml;gen</h1>
        <form onSubmit="submitForm('forms/addEmployer.php', this);return false">
            <input type="text" name="txtReadEmployerValuesFromWebSite" />
            <input type="submit"  value="Werte von Website einlesen" />
        </form>
    <form onSubmit="submitForm('forms/addEmployer.php', this);return false;">
        <fieldset>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtCompany">Firma</label>
                </div>
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <input type="text" name="employer[company]" value="<?php echo htmlspecialchars($currentEmployer['companyName'] ?? ''); ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtCompanyStreet">Stra&szlig;e</label>
                </div>
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <input type="text" name="employer[street]" value="<?php echo htmlspecialchars($currentEmployer['street'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtCompanyPostcode">Postleitzahl</label>
                </div>
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <input type="text" name="employer[postcode]" value="<?php echo htmlspecialchars($currentEmployer['postcode'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtCompanyCity">Stadt</label>
                </div>
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <input type="text" name="employer[city]" value="<?php echo htmlspecialchars($currentEmployer['city'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Chef-Geschlecht</label>
                </div>
                <div class="input-group">
                    <input type="hidden" name="employer[gender]" value="x" />
                    <input type="radio" id="employer[gender]" name="employer[gender]" value="m" <?php if($currentEmployer['gender'] === 'm') echo 'checked="checked"'; ?>/><label for="employer[gender]">M&auml;nnlich</label>
                    <input type="radio" id="employer[gender]" name="employer[gender]" value="f" <?php if($currentEmployer['gender'] === 'f') echo 'checked="checked"'; ?>/><label for="employer[gender]">Weiblich</label>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtBossDegree">Chef-Titel</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" name="employer[degree]" value="<?php echo htmlspecialchars($currentEmployer['degree'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtBossFirstName">Chef-Vorname</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" name="employer[firstName]" value="<?php echo htmlspecialchars($currentEmployer['firstName'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtBossLastName">Chef-Nachname</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" name="employer[lastName]" value="<?php echo htmlspecialchars($currentEmployer['lastName'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtBossEmail">Email</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" name="employer[email]" value="<?php echo htmlspecialchars($currentEmployer['email'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtBossMobilePhone">Telefon mobil</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" name="employer[mobilePhone]" value="<?php echo htmlspecialchars($currentEmployer['mobilePhone'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtBossPhone">Telefon fest</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" name="employer[phone]" value="<?php echo htmlspecialchars($currentEmployer['phone'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row">
                <input type="submit" name="sbmAddEmployer" value="Arbeitgeber hinzuf&uuml;gen" />
                <label><?php echo htmlspecialchars($addEmployerMsg) ?? '';?></label>
            </div>
        </fieldset>
    </form>
</div>

