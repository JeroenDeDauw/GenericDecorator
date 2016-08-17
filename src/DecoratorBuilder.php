<?php

declare( strict_types = 1 );

namespace GenericDecorator;

use PHPUnit_Framework_MockObject_Generator;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class DecoratorBuilder {

	private $objectToDecorate;
	private $before;
	private $after;

	public function __construct( $objectToDecorate ) {
		$this->objectToDecorate = $objectToDecorate;
		$this->before = function() {};
		$this->after = function() {};
	}

	public static function newBuilder( $objectToDecorate ): self {
		return new self( $objectToDecorate );
	}

	public function withBefore( callable $before ): self {
		$this->before = $before;
		return $this;
	}

	public function withAfter( callable $after ): self {
		$this->after = $after;
		return $this;
	}

	/**
	 * @return object
	 */
	public function newDecorator() {
		$methodNames = $this->getMethodNames();
		$decorator = $this->newMock( $this->getDecoratedType(), $methodNames );

		foreach ( $methodNames as $methodName ) {
			$this->decorateMethod( $decorator, $methodName );
		}

		$this->assertTypeRetained( $decorator );

		return $decorator;
	}

	private function newMock( string $type, array $methods ): \PHPUnit_Framework_MockObject_MockObject {
		$generator = new PHPUnit_Framework_MockObject_Generator();

		return $generator->getMock( $type, $methods, [], '', false );
	}

	private function getDecoratedType(): string {
		return get_class( $this->objectToDecorate );
	}

	private function getMethodNames(): array {
		$methodNames = array_filter(
			get_class_methods( $this->getDecoratedType() ),
			function( string $methodName ) {
				return substr( $methodName, 0, 2 ) !== '__';
			}
		);

		return $methodNames;
	}

	private function decorateMethod( \PHPUnit_Framework_MockObject_MockObject $decorator, string $methodName ) {
		$decoratedMethod = $decorator->method( $methodName );

		$decoratedMethod->willReturnCallback(
			function() use ( $methodName ) {
				call_user_func_array( $this->before, func_get_args() );
				$returnValue = call_user_func_array( [ $this->objectToDecorate, $methodName ], func_get_args() );
				call_user_func_array( $this->after, func_get_args() );

				return $returnValue;
			}
		);
	}

	private function assertTypeRetained( $decorator ) {
		$expectedType = $this->getDecoratedType();

		if ( !( $decorator instanceof $expectedType ) ) {
			throw new \LogicException( 'Decorator not of the correct type' );
		}
	}

}
