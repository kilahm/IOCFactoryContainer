<?hh // strict

namespace kilahm\IOC\Test;

use HackPack\HackUnit\Core\TestCase;
use kilahm\IOC\CircularDependencyException;
use kilahm\IOC\FactoryContainer;
use kilahm\IOC\Test\Fixtures\Containable;

class FactoryRunnerTest extends TestCase
{
    private static bool $loaded = false;
    private static string $projectDir = '';

    public function testContainerReturnsCorrectObject() : void
    {
        $container = new FactoryContainer();
        $this
            ->expect($container->getMakeit())
            ->toBeInstanceOf(Containable::class);
    }

    public function testContainerReturnsSameObject() : void
    {
        $container = new FactoryContainer();
        $first = $container->getMakeit();
        $second = $container->getMakeit();
        $this->expect($first)->toBeIdenticalTo($second);
    }

    public function testContainerReturnsDifferentObjects() : void
    {
        $container = new FactoryContainer();
        $first = $container->newMakeit();
        $second = $container->newMakeit();
        $this->expect($first === $second)->toEqual(false);
    }

    public function testContainerDetectsCircularDependencies() : void
    {
        $container = new FactoryContainer();
        $this->expectCallable(
            ()==>{$container->newA();}
        )->toThrow(CircularDependencyException::class);
    }
}
