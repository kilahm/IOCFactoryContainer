<?hh // strict

namespace kilahm\Test;

use kilahm\IOC\IOCContainer;

<<ConsistentConstruct>> class ContainableC
{
    public static function factory(MockContainer $c) : this
    {
        return new static($c->a());
    }

    private function __construct(ContainableA $b)
    {

    }
}
