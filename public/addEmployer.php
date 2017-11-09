<?php
    session_start();
    require_once('../src/validate.php');
    require_once('../src/dbFunctions.php');
    require_once('../src/useCase.php');
    require_once('../src/config.php');

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
        <form action="addEmployer.php" method="post">
        <input type="text" name="txtReadEmployerValuesFromWebSite" />
        <input type="submit" name="sbmReadEmployerValuesFromWebSite" value="Werte von Website einlesen" />
    </form>
    <form onSubmit="submitForm('addEmployer.php', this);return false;">
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
        <input type="text" name="employer[company]" value="<?php echo htmlspecialchars($currentEmployer['$firmaName'] ?? ''); ?>" />
        <label for="txtCompanyStreet">Stra&szlig;e</label>
        <input type="text" name="employer[street]" value="<?php echo htmlspecialchars($currentEmployer['$firmaStrasse'] ?? ''); ?>"/>
        <label for="txtCompanyPostcode">Postleitzahl</label>
        <input type="text" name="employer[postcode]" value="<?php echo htmlspecialchars($currentEmployer['$firmaPlz'] ?? ''); ?>"/>
        <label for="txtCompanyCity">Stadt</label>
        <input type="text" name="employer[city]" value="<?php echo htmlspecialchars($currentEmployer['$firmaStadt'] ?? ''); ?>"/>
        <div>
            <label>Chef-Geschlecht</label>
            <div>
                <input type="hidden" name="rbBossGender" value="x" />
                <label><input type="radio" name="employer[gender]" value="m" <?php if(isset($_POST['rbBossGender']) && $_POST['rbBossGender'] === 'm') echo 'checked="checked"'; ?>/>M&auml;nnlich</label>
                <label><input type="radio" name="employer[gender]" value="f" <?php if(isset($_POST['rbBossGender']) && $_POST['rbBossGender'] === 'f') echo 'checked="checked"'; ?>/>Weiblich</label>
            </div>
        </div>
        <label for="txtBossDegree">Chef-Titel</label>
        <input type="text" name="employer[degree]" value="<?php echo htmlspecialchars($currentEmployer['$chefTitel'] ?? ''); ?>"/>
        <label for="txtBossFirstName">Chef-Vorname</label>
        <input type="text" name="employer[firstName]" value="<?php echo htmlspecialchars($currentEmployer['$chefVorname'] ?? ''); ?>"/>
        <label for="txtBossLastName">Chef-Nachname</label>
        <input type="text" name="employer[lastName]" value="<?php echo htmlspecialchars($currentEmployer['$chefNachname'] ?? ''); ?>"/>
        <label for="txtBossEmail">Email</label>
        <input type="text" name="employer[email]" value="<?php echo htmlspecialchars($currentEmployer['$firmaEmail'] ?? ''); ?>"/>
        <label for="txtBossMobilePhone">Telefon mobil</label>
        <input type="text" name="employer[mobilePhone]" value="<?php echo htmlspecialchars($currentEmployer['$firmaMobil'] ?? ''); ?>"/>
        <label for="txtBossPhone">Telefon fest</label>
        <input type="text" name="employer[phone]" value="<?php echo htmlspecialchars($currentEmployer['$firmaTelefon'] ?? ''); ?>"/>
        <input type="submit" name="sbmAddEmployer" value="Arbeitgeber hinzuf&uuml;gen" />
        <label><?php echo htmlspecialchars($addEmployerMsg) ?? '';?></label>
    </form>
</div>

