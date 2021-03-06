When running Phan on more than one core, you may encounter a problem whereby an issue will go undetected until a seemingly random point in the future. This article attempts to explain (but not forgive) this odd behavior.

# Phan On Multiple Cores

Analyzing large code bases can be painfully slow. As such, Phan provides the ability to run an analysis on many cores. Because of the very large amount of random IO that takes place during analysis and because of the weak support for threading in PHP, Phan takes the approach of first parsing all code on a single core and then forking off to different processes in order to analyze subsets of the code base.

The reduction in time required to analyze a full code base is significant, but it comes with a pretty major trade-off.

# File Ordering Affects Analysis

At runtime, PHP reads code on-demand in an order defined by the initial file that's executed and your `require`/`include` statements or by how your auto-loader is triggered. Because PHP can be very weakly typed and because file ordering will matter when trying to deduce types, you're required to define an ordering for files when passing them into Phan.

Consider the following four files.

```php
// A.php
class C {}
```

```php
// B.php
$v1 = new C;
$v1->property = 'string';

$v2 = new D;
print $v2->f($v1);
```

```php
// C.php
$v = new C;
$v->property = 42
```

```php
// D.php
class D {
    function f(C $p) {
        return ($p->property * 42);
    }
}
```

Executing this code via `php C.php` (with a sufficient auto-loader) would give you no problems. If, however, this code was to be analyzed in the order `A.php`, `B.php`, `C.php`, `D.php`, Phan would emit an issue from file `C.php` explaining that you're attempting to assign an `int` to a property with type `string`.

Now consider a different ordering. If you analyzed the code with files in the order `D.php`, `C.php`, `B.php`, `A.php`, Phan would emit an issue for file `B.php` explaining that you're attempting to assign a `string` to a property of type `int`. Bummer.

# File Ordering On Multiple Cores

The file ordering issue is complicated enough on a single core, but now consider what happens when Phan is asked to run on multiple cores.

When Phan runs on `n` cores, it splits the work as evenly as it can among them. If again we wanted to analyze the four files given above, but this time on two cores, we might split up the work as follows.

**Core 1**: `A.php`, `B.php` | **Core 2**: `C.php`, `D.php`

When analyzed in this way, Phan would emit no issues, given that it can't see the conflicting types between `B.php` and `C.php`. This issue would happily make its way into your code base and sit hidden lying in wait for the right time to attack.

If at some point in the future, a totally unrelated file `E.php` is added, Phan might split up the work between the two cores as follows.

**Core 1**: `A.php`, `B.php`, `C.php` | **Core 2**: `D.php`, `E.php`

Now that both `B.php` and `C.php` are on the same core, the trap is sprung, and you'll get an issue emitted for a bad type assignment from `C.php`.

If you see large inconsistencies between the analysis results with multiple cores (e.g. on 2 cores vs 3 cores),
then setting the config setting `consistent_hashing_file_order` to true
may make the issue sets on multiple cores a bit more predictable.
(But if you want/need Phan to parse directories in a certain order, you can't use this setting)

# What To Do About It

This limitation of PHP and Phan is a big bummer, and there isn't much that can be done to make it better.

If you find yourself getting hit by one of these, you have a few options.

* They're pretty rare, so fix the issues that mysteriously fire as they come up.
* Add a `@suppress` annotation to inhibit the issue you got hit with and admit that nothing is perfect.
* Switch to using a single-core and a stable file ordering.
* Make everything in your code-base strictly typed.
  (E.g. if phan infers the wrong type for a function parameter when there are 3 cores,
   then add an `@param int|false $x` annotation if you know those are the types expected for that function parameter. Or add real types.)

None of those are great, but this is the world we live in. We wish you the best of luck.
