<?php

declare( strict_types = 1 );

namespace GenericDecorator\Tests;

use GenericDecorator\DecoratorBuilder;
use GenericDecorator\Tests\TestDoubles\ServiceClass;
use GenericDecorator\Tests\TestDoubles\ServiceImplementingPartialInterface;
use GenericDecorator\Tests\TestDoubles\ThrowingService;

/**
 * @covers GenericDecorator\DecoratorBuilder
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DecoratorBuilderTest extends \PHPUnit_Framework_TestCase {

	public function testGivenNoSpecification_newDecoratorReturnsCorrectType() {
		$coreService = new ServiceClass();

		$decoratedService = DecoratorBuilder::newBuilder( $coreService )->newDecorator();

		$this->assertInstanceOf( ServiceClass::class, $decoratedService );
	}

	public function testGivenNoSpecification_decoratorCallsDecoratedClass() {
		$coreService = new ServiceClass();

		/**
		 * @var ServiceClass $decoratedService
		 */
		$decoratedService = DecoratorBuilder::newBuilder( $coreService )->newDecorator();

		$decoratedService->record( 'pink fluffy unicorns' );
		$decoratedService->record( 'dancing on rainbows' );

		$this->assertSame(
			[ 'pink fluffy unicorns', 'dancing on rainbows' ],
			$coreService->recordCalls
		);
	}

	public function testGivenBeforeFunction_itIsCalledBeforeDecoratedMethod() {
		$coreService = new ServiceClass();

		/**
		 * @var ServiceClass $decoratedService
		 */
		$decoratedService = DecoratorBuilder::newBuilder( $coreService )
			->withBefore( function( string $message ) use ( $coreService ) {
				$coreService->recordCalls[] = 'Before: ' . $message;
			} )
			->newDecorator();

		$decoratedService->record( 'a' );
		$decoratedService->record( 'b' );

		$this->assertSame(
			[ 'Before: a', 'a', 'Before: b', 'b' ],
			$coreService->recordCalls
		);
	}

	public function testGivenAfterFunction_itIsCalledAfterDecoratedMethod() {
		$coreService = new ServiceClass();

		/**
		 * @var ServiceClass $decoratedService
		 */
		$decoratedService = DecoratorBuilder::newBuilder( $coreService )
			->withAfter( function( string $message ) use ( $coreService ) {
				$coreService->recordCalls[] = 'After: ' . $message;
			} )
			->newDecorator();

		$decoratedService->record( 'a' );
		$decoratedService->record( 'b' );

		$this->assertSame(
			[ 'a', 'After: a', 'b', 'After: b' ],
			$coreService->recordCalls
		);
	}

	public function testReturnValueOfDecoratedMethodIsReturned() {
		$coreService = new ServiceClass();

		/**
		 * @var ServiceClass $decoratedService
		 */
		$decoratedService = DecoratorBuilder::newBuilder( $coreService )->newDecorator();

		$this->assertSame( $coreService->getFixedValue(), $decoratedService->getFixedValue() );
	}

	public function testDecorationOfMethodInImplementedInterface() {
		$coreService = new ServiceImplementingPartialInterface();

		/**
		 * @var ServiceImplementingPartialInterface $decoratedService
		 */
		$decoratedService = DecoratorBuilder::newBuilder( $coreService )
			->withBefore( function( string $message ) use ( $coreService ) {
				$coreService->recordCalls[] = 'Before: ' . $message;
			} )
			->newDecorator();

		$decoratedService->record( 'a' );
		$decoratedService->record( 'b' );

		$this->assertSame(
			[ 'Before: a', 'a', 'Before: b', 'b' ],
			$coreService->recordCalls
		);
	}

	public function testDecorationOfMethodNotInImplementedInterface() {
		$coreService = new ServiceImplementingPartialInterface();
		$beforeCalls = [];

		/**
		 * @var ServiceImplementingPartialInterface $decoratedService
		 */
		$decoratedService = DecoratorBuilder::newBuilder( $coreService )
			->withBefore( function() use ( $coreService, &$beforeCalls ) {
				$beforeCalls[] = func_get_args();
			} )
			->newDecorator();

		$this->assertSame( $coreService->getFixedValue(), $decoratedService->getFixedValue() );
		$this->assertSame( [ [] ], $beforeCalls );
	}

//	public function testCanDecorateDecorator() {
//		$coreService = new ServiceClass();
//
//		/**
//		 * @var ServiceClass $decoratedService
//		 */
//		$decoratedService = DecoratorBuilder::newBuilder( $coreService )->newDecorator();
//
//		/**
//		 * @var ServiceClass $decoratedDecorator
//		 */
//		$decoratedDecorator = DecoratorBuilder::newBuilder( $decoratedService )->newDecorator();
//
//		$decoratedDecorator->record( 'pink fluffy unicorns' );
//		$decoratedDecorator->record( 'dancing on rainbows' );
//
//		$this->assertSame(
//			[ 'pink fluffy unicorns', 'dancing on rainbows' ],
//			$coreService->recordCalls
//		);
//	}

	public function testExceptionsAreNotCaughtByDefault() {
		$coreService = new ThrowingService();

		/**
		 * @var ServiceClass $decoratedService
		 */
		$decoratedService = DecoratorBuilder::newBuilder( $coreService )->newDecorator();

		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( ThrowingService::ERROR_MESSAGE );
		$decoratedService->getFixedValue();
	}

}
