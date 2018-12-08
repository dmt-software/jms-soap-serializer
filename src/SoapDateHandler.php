<?php

namespace DMT\Soap\Serializer;

use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\DateHandler;
use JMS\Serializer\Handler\SubscribingHandlerInterface;

/**
 * Class SoapDateHandler
 *
 * @package DMT\Soap
 */
class SoapDateHandler implements SubscribingHandlerInterface
{
    /**
     * @var DateHandler
     */
    protected $dateHandler;

    public static function getSubscribingMethods()
    {
        $methods = [];

        foreach (['DateTime', 'DateTimeImmutable', 'DateInterval'] as $type) {
            $methods[] = [
                'type' => $type,
                'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
                'format' => 'soap',
                'method' => 'deserialize' . $type . 'FromXml'
            ];

            $methods[] = [
                'type' => $type,
                'format' => 'soap',
                'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
                'method' => 'serialize' . $type,
            ];
        }

        return $methods;
    }

    public function __construct()
    {
        $this->dateHandler = new DateHandler();
    }

    public function __call($name, $arguments)
    {
        return call_user_func([$this->dateHandler, $name], ...$arguments);
    }
}
