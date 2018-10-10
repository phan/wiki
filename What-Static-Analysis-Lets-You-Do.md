Running a static analyzer on your code and blocking deploys on code that doesn't pass gives you some powers you didn't have before.

## Sandbox Old Confusing Code

When you write a better version of something, you can prevent the old version from ever being used again outside of its existing callers by adding a `@deprecated` annotation to the replaced element and `@suppress PhanDeprecatedFunction` or `@suppress PhanDeprecatedClass` to existing call sites.

```php
class C {
    /** @deprecated */
    function old() {}

    function new() {}
}

class D {
    /** @suppress PhanDeprecatedFunction **/
    function f() {
        C::old();
    }
}
```


## Communicate Expectations

If you find a piece of code you'd like to use, but you aren't certain about its return type, you can add a `@return` annotation to test your expectation and make sure that future development lines up with your expectations.

Annotating the expected return types and types of method or function parameters lets you communicate your expectations to other engineers and makes it easier to work with the damage caused by the slow insidious creep of extra optional parameters and one-off returns.


## Produce Correct Documentation

Without static analysis, your type annotations are going to drift away from reality. By adding a static analyzer and blocking deployment when they're incorrect, you're making it significantly easier to read and trust your code base.
