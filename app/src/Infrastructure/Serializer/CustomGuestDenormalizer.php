<?php
declare(strict_types=1);


namespace App\Infrastructure\Serializer;

use App\Domain\Entity\Guest;
use DateTime;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CustomGuestDenormalizer implements DenormalizerInterface
{
    /**
     * @throws \Exception
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): Guest
    {
        return new Guest(
            $data['name'],
            $data['lastname'],
            new DateTime($data['birthdate']),
            $data['passport'],
            $data['country']
        );
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return $type === Guest::class;
    }
}