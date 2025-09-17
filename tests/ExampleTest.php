<?php
/**
 * Class ExampleTest
 *
 * @created      08.03.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\LibraryTemplateTest;

use chillerlan\LibraryTemplate\Example;
use PHPUnit\Framework\TestCase;

/**
 * An example unit test
 */
class ExampleTest extends TestCase{

	public function testInstance():void{
		$obj = new Example;

		$this::assertInstanceOf(Example::class, $obj);
		$this::assertSame('hi', $obj->hello());
	}

}
