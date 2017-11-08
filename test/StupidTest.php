<?php

require_once('vendor/phpunit/phpunit/src/Framework/TestCase.php');
require_once('src/useCase.php');
require_once('src/validate.php');
require_once('src/helperFunctions.php');

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;


class StupidTest extends TestCase
{
    use TestCaseTrait;

    static private $pdo = null;
    public $conn = null;

    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new PDO('mysql:host=localhost;dbname=jobApplication_mem', 'spamy', '1234');
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, 'heap');
        }

        return $this->conn;
    }

    public function testNonExistingEmailShouldNotBeLoggedIn()
    {
        $userId = ucLogin(StupidTest::$pdo, 'notExistingInDatabase@email.de', 'egal');
        $this->assertEquals(-1, $userId);
    }

    public function testExistingEmailWithWrongPasswordShouldNotBeLoggedInWhenConfirmationStringIsNotNull()
    {
        $userId = ucLogin(StupidTest::$pdo, 'rene.ederer.nbg@gmail.com', 'falschesPasswort');
        $this->assertEquals(-1, $userId);
    }

    public function testExistingEmailWithWrongPasswordShouldNotBeLoggedInWhenConfirmationStringIsNull()
    {
        $userId = ucLogin(StupidTest::$pdo, 'helmut@goerke.de', 'falschesPasswort');
        $this->assertEquals(-1, $userId);
    }

    public function testExistingEmailWithCorrectPasswordShouldNotBeLoggedInWhenConfirmationStringIsNotNull()
    {
        $userId = ucLogin(StupidTest::$pdo, 'rene.ederer.nbg@gmail.com', '1234');
        $this->assertEquals(-1, $userId);
    }

    public function testExistingEmailWithCorrectPasswordShouldBeLoggedInWhenConfirmationStringIsNull()
    {
        $userId = ucLogin(StupidTest::$pdo, 'helmut@goerke.de', 'helmut');
        $this->assertEquals(2, $userId);
    }

    public function testNewRegistrationWithNonMatchingPasswordsShouldReturnInvalidTaskResult()
    {
        $sendMailStub = function($from, $fromName, $subject, $body, $to, $pdfAttachment) { return new TaskResult(true, [], []); };
        $taskResult = ucRegisterNewUser(StupidTest::$pdo, 'someEmail@bewerbungsspam.de', 'password1', 'passwordWronglyRepeated', $sendMailStub);
        $this->assertFalse($taskResult->isValid);
    }

    public function testNewRegistrationWithMatchingPasswordsAndExistingEmailShouldReturnInvalidTaskResult()
    {
        $sendMailStub = function($from, $fromName, $subject, $body, $to, $pdfAttachment) { return new TaskResult(true, [], []); };
        $taskResult = ucRegisterNewUser(StupidTest::$pdo, 'rene.ederer.nbg@gmail.com', 'somePassword', 'somePassword', $sendMailStub);
        $this->assertFalse($taskResult->isValid);
    }

    public function testNewRegistrationWithMatchingPasswordsAndNonExistingEmailShouldReturnValidTaskResult()
    {
        $sendMailStub = function($from, $fromName, $subject, $body, $to, $pdfAttachment) { return new TaskResult(true, [], []); };
        $taskResult = ucRegisterNewUser(StupidTest::$pdo, 'someEmail@bewerbungsspam.de', 'somePassword', 'somePassword', $sendMailStub);
        $this->assertTrue($taskResult->isValid);
    }

    public function testUploadTemplate()
    {
    }

    public function testSetUserDetails()
    {
        ucSetUserDetails(StupidTest::$pdo, 1,
            [ 'gender' => 'f'
            , 'degree' => 'Dr.'
            , 'firstName' => 'Michaela'
            , 'lastName' => 'Mittermeier'
            , 'street' => 'Mittermeierstr. 31'
            , 'postcode' => '80431'
            , 'city' => 'München'
            , 'mobilePhone' => '0171 9828327'
            , 'phone' => '020 8218317'
            , 'birthday' => '1981-07-19'
            , 'birthplace' => 'Fürth'
            , 'maritalStatus' => 'verheiratet']);
        $queryTable = $this->getConnection()->createQueryTable('userDetails', 'select * from userDetails');
        $expectedTable = $this->createFlatXmlDataSet("/var/www/html/jobApplicationSpam/test/ucSetUserDetailsExpected.xml")->getTable("userDetails");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testAddEmployer()
    {
        ucAddEmployer(StupidTest::$pdo, 1,
            [ 'company' => 'GmbH'
            , 'street' => 'GmbH Strasse 4b'
            , 'postcode' => '90443'
            , 'city' => 'Nürnberg'
            , 'gender' => 'm'
            , 'degree' => "Dr."
            , 'firstName' => 'Thomas'
            , 'lastName' => 'Meier'
            , 'email' => 'thomas.meier@gmbh.de'
            , 'mobilePhone' => '0151 9348934'
            , 'phone' => '0911 1931141' ]);
        $queryTable = $this->getConnection()->createQueryTable('employer', 'select * from employer');
        $expectedTable = $this->createFlatXmlDataSet("/var/www/html/jobApplicationSpam/test/ucAddEmployerExpected.xml")->getTable("employer");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }










    public function getDataSet()
    {
        return $this->createFlatXmlDataSet('/var/www/html/jobApplicationSpam/test/myFlatXmlFixture.xml');
    }
}
