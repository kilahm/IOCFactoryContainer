<?hh // strict

namespace kilahm\IOC\Test\Fixtures;

use kilahm\IOC\FactoryContainer;

class Provider
{
    <<provides('fromProvider')>>
    public static function provide(FactoryContainer $c) : Containable
    {
        return new Containable();
    }
}
