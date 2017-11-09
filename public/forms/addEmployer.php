<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    session_start();
    require_once('/var/www/html/jobApplicationSpam/src/validate.php');
    require_once('/var/www/html/jobApplicationSpam/src/dbFunctions.php');
    require_once('/var/www/html/jobApplicationSpam/src/useCase.php');
    require_once('/var/www/html/jobApplicationSpam/src/config.php');

    $addEmployerMsg = '';
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
?>
<div>
    <h1>Arbeitgeber hinzuf&uuml;gen</h1>
        <form action="forms/addEmployer.php">
        <input type="text" name="txtReadEmployerValuesFromWebSite" />
        <input type="submit" name="sbmReadEmployerValuesFromWebSite" value="Werte von Website einlesen" />
    </form>
    <form onSubmit="submitForm('forms/addEmployer.php', this);return false;">
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
        <fieldset>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtCompany">Firma</label>
                </div>
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <input type="text" name="employer[company]" value="<?php echo htmlspecialchars($currentEmployer['$firmaName'] ?? ''); ?>" />
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtCompanyStreet">Stra&szlig;e</label>
                </div>
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <input type="text" name="employer[street]" value="<?php echo htmlspecialchars($currentEmployer['$firmaStrasse'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtCompanyPostcode">Postleitzahl</label>
                </div>
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <input type="text" name="employer[postcode]" value="<?php echo htmlspecialchars($currentEmployer['$firmaPlz'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtCompanyCity">Stadt</label>
                </div>
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <input type="text" name="employer[city]" value="<?php echo htmlspecialchars($currentEmployer['$firmaStadt'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label>Chef-Geschlecht</label>
                </div>
                <div class="input-group">
                    <input type="hidden" name="rbBossGender" value="x" />
                    <input type="radio" name="employer[gender]" value="m" <?php if(isset($_POST['rbBossGender']) && $_POST['rbBossGender'] === 'm') echo 'checked="checked"'; ?>/><label>M&auml;nnlich</label>
                    <input type="radio" name="employer[gender]" value="f" <?php if(isset($_POST['rbBossGender']) && $_POST['rbBossGender'] === 'f') echo 'checked="checked"'; ?>/><label>Weiblich</label>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtBossDegree">Chef-Titel</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" name="employer[degree]" value="<?php echo htmlspecialchars($currentEmployer['$chefTitel'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtBossFirstName">Chef-Vorname</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" name="employer[firstName]" value="<?php echo htmlspecialchars($currentEmployer['$chefVorname'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtBossLastName">Chef-Nachname</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" name="employer[lastName]" value="<?php echo htmlspecialchars($currentEmployer['$chefNachname'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtBossEmail">Email</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" name="employer[email]" value="<?php echo htmlspecialchars($currentEmployer['$firmaEmail'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtBossMobilePhone">Telefon mobil</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" name="employer[mobilePhone]" value="<?php echo htmlspecialchars($currentEmployer['$firmaMobil'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row responsive-label">
                <div class="col-sm-12 col-md-1" style="min-width:130px">
                    <label for="txtBossPhone">Telefon fest</label>
                </div>
                <div class="col-sm-12 col-md-1">
                    <input type="text" name="employer[phone]" value="<?php echo htmlspecialchars($currentEmployer['$firmaTelefon'] ?? ''); ?>"/>
                </div>
            </div>
            <div class="row">
                <input type="submit" name="sbmAddEmployer" value="Arbeitgeber hinzuf&uuml;gen" />
                <label><?php echo htmlspecialchars($addEmployerMsg) ?? '';?></label>
            </div>
        </fieldset>
    </form>
</div>

