<?php

namespace DMT\Test\Soap\Serializer;

use DateTime;
use DMT\Soap\Serializer\SoapNamespaceInterface;
use DMT\Test\Soap\Serializer\Fixtures\Language;
use DMT\Test\Soap\Serializer\Fixtures\LanguageWithPrefix;
use PHPUnit\Framework\TestCase;

/**
 * Class SoapSerializationVisitorTest
 *
 * @package DMT\Soap\Serializer
 */
class SoapSerializationTest extends TestCase
{
    use SoapSerializerSetUpTrait;

    /**
     * @dataProvider provideLanguage
     *
     * @param string $name
     * @param int $complexity
     * @param DateTime $date
     */
    public function testSerialization(string $name, int $complexity, DateTime $date)
    {
        $xml = simplexml_load_string($this->serializer->serialize(new Language($name, $complexity, $date), 'soap'));

        static::assertContains(SoapNamespaceInterface::SOAP_NAMESPACES[SOAP_1_1], $xml->getNamespaces());
        static::assertSame('Envelope', $xml->getName());
        static::assertSame('Body', $xml->xpath('/*[local-name()="Envelope"]/*')[0]->getName());

        $message = $xml->xpath('/*[local-name()="Envelope"]/*')[0]->children()[0];
        static::assertContains('http://xmpl-namespace.nl', $message->getNamespaces());
        static::assertSame($name, strval($message->name));
        static::assertSame($complexity, intval($message->complexity));
        static::assertSame($date->format('Y-m-d'), strval($message->since));
    }

    /**
     * @dataProvider provideLanguage
     *
     * @param string $name
     * @param int $complexity
     * @param DateTime $date
     */
    public function testSerializationWithPrefix(string $name, int $complexity, DateTime $date)
    {
        $serialized = $this->serializer->serialize(new LanguageWithPrefix($name, $complexity, $date), 'soap');

        $xml = simplexml_load_string($serialized);

        static::assertContains(SoapNamespaceInterface::SOAP_NAMESPACES[SOAP_1_1], $xml->getNamespaces());
        static::assertSame('Envelope', $xml->getName());
        static::assertSame('Body', $xml->xpath('/*[local-name()="Envelope"]/*')[0]->getName());

        /** @var \SimpleXMLElement $message */
        $message = $xml->xpath('/*[local-name()="Envelope"]/*')[0]->children('http://xmpl-namespace.nl')[0];
        static::assertContains('http://xmpl-namespace.nl', $message->getNamespaces());
        static::assertContains('ns', array_keys($message->getNamespaces()));
        static::assertSame($name, strval($message->name));
        static::assertSame($complexity, intval($message->complexity));
        static::assertSame($date->format('Y-m-d'), strval($message->since));

        $lang = $message->children('http://xmpl-namespace.nl')[0];
        static::assertContains('http://xmpl-namespace.nl', $lang->getNamespaces());
        static::assertContains('ns', array_keys($lang->getNamespaces()));
    }

    public function provideLanguage(): array
    {
        return [
            ['F#', 103, new DateTime('2005-05-01')],
            ['JavaScript', 64, new DateTime('1995-09-13')],
            ['Perl', 40, new DateTime('1987-12-18')]
        ];
    }
}
