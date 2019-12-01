# PHPStan rules

## `@overrides` or must call parent

If there is overriding method, it must either has annotation `@overrides` or must call parent method.

## Sealed classes

Sealed classes has known children without possibility of declaring any other later. 

## require_once return value used

This is usually not what you expect.
