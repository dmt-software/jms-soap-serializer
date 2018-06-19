<?php

namespace DMT\Soap\Serializer;

/**
 * Interface SoapNamespaceInterface
 *
 * @package DMT\Soap
 */
interface SoapNamespaceInterface
{
    /**
     * @static int
     */
    const SOAP_1_1 = 1;

    /**
     * @static int
     */
    const SOAP_1_2 = 2;

    /**
     * @static array
     */
    const SOAP_NAMESPACES = [
        self::SOAP_1_1 => 'http://schemas.xmlsoap.org/soap/envelope/',
        // self::SOAP_1_2 => 'http://www.w3.org/2003/05/soap-envelope',
    ];
}
