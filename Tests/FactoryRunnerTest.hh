<?hh // strict

namespace kilahm\Test;

use kilahm\IOC\CircularDependencyException;
use HackPack\HackUnit\Core\TestCase;

class FactoryRunnerTest extends TestCase
{
    public function testContainerReturnsNewObject() : void
    {
        $container = new MockContainer();
        $this->expect($container->makeit())->toEqual(Containable::class);
    }

    public function testContainerDetectsCircularDependencies() : void
    {
        $container = new MockContainer();
        $this->expectCallable(
            ()==>{$container->a();}
        )->toThrow(CircularDependencyException::class);
    }
}

