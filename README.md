# PHPStan rules

## `@overrides` or must call parent

If there is overriding method, it must either has annotation `@overrides` or must call parent method.

````php
class BasePresenter {

  private $dep;

  public function injectFoo(Dependency $dep): void {
      $this->dep = $dep
  }

}

class LoginPresenter extends BasePresenter {

  private $dep;
  
  public function injectFoo(Dependency $dep): void {
      $this->dep = $dep
  }

}
````

Requires `LoginPresenter` to be explicitly tell that you are intentionally overriding method:

````php
class LoginPresenter extends BasePresenter {

  private $dep;
  
  /**
   * @overrides
   */
  public function injectFoo(Dependency $dep): void {
      $this->dep = $dep
  }

}
````

or call parent function:

````php
class LoginPresenter extends BasePresenter {

  private $dep;
  
  public function injectFoo(Dependency $dep): void {
      $this->dep = $dep
      parent::injectFoo($dep);
  }

}
````

## Sealed classes

Sealed classes are used for representing restricted class hierarchies. 

````php

// file: enum.php

/**
 * @sealed
 */
abstract class EnumFoo {
}

final class Value1 extends EnumFoo {}
final class Value2 extends EnumFoo {}
final class Value3 extends EnumFoo {}

// file: other.php

final class Value4 extends EnumFoo {} // not allowed
````

This restricts declaring any other sub-type of `EnumFoo`, then `Value1`, `Value2`, `Value3`. This is usefull, because it should then understand that:

````php
\assert($value instanceof EnumFoo);

switch (getclass($value)): {
  case Value1::class:
    break;
  case Value2::class:
    break;
  case Value3::class:
    break;
  default:
    echo "unreachable code!";
    break;
}
````

Type system should know that default case is unreachable, because there cannot be any other type decalared anywhere.

## Overriding property in child class

````php
class BasePresenter {

  /** @var Dependency */
  private $dep;

}

class LoginPresenter extends BasePresenter {

  /** @var Dependency2 */
  private $dep; // error

}
````

## require_once return value used

This is usually not what you expect.

