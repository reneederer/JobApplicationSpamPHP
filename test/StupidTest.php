<?php

require_once('vendor/phpunit/phpunit/src/Framework/TestCase.php');
require_once('src/Stupid.php');

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;


class StupidTest extends TestCase
{
    use TestCaseTrait;

    static private $pdo = null;
    private $conn = null;

    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new PDO('mysql:host=localhost;dbname=jobApplication', 'root', '1234');
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, 'InnoDB');
        }

        return $this->conn;
    }



    public function testAddEntry()
    {
        $queryTable = $this->getConnection()->createQueryTable('user', 'SELECT * FROM user');
        $expectedTable = $this->createFlatXmlDataSet("/var/www/html/jobApplicationSpam/test/expectedBook.xml")->getTable("user");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testAddEntry1()
    {
        $queryTable = $this->getConnection()->createQueryTable('user', 'SELECT * FROM user');
        $expectedTable = $this->createFlatXmlDataSet("/var/www/html/jobApplicationSpam/test/expectedBook.xml")->getTable("user");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function getDataSet()
    {
        return $this->createFlatXmlDataSet('/var/www/html/jobApplicationSpam/test/myFlatXmlFixture.xml');
    }
}
