<?hh // strict

namespace kilahm\IOC\Test;

use HackPack\HackUnit\Core\TestCase;
use kilahm\IOC\FactoryContainer;

class FactoryCompilerTest extends TestCase
{
    private Set<string> $expectedMethods = Set{
        '__construct',
            'getB',
            'newB',
            'getA',
            'newA',
            'getC',
            'newC',
            'getFromProvider',
            'newFromProvider',
            'getMakeit',
            'newMakeit',
    };

    public function testCompiledFactoryHasExpectedMethods() : void
    {
        $actualMethods = get_class_methods(FactoryContainer::class);
        $missingMethods = $this->expectedMethods->toSet()->removeAll($actualMethods);
        $extraMethods = Set::fromItems($actualMethods)->removeAll($this->expectedMethods);
        $errMsg = '';
        if( ! $missingMethods->isEmpty()) {
            $errMsg .= 'Factory is missing expected methods ' . implode(', ', $missingMethods);
        }
        if( ! $extraMethods->isEmpty()) {
            $errMsg .= $errMsg === '' ? '' : PHP_EOL;
            $errMsg .= 'Factory has unexpected methods ' . implode(', ', $extraMethods);
        }

        if($errMsg !== '') {
            throw new \Exception($errMsg);
        }
    }
}
