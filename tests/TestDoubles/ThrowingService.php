<?php

declare( strict_types = 1 );

namespace GenericDecorator\Tests\TestDoubles;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ThrowingService {

	const ERROR_MESSAGE = 'some error message';

	/**
	 * @return int
	 * @throws \RuntimeException
	 */
	public function getFixedValue(): int {
		throw new \RuntimeException( self::ERROR_MESSAGE );
	}

}
