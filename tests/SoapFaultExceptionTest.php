<?php

namespace DMT\Test\Soap\Serializer;

use DMT\Soap\Serializer\SoapFaultException;
use PHPUnit\Framework\TestCase;

/**
 * Class SoapFaultTest
 *
 * @package DMT\Test\Soap
 */
class SoapFaultExceptionTest extends TestCase
{
    /**
     * Test custom soap fault.
     */
    public function testSoapFault()
    {
        $fault = new SoapFaultException(
            'Client.Validation',
            'Division by zero',
            'http://example.org/uri',
            [
                'message' => 'Can not divide by zero'
            ]
        );
        $this->assertSame('Client.Validation', $fault->getFaultCode());
        $this->assertSame('Division by zero', $fault->getMessage());

        $this->assertSame('Client.Validation', $fault->code);
        $this->assertSame('Division by zero', $fault->reason);
        $this->assertSame('http://example.org/uri', $fault->node);
        $this->assertContains('Can not divide by zero', $fault->detail);
    }
}
