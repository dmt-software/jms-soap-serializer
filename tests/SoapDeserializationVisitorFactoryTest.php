<?php

namespace DMT\Test\Soap\Serializer;

use DMT\Soap\Serializer\SoapDeserializationVisitorFactory;
use PHPUnit\Framework\TestCase;

class SoapDeserializationVisitorFactoryTest extends TestCase
{
    public function testEnableExternalEntities()
    {
        $factory = new SoapDeserializationVisitorFactory();
        $clone = clone($factory);

        static::assertEquals($clone, $factory);
        static::assertInstanceOf(SoapDeserializationVisitorFactory::class, $factory->enableExternalEntities(true));
        static::assertNotEquals($clone, $factory);
    }
}
