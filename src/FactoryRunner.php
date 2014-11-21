<?hh // strict

namespace kilahm\IOC;


final class FactoryRunner<Tcontainer>
{
    private Set<string> $fetching = Set{};

    private ?Tcontainer $container;

    public function setContainer(Tcontainer $container) : void
    {
        $this->container = $container;
    }

    public function make<Tobject>((function(Tcontainer):Tobject) $factory) : Tobject
    {
        // Some way of uniquely identifying a factory with a string
        $handle = serialize($factory);
        if($this->fetching->contains($handle)){
            $msg = 'Circular dependency chain: ';
            foreach($this->fetching as $serArray){
                $class = unserialize($serArray)[0];
                $msg .= $class . ' -> ';
            }
            $thisclass = unserialize($handle)[0];
            throw new CircularDependencyException($msg . ' -> ' . $thisclass);
        }
        // First mark that we are instantiating the class
        $this->fetching->add($handle);
        if($this->container === null) {
            throw new \RuntimeException('You must set the container of the factory runner before using the runner.');
        }
        $ins = $factory($this->container);
        // Then remove the mark
        $this->fetching->remove($handle);
        return $ins;
    }
}
