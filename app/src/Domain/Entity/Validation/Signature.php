<?php
declare(strict_types=1);


namespace App\Domain\Entity\Validation;

class Signature
{
    const SECRET = '4EO9GOalqXvmEJcxNFOEFqcD';
    public static function verifySignature(string $locator, string $roomNumber, string $signature): bool
    {
        // Concatenate the locator and room number with the KEY
        $data = $locator . $roomNumber;

        // HMAC hash using SHA-256 algorithm
        $computedSignature = hash_hmac('sha256', $data, self::SECRET);

        // validate
        return hash_equals($computedSignature, $signature);
    }
}