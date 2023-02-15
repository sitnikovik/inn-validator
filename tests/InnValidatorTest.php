<?php

namespace Sitnikovik\InnValidator\Tests;

use PHPUnit\Framework\TestCase;
use Sitnikovik\InnValidator\InnValidator;

/**
 * Tests the package functionality.
 */
final class InnValidatorTest extends TestCase
{
    /**
     * Tests INN validation.
     *
     * @return void
     */
    public function testValidation(): void
    {
        $this->assertTrue(InnValidator::validate('7830002293'));
        $this->assertFalse(InnValidator::validate('7830002294'));

        $this->assertTrue(InnValidator::validate('500100732259'));
        $this->assertFalse(InnValidator::validate('500111732259'));
    }
}