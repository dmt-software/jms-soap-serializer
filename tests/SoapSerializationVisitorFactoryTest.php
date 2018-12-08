<?php

namespace DMT\test\Soap\Serializer;

use DMT\Soap\Serializer\SoapNamespaceInterface;
use DMT\Soap\Serializer\SoapSerializationVisitorFactory;
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

        static::assertAttributeNotSame('2.0', 'defaultVersion', $factory);
        static::assertInstanceOf(SoapSerializationVisitorFactory::class, $factory->setDefaultVersion('2.0'));
        static::assertAttributeSame('2.0', 'defaultVersion', $factory);
    }

    /**
     * Test setting XML encoding.
     */
    public function testSetDefaultEncoding()
    {
        $factory = new SoapSerializationVisitorFactory();

        static::assertAttributeNotSame('iso-8859-1', 'defaultEncoding', $factory);
        static::assertInstanceOf(SoapSerializationVisitorFactory::class, $factory->setDefaultEncoding('iso-8859-1'));
        static::assertAttributeSame('iso-8859-1', 'defaultEncoding', $factory);
    }

    /**
     * Test setting output format.
     */
    public function testSetFormatOutput()
    {
        $factory = new SoapSerializationVisitorFactory();

        static::assertAttributeNotSame(false, 'formatOutput', $factory);
        static::assertInstanceOf(SoapSerializationVisitorFactory::class, $factory->setFormatOutput(false));
        static::assertAttributeSame(false, 'formatOutput', $factory);
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

    /**
     * @expectedException \JMS\Serializer\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unsupported SOAP version
     */
    public function testSetUnknownSoapVersion()
    {
        if (!defined('SOAP_1_3')) {
            define('SOAP_1_3', 3);
        }

        (new SoapSerializationVisitorFactory())
            ->setSoapVersion(SOAP_1_3)
            ->getVisitor();
    }

    /**
     * @return \Generator
     */
    public function provideVersion()
    {
        foreach (SoapNamespaceInterface::SOAP_NAMESPACES as $version => $namespace) {
            yield [$version, $namespace];
        }
    }
}
