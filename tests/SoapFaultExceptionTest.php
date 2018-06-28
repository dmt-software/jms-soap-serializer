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
        static::assertSame('Client.Validation', $fault->getFaultCode());
        static::assertSame('Division by zero', $fault->getMessage());

        static::assertSame('Client.Validation', $fault->code);
        static::assertSame('Division by zero', $fault->reason);
        static::assertSame('http://example.org/uri', $fault->node);
        static::assertContains('Can not divide by zero', $fault->detail);
    }
}
