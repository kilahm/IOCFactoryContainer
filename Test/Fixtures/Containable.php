<?hh // strict

namespace kilahm\IOC\Test\Fixtures;

use kilahm\IOC\FactoryContainer;

final class Containable implements ContainableInterface
{
    <<provides('kilahm\IOC\Test\Fixtures\ContainableInterface', 'makeit')>>
    public static function factory(FactoryContainer $c) : this
    {
        return new static();
    }
}
