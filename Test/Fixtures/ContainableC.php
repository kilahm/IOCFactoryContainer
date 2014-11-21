<?hh // strict

namespace kilahm\IOC\Test\Fixtures;

use kilahm\IOC\FactoryContainer;

<<__ConsistentConstruct>> class ContainableC
{
    <<provides('kilahm\IOC\Test\Fixtures\ContainableC', 'c')>>
    public static function factory(FactoryContainer $c) : this
    {
        return new static($c->newA());
    }

    private function __construct(ContainableA $a)
    {

    }
}
