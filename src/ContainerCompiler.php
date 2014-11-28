<?hh // strict

namespace kilahm\IOC;

use kilahm\Scanner\ClassScanner;
use ReflectionClass;
use ReflectionMethod;

type FactoryInfo = shape(
    'alias' => string,
    'className' => string,
    'methodName' => string,
    'provides' => string,
);

final class ContainerCompiler
{
    private Vector<FactoryInfo> $factoryList = Vector{};

    const string CONTAINER_FILENAME = 'FactoryContainer.php';

    public function __construct(private Map<string,string> $classMap)
    {
    }

    public function compile(string $outFileName) : void
    {
        // Initialize the container and include it
        $this->factoryList->clear();
        file_put_contents($outFileName, $this->makeContainerContent());
        /* HH_FIXME[1002] */
        require_once($outFileName);

        // Require the files given to us
        $this->includeFiles($this->classMap->toVector());

        // Loop through classmap to find factories
        $this->classMap->mapWithKey(($className, $fileName) ==> {
            $this->findFactories(new ReflectionClass($className));
        });

        // Write out the real container
        file_put_contents($outFileName, $this->makeContainerContent());
    }

    private function includeFiles(Vector<string> $fileNames) : void
    {
        foreach($fileNames as $fileName){
            /* HH_FIXME[1002] */
            require_once($fileName);
        }
    }

    private function findFactories(ReflectionClass $reflector) : void
    {
        foreach($reflector->getMethods(ReflectionMethod::IS_STATIC) as $staticMethod) {
            // The factory must only accept the IOC Container class as a parameter
            if($staticMethod->getNumberOfParameters() > 1) {
                continue;
            }

            $providesArgs = Vector::fromItems($staticMethod->getAttribute('provides'));
            if($providesArgs->count() < 2) {
                continue;
            }

            $provides = '\\' . (string)$providesArgs[0];
            $alias = (string)$providesArgs[1];

            $parameterClass = $staticMethod->getParameters()[0]->getClass();

            if( ! class_exists($provides) && ! interface_exists($provides)) {
                echo "$provides is not defined.";
                continue;
            }
            if($parameterClass->getName() != 'kilahm\IOC\FactoryContainer') {
                continue;
            }
            $this->factoryList[] = shape(
                'alias' => $alias,
                'provides' => $provides,
                'className' => '\\' . $reflector->getName(),
                'methodName' => $staticMethod->getName(),
            );
        }
    }

    private function makeContainerContent() : string
    {
        return
            $this->getContainerHead() .
            implode(PHP_EOL, $this->factoryList->map($f ==> $this->makeContainerMethod($f))) .
            $this->getContainerFoot();
    }

    private function getContainerHead() : string
    {
        return <<<'PHP'
<?hh // strict

namespace kilahm\IOC;

class FactoryContainer
{
    private FactoryRunner<FactoryContainer> $runner;

    public function __construct()
    {
        $this->runner = new FactoryRunner();
        $this->runner->setContainer($this);
    }

PHP;

    }

    private function makeContainerMethod(FactoryInfo $factory) : string
    {
        $alias = ucfirst($factory['alias']);
        $provides = $factory['provides'];
        $methName= $factory['methodName'];
        $className = $factory['className'];

        return <<<PHP

    <<__Memoize>>
    public function get$alias() : $provides
    {
        return \$this->new$alias();
    }

    public function new$alias() : $provides
    {
        return \$this->runner->make(class_meth('$className', '$methName'));
    }
PHP;
    }

    private function getContainerFoot() : string
    {
        return PHP_EOL . '}';
    }
}
