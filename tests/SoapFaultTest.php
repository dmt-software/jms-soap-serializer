<?php

namespace DMT\Test\Soap\Serializer;

use DMT\Soap\Serializer\SoapFault;
use PHPUnit\Framework\TestCase;

/**
 * Class SoapFaultTest
 *
 * @package DMT\Test\Soap
 */
class SoapFaultTest extends TestCase
{
    /**
     * Test custom soap fault.
     */
    public function testSoapFault()
    {
        $fault = new SoapFault('Division by zero', 'Client', [
            'faultcode' => 'SOAP-ENV:Client',
            'faultstring' => 'Division by zero',
            'detail' => ['message' => 'Can not divide by zero']
        ]);

        static::assertSame('Client', $fault->getFaultCode());
        static::assertSame('Division by zero', $fault->getMessage());
        static::assertSame('SOAP-ENV:Client', $fault->faultcode);
        static::assertSame('Division by zero', $fault->faultstring);
        static::assertContains('Can not divide by zero', $fault->detail);
    }
}
