<?hh // strict

namespace kilahm\IOC\Test\Fixtures;

use kilahm\IOC\FactoryContainer;

<<__ConsistentConstruct>> class ContainableC
{
    <<provides('c')>>
    public static function factory(FactoryContainer $c) : this
    {
        return new static($c->newA());
    }

    private function __construct(ContainableA $a)
    {

    }
}
