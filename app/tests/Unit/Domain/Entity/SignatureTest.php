<?php
declare(strict_types=1);


namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Validation\Signature;
use Monolog\Test\TestCase;

class SignatureTest extends TestCase
{
    public function testVerifySignatureWithSampleSource(): void
    {
        $locator = '61F80321790C5';
        $roomNumber = '291';
        $signature = "e8b558125c709621bd5a80ca25f772cc7a3a4b8b0b86478f355740af5d7558a8";

        $this->assertTrue(Signature::verifySignature($locator, $roomNumber, $signature));
    }
    public function testVerifySignatureWithValidSignature(): void
    {
        $locator = 'ABC123';
        $roomNumber = '101';
        $signature = hash_hmac('sha256', $locator . $roomNumber, Signature::SECRET);

        $this->assertTrue(Signature::verifySignature($locator, $roomNumber, $signature));
    }

    public function testVerifySignatureWithInvalidSignature(): void
    {
        $locator = 'ABC123';
        $roomNumber = '101';
        $signature = hash_hmac('sha256', $locator . $roomNumber . 'extra_chars', Signature::SECRET);

        $this->assertFalse(Signature::verifySignature($locator, $roomNumber, $signature));
    }

}