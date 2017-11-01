<?php

namespace Tests\Unit;
require_once('app/odtFunctions.php');

//require_once(

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OdtFunctionsTest extends TestCase
{
    public function testReplaceAllInStringRemoveSpanTags1()
    {
        $beforeXML = "<?xml version=\"1.0\"?>\n<office xmlns:text=\"a\"><document>\$firm<text:span>e</text:span>nName</document></office>\n";
        $afterXML =  "<?xml version=\"1.0\"?>\n<office xmlns:text=\"a\"><document>GmbH</document></office>\n";
        $this->assertEquals($afterXML, replaceAllInStringRemoveSpanTags($beforeXML, ["\$firmenName" => "GmbH" ]));
    }
    public function testReplaceAllInStringRemoveSpanTags2()
    {
        $beforeXML = "<?xml version=\"1.0\"?>\n<office xmlns:text=\"a\"><document>$<text:span>fi</text:span>rm<text:span>enName</text:span></document></office>\n";
        $afterXML =  "<?xml version=\"1.0\"?>\n<office xmlns:text=\"a\"><document>GmbH</document></office>\n";
        $this->assertEquals($afterXML, replaceAllInStringRemoveSpanTags($beforeXML, ["\$firmenName" => "GmbH" ]));
    }
    public function testReplaceAllInStringRemoveSpanTags3()
    {
        $beforeXML = '<?xml version="1.0"?> <office xmlns:text="a"><document>$<text:span size="20">f<text:span>i</text:span><text:span>r</text:span>m<text:span size="20">en</text:span>Name</text:span></document></office>';
        $afterXML =  '<?xml version="1.0"?> <office xmlns:text="a"><document>GmbH</document></office>';
        $this->assertEquals($afterXML, preg_replace('/\s+/', ' ', trim(replaceAllInStringRemoveSpanTags($beforeXML, ["\$firmenName" => "GmbH" ]))));
    }
}
