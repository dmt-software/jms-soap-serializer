<?php

namespace DMT\Test\Soap\Serializer;

use DMT\Soap\Serializer\SoapDeserializationVisitor;
use DMT\Soap\Serializer\SoapFault;
use DMT\Test\Soap\Serializer\Fixtures\Language;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class SoapDeserializationVisitorTest
 *
 * @package DMT\Test\Soap
 */
class SoapDeserializationVisitorTest extends TestCase
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $response = <<<TXT
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://xmpl-namespace.nl">
  <SOAP-ENV:Body>
    <Language>
      <name>%s</name>
      <complexity>%d</complexity>
    </Language>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
TXT;

    /**
     * @var string
     */
    protected $fault = <<<TXT
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
  <SOAP-ENV:Body>
    <SOAP-ENV:Fault>
      <faultcode>SOAP-ENV:%s</faultcode>
      <faultstring>%s</faultstring>
      <detail><message>%s</message></detail>
    </SOAP-ENV:Fault>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
TXT;

    public function setUp()
    {
        AnnotationRegistry::registerUniqueLoader('class_exists');

        $this->serializer = SerializerBuilder::create()
            ->setDeserializationVisitor(
                'soap',
                new SoapDeserializationVisitor(
                    new SerializedNameAnnotationStrategy(
                        new IdenticalPropertyNamingStrategy()
                    )
                )
            )
            ->build();
    }

    /**
     * @dataProvider provideLanguage
     *
     * @param string $name
     * @param int $complexity
     */
    public function testDeserialization(string $name, int $complexity)
    {
        $response = sprintf($this->response, $name, $complexity);
        $language = $this->serializer->deserialize($response, Language::class, 'soap');

        /** @var Language $language */
        static::assertSame($name, $language->getName());
        static::assertSame($complexity, $language->getComplexity());
    }

    public function provideLanguage(): array
    {
        return [
            ['C#', 102], ['PHP', 67], ['Java', 50], ['Ruby', 39]
        ];
    }

    /**
     * @expectedException \JMS\Serializer\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unsupported SOAP version
     */
    public function testUnsupportedVersion()
    {
        $xml = '<env:Envelope xmlns:env="http://soap-version.com/unknown"/>';
        $this->serializer->deserialize($xml, \stdClass::class, 'soap');
    }

    /**
     * @dataProvider provideSoapFault
     *
     * @param string $reason
     * @param string $code
     * @param string $detail
     */
    public function testSoapFault(string $reason, string $code, string $detail)
    {
        static::expectException(SoapFault::class);

        try {
            $fault = sprintf($this->fault, $code, $reason, $detail);
            $this->serializer->deserialize($fault, \stdClass::class, 'soap');
        } catch (SoapFault $fault) {
            static::assertSame($reason, $fault->getMessage());
            static::assertSame($code, $fault->getFaultCode());
            static::assertContains($detail, $fault->detail);

            throw $fault;
        }
    }

    public function provideSoapFault(): array
    {
        return [
            ['Lorum ipsum', 'Client.NotFound', 'Lorum ipsum dolor sit amet.'],
            ['Praesent ut.', 'Server', 'Praesent ut eros lacus.'],
        ];
    }
}
