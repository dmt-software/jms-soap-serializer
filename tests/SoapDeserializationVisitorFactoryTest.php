<?php

namespace DMT\test\Soap\Serializer;

use DMT\Soap\Serializer\SoapDeserializationVisitorFactory;
use PHPUnit\Framework\TestCase;

class SoapDeserializationVisitorFactoryTest extends TestCase
{
    public function testEnableExternalEntities()
    {
        $factory = new SoapDeserializationVisitorFactory();

        static::assertAttributeNotSame(false, 'disableExternalEntities', $factory);
        static::assertInstanceOf(SoapDeserializationVisitorFactory::class, $factory->enableExternalEntities(true));
        static::assertAttributeSame(false, 'disableExternalEntities', $factory);
    }
}
