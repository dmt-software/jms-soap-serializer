<?php

namespace DMT\Test\Soap\Serializer;

use DMT\Soap\Serializer\SoapNamespaceInterface;
use DMT\Soap\Serializer\SoapSerializationVisitorFactory;
use Generator;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\XmlSerializationVisitor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SoapSerializationVisitorFactoryTest extends TestCase
{
    /**
     * Test setting XML version.
     */
    public function testSetDefaultVersion(): void
    {
        $factory = new SoapSerializationVisitorFactory();
        $clone = clone($factory);

        $this->assertEquals($clone, $factory);
        $this->assertInstanceOf(SoapSerializationVisitorFactory::class, $factory->setDefaultVersion('2.0'));
        $this->assertNotEquals($clone, $factory);
    }

    /**
     * Test setting XML encoding.
     */
    public function testSetDefaultEncoding(): void
    {
        $factory = new SoapSerializationVisitorFactory();
        $clone = clone($factory);

        $this->assertEquals($clone, $factory);
        $this->assertInstanceOf(SoapSerializationVisitorFactory::class, $factory->setDefaultEncoding('iso-8859-1'));
        $this->assertNotEquals($clone, $factory);
    }

    /**
     * Test setting output format.
     */
    public function testSetFormatOutput(): void
    {
        $factory = new SoapSerializationVisitorFactory();
        $clone = clone($factory);

        $this->assertEquals($clone, $factory);
        $this->assertInstanceOf(SoapSerializationVisitorFactory::class, $factory->setFormatOutput(false));
        $this->assertNotEquals($clone, $factory);
    }

    #[DataProvider(methodName: 'provideVersion')]
    public function testSetSoapVersion(int $version, string $namespace): void
    {
        /** @var XmlSerializationVisitor $visitor */
        $visitor = (new SoapSerializationVisitorFactory())
            ->setSoapVersion($version)
            ->getVisitor();

        $this->assertSame($namespace, $visitor->getCurrentNode()->namespaceURI);
    }

    public function testSetUnknownSoapVersion(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('Unsupported SOAP version'));

        if (!defined('SOAP_1_3')) {
            define('SOAP_1_3', 3);
        }

        (new SoapSerializationVisitorFactory())
            ->setSoapVersion(SOAP_1_3)
            ->getVisitor();
    }

    public static function provideVersion(): iterable
    {
        foreach (SoapNamespaceInterface::SOAP_NAMESPACES as $version => $namespace) {
            yield [$version, $namespace];
        }
    }
}
