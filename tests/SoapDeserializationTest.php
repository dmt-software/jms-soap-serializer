<?php

namespace DMT\Test\Soap\Serializer;

use DMT\Soap\Serializer\SoapFaultException;
use DMT\Test\Soap\Serializer\Fixtures\Language;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Serializer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SoapFault;
use stdClass;

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

    #[DataProvider(methodName: 'provideLanguage')]
    public function testDeserialization(string $name, int $complexity, string $date = null): void
    {
        $response = sprintf($this->response, $name, $complexity, $date);
        $language = $this->serializer->deserialize($response, Language::class, 'soap');

        /** @var Language $language */
        $this->assertSame($name, $language->getName());
        $this->assertSame($complexity, $language->getComplexity());
        $this->assertEquals($date, $language->getSince()->format('Y-m-d'));
    }

    public static function provideLanguage(): iterable
    {
        return [
            ['C#', 102, '2002-01-10'],
            ['PHP', 67, '1995-06-12'],
            ['Java', 50, '1995-05-23'],
            ['Ruby', 39, '1990-07-01']
        ];
    }

    public function testUnsupportedVersion(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('Unsupported SOAP version'));

        $xml = '<env:Envelope xmlns:env="http://soap-version.com/unknown"/>';
        $this->serializer->deserialize($xml, stdClass::class, 'soap');
    }

    #[DataProvider(methodName: 'provideSoapFault')]
    public function testSoapFault(string $code, string $reason, string $node, string $detail): void
    {
        static::expectException(SoapFaultException::class);

        try {
            $fault = vsprintf($this->fault, func_get_args());
            $this->serializer->deserialize($fault, stdClass::class, 'soap');
        } catch (SoapFaultException $fault) {
            $this->assertSame($reason, $fault->getMessage());
            $this->assertSame($code, $fault->getFaultCode());

            $this->assertSame($code, $fault->code);
            $this->assertSame($reason, $fault->reason);
            $this->assertSame($node, $fault->node);
            $this->assertTrue(strpos($fault->detail['message'], $detail) !== false);

            /** @var SoapFault $soapFault */
            $soapFault = $fault->getPrevious();
            $this->assertInstanceOf(SoapFault::class, $soapFault);
            $this->assertSame($code, $soapFault->faultcode);
            $this->assertSame($reason, $soapFault->faultstring);
            $this->assertSame($node, $soapFault->faultactor);
            $this->assertTrue(strpos($soapFault->detail['message'], $detail) !== false);

            throw $fault;
        }
    }

    public static function provideSoapFault(): iterable
    {
        return [
            ['Client.NotFound', 'Lorum ipsum', 'http://xmpl.fault.com/err', 'Lorum ipsum dolor sit amet.'],
            ['Server', 'Praesent ut.', 'http://xmpl.fault.com/some', 'Praesent ut eros lacus.'],
        ];
    }

    public function testSoapFaultEmptyElements(): void
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
            $this->serializer->deserialize($fault, stdClass::class, 'soap');
        } catch (SoapFaultException $fault) {
            $this->assertSame('Error in soap call', $fault->getMessage());
            $this->assertSame('Client', $fault->getFaultCode());

            $this->assertSame('Client', $fault->code);
            $this->assertSame('Error in soap call', $fault->reason);
            $this->assertNull($fault->node);
            $this->assertNull($fault->detail);

            /** @var SoapFault $soapFault */
            $soapFault = $fault->getPrevious();
            $this->assertInstanceOf(SoapFault::class, $soapFault);
            $this->assertSame('Client', $soapFault->faultcode);
            $this->assertSame('Error in soap call', $soapFault->faultstring);
            $this->assertEmpty($soapFault->faultactor ?? '');
            $this->assertEmpty($soapFault->detail ?? '');

            throw $fault;
        }
    }

    #[DataProvider(methodName: 'provideMessages')]
    public function testSoap12Fault(string $locale, string $expected): void
    {
        static::expectException(SoapFaultException::class);
        locale_set_default($locale);

        try {
            $this->serializer->deserialize($this->fault12, stdClass::class, 'soap');
        } catch (SoapFaultException $fault) {
            $this->assertSame($expected, $fault->getMessage());
            $this->assertSame('Receiver.Validation.Error', $fault->getFaultCode());

            $this->assertSame('Receiver.Validation.Error', $fault->code);
            $this->assertSame($expected, $fault->reason);
            $this->assertSame('http://example.org/node', $fault->node);
            $this->assertTrue(in_array('Division by zero', $fault->detail['message']));

            /** @var SoapFault $soapFault */
            $soapFault = $fault->getPrevious();
            $this->assertInstanceOf(SoapFault::class, $soapFault);
            $this->assertSame('Server.Validation.Error', $soapFault->faultcode);
            $this->assertSame($expected, $soapFault->faultstring);
            $this->assertSame('http://example.org/node', $soapFault->faultactor);
            $this->assertTrue(in_array('Division by zero', $soapFault->detail['message']));

            throw $fault;
        }
    }

    public static function provideMessages(): iterable
    {
        return [
            ['en_US', 'Division by zero'],
            ['nl_NL', 'Division by zero'],
            ['fr_FR', 'Devision par zéro'],
        ];
    }
}
