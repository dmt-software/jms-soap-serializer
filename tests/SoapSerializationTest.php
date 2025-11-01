<?php

namespace DMT\Test\Soap\Serializer;

use DateTime;
use DMT\Soap\Serializer\SoapNamespaceInterface;
use DMT\Test\Soap\Serializer\Fixtures\Language;
use DMT\Test\Soap\Serializer\Fixtures\LanguageWithPrefix;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class SoapSerializationVisitorTest
 *
 * @package DMT\Soap\Serializer
 */
class SoapSerializationTest extends TestCase
{
    use SoapSerializerSetUpTrait;

    #[DataProvider(methodName: 'provideLanguage')]
    public function testSerialization(string $name, int $complexity, DateTime $date): void
    {
        $xml = simplexml_load_string($this->serializer->serialize(new Language($name, $complexity, $date), 'soap'));

        $this->assertContains(SoapNamespaceInterface::SOAP_NAMESPACES[SOAP_1_1], $xml->getNamespaces());
        $this->assertSame('Envelope', $xml->getName());
        $this->assertSame('Body', $xml->xpath('/*[local-name()="Envelope"]/*')[0]->getName());

        $message = $xml->xpath('/*[local-name()="Envelope"]/*')[0]->children()[0];
        $this->assertContains('http://xmpl-namespace.nl', $message->getNamespaces());
        $this->assertSame($name, strval($message->name));
        $this->assertSame($complexity, intval($message->complexity));
        $this->assertSame($date->format('Y-m-d'), strval($message->since));
    }

    #[DataProvider(methodName: 'provideLanguage')]
    public function testSerializationWithPrefix(string $name, int $complexity, DateTime $date): void
    {
        $serialized = $this->serializer->serialize(new LanguageWithPrefix($name, $complexity, $date), 'soap');

        $doc = new \DOMDocument();
        $doc->loadXML($serialized);

        $xpath = new \DOMXPath($doc);
        $node = $xpath->query('//*[local-name()="Language" and namespace-uri()="http://xmpl-namespace.nl"]')[0];
        $name = $xpath->query('//*[local-name()="name" and namespace-uri()="http://xmpl-namespace.nl"]')[0];
        $complexity = $xpath->query('//*[local-name()="complexity" and namespace-uri()="http://xmpl-namespace.nl"]')[0];
        $since = $xpath->query('//*[local-name()="since" and namespace-uri()="http://xmpl-namespace.nl"]')[0];

        $this->assertSame('ns', $node->prefix);
        $this->assertSame('ns', $name->prefix);
        $this->assertSame('ns', $complexity->prefix);
        $this->assertSame('ns', $since->prefix);
    }

    public static function provideLanguage(): iterable
    {
        return [
            ['F#', 103, new DateTime('2005-05-01')],
            ['JavaScript', 64, new DateTime('1995-09-13')],
            ['Perl', 40, new DateTime('1987-12-18')]
        ];
    }
}
