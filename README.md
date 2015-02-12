IOC Factory Container
===================

[![Join the chat at https://gitter.im/kilahm/IOCFactoryContainer](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/kilahm/IOCFactoryContainer?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Build Status](https://travis-ci.org/kilahm/IOCFactoryContainer.svg?branch=master)](https://travis-ci.org/kilahm/IOCFactoryContainer) [![HHVM Status](http://hhvm.h4cc.de/badge/kilahm/ioc-factory-container.svg)](http://hhvm.h4cc.de/package/kilahm/ioc-factory-container)

Compile a type safe IOC Container from user defined attributes.

## Use

This library includes an executable that will scan your project directory for factories then construct a valid hack file that aliases all your factories as public instance methods of a single class.  Your factory methods must accept only a single parameter which is the factory container.

The factory container should only be instantiated once in your application's bootstrap.  You should then be able to use the container to instantiate your application class and run it.

### Mark factories with attributes

Here is an example class with its factory function marked.

```php
<?hh // strict

final class A
{
  <<provides('myA')>>
  public static function factory(FactoryContainer $c) : this
  {
    return new static();
  }
}
```

The attribute name is `provides` and requires one paramter, which is an alias for the factory method. The above definition would compile to:

```php
...
  <<__Memoize>>
  public function getMyA() : /Foo
  {
    return $this->newMyA();
  }
  
  public function newMyA() : /Foo
  {
    return $this->runner->make(class_meth('/A', 'factory'));
  }
...
```

Note that you are able to retrieve the same instance by calling `$factory->getMyA()` any number of times, but calling `$factory->makeMyA()` will always return a new instance.

### Run the factory container compiler

After defining some factory methods, run the `findfactories` executable, which can be found in your `vendor/bin` directory.
The executable takes any number of arguments which will be interpreted as base directories to scan.
The executable will also accept any number of `--exclude="..."` long options which is interpreted as a directory to ignore.

```bash
vendor/bin/findfactories src/ other/path --exclude="src/ignore” --exclude=”other/path/to/ignore"
```

The command above (if run from your project directory) will recursively scan the `src/` and `other/path/` directories, except for the `src/ignore`, `other/path/to/ignore` directories and their children.
The resulting factory container file will be created in the project root and will be named `FactoryContainer.php`.
You may optionally provide the path to install `FactoryContainer.php` by using the `--install-path="dir/to/put/file"` option.

### Namespaces

You may use namespaces and `use` as normal, and the compiler will expand the class names to include their namespace.

```php
<?hh // strict

namespace Foo\Baz;

use Foo\Bar\IFoo;

final class Foo implements IFoo
{
  <<provides('realFoo')>>
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
  <<provides('fooBar')>>
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
  public function getRealFoo() : \Foo\Baz\Foo
  {
    return $this->newRealFoo();
  }
  
  public function newRealFoo() : \Foo\Baz\Foo
  {
    return $this->runner->make(class_meth('\Foo\Baz\Foo', 'fooFactory'));
  }
  
  <<__Memoize>>
  public function getFooBar() : \Bar\FooBar
  {
    return $this->newFooBar();
  }
  
  public function newFooBar() : \Bar\FooBar
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
  <<provides('awesomeWithFoo')>>
  public static function makeWithRealFoo(FactoryContainer $c) : this
  {
    return new static($c->getRealFoo(), $c->getA());
  }
  
  <<provides('awesomeWithBar')>>
  public static function makeWithFooBar(FactoryContainer $c) : this
  {
    return new static($c->getFooBar(), $c->getA());
  }
  
  public function __construct(private IFoo $foo, private A $a)
  {
  }
```
