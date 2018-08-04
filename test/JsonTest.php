<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace KryuuCommon\JsonStableStringifyTest;

use KryuuCommon\JsonStableStringify\Json;
use PHPUnit\Framework\TestCase;

/**
 * Description of JsonTest
 *
 * @author spawn
 */
class JsonTest extends TestCase {
    
    private $result =  '{"a":1,"b":{"ba":2,"bb":[{"bba":[5,6,7],"bbb":8,"bbc":9},"3 - Hello",4]},"c":[{"z":{"z1":10,"z2":"11 - hello"},"zz":12},"13 - hello",14],"d":"15 - mullama"}';
    private $testData ='{"c":[{"zz":12,"z":{"z2":"11 - hello","z1":10}},"13 - hello",14],"a":1,"d":"15 - mullama","b":{"bb":[{"bbc":9,"bbb":8,"bba":[5,6,7]},"3 - Hello",4],"ba":2}}';            
    
    public function testStringify() {
        
        $stableJson = new Json();
        
        $this->assertEquals(
            $this->result,
            $stableJson->stringify(json_decode($this->testData))
        );
    }    
    
}
