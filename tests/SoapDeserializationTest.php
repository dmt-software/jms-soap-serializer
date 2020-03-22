<?php

namespace DMT\Test\Soap\Serializer;

use DMT\Soap\Serializer\SoapFaultException;
use DMT\Test\Soap\Serializer\Fixtures\Language;
use JMS\Serializer\Serializer;
use PHPUnit\Framework\TestCase;

/**
 * Class SoapDeserializationVisitorTest
 *
 * @package DMT\Test\Soap
 */
class SoapDeserializationTest extends TestCase
{
    use SoapSerializerSetUpTrait;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $response = <<<TXT
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
  <SOAP-ENV:Body xmlns="http://xmpl-namespace.nl">
    <Language>
      <name>%s</name>
      <complexity>%d</complexity>
      <since>%s</since>
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
      <faultactor>%s</faultactor>
      <detail><message>%s</message></detail>
    </SOAP-ENV:Fault>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
TXT;

    /**
     * @var string
     */
    protected $fault12 = <<<TXT
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" 
    xmlns:xml="http://www.w3.org/XML/1998/namespace">
  <env:Body>
    <env:Fault>
      <env:Code>
        <env:Value>env:Receiver</env:Value>
        <env:Subcode>
          <env:Value>ns1:Validation</env:Value>
          <env:Subcode>
            <env:Value>ns1:Error</env:Value>
          </env:Subcode>
        </env:Subcode>
      </env:Code>
      <env:Reason>
        <env:Text xml:lang="en">Division by zero</env:Text>
        <env:Text xml:lang="fr">Devision par zéro</env:Text>
      </env:Reason>
      <env:Node>http://example.org/node</env:Node>
      <env:Detail xmlns:ns1="http://example.org/ns">
        <ns1:message>Division by zero</ns1:message>
        <ns1:message>Don't do it again!</ns1:message>
        </env:Detail>
    </env:Fault>
  </env:Body>
</env:Envelope>
TXT;

    /**
     * @dataProvider provideLanguage
     *
     * @param string $name
     * @param int $complexity
     * @param string $date
     */
    public function testDeserialization(string $name, int $complexity, string $date = null)
    {
        $response = sprintf($this->response, $name, $complexity, $date);
        $language = $this->serializer->deserialize($response, Language::class, 'soap');

        /** @var Language $language */
        static::assertSame($name, $language->getName());
        static::assertSame($complexity, $language->getComplexity());
        static::assertEquals($date, $language->getSince()->format('Y-m-d'));
    }

    public function provideLanguage(): array
    {
        return [
            ['C#', 102, '2002-01-10'],
            ['PHP', 67, '1995-06-12'],
            ['Java', 50, '1995-05-23'],
            ['Ruby', 39, '1990-07-01']
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
     * @param string $code
     * @param string $reason
     * @param string $node
     * @param string $detail
     */
    public function testSoapFault(string $code, string $reason, string $node, string $detail)
    {
        static::expectException(SoapFaultException::class);

        try {
            $fault = vsprintf($this->fault, func_get_args());
            $this->serializer->deserialize($fault, \stdClass::class, 'soap');
        } catch (SoapFaultException $fault) {
            static::assertSame($reason, $fault->getMessage());
            static::assertSame($code, $fault->getFaultCode());

            static::assertSame($code, $fault->code);
            static::assertSame($reason, $fault->reason);
            static::assertSame($node, $fault->node);
            static::assertContains($detail, $fault->detail['message']);

            /** @var \SoapFault $soapFault */
            $soapFault = $fault->getPrevious();
            static::assertInstanceOf(\SoapFault::class, $soapFault);
            static::assertSame($code, $soapFault->faultcode);
            static::assertSame($reason, $soapFault->faultstring);
            static::assertSame($node, $soapFault->faultactor);
            static::assertContains($detail, $soapFault->detail['message']);

            throw $fault;
        }
    }

    public function provideSoapFault(): array
    {
        return [
            ['Client.NotFound', 'Lorum ipsum', 'http://xmpl.fault.com/err', 'Lorum ipsum dolor sit amet.'],
            ['Server', 'Praesent ut.', 'http://xmpl.fault.com/some', 'Praesent ut eros lacus.'],
        ];
    }

    public function testSoapFaultEmptyElements()
    {
        $fault = <<<TXT
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
  <SOAP-ENV:Body>
    <SOAP-ENV:Fault>
      <faultcode>SOAP-ENV:Client</faultcode>
      <faultstring>Error in soap call</faultstring>
      <faultactor></faultactor>
      <detail></detail>
    </SOAP-ENV:Fault>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
TXT;
        static::expectException(SoapFaultException::class);

        try {
            $this->serializer->deserialize($fault, \stdClass::class, 'soap');
        } catch (SoapFaultException $fault) {
            static::assertSame('Error in soap call', $fault->getMessage());
            static::assertSame('Client', $fault->getFaultCode());

            static::assertSame('Client', $fault->code);
            static::assertSame('Error in soap call', $fault->reason);
            static::assertNull($fault->node);
            static::assertNull($fault->detail);

            /** @var \SoapFault $soapFault */
            $soapFault = $fault->getPrevious();
            static::assertInstanceOf(\SoapFault::class, $soapFault);
            static::assertSame('Client', $soapFault->faultcode);
            static::assertSame('Error in soap call', $soapFault->faultstring);
            static::assertObjectNotHasAttribute('faultactor', $soapFault);
            static::assertObjectNotHasAttribute('detail', $soapFault);

            throw $fault;
        }
    }

    /**
     * @dataProvider provideMessages
     *
     * @param string $locale
     * @param string $expected
     */
    public function testSoap12Fault(string $locale, string $expected)
    {
        static::expectException(SoapFaultException::class);
        locale_set_default($locale);

        try {
            $this->serializer->deserialize($this->fault12, \stdClass::class, 'soap');
        } catch (SoapFaultException $fault) {
            static::assertSame($expected, $fault->getMessage());
            static::assertSame('Receiver.Validation.Error', $fault->getFaultCode());

            static::assertSame('Receiver.Validation.Error', $fault->code);
            static::assertSame($expected, $fault->reason);
            static::assertSame('http://example.org/node', $fault->node);
            static::assertContains('Division by zero', $fault->detail['message']);

            /** @var \SoapFault $soapFault */
            $soapFault = $fault->getPrevious();
            static::assertInstanceOf(\SoapFault::class, $soapFault);
            static::assertSame('Server.Validation.Error', $soapFault->faultcode);
            static::assertSame($expected, $soapFault->faultstring);
            static::assertSame('http://example.org/node', $soapFault->faultactor);
            static::assertContains('Division by zero', $soapFault->detail['message']);

            throw $fault;
        }
    }

    public function provideMessages(): array
    {
        return [
            ['en_US', 'Division by zero'],
            ['nl_NL', 'Division by zero'],
            ['fr_FR', 'Devision par zéro'],
        ];
    }
}
