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

        $this->assertEquals($clone, $factory);
        $this->assertInstanceOf(SoapDeserializationVisitorFactory::class, $factory->enableExternalEntities(true));
        $this->assertNotEquals($clone, $factory);
    }
}
