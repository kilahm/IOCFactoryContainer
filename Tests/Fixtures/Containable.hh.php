<?hh // strict

namespace kilahm\Test;

use kilahm\IOC\IOCContainer;

<<ConsistentConstruct>> class Containable
{
    public static function factory(MockContainer $c) : this
    {
        return new static();
    }
}
