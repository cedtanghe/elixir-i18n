<?php

namespace Elixir\Test\I18N;

use Elixir\I18N\Loader\MOLoader;
use Elixir\I18N\Loader\POLoader;
use Elixir\I18N\PluralForms;

class I18NTest extends \PHPUnit_Framework_TestCase
{
    public function testParsePluralForms()
    {
        // FR
        $parsed = PluralForms::parse('nplurals=2; plural=(n > 1);');
        $this->assertInternalType('array', $parsed);
        
        // UK
        $parsed = PluralForms::parse('nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : (n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2));');
        $this->assertInternalType('array', $parsed);
    }
    
    public function testEvaluatePluralForms()
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
    
    public function testPOParser()
    {
        $poParser = new POLoader();
        $data  = $poParser->load(__DIR__ . '/en.po');
        
        $this->assertArrayHasKey('messages', $data);
        $this->assertArrayHasKey('@count jour', $data['messages']);
        $this->assertEquals('@count day', $data['messages']['@count jour'][0]);
        $this->assertEquals('@count days', $data['messages']['@count jour'][1]);
        
        $this->assertArrayHasKey('metadata', $data);
        $this->assertArrayHasKey('Plural-Forms', $data['metadata']);
        $this->assertEquals('nplurals=2; plural=(n!=1);', $data['metadata']['Plural-Forms']);
    }
    
    public function testMOParser()
    {
        $moParser = new MOLoader();
        $data  = $moParser->load(__DIR__ . '/en.mo');
        
        $this->assertArrayHasKey('messages', $data);
        $this->assertArrayHasKey('@count jour', $data['messages']);
        $this->assertEquals('@count day', $data['messages']['@count jour'][0]);
        $this->assertEquals('@count days', $data['messages']['@count jour'][1]);
        
        $this->assertArrayHasKey('metadata', $data);
        $this->assertArrayHasKey('Plural-Forms', $data['metadata']);
        $this->assertEquals('nplurals=2; plural=(n!=1);', $data['metadata']['Plural-Forms']);
    }
}
