# Generic Decorator

[![Build Status](https://secure.travis-ci.org/JeroenDeDauw/GenericDecorator.png?branch=master)](http://travis-ci.org/JeroenDeDauw/GenericDecorator)
[![Latest Stable Version](https://poser.pugx.org/jeroen/generic-decorator/version.png)](https://packagist.org/packages/jeroen/generic-decorator)
[![Download count](https://poser.pugx.org/jeroen/generic-decorator/d/total.png)](https://packagist.org/packages/jeroen/generic-decorator)

A builder for generic and type safe PHP decorators.

## Usage

This library provides the `DecoratorBuilder` class, which follows the
[Builder pattern](https://en.wikipedia.org/wiki/Builder_pattern), and
thus is similar in use as PHPUnit's MockBuilder interface.

You construct a new builder by calling `DecoratorBuilder::newBuilder` and
passing in the object you want to decorate. Then you can call `withBefore`
and `withAfter`, to define the decorated behaviour. Finally you call
`newDecorator` and get the decorated instance.

```php
public function __construct() {
    $this->repository = new DoctrineKittenRepository( /* ... */ );
    $this->stopWatch = new Stopwatch();
}

public function newProfilingKittenRepository(): KittenRepository {
    return DecoratorBuilder::newBuilder( $this->repository )
    	->withBefore( function() {
    		$this->stopWatch->start( 'KittenRepository' );
    	} )
    	->withAfter( function() {
			$this->stopWatch->stop( 'KittenRepository' );
		} )
		->newDecorator();
}
```

The callable provided to `withBefore` and `withAfter` receives all
arguments the decorated method receives.

```php
->withBefore( function() {
	$this->logger->alert( 'KittenRepository', [ 'arguments' => func_get_args() ] );
} )
```

## Missing features / roadmap

* Allow decorating generated decorators (this will fatal error at present)
* Support try-catch, ie for logging decorators
* Provide a way to get the name of the calling method (anyone knows how to do that without crazy debug_backtrace hacks?)
* Test presence of private and protected methods
* Test final class
* Test exceptions
* Test internal calls to public methods

## Release notes

### 0.1.1 (2016-08-16)

* Decorating classes with required constructor parameters now works

### 0.1.0 (2016-07-29)

* Initial release