A large mature PHP code base is very unlikely to pass analysis cleanly. This tutorial is meant to provide some guidance on how to get started analyzing your code and get to a place where static analysis is actually useful.

# Getting Started

The process of starting analysis looks like

* Get Phan running
* Set up your environment
* Start doing the weakest analysis possible and fixing annotations
* Slowly ramp-up the strength of the analysis

## Get Phan Running

The first step is to get Phan running. Take a look at [the README](https://github.com/etsy/phan#getting-it-running) for help getting PHP7, the php-ast module and Phan installed.

If you have PHP version 7 installed with the [php-ast](https://github.com/nikic/php-ast) module and your project uses composer, you can just run `composer require --dev "etsy/phan:dev-master"` to add Phan to your project with the binary available in `vendor/bin/phan`.

