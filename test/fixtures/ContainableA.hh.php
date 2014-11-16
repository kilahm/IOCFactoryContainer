<?hh // strict

namespace kilahm\Test;

<<ConsistentConstruct>> class ContainableA
{
    public static function factory(MockContainer $c) : this
    {
        invariant($c instanceof MockContainer, 'WTF');
        return new static($c->b());
    }

    private function __construct(ContainableB $b)
    {

    }
}
