<?php
use PHPUnit\Framework\TestCase;
use vekas\RDC\RDC;


class RDCTest extends TestCase{ 
    function testInstantiateRDC(){
        $rdc = new RDC(__DIR__."/../returningFile");
        $this->assertInstanceOf(RDC::class,$rdc);
    }
    function testAddDependencyToExistedScript(){
        $script = "<?php 
            return [
                dep1::class => new dep1
            ]
        ";

        $rdc = new RDC(__DIR__."/../returningFile");
        
        $reflection =  new ReflectionClass($rdc);
        $property = $reflection->getProperty("script");
        $property->setAccessible(true);
        $property->setValue($rdc,$script);

        $rdc->addDeps([
            "dep2"
        ]);

        $depsToArray = $reflection->getMethod("getDepsFromString");
        $depsToArray->setAccessible(true);
        $rdc->update();
        
        $existedDeps = $depsToArray->invoke($rdc,$rdc->getScript());
        $this->assertSame([
            "dep1",
            "dep2"
        ],$existedDeps);
    }
}