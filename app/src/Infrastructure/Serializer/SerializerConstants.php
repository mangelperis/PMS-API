<?php
declare(strict_types=1);


namespace App\Infrastructure\Serializer;

class SerializerConstants
{
    //PMS Input keys to map
    public const PMS_REQUIRED_KEYS = ['hotel_id', 'booking', 'guest'];

    //Serializer used Key Group Name
    public const SERIALIZER_GROUP = 'STAY';

}