<?hh // strict

namespace kilahm\IOC\Test\Fixtures;

use kilahm\IOC\FactoryContainer;

<<__ConsistentConstruct>> class ContainableB
{
    <<provides('b')>>
    public static function factory(FactoryContainer $c) : this
    {
        return new static($c->newC());
    }

    private function __construct(ContainableC $c)
    {

    }
}
