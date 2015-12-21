This is a guide for developer's looking to hack on Phan.

## Core Concepts

There are a few concepts that are important to understand when looking at the Phan code.

### FQSEN

An [FQSEN](https://github.com/etsy/phan/blob/master/src/Phan/Language/FQSEN.php) is a Fully Qualified Structural Element Name. Any element in PHP that can be accessed from elsewhere has an FQSEN that we use to look it up. In the following code for example

```php
namespace NS;

class A {
    const C = 'foo';
    public $p = 42;
    public function f() {}
}

function g() {}
```

we have the following fully qualified structural element names;

* **\NS\a** is the class `A` in namespace `\NS`
* **\NS\a::C** is the constant `C` in class `\NS\a`
* **\NS\a::$p** is the property `$p` in class `\NS\a`
* **\NS\a::f** is function `f` in the class `\NS\a`
* **\NS::g** is the function `g` in namespace `\NS`

Its important to note that even if we didn't have a namespace defined for the code, we'd still be in the implicit root namespace `\`. For example, in the code

```php
class B {
    function f() {}
}
function g() {}
```

we have the following FQSENs.

* **\b** is the class `B`
* **\b::f** is the function `f` in class `B`
* **\g** is the function `g`

You'll note that all class and function FQSENs lowercase the name. We do this because function and class names are case insensitive in PHP. We'll likely make an option for enforcing casing in function and class names in future releases of Phan.

An FQSEN for an element will inherit from the abstract class [\Phan\Language\FQSEN](https://github.com/etsy/phan/blob/master/src/Phan/Language/FQSEN.php). The actual FQSEN for each element (classes, methods, constants, properties, functions) will be defined by classes in the [\Phan\Language\FQSEN](https://github.com/etsy/phan/tree/master/src/Phan/Language/FQSEN) namespace.
