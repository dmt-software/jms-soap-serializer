<?php

namespace DMT\Test\Soap\Serializer;

use DMT\Soap\Serializer\SoapNamespaceInterface;
use DMT\Soap\Serializer\SoapSerializationVisitorFactory;
use Generator;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\XmlSerializationVisitor;
use PHPUnit\Framework\TestCase;

class SoapSerializationVisitorFactoryTest extends TestCase
{
    /**
     * Test setting XML version.
     */
    public function testSetDefaultVersion()
    {
        $factory = new SoapSerializationVisitorFactory();
        $clone = clone($factory);

        static::assertEquals($clone, $factory);
        static::assertInstanceOf(SoapSerializationVisitorFactory::class, $factory->setDefaultVersion('2.0'));
        static::assertNotEquals($clone, $factory);
    }

    /**
     * Test setting XML encoding.
     */
    public function testSetDefaultEncoding()
    {
        $factory = new SoapSerializationVisitorFactory();
        $clone = clone($factory);

        static::assertEquals($clone, $factory);
        static::assertInstanceOf(SoapSerializationVisitorFactory::class, $factory->setDefaultEncoding('iso-8859-1'));
        static::assertNotEquals($clone, $factory);
    }

    /**
     * Test setting output format.
     */
    public function testSetFormatOutput()
    {
        $factory = new SoapSerializationVisitorFactory();
        $clone = clone($factory);

        static::assertEquals($clone, $factory);
        static::assertInstanceOf(SoapSerializationVisitorFactory::class, $factory->setFormatOutput(false));
        static::assertNotEquals($clone, $factory);
    }

    /**
     * @dataProvider provideVersion
     *
     * @param int $version
     * @param string $namespace
     */
    public function testSetSoapVersion(int $version, string $namespace)
    {
        /** @var XmlSerializationVisitor $visitor */
        $visitor = (new SoapSerializationVisitorFactory())
            ->setSoapVersion($version)
            ->getVisitor();

        static::assertSame($namespace, $visitor->getCurrentNode()->namespaceURI);
    }

    public function testSetUnknownSoapVersion()
    {
        $this->expectExceptionObject(new InvalidArgumentException('Unsupported SOAP version'));

        if (!defined('SOAP_1_3')) {
            define('SOAP_1_3', 3);
        }

        (new SoapSerializationVisitorFactory())
            ->setSoapVersion(SOAP_1_3)
            ->getVisitor();
    }

    /**
     * @return Generator
     */
    public function provideVersion()
    {
        foreach (SoapNamespaceInterface::SOAP_NAMESPACES as $version => $namespace) {
            yield [$version, $namespace];
        }
    }
}
