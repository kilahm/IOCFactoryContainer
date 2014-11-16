<?hh // strict

namespace kilahm\Test;

use kilahm\IOC\FactoryRunner;

class MockContainer
{
    private FactoryRunner<MockContainer> $runner;
    public function __construct()
    {
        $this->runner = new FactoryRunner();
        $this->runner->setContainer($this);
    }

    public function a() : ContainableA
    {
        return $this->runner->make(class_meth(ContainableA::class, 'factory'));
    }

    public function b() : ContainableB
    {
        return $this->runner->make(class_meth(ContainableB::class, 'factory'));
    }

    public function c() : ContainableC
    {
        return $this->runner->make(class_meth(ContainableC::class, 'factory'));
    }

    public function makeit() : Containable
    {
        return $this->runner->make(class_meth(Containable::class, 'factory'));
    }
}
