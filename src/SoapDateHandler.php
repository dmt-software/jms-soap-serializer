<?php

namespace DMT\Soap\Serializer;

use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\DateHandler;

class SoapDateHandler extends DateHandler
{
    public static function getSubscribingMethods()
    {
        $methods = [];

        foreach (['DateTime', 'DateTimeImmutable', 'DateInterval'] as $type) {
            $methods[] = [
                'type' => $type,
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'soap',
                'method' => 'deserialize' . $type . 'FromXml'
            ];

            $methods[] = [
                'type' => $type,
                'format' => 'soap',
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'method' => 'serialize' . $type,
            ];
        }

        return $methods;
    }
}
