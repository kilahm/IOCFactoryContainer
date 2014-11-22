IOC Factory Container
===================

Compile a type safe IOC Container from user defined attributes.

[![Build Status](https://travis-ci.org/kilahm/IOCFactoryContainer.svg?branch=master)](https://travis-ci.org/kilahm/IOCFactoryContainer)

## Use

This library includes an executable that will scan your project directory for factories then construct a valid hack file that aliases all your factories as public instance methods of a single class.  Your factory methods must accept only a single parameter which is the factory container.

The factory container should only be instantiated once in your application's bootstrap.  You should then be able to use the container to instantiate your application class and run it.

### Mark factories with attributes

Here is an example class with its factory function marked.

```php
<?hh // strict

final class A
{
  <<provides('A', 'classA')>>
  public static function makeA(FactoryContainer $c) : this
  {
    return new static();
  }
}
```

The attribute name is `provides` and requires two paramters.  The first is the name of the class or interface this factory provides (must include the full namespace) and the second is the alias.  The alias is used as the name of the public instance method of the container and the class name is used as the return type.  Below are the methods that would be created for the factory defined above.

```php
...
  <<__Memoize>>
  public function getClassA() : /A
  {
    return $this->newClassA();
  }
  
  public function newClassA() : /A
  {
    return $this->runner->make(class_meth('/A', 'makeA'));
  }
...
```

Note that you are able to retreive the same instance by calling `$factory->getClassA()` any number of times, but calling `$factory->newClassA()` will always return a new instance.

### Run the factory container compiler

After defining some factory methods, run the `findfactories` executable, which can be found in your `vendor/bin` directory.  The executable takes any number of arguments which will be interpreted as base directories to scan.  The executable will also accept `--exclude="..."` as a long option which is interpreted as a space delimited list of directories to ignore.

```bash
vendor/bin/findfactories src/ other/path --exclude="src/ignore other/path/to/ignore"
```

The command above (if run from your project directory) will recursively scan the `src/` and `other/path/` directories for files containing factory definitions.  The resulting factory container file will be created in the project root and will be named `FactoryContainer.php`.  You may optionally provide the path to install `FactoryContainer.php` by using the `--install-path="dir/to/put/file"` option.

### Namespaces and Interfaces

Often you will define an interface (or include one from a package) then define multiple classes that all implement the interface.  All of these classes may include a factory method marked to provide the interface.

```php
<?hh // strict

namespace Foo\Bar;

interface IFoo
{
  ...
}
```

Note that you must include the entire namespace when specifying the class/interface that the factory provides.

```php
<?hh // strict

namespace Foo\Baz;

use Foo\Bar\IFoo;

final class Foo implements IFoo
{
  <<provides('Foo\Bar\IFoo', 'realFoo')>>
  public static function fooFactory(FactoryContainer $c) : this
  {
    return new static();
  }
  
  ...
  
}
```

```php
<?hh // strict

namespace Bar;

use Foo\Bar\IFoo;

final class FooBar implements IFoo
{
  <<provides('Foo\Bar\IFoo', 'fooBar')>>
  public static function barFactory(FactoryContainer $c) : this
  {
    return new static();
  }
  
  ...
  
}
```

The files above would compile to container methods shown below.

```php
...
  <<__Memoize>>
  public function getRealFoo() : Foo\Bar\IFoo
  {
    return $this->newRealFoo();
  }
  
  public function newRealFoo() : Foo\Bar\IFoo
  {
    return $this->runner->make(class_meth('\Foo\Baz\Foo', 'fooFactory'));
  }
  
  <<__Memoize>>
  public function getFooBar() : \Foo\Bar\IFoo
  {
    return $this->newFooBar();
  }
  
  public function newFooBar() : \Foo\Bar\IFoo
  {
    return $this->runner->make(class_meth('\Bar\FooBar', 'barFactory'));
  }
...
```

None of the examples above were particularly useful factories.  This tool would be more useful when a particular class has many dependencies and you wish to encapsulate instantiation inside of a single method.

```php
<?hh // strict

namespace CoolStuff;

use A;
use Foo\Bar\IFoo;

<<__ConsistentConstruct>>
class Awesome
{
  <<provides('CoolStuff\Awesome', 'totally')>>
  public static function makeWithRealFoo(FactoryContainer $c) : this
  {
    return new static($c->getRealFoo(), $c->getA());
  }
  
  <<provides('CoolStuff\Awesome', 'radical')>>
  public static function makeWithFooBar(FactoryContainer $c) : this
  {
    return new static($c->getFooBar(), $c->getA());
  }
  
  public function __construct(private IFoo $foo, private A $a)
  {
  }
```
