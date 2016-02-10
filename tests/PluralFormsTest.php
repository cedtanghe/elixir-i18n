<?php

namespace Elixir\Test\I18N;

use Elixir\I18N\PluralForms;

class PluralFormsTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        // FR
        $parsed = PluralForms::parse('nplurals=2; plural=(n > 1);');
        $this->assertInternalType('array', $parsed);
        
        // UK
        $parsed = PluralForms::parse('nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : (n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2));');
        $this->assertInternalType('array', $parsed);
    }
    
    public function testEvaluate()
    {
        // FR
        $pluralForm = 'nplurals=2; plural=(n > 1);';
        
        $this->assertEquals(0, PluralForms::evaluate(0, $pluralForm));
        $this->assertEquals(1, PluralForms::evaluate(5, $pluralForm));
        
        // KW
        $plural = PluralForms::get('kw')['plural'];
        
        $this->assertEquals(0, PluralForms::evaluate(1, $plural));
        $this->assertEquals(1, PluralForms::evaluate(2, $plural));
        $this->assertEquals(2, PluralForms::evaluate(3, $plural));
        $this->assertEquals(3, PluralForms::evaluate(10, $plural));
    }
    
    public function testPO()
    {
        $fileHandler = new \Sepia\FileHandler(__DIR__ . '/centralesupelec.en-AU.po');
        $poParser = new \Sepia\PoParser($fileHandler);
        $entries  = $poParser->parse();
        
        print_r($entries['@count jour']);
    }
    
    public function testMO()
    {
        include_once 'Gettext.php';
        
        $moParser = new \Gettext();
        $entries  = $moParser->load(__DIR__ . '/centralesupelec.en-AU.mo');
        
        print_r($entries['@count jour']);
    }
}
