<?php

declare( strict_types = 1 );

namespace GenericDecorator\Tests\TestDoubles;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ServiceWithConstructorParams {

	private $maw;

	public function __construct( string $maw ) {
		$this->maw = $maw;
	}

	public function getSomeString(): string {
		return $this->maw;
	}

}
