<?php

require_once('vendor/phpunit/phpunit/src/Framework/TestCase.php');
require_once('src/useCase.php');
require_once('src/validate.php');

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
                self::$pdo = new PDO('mysql:host=localhost;dbname=jobApplication_mem', 'root', '1234');
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, 'heap');
        }

        return $this->conn;
    }



    public function testAddEmployer()
    {
        ucAddEmployer(StupidTest::$pdo, 1, "GmbH", "GmbH Strasse 4b", "90443", "Nürnberg", "m", "Dr.", "Thomas", "Meier", "thomas.meier@gmbh.de", "0151 9348934", "0911 1931141");
        $queryTable = $this->getConnection()->createQueryTable('employer', 'select * from employer');
        $expectedTable = $this->createFlatXmlDataSet("/var/www/html/jobApplicationSpam/test/ucAddEmployerExpected.xml")->getTable("employer");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testSetUserDetails()
    {
        ucSetUserDetails(StupidTest::$pdo, 1, "f", "Dr.", "Michaela", "Mittermeier", "Mittermeierstr. 31", "80431", "München", "0171 9828327", "020 8218317", "1981-07-19", "Fürth", "verheiratet");
        $queryTable = $this->getConnection()->createQueryTable('userDetails', 'select * from userDetails');
        $expectedTable = $this->createFlatXmlDataSet("/var/www/html/jobApplicationSpam/test/ucSetUserDetailsExpected.xml")->getTable("userDetails");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function getDataSet()
    {
        return $this->createFlatXmlDataSet('/var/www/html/jobApplicationSpam/test/myFlatXmlFixture.xml');
    }
}
