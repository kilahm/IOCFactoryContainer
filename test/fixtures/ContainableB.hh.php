<?hh // strict

namespace kilahm\Test;

use kilahm\IOC\IOCContainer;

<<ConsistentConstruct>> class ContainableB
{
    public static function factory(MockContainer $c) : this
    {
        return new static($c->c());
    }

    private function __construct(ContainableC $b)
    {

    }
}
