<?php

namespace DMT\Soap\Serializer;

/**
 * Interface SoapNamespaceInterface
 *
 * @package DMT\Soap
 */
interface SoapNamespaceInterface
{
    public const int SOAP_1_1 = 1;

    public const int SOAP_1_2 = 2;

    public const array SOAP_NAMESPACES = [
        self::SOAP_1_1 => 'http://schemas.xmlsoap.org/soap/envelope/',
        self::SOAP_1_2 => 'http://www.w3.org/2003/05/soap-envelope',
    ];
}
