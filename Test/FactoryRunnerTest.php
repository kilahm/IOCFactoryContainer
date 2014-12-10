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

    public function setUp() : void
    {
        if(! self::$loaded) {
            self::$loaded = true;

            // Find the project dir based on composer
            $basedir = __DIR__;

            do {
                if(file_exists($basedir . '/composer.json') && file_exists($basedir . '/vendor/autoload.php')){
                    break;
                }
                $basedir = dirname($basedir);
                if($basedir === '/'){
                    // This will cause all of the tests in this suite to fail
                    return;
                }
            } while (true);

            self::$projectDir = $basedir;
            spl_autoload_register((string $class) ==> {
                /* HH_FIXME[1002] */
                include_once $basedir . '/FactoryContainer.php';
            });
        }
    }

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

    public function testCompiledClassIsExpected() : void
    {
        $this->expect(file_get_contents(self::$projectDir . '/FactoryContainer.php'))
            ->toEqual(file_get_contents(__DIR__ . '/Fixtures/ExpectedFactory.txt'));
    }
}
