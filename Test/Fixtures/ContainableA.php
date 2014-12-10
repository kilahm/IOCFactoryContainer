<?hh // strict

namespace kilahm\IOC\Test\Fixtures;

use kilahm\IOC\FactoryContainer;

<<__ConsistentConstruct>> class ContainableA
{
    <<provides('a')>>
    public static function factory(FactoryContainer $c) : this
    {
        return new static($c->newB());
    }

    private function __construct(ContainableB $b)
    {

    }
}
