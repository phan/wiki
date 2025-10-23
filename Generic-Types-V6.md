# Generic Types in Phan v6

Phan v6 significantly expands support for generic (templated) classes, interfaces, traits, and functions using PHPDoc annotations with `@template` and related tags. This allows you to write type-safe, reusable code that works with multiple types while maintaining strong static analysis.

**What's New in v6:**
- Generic interfaces with `@implements` / `@phan-implements`
- Generic traits with `@use` / `@phan-use`
- Template constraints with `@template T of SomeClass`
- Variance annotations: `@template-covariant` and `@template-contravariant`
- Utility types: `key-of<T>`, `value-of<T>`, `int-range<min, max>`, `positive-int`, `negative-int`

## Table of Contents

1. [Basic Generic Classes](#basic-generic-classes)
2. [Generic Interfaces](#generic-interfaces)
3. [Generic Traits](#generic-traits)
4. [Template Constraints](#template-constraints)
5. [Variance Annotations](#variance-annotations)
6. [Function Templates](#function-templates)
7. [Utility Types](#utility-types)
8. [Rules and Limitations](#rules-and-limitations)

## Basic Generic Classes

### Declaring Template Types

All template types for a generic class must be declared on the class via doc block comments using the `@template` annotation.

```php
/**
 * A generic box that holds a value of type T
 * @template T
 */
class Box {
    /** @var T */
    private $value;

    /** @param T $value */
    public function __construct($value) {
        $this->value = $value;
    }

    /** @return T */
    public function get() {
        return $this->value;
    }
}

// Phan infers Box<int> from the constructor argument
$intBox = new Box(42);
$val = $intBox->get();  // Phan knows this is an int

// Phan infers Box<string> from the constructor argument
$strBox = new Box("hello");
$str = $strBox->get();  // Phan knows this is a string
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUhBSDmBTAdkgTgSwMaQEYD2AHpAC4AWAhqZOQQDYAmAzpJZAG6X0CuSkBAGZkAngAd+AFSgwAAqSQBbMfWpSZwcNlXNWAIWKQA3lEhnQcrukiToms2bFYuCyABIuvJAG5wp82CQsmKU6JSKNu6efHb+kGI8ePQ4kII8KNikmAQokAD6edg5zKToPJkAFB7cfACUxnEO7hSYzAC0AHzR-AC8UTU+cQC+fk0WQehIpDzoubYg9g4JSSlpGVk5iFMV9SZNTZPTs83krZ3dvk0jI+DAwJAAClS5mCiCGPrEADyvpB2p6AIEQo-CKKBKZUyBGsoQQPEUqFI4DcvwMJD6aAA7pA0RUACwAJlqvmq9EgfRRKFIaM6yFIO285nuT0ouQA1igCJjWC1WK02C8qX47o9npBXu90J8iF8Ia8EP9BIDgeRQcVSuVSNC2Og4QihW4IWjyZAsTjiBUAESq+j0AiW4nIiEmw2lGkdOkMpmi1mQDlcnmnPmsdhylAIcBAA&php=84&phan=v6-dev&ast=1.1.3)**


### Constructor Must Define All Template Types

All template types must be filled in with concrete types via the constructor. This allows Phan to infer the generic type from constructor arguments.

```php
/**
 * @template T
 */
class Container {
    /** @var T */
    protected $item;

    /** @param T $item */
    public function __construct($item) {
        $this->item = $item;
    }
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUojDgDGWAzmZAMID2AdvJgJZ2IBOkA3lJL6DLABumDrmhFevVGxpJiSACaQAJEyTIA3OB58wcVCMzI8KtSnE7IqAK4AjdE2KQAZtbrym9SAH1vxemTwbNbyABSq6gCUXJaSKvAAFkxkALQAfGbGALym6lpxAL7gRUA&php=84&phan=v6-dev&ast=1.1.3)**


If template types aren't specified as constructor parameters, Phan will emit `PhanGenericConstructorTypes`.

### Extending Generic Classes

Classes extending generic classes need to fill in the types of the generic parent class via the `@extends` annotation.

```php
/**
 * @template T
 */
class Box {
    /** @var T */
    protected $value;

    /** @param T $value */
    public function __construct($value) {
        $this->value = $value;
    }
}

// Concrete type parameter
/**
 * @extends Box<int>
 */
class IntBox extends Box {
    public function __construct(int $value) {
        parent::__construct($value);
    }
}

// Passing template through
/**
 * @template T2
 * @extends Box<T2>
 */
class GenericBox extends Box {
    /** @param T2 $value */
    public function __construct($value) {
        parent::__construct($value);
    }
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUojDgDGWAzmZAEID2AHpAN5SSugywBumATntEVatUPGkmJIAJpAAk3dAFdEAbnAs2YOKl6Zk-OZkWIB6yKgUAjdAEtikAGYKAdhOs0nkAPqfi7svB4FCQAKAyMASiZTIVl4AAtrMgBaAD55JUgAXll0lVMAX3BC8GBgSABhd2IeRBx4AE9UY20eXVrEHhKwAjhEOiQnSUpaOgAeayd4FIIiUkwKSABJSZHIPoGh6noomPMrWwdnV3cvHz8AoPhgifgcwyVI5hjd3kRJgC53718nf0CQsIPVQxQrFUqQAAK8zIEwA5pAkGgsHU4qIFLC4l0INA4IiMNhjLgAEw9WDrN6bEajYnTExzBYAcTeHVsq3Jg2G2yeQnYWh0emJdyMJl2lhsdkcLngbg833O-yugMQj2iQhab3gnzlvwuANy4WBQlB4CAA&php=84&phan=v6-dev&ast=1.1.3)**


## Generic Interfaces

**New in v6:** Phan now supports generic interfaces using the `@implements` annotation.

### Declaring Generic Interfaces

```php
/**
 * @template T
 */
interface Repository {
    /**
     * @param int $id
     * @return T
     */
    public function find(int $id);

    /**
     * @param T $entity
     */
    public function save($entity): void;
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUojDgCWAdkgE4BmmAxopAEqKoD2AzsfKxQJ6QBvKJBGgIIiTFipMFTMkhl4kACTEAJsMlwKieAFcKpPFpGFTqfQCN0xWpCr7SteMVbGqZdQAolqjQCUANzgpmKm0HAycgq4qojkXLwR5hKQljZ2Dk4ubsbsmABuiN4qCa7wvAEAXJCFrBohAL7gQA&php=84&phan=v6-dev&ast=1.1.3)**

### Implementing Generic Interfaces

Use the `@implements` annotation to specify the template type when implementing a generic interface:

```php
class User {
    public string $name = '';
}

/**
 * @implements Repository<User>
 */
class UserRepository implements Repository {
    public function find(int $id): User {
        return new User();
    }

    public function save($entity): void {
        // Phan knows $entity is User
        echo $entity->name;
    }
}

$repo = new UserRepository();
$user = $repo->find(1);
echo $user->name;  // Phan knows $user is User
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=MYGwhgzhAECqEFMBO0DeAoaXoAcCuARiAJbDQQAuSxAdgObQAkNYAtgtALzQDkPA3OgC+6dAHoAVBMwToAAWKscIBOxoUYAJQQ4A9hGIVdSAJ4AeeMgB8MselCQYlpNr0Gjp6IuWqE6rTr6hsYmaJjY+ESk0ABmeDTAFMS6NLG0ACYAFLQUTMTpAJQAXHCIKBjYldBICBR4SKk0CADupciZBYKVIuFYkSRkcQlJKeRgAG4ImYx+SRQmxdDjuvlhVdhiYtAACgAWYKkA1jS6zTAz6oahxE5lvVUIwLu6TLNXALRWLOxd2CI9jBqei40CarWcriCHhMHUEjDwZRBgMCnxiGUyAEZOuhHs8mAjkJ9vgh+FhNjt9kcTmd8Yibm0kOggA&php=84&phan=v6-dev&ast=1.1.3)**


### Multiple Interfaces

You can implement multiple generic interfaces with different template parameters:

```php
/**
 * @template TKey
 * @template TValue
 * @implements Iterator<TKey, TValue>
 * @implements ArrayAccess<TKey, TValue>
 */
class Collection implements Iterator, ArrayAccess {
    /** @var array<TKey, TValue> */
    private $items = [];

    // Iterator implementation...
    public function current(): mixed { return current($this->items); }
    public function key(): mixed { return key($this->items); }
    public function next(): void { next($this->items); }
    public function rewind(): void { reset($this->items); }
    public function valid(): bool { return key($this->items) !== null; }

    // ArrayAccess implementation...
    public function offsetExists(mixed $offset): bool {
        return isset($this->items[$offset]);
    }
    public function offsetGet(mixed $offset): mixed {
        return $this->items[$offset];
    }
    public function offsetSet(mixed $offset, mixed $value): void {
        $this->items[$offset] = $value;
    }
    public function offsetUnset(mixed $offset): void {
        unset($this->items[$offset]);
    }
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUDSiAnlDAihtongGqboCuipcAlmuiogHbwDOkAJJIATtgD2IgDwFiAGlr0mAPhax2GLrwEBBEWKI6AxkcR8+MwkQW46jRKujBwRrOcgBhcek5H4rcW5IDU5kHn4hUQkRBT0DY1N3AG8oSDTQMgA3TBFIHINLeUV7ZSdUtNQRVmycABJWJGQBAF5IAG0AXQBucHLIYGBIxDF4SWCOLXhsAO4AOnm+1AYAI3RWI0gAMwZuPxnIIwZ9cIAKAEoALkhkVgAPRAATSCTIEUR4I6DD494T2vgABasPgAWmUDRQfDOXUgAF9Fis1httrt-IFIABrYjnK43e5PF5vD4iIJYoh-QHAsEQprQuEI1brLY7Pbo7iIW7wHGQTLiVgEyDszkUoGg8GNKEw+FpCqIpko1lBN4Ad1Y3Ae3N5-OerzM7xFVPFkLp0plS0ZyJZaKC2TWGsukGW4m8OqJn0x2P+oupErOkAAhM1WtwGD4pb0Zf1BnFMIYTGYBCFJtNAvNZgykczUftxJtNnx3gBRW7A-gnPGPSC1XP594Op0ulKRyNuknBcz6r2Gml8NrVvMF+AdaF9U2yi1ZxWQGuDgDi+orT37tfgDsXzz6Lfe7q7Yp7fZn726o4z8qtOYH7wAygu7pXl4OFOvaramA6tQTNzLdz7IQfL0OkCtC+SiID0kZjpA5qZgq1rTgBACq3CDuWd5Loeq5XB+G7NjKOwoT+RpNP+K7DuBMrwvCQA&php=84&phan=v6-dev&ast=1.1.3)**

### Nested Generic Types

Template parameters can themselves be generic:

```php
/**
 * @template T
 */
interface Repository {
    /** @return T */
    public function find(int $id);
}

/**
 * @implements Repository<array<string, User>>
 */
class UserMapRepository implements Repository {
    /**
     * @return array<string, User>
     */
    public function find(int $id): array {
        return ['user' => new User()];
    }
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUojDgCWAdkgE4BmmAxopAEqKoD2AzsfKxQJ6QBvKJBGgYsCongBXCqTzQiIkamkAjdMVqQq00rXjFW8qmQAmACjLxIAEmJmAlAG5wAX3DgxBOMTToURHJ2JhYOLh5eAB5MCgpMaPZ4CjIAcwAaSABVdkQKAD58giJaLHYQnLyAWUxUZjZObj5IPwxA4NCGiOahZUhvPug4SRk5SFj4xOS0zMqC4WVCBchVDS0dPQMjE3MrcjsHRwAucbiEwWW+kdl5AG0AcmlcinvIAF58yFJEAHds54sjgAuq4+h4PEA&php=84&phan=v6-dev&ast=1.1.3)**

### Alternative: `@phan-implements`

You can use `@phan-implements` if you want to use Phan-specific annotations that don't interfere with other tools:

```php
/**
 * @template T
 * @phan-implements Repository<T>
 */
class GenericRepository implements Repository {
    // Implementation...
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUoyyoAWmAdgLQCWa6KiZ8AzpAEqKoD2TV8nATgE8APLgB8BYOADGWJiwDiDRPyrT2XHnyGQaGeoxYbuvAYMgBvKJBvBgkAJK0D8bFU5kAdN-ABfcEA&php=84&phan=v6-dev&ast=1.1.3)**

## Generic Traits

**New in v6:** Phan now supports generic traits using the `@use` annotation.

### Declaring Generic Traits

```php
/**
 * @template T
 */
trait Repository {
    /** @var list<T> */
    private $items = [];

    /**
     * @param T $item
     */
    public function add($item): void {
        $this->items[] = $item;
    }

    /**
     * @return T|null
     */
    public function first() {
        return $this->items[0] ?? null;
    }
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUojDjwBOmAlvJAEqKoD2AzpfSQJ6QDeUkvoMsAG6YSkdOUbwAPLgB80Ir16oS5YTgAklFI0gBeSAG0AugG5wPPmEu8BqEZmR5IWpMhsKbqAK4AjcQDGkABm3gB2AfDk9GGQmAAm8QAUrigAlABckIL05PFcHkoa8AAWEgC0strIjCb6LtXmSpAAvhbN-B4CJIjw3iSxuAA+Yd7o6F2KSj7+5EGhEVExIeQkkklpBc3NPX0DLqUVVW61AAzGkAD8l5Cj401KbW1AA&php=84&phan=v6-dev&ast=1.1.3)**

### Using Generic Traits

Use the `@use` annotation to specify the template type when using a generic trait:

```php
class Article {
    public string $title = '';
}

/**
 * @use Repository<Article>
 */
class ArticleService {
    use Repository;
}

$service = new ArticleService();
$service->add(new Article());
$article = $service->first();  // Phan knows this is Article|null
echo $article->title;
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=MYGwhgzhAECCBOAXAlqAptA3gKGn6ADgK4BGIq0Ei8yAdgObQAkKiIGAvNAOTcDc2AL7ZsAegBU43OOgABIhAwAlNAQD2EZIjXwAngB4EKdAD5po7KEgwjqdgGU08AG6oMOfNAXLVGrTt0BYWwmRRc3aC5aNAB3OCQ7NEdw4DQACgBKAVCnV1SAWhMwABNitOi423RMrJCwBPRI5jC8tEKAM2R4Kky+PFFRaAAFAAswWmgAa1o1GJhEEeQYJfjjdgAfWiIQEGw0YBG1Znq1tpNWdgEgA&php=84&phan=v6-dev&ast=1.1.3)**


### Combining Class Templates with Trait Templates

```php
/**
 * @template T
 */
trait Storage {
    /** @var T */
    private $data;

    /** @param T $value */
    public function store($value): void {
        $this->data = $value;
    }

    /** @return T */
    public function retrieve() {
        return $this->data;
    }
}

/**
 * @template T
 * @use Storage<T>
 */
class Container {
    use Storage;

    /** @param T $initial */
    public function __construct($initial) {
        $this->store($initial);
    }
}

$container = new Container(42);
$value = $container->retrieve();  // Phan knows this is an int
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUojDjwBOmAlvJAMrwD2ZA5opAN5SSegywBumJPNCKdOqEuX44AJABNsmANzgOXMHFQDMyIdP7oAri0KrIqAwCN05AMaQAZgYB2N+OTpPIAZ3olEACj1MQ0QASgAuSF46clk2U1FIaXgAC3IvAFoAPnl4TEgAXiT9I2VEgF8VRO44P3gDEk9cYVNzK1sHZ1d3TzqJRF4A0PjExLqGz2S0zJyFMtFKyvBuAjgkNCwcfGg4Ay8WWgZMZgAeXCyCIhssLy9IAGEPPPInREF2RL2D32PEZVMarBNGQdM1pC9KORgi1Em1rHZHC43B5IAB9VE2Dw+EgGVyBCFuYLDD6jThTdLZHwMALgpyQonzTiLFTSTFOZ6vQRFV4AdweTwonP8ABYAEyhZRBEKFJJsjlvbJ9cgDIaKLjASAABRSmE8AGsnHQeXdUulIGbdeb2eAgA&php=84&phan=v6-dev&ast=1.1.3)**

### Alternative: `@phan-use`

Similar to `@phan-implements`, you can use `@phan-use` for Phan-specific annotations:

```php
/**
 * @template T
 * @phan-use Repository<T>
 */
class Service {
    use Repository;
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUoyyoAWmAdgLQCuAzopAEqKoD2NAlvCwE4CeAPLgB8BYOADGWGjUgBlRNwBu7cfQDeUSFtr0mrDlz4BucAF9wQA&php=84&phan=v6-dev&ast=1.1.3)**

## Template Constraints

**New in v6:** Template parameters can be constrained to specific types using `@template T of SomeClass`.

### Basic Constraints

Constraints ensure that template arguments satisfy a specific type bound:

```php
class Animal {
    public function makeSound(): string {
        return "some sound";
    }
}

class Dog extends Animal {
    public function makeSound(): string {
        return "woof";
    }
}

class Cat extends Animal {
    public function makeSound(): string {
        return "meow";
    }
}

/**
 * @template T of Animal
 */
class Shelter {
    /** @var T */
    private $resident;

    /** @param T $animal */
    public function __construct($animal) {
        $this->resident = $animal;
    }

    /** @return T */
    public function getResident() {
        return $this->resident;
    }

    public function hearSound(): string {
        // Phan knows $resident has makeSound() method
        return $this->resident->makeSound();
    }
}

// OK: Dog extends Animal
/**
 * @extends Shelter<Dog>
 */
class DogShelter extends Shelter {}

// ERROR: string does not extend Animal
/**
 * @extends Shelter<string>
 */
class InvalidShelter extends Shelter {}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=MYGwhgzhAECCB2BLAtmE0DeAoavoAcBXAIxEWGgDND5gAXRAe3mlQGsBTAZUZoBMAFAEoAXNAh0ATongBzTDjxLJHOoUksARBEbIO43vD6aA3ItwBfLFayhIMACKN5HAB50ORmAhRoFSolJyKhp6JhZ2bkNBUXEpGXlsJWVVdS0Ad0ZGSlNzaCsbOyhoAGEwOmg3Dy84JFR0JLxAsgpqWgZmVjBOHn5hMQlpOX9k3BU1DWhNPUZ03KUCrCwAegAqVZxV6AABD2R8cA9oABVobNrfEE3l23BirgALDhAPSRHcNa3tgDcwN9PVjcAtJfkcACQqCCIPieOhmPKfHb4P5gZAnaBgsB1PyAvLNYJtMKdAD6xOAzEGhHoAkx2JAQneyTBdAeiAgAFoAHyQ6Gw6AAXgxWMuZgWSyUiO24zS6NxARILRC7XC0FkqgAShwoTD4HRhIyUhMWMzWRzuVrebrRXgbPKgq1Qh0WE8-r0jP04kNEnkJctoAAFB5Y6BseCzGAQi06ipBmCRN0xViqB6MPg+vDSyYmtlcnnRrnx6LCa2WaxLZZ+gDyAGkxE4XO5PHxvHSVutNjsqk2YI9nq8ADz1znXW72aD13svDhvLs1SevTA2CvQACi6vVlfVA3iwz4jC10DDFVnfAu9TbG2gXxPPaeU8k-cGCWHV5uRRgAEl4L8yHx59PKkbOc7wXDArCAA&php=84&phan=v6-dev&ast=1.1.3)**

**Test this example:**
```bash
~/phan/phan -n example.php
# Expected error on InvalidShelter:
# PhanTemplateTypeConstraintViolation Template type T of \Shelter must be compatible with \Animal, but string was provided
```

### Constraints with Interfaces

```php
interface Timestamped {
    public function getTimestamp(): int;
}

/**
 * @template T of Timestamped
 */
class TimestampedCollection {
    /** @var list<T> */
    private $items = [];

    /** @param T $item */
    public function add($item): void {
        $this->items[] = $item;
    }

    /**
     * @return T|null
     */
    public function getMostRecent() {
        $latest = null;
        foreach ($this->items as $item) {
            if ($latest === null || $item->getTimestamp() > $latest->getTimestamp()) {
                $latest = $item;
            }
        }
        return $latest;
    }
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=JYOwLgpgTgZghgYwgAgCrALYQM5jhgBwgBNkBvAKGWuQIFcAjAG2AWRjpATGAHsRkAcwhh0WXPgIAKAJQAuZKDABuCgF8KFAPQAqHVR3IAApEJM4kNMl4w0mHHkIkDWignPZsd8Y6LEAwrxMTBDcfAKUNMi6hkYAbnBQyCy4ADyoAHzIOq5RBFDACZYAJMCmXgC8yADaALqqVDQxxgSJ+Falptm5NPTMrOycYfzIcMTEUp0QGPLIcbzApJFRNMVgABbA2AC0GWXT2HXIVVMYqlEajdQxV9SxUCJ0UAKoAD4gdMG33bd9LGwcLg8EbCMAAWV4uAASqEIOBZORvqtzJBcMdkB9gucVtQYLwHoh1shJhstrt9hgvHAvKcZIicTjgLZJiiHMcKlVMUxkK9XshTrtQWIHJIEVliqzcIKRMKJIRZHTlgyGRKLGyThTscrqBplbqGQ8wE8BKrUSpbhoNEA&php=84&phan=v6-dev&ast=1.1.3)**

### Constraints on Functions

Template constraints work on functions and methods too:

```php
/**
 * @template T of Animal
 * @param T $animal
 * @return T
 */
function cloneAnimal($animal) {
    return clone $animal;
}

$dog = new Dog();
$clonedDog = cloneAnimal($dog);  // Phan knows this is Dog

// ERROR: string is not an Animal
$str = cloneAnimal("not an animal");
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBVID2AZpAIIB2AlspulDLKpgE6bJ6QAkmVNd0cZongBXZuTz1g4IiPIBjeJQIT56FYgrVaACm69aASkgBvKJAtDR4yGo1ce29AG5wAX3DhOAEwIBzSABeSHJEAHdIABF-HUNXTjtQ72iA4MTNA3Q9Xz84i2BgSAAFAAseSABrcgIwgGdIeBLKeuao-08CyABRACUegHkegC5IWvhmSnIA1ur4SHKtPi8x5iDbdVDF3QAiWfmJRz5tuPAgA&php=84&phan=v6-dev&ast=1.1.3)**

**Test this example:**
```bash
~/phan/phan -n example.php
# Expected error: PhanTemplateTypeConstraintViolation
```

### Union Type Constraints

Constraints can be union types:

```php
/**
 * @template T of int|string
 * @param T $value
 * @return T
 */
function identity($value) {
    return $value;
}

identity(42);      // OK
identity("test");  // OK
identity(true);    // ERROR: bool is not int|string
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBVID2AZpAJYB28APgM7wBOFA5lDLKpvZsnpACQA3TOgCuiVnHqJ4I+uTytg4IiPIBjeKQLzSAE0SVS8AJ4AKQcLEBKSAG8okR1Jlz+Q0YgDc4AL7hwegaaJqYALABMVp6OMZDAwJAA8gDSAfqGIQBESHSZUY7xSamBGWYM1tExhQCiAEq1ibUAXJAARgQE6GQ0kOQE8GSUtAzM4EA&php=84&phan=v6-dev&ast=1.1.3)**

### Intersection Type Constraints

**New in v6:** Constraints can require multiple interfaces:

```php
interface Serializable {
    public function serialize(): string;
}

interface Validatable {
    public function validate(): bool;
}

/**
 * @template T of Serializable&Validatable
 */
class Processor {
    /** @param T $item */
    public function process($item): string {
        if ($item->validate()) {
            return $item->serialize();
        }
        return '';
    }
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=JYOwLgpgTgZghgYwgAgMrWHANsAXnAIyxQG8AoZS5ABwFcjgFkZaQExgB7EZAZw2x4IACgCUALj5gooAOYBuMgF8yZUJFiIUANUEATOGELFk5KjXo4mLNh27IAbvsMiJyAp05ZFKsgHoAKgCKAOQAAUgAW2osF2QAFWROGDQBHHwiCAAyXRwDI0yQvzIEWN5eZAAFKE4kcs4oUwoqQNCw6jgoOEiE5AASYCjkAOLzOgZrVnYuHmoaut5hAai3Xmk5JvNzYBSlwYhIgFoAPic8lzFRTa2bqAgwWigeZYOT-hlBXFdFG8oVX+QdweT2QAHJQT8qCoVEA&php=84&phan=v6-dev&ast=1.1.3)**

## Variance Annotations

**New in v6:** Phan supports variance annotations to enforce proper template usage in read/write positions.

### Covariant Templates

Use `@template-covariant T` when the template type is only used in "output" positions (return types, readonly properties):

```php
/**
 * @template-covariant T
 */
interface Producer {
    /** @return T */
    public function produce();
}

/**
 * @template-covariant T
 */
class ReadOnlyBox {
    /** @var T */
    private $value;

    /** @param T $value */
    public function __construct($value) {
        $this->value = $value;
    }

    /** @return T */
    public function get() {
        return $this->value;
    }

    // ERROR: covariant template in parameter position
    /** @param T $value */
    public function set($value): void {
        $this->value = $value;
    }
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGSC0BjAewDdMAnAS0wDt5IAVKEYccmxUgM0z0UgAVSBACYBXHqUgBvKJFmgYsUongjSVetGazZqEQCN05PJA4iqeeOQLrUg0TwAUASgDc4AL7hw8xnCRosXEISCmpaBk1wPCwAZxjIACVETCEAeSp0AE8AIQIADykZOTA4EI0mIshbchIkSAASEnQRRDdK+ThUMkxkDUbMZt4K7Sr9Q2NTc0trSAB9WcIqGPhSMXgHfsGnQpGR+vgAC3IYnAA+JpbIAF4Gi9bKz3aSxWVVdTpIkd0DIxMzCys6gA5spnDtdrIlCo1A1Dscznc3CNHiNgMBIABRBIJVIJABckGCZEoNEg-gw2F4rCq3WQynYVQIMXI0yoTwUXVIPT6d0+2m+4z+U0BkBioM2LScBKIBHIQnBEP2RxO5wGlxuEvuyI84CAA&php=84&phan=v6-dev&ast=1.1.3)**

**Test this example:**
```bash
~/phan/phan -n example.php
# Expected error: PhanTemplateTypeVarianceViolation
```

Covariant templates enable safe subtyping:
```php
// With Producer<T> being covariant:
// Producer<Dog> is a subtype of Producer<Animal>
// This is safe because you can only read from it
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PTAEHUEsBcAtQAoCcD2ATArgYwKZIDwAqAfKAEY6QB2A5qFigG4CGSkzV0AXAFAiKpMuAgBEUNUpADOoZqCkYy0AJ4AHHKBQAzAemx58AQSqQAtswA2xPmEKxpoB1OZaNFLMwxSNylBnocmlQWyqBIOMxooFqopo7QPEA&php=84&phan=v6-dev&ast=1.1.3)**

### Contravariant Templates

Use `@template-contravariant T` when the template type is only used in "input" positions (parameters):

```php
/**
 * @template-contravariant T
 */
interface Consumer {
    /** @param T $value */
    public function consume($value): void;
}

/**
 * @template-contravariant T
 */
class Sink {
    /** @param T $value */
    public function accept($value): void {
        // Process value
    }

    // ERROR: contravariant template in return position
    /** @return T */
    public function produce() {
        throw new Exception("Cannot produce");
    }
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGSC0BjAewDt4AnTAN01IEtMTIAVKEYcGkxUgM0z0UgBhYgGcArsi6QA3lEjzQMWKmqZkTSABIq6MQNZz5qMQCN0NPJG5iieeDWKRCRcZIAU2zLsQBKAFyQFAQ0ACYA3OAAvuDgiixwSGhYuM5klNR0DMzQbHhYIiKQAMocANYyhpCKcCrk6oxaOno5lcZmFlY2dg5EkHz8qPAeTb4BQaEV8lNTwMCQAAqkBPwFgV56ldGVs5AAogBK+wDy+wGp5FS09PCQiRjYAhyQpIjwYqS9qAQiNPbE22A4C83h8NAZpm1zJZrLY-p8liExPw3D5JtMpvAABZLADukCIiDxuwAHgM4W4AESCehEAg3VAIpGICk+CLTaLRIA&php=84&phan=v6-dev&ast=1.1.3)**

**Test this example:**
```bash
~/phan/phan -n example.php
# Expected error: PhanTemplateTypeVarianceViolation
```

Contravariant templates enable safe subtyping in the opposite direction:
```php
// With Consumer<T> being contravariant:
// Consumer<Animal> is a subtype of Consumer<Dog>
// This is safe because you can only write to it
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PTAEHUEsBcAtQMIHsB2BnArgWwKYCcAeAFQD5QAjHSFAc1AGNVo8BDANxb0hZWgC4AUCESpMuQgEEUkLCwA2ZSGlAtQmctACeABxygkAMxHps+AgBEkNEkLBFYS0I7QsDeyvRYY0ezUgwMPPoocpqgAO5c0HrQSE7QAkA&php=84&phan=v6-dev&ast=1.1.3)**

### Invariant Templates (Default)

Without variance annotations, templates are invariant and can be used in both positions:

```php
/**
 * @template T  (invariant by default)
 */
class Box {
    /** @var T */
    private $value;

    /** @param T $value */
    public function set($value): void {  // OK: can write
        $this->value = $value;
    }

    /** @return T */
    public function get() {  // OK: can read
        return $this->value;
    }
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBVKQAUAlgHYBumATsZqfJAEYCekAJogGaYCu68ASighg4AMZYAzpMgAhAPYAPSAG8oBSKBixKVPNFEbIqGpRwASSuh6IA3OHUEtcVNUzJ9lzNcQHHxnkZ0YjFITh5SMXhieVJISUR4Qi8fAQAuSHJ5YjZVJ2BIAHkAaQyxOkgAdxokfyNzeAALYkkAWgA+KxtIAF5IFJt7IwBfByNnWCpEnio4-BF-VEDg0PDI6NjIAHNEwgE8zQKSsoqpzDY6jSn4GbiG5rbO70H-UdGgA&php=84&phan=v6-dev&ast=1.1.3)**

### Variance and Properties

**New in v6:** Phan enforces variance rules on properties:

- Covariant templates are only allowed on `readonly` or `@phan-read-only` properties
- Contravariant templates are not allowed on any properties
- Arrays and other mutable structures require invariant templates

```php
/**
 * @template-covariant T
 */
class Container {
    /**
     * OK: readonly property with covariant template
     * @var T
     */
    public readonly $value;

    /**
     * ERROR: mutable property with covariant template
     * @var T
     */
    public $mutableValue;

    /**
     * ERROR: even readonly arrays are invariant
     * @var array<T>
     * @readonly
     */
    public $items;
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGSC0BjAewDdMAnAS0wDt5IAVKEYcPLAZzcgGECbNyqiUpADeUSBNAQJMmAHkA0gC5IpRJgAmvdAE9IqUgVRD4egO7l4AC0iESFarSRosScbLj367iUx+oAVwAjdHI8VXUtKl1IABISdADEAG5wHykfaEgAUQAlXLlclWQA+EwQxH1DY1JTSAtrW2IyShpIZwxsREyYWC8GGV9mQcCQsLiSsoqANUxElLTBjMGsvIKiyEQiRCoIzW09MlJMHU4ySoF7VvgezzJII5OAHjoAPlvYNX3onR7hmVGoXCsUsKDYqQAvuAgA&php=84&phan=v6-dev&ast=1.1.3)**

## Function Templates

Templates can be inferred from function and method parameters, useful for inferring return types.

### Basic Function Templates

```php
/**
 * @template T
 * @param T[] $array
 * @return T
 * @throws InvalidArgumentException
 */
function first(array $array) {
    if (count($array) === 0) {
        throw new InvalidArgumentException("Array is empty");
    }
    return reset($array);
}

$users = [new User(), new User()];
$user = first($users);  // Phan knows this is User

$numbers = [1, 2, 3];
$num = first($numbers);  // Phan knows this is an int
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUoyyqYBOmyeA2gLqQAkpZAngXCYvAK4kB2erCABYkA9gHcAzpACSPAG6Z0ASwAmAQRIBzTskQ94AUQAeAY0Sp4SkTwLBwAM048Tl65HtKSE+AApGmJnp-JgBKSABvKEhopXtIHxMRJ18GEmYwgF4syAAGMMjowsL4YXFIHkQxGXlFVQ1tXX1jMwsrHh8AIg1mSCUpFAsmDpCAbijogF9xyHYuXhnECQ4fVPSxqfA6TiWvSAzISgqqgFUdnxCAGnLKyFPEEnPqMa2dvfdPbxXt+4lR6OBgJAAAqCTB8ADWPHEUhKfV6UjuJHAmx4OgARj83pQAIxXABMVwAzE8UTo3h4vClUcgMV4-pAAcDQRCoZJILCpHCwb19OAgA&php=84&phan=v6-dev&ast=1.1.3)**

### Templates from Closure Return Types

```php
/**
 * @template T
 * @param Closure(int):T $callback
 * @return list<T>
 */
function generate(Closure $callback, int $count): array {
    $result = [];
    for ($i = 0; $i < $count; $i++) {
        $result[] = $callback($i);
    }
    return $result;
}

$strings = generate(
    function(int $i): string {
        return "Item $i";
    },
    5
);
// Phan infers list<string>

$objects = generate(
    function(int $i): stdClass {
        $obj = new stdClass();
        $obj->index = $i;
        return $obj;
    },
    3
);
// Phan infers list<stdClass>
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUoyyqYBOmykAwugPYDOAriYgBQCWAdvAJQBcukACQBjTOnQAjTMIDWBOM3hMOkdGzrwAPLgB8BYOABmDDsPhsaKgOaIOiMkhbV6TRENHipsgDSRO8dxoTHl5IUjIAT0gAbyhIeMFmRnQAgF5IAG0AXQBuOPjDGhJIFkE2SHSABhyhcs1A4JqygGpm7hj8+K7ExGT4bIr3MUlpGVK2bjyuyABfTsVlISSGFLy58EENEk4rOkGbOwdWTuNTc0t2Llq+SC2djunphZIVACIASSQKMteprpnvJ0AKzgSbgYDASAABQAFpgVJxDPY9moNJo7hwrHoNjQJAArRBmPbpA72bDHaanMwWDiXAJlG4aAAm1EwdD2sUe3VxeMGdgA7rd4CysOyWGCuQkeQBaHScJmIAAegzKfy5zxUgh5atmgOmAGZQXkIdC4QiOEiSCj1Fpmaz2XogA&php=84&phan=v6-dev&ast=1.1.3)**

### Multiple Template Parameters

```php
/**
 * @template TKey
 * @template TValue
 * @param iterable<TKey, TValue> $items
 * @return array<TKey, TValue>
 */
function toArray(iterable $items): array {
    $result = [];
    foreach ($items as $key => $value) {
        $result[$key] = $value;
    }
    return $result;
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUDSiAnlDAihtongGqboCuipcqmATpspAJZKcAjdIgA8BYgBpa9JgD5IAEj4oAzi1jtE8BuwB2kDpyJjCRKbjqNEs0sHAAzBroDG8HgHt98dwEF2RgAplQWFFZWQVAEoALgN-TCJIAG8oSDSFTRUGdHhIAF5IAG0AXQBuVLT7d01MZwALSAClJAiDFUUAa2J8+QUANxlESOSKtLGMxCycwoUuomL8xQGrcrHIAF9RzW09RUzs+HLNoA&php=84&phan=v6-dev&ast=1.1.3)**

### `class-string<T>` Type

Create instances of classes based on their name:

```php
/**
 * @template T
 * @param class-string<T> $className
 * @return T
 */
function create(string $className) {
    return new $className();
}

$user = create(User::class);      // Phan knows this is User
$article = create(Article::class);  // Phan knows this is Article
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUoyyqYBOmykAxlgM40C0N8JAlgHYDmAPLgHyQASapjoA5cogJwSieAFcSbPAWDgAZnLaV4LAPZLKM7IgAUTVp0HCxEgJSQA3lEguZ8xZDaIA7ldo1xZFNbAG5wAF9wcAE5GkQSSABeKiMkEwBVOJIALmzrGlCXIshgYEgABQALTCUAazZdbxpIeEqWZvbITPjo0h1qRCSUxGMTAEESfvREXPzCkrKqmsh6xubWzs6JqckgA&php=84&phan=v6-dev&ast=1.1.3)**

## Utility Types

**New in v6:** Phan supports several utility types for more precise type definitions.

### `key-of<T>`

Extract the key type from an array shape or generic array:

```php
/**
 * @param key-of<array{foo: int, bar: string, baz: bool}> $key
 */
function processKey(string $key): void {
    // $key can only be 'foo', 'bar', or 'baz'
    echo $key;
}

processKey('foo');  // OK
processKey('bar');  // OK
processKey('qux');  // ERROR: 'qux' is not a valid key
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAHAhgJ0QW0gawKYE8C0A9gGYA8KquA3sYYQFyQCWAdgC4A0kARiowM5tkrAOZdeAL0bc6AGwC+APkgASHLighg4YgFcWAYzZNCLSPGSED2fvwDSeABSDhLEavUBKRgDdCTABNIKihIMOBgDzxIA0QzU1lcHmxIAHJaQlSuVN5kLMhCZDTJVNCw7AMAC0Io3ABucHlwcAsrG3sndLpUzzrwyIB5OxbLa1sHXEcclB6+yAjIIZG28c6AR10AD1n+yABRACUDgYPGVI3t5n5IFkI2SERIH0RZQKw8cCA&php=84&phan=v6-dev&ast=1.1.3)**

**Test this example:**
```bash
~/phan/phan -n example.php
# Expected error: PhanTypeMismatchArgumentProbablyReal
```

With generic arrays:
```php
/**
 * @template T
 * @param array<string, T> $array
 * @param key-of<array<string, T>> $key
 * @return T
 */
function getByKey(array $array, $key) {
    return $array[$key];
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUoyyqYBOmykpZAngDwDO8JAlgHYDmANHgHyQAkVTNQJxiZCgGtE1ALQB7AGa0hdRiw7dcPPv2kjocEongBXEqzwFg4RadYBjeM3mX2JgELUA0jIAUqgKq3HoyAJSQAN5QkLHGZhZBJDQA2qHUALoA3OAAvuBAA&php=84&phan=v6-dev&ast=1.1.3)**

### `value-of<T>`

Extract the value type from an array shape or generic array:

```php
/**
 * @param value-of<array{age: int, name: string}> $value
 */
function processValue($value): void {
    // $value can be int or string
}

processValue(42);      // OK
processValue("John");  // OK
processValue(true);    // ERROR: bool is not int|string
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAHAhgJ0QW0gN0QGwK4CmAtAPYBmAPCqgJ4DeiA5gQFyQCWAdgC4A0kndK0gBnbsi6MAvgD5IAEmz4CUEMHBk8nAMbd2JTpHjIS2giJEA1XIQAUimwQCUbTCXYATSHSiQ-wYAUlQkhtREMAIwIOHkgSZFFxSXApcHBjU3MrR1sAFgAmJwBuP1LIAMgAeQBpdJMzC2tlWwAiACkSAAtOFuL-QJq6zMac8UI+0oqAUQAlGcqZtgiSEhwOEQESbhjuAB8xCU5GcCA&php=84&phan=v6-dev&ast=1.1.3)**

**Test this example:**
```bash
~/phan/phan -n example.php
# Expected error: PhanTypeMismatchArgument
```

### `int-range<min, max>`

Define an integer range with inclusive bounds:

```php
/**
 * @param int-range<1, 100> $percentage
 */
function setOpacity(int $percentage): void {
    echo "Opacity: $percentage%";
}

setOpacity(50);   // OK
setOpacity(100);  // OK
setOpacity(0);    // ERROR: 0 is below minimum
setOpacity(150);  // ERROR: 150 exceeds maximum
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAHAhgJ0QW0gSwHYBcC0q2A5gKYA8AjADSSUAM9AfJACTynIDGpeiZUEMHAAzAK7YuuTAHtskAM6lcAeSRdMuAJ4AKHLjYduvXP1IBKAFyQAbjMwATSAG8okd6S4ALGZABEaoga2tbsnDx8ZACkfgDc4AC+4OBKquqaugCs9Oax7pDAwJAqANIpyoHBugw5eQVFpeVpQRk6tfn1kACiAEo9Kj3W9FgKkABGpAA2MgDukGg4mGhiaE2VrZTZue6F3X0D1pvDpAAePKQOo2iIJ0sr4EA&php=84&phan=v6-dev&ast=1.1.3)**

**Test this example:**
```bash
~/phan/phan -n example.php
# Expected errors: PhanTypeMismatchArgument for out-of-range values
```

Ranges work with literal values:
```php
/**
 * @param int-range<-10, 10> $offset
 */
function adjustPosition(int $offset): void {
    // $offset is between -10 and 10
}

adjustPosition(-5);   // OK
adjustPosition(11);   // ERROR
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAHAhgJ0QW0gSwHYBcC0q2A5gKYA8+AjAAwA0ktAfJACQD2AZpwM6m5QQwcJwCu2AMa5M7bJEQATAFaieuAArsemabIAUOXGy69+ASgBckAG7tMCyAG8okV8GDHufI5h6QARvwA7qSkctQ08tgOtOAAvuDgiipqmtq62Hr4AKxmANyukO6QAPIA0knKqhpaOjKZVFT5hcUAogBK7SXt4EA&php=84&phan=v6-dev&ast=1.1.3)**

### `positive-int` and `negative-int`

Shortcuts for common integer ranges:

```php
/**
 * @param positive-int $count
 */
function createItems(int $count): array {
    // $count must be > 0
    return array_fill(0, $count, null);
}

createItems(5);   // OK
createItems(0);   // ERROR: 0 is not positive
createItems(-1);  // ERROR: -1 is not positive

/**
 * @param negative-int $debt
 */
function recordDebt(int $debt): void {
    // $debt must be < 0
    echo "Debt: $debt";
}

recordDebt(-100);  // OK
recordDebt(0);     // ERROR: 0 is not negative
recordDebt(50);    // ERROR: 50 is not negative
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAHAhgJ0QW0vA9gZwJYAueAbgKYC0eAdgZACQDGWArjVCMOAGasNFZVIDZKUQFSASXFocACmq1GLGgEoAXJBSoAnpADeUSEeDB6TVrTTMctAEalIAPkgAGQ0ZEFmyQVsTaAfS48ABsQ2RcAGjNlAmiqZjCVAG5wAF9wcGFRcSlSGVkAVhSjSBNIAHkAaSyRMUlpORcS41MAUQAlDoqOjRdIPBxIKixabHwiMlqchvy5cgBGFvLO7t7IRYGhkbHcQhJSTNAIaDgkVAwqUgBzMQPKGnoAE1JbAnZOHio+PAFIESYyCeABFXgR5I86C83upIMQsHgnvp3GVTFCwZArDZIPZIAAeVwo0gMAAWWEgACJQW8NOi3hTUhlwACsEDqeDFi5mslWpUaiy2WCIi1Sisuj0+lthqNhjc7lMBSChYVuaVUZBVhLICqpTtZbdJocgA&php=84&phan=v6-dev&ast=1.1.3)**

**Test this example:**
```bash
~/phan/phan -n example.php
# Expected errors for invalid values
```

## Rules and Limitations

### Constructor Rule

All template types of a generic class must be inferable from the constructor parameters:

```php
/**
 * @template T
 */
class Box {
    /** @var T */
    private $value;

    /** @param T $value */
    public function __construct($value) {  // T is inferred from $value
        $this->value = $value;
    }
}

// OK: Phan can infer T from constructor argument
$box = new Box(42);  // Inferred as Box<int>
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUojDgDGWAzmZAEID2AHpAN5SSugywBumATntEVatUPAJbccAEm7oArogDc4FmzBxUvTMn7TMcxAJWRUsgEbpRxSADNZAO2LxRNO5AD6b4i7LwesxwAUuvoAlExswPyilKJ21og8PIgAJjY8NNrB8kZCkJLwABbRALQAfDLykAC8eRWKRgC+4E3gwJEA8gDSAFyQAAoFmK7EQ5Cx8Xy4aRmQXnY+fo40fLwA5rLIiHbw4JKm9NWQdogA7tT0AQAsAEwhChGQAJJxCUmpmJS0dAA8sfCl4CAA&php=84&phan=v6-dev&ast=1.1.3)**

If a template cannot be inferred, Phan will emit `PhanGenericConstructorTypes`.

### No Generic Statics

Constants and static methods on a generic class cannot reference template types:

```php
/**
 * @template T
 */
class Container {
    // ERROR: static property cannot use template type T
    /** @var T */
    private static $default;

    // ERROR: static method cannot use template type T
    /** @return T */
    public static function getDefault() {
        return self::$default;
    }
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUojDgDGWAzmZAMID2AdvJgJZ2IBOkA3lJL8MJACiAJWEB5YQC5IZRvCbFIqNjVTt4AT0jFMdOjXiQArmUSQkaLDk1q8PPmDgA3TB1zQivXsqYucs7AVIABIAE0QAM0wjdHgAbnB7SH4hUQlpAPlFZER4AAsaUO1dfUMTMwsMbAqNW3wvZMdYNlyjNjo8DyTUIwAjdCDMoIijOmJ5ekgAc1yAEUjo2IAKAEouJIaW+DaO03QIyUkwhZj4pIBfcEugA&php=84&phan=v6-dev&ast=1.1.3)**

**Workaround:** Use function-level templates on static methods:

```php
/**
 * @template T
 */
class Container {
    /**
     * @template U
     * @param U $value
     * @return U
     */
    public static function identity($value) {
        return $value;
    }
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUojDgDGWAzmZAMID2AdvJgJZ2IBOkA3lJL6BL0EwEKDNkSQAqjyFxUmNpmRTIAEgBumdAFdEM3sLaJ42tnSn7oRQZFTaARuibFIZRvGeQAZtrrEP9JBMACaIDEzwAJ4AFBpaugCUXJaCRiZmapo6iADc+gC+4IVAA&php=84&phan=v6-dev&ast=1.1.3)**

### Template Parameter Count

When using `@extends`, `@implements`, or `@use`, the number of template parameters must match:

```php
/**
 * @template T
 * @template U
 */
interface Pair {
    // ...
}

// ERROR: Missing template parameter
/**
 * @implements Pair<int>
 */
class BadPair implements Pair {}

// ERROR: Too many template parameters
/**
 * @implements Pair<int, string, bool>
 */
class BadPair2 implements Pair {}

// OK: Correct number of parameters
/**
 * @implements Pair<int, string>
 */
class GoodPair implements Pair {}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBUowIobaKQCqBw4AlgHZIBOAZpgMZkAKmNjkA3lEjDgwSADpJ4AL7hwoyAFEASsoDyygFyQAsjQDO++gHNISNFhypMjTMkRN5YAnBoWUiBvsjdeAHnp4AD4qcDYsQ0gAIUwAE18+NwwPLx8ePn5ZeTEVdS08AHsCyGRMOgBPM2JLMmtbeyZ9JwhoV3d7VISAhgAaSH14RhM+gCMi9BDoanDMSJj49IAmSCT0FPhvBIEshTUAaW0AYQLGRkQ2eEg6AFdkEcQ+AuZIOrsHB6bQFsJV9c307rwPoDIZ0YyTEDTCLeADiRQWvBW7U8GzSiMy4CAA&php=84&phan=v6-dev&ast=1.1.3)**

Phan will emit warnings for mismatched parameter counts.

### Template Bound Inheritance

Child class templates do **not** automatically inherit bounds from parent templates:

```php
/**
 * @template T of Animal
 */
class AnimalContainer {}

/**
 * @template T  // This T is NOT constrained to Animal
 */
class SpecificContainer extends AnimalContainer {}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBVID2AZpAIIB2AlspulCMOAMZYDOrZVN6AwgefEyVyiAE6QA3gF9w4UBGhwkaLDnyRgwPAAtKHfHsgA5APL4m-VvFFCRAE0jwCnarXqMWmdpADKqREyURJRMfAK2YpCIAB5I5HYcFK68-ILCkdLgQA&php=84&phan=v6-dev&ast=1.1.3)**

**Workaround:** Explicitly redeclare the constraint:

```php
/**
 * @template T of Animal
 */
class SpecificContainer extends AnimalContainer {}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGTIBVID2AZpAIIB2AlspulCMOAMZYDOrkAyqok5UZSYBhAuXiZK5RACdIiAB5JyAEw4VqtEWIlTZAbwC+4IA&php=84&phan=v6-dev&ast=1.1.3)**

### Variance and Nested Structures

Arrays and other mutable nested structures are always treated as invariant, even with variance annotations:

```php
/**
 * @template-covariant T
 */
class Container {
    /**
     * ERROR: arrays are invariant, even with covariant template
     * @var array<T>
     */
    public $items;
}
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=PQKhCgAIUgBAXApgWwA4BsCGSC0BjAewDdMAnAS0wDt5IAVKEYcPLAZzcgGECbNyqiUpADeUSBNAQJMmAFEASgoDyCgFyQypTAE9OZRJAEkK1eABpIiIoiqQA7uXgALSIROUakJGixJxsnAmmqTaOgA8dAB8ARJMsagArgBG6OR4kAAkTihsANzgAL7gQA&php=84&phan=v6-dev&ast=1.1.3)**

This is because arrays are mutable in both directions (read and write).

### Performance Considerations

Phan's generics implementation is designed to minimize false positives while maintaining analysis speed:

- Template validation is lazy (only when instantiated)
- Constraint checks are cached
- No additional analysis passes required

For best performance:
- Use constraints judiciously (only when needed for type safety)
- Avoid deeply nested generic types when simpler types suffice
- Prefer concrete types over templates in hot code paths when type safety isn't critical

## Example: Complete Generic Data Structure

Here's a complete example showing many generic features:

```php
<?php

/**
 * Generic collection with type constraints
 *
 * @template T of object
 */
class Collection {
    /** @var list<T> */
    private $items;

    /**
     * @param list<T> $items
     */
    public function __construct(array $items = []) {
        $this->items = $items;
    }

    /**
     * Add an item to the collection
     * @param T $item
     */
    public function add($item): void {
        $this->items[] = $item;
    }

    /**
     * Get all items
     * @return list<T>
     */
    public function all(): array {
        return $this->items;
    }

    /**
     * Get first item
     * @return T|null
     */
    public function first() {
        return $this->items[0] ?? null;
    }

    /**
     * Map to a new collection
     * @template U of object
     * @param Closure(T):U $mapper
     * @return Collection<U>
     */
    public function map(Closure $mapper): Collection {
        $result = new Collection();
        foreach ($this->items as $item) {
            $result->add($mapper($item));
        }
        return $result;
    }

    /**
     * Filter items
     * @param Closure(T):bool $predicate
     * @return Collection<T>
     */
    public function filter(Closure $predicate): Collection {
        $result = new Collection();
        foreach ($this->items as $item) {
            if ($predicate($item)) {
                $result->add($item);
            }
        }
        return $result;
    }
}

class User {
    public function __construct(
        public string $name,
        public int $age
    ) {}
}

class UserDTO {
    public function __construct(
        public string $name
    ) {}
}

// Create a collection of users
$users = new Collection([new User("Alice", 30), new User("Bob", 25)]);

// Map to DTOs - Phan infers Collection<UserDTO>
$dtos = $users->map(fn(User $u) => new UserDTO($u->name));

// Filter users - Phan infers Collection<User>
$adults = $users->filter(fn(User $u) => $u->age >= 18);

// Type safety: this would error
// $users->add("not a user");  // ERROR: string is not object
// $users->add(new stdClass());  // ERROR: stdClass is not User
```

**[Try this example in Phan-in-Browser →](https://phan.github.io/demo/?c=DwfgDgFmBQ0PQCoHQAQJQcQKYDssCcBLAYxWIHsAbSrYgF0PJxQHdC6IU6BPMLMpgGc6+AIaEcdQamRoUAATpYAtmEqilKACopyAM10AjAFa06MuNGLrBglAGEqNeo2YBvVCi+J08gG6i+CiUhMLAWgB8aJZeXmBEAZoAJOwqggDcsLEoPp6xvmCBosrBoXThUSlKytLZ0XkoYACuhiGkek04LkwoAPq9FDjC+E30ABSBYtwoVWkoALwoANoAugCUKB51sUkcoQC0Eak1CzPHGQ0AvlmxuXXoAIIAJk8ooszHXORcEPwU1GZXA05PJCmISjpZspgQgYrFmq0SCgOl0GD1RC8xlC1gAuFB+ciEV5bbYzPaCQ7nVanKGZbLXBp3bLobB0N7UFDnGEKfBYOhNfDMEJhSIwuFxFptZGdbrMUTUMa4t74KabYGxXn8wVkiAHI7VC70m7eJDc1nIwj4YSc6rc+SagXMLQAHxwTWoYoaCKlKNlFqtdEVatJKAd2t2uop+rSSwADCsUCAQCg3dQ6bEGdkmfkUABZURgL5vFNYFgCAGyu3VNQafgAVV0BnIJjMdrBxQclHIggFWDGWlxDaSygLfHwdrDzEcFbROGAdYinuy3qRvtnKBHYDG9i7Pd5M03Y6V0+c65J2ySvJ7lDZizwZZPgKYivT2z05F5omInCx5MpBreOxsWDEMdivd06EODEnixQ8CCxY41jWV86muENJxmcCbxQzNblNe4UAAMUIG8CBtNI2yKEod27Xt+1xQxyCoGZ4iwJ4SFrCc+UdBwnCfOdRXucVGklVcZXXPQSKUfBt13XsWN5djiFrY8+L9c86kvLBr1vEsHzU2cX3VLx30-b8UF-SN-zmUQgMQkDQM5AwsVYpTawQ6okIcxywO0iCoMxbEUJDNDSVC7YMK0nScOgBlrFsuw60EMiNJXdpxNcPoBiEERRkDYyRMRUhhgkABzGYcGKLAABoCrSzlJBmURSqwBoNjca44psRLkvwAARLQAHlvPqtdMv6QZhjysY6tE4qRDKiqqrazZOtgOA4AcT9NFEctT0y-QUCaXrpCSY6CDsO9S14mdXDGJZ7xQJL4IAIgeNosBe6qUAAZljNZvse56ZJegAhZsvpQAAmABWNZ1kyeBNvzQs6G+AbBrsfYUAABQgd4Gr0C6bv2ph516jHFySJ40cumZzqtQ5NzGPQcDGYH6Y2eYoiBimhqxJpDkq5QsCQxGNqIqSyIZrHcfxj4cCJq0Sf48mCCpjEILps6TsOSTSJk1n2d6zmFkqQWIma-gIkWABGAAOZD1s2rReH4QRRCJng8XJVhyHdV4CHwD8kfp3XLcxF6cHINldoZl7kO8TaAFEACVU8G1O8RKnBytCFMY6MUx6FDnWLoCmDHuEJ4dwSxVE5yFP08z7O6Br7rOTsaO2WB6AgA&php=84&phan=v6-dev&ast=1.1.3)**


## Further Reading

- [Phan Issue Types](https://github.com/phan/phan/wiki/Issue-Types-Caught-by-Phan)
