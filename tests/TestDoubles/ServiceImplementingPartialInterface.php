<?php

declare( strict_types = 1 );

namespace GenericDecorator\Tests\TestDoubles;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ServiceImplementingPartialInterface implements PartialServiceInterface {

	public $recordCalls = [];

	public function record( string $message ) {
		$this->recordCalls[] = $message;
	}

	public function getFixedValue(): int {
		return 42;
	}

}
