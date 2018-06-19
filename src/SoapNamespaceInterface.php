<?php

namespace DMT\Soap\Serializer;

/** @codeCoverageIgnoreStart */
if (!defined('SOAP_1_1')) {
    define('SOAP_1_1', 1);
}

if (!defined('SOAP_1_2')) {
    define('SOAP_1_2', 2);
}
/** @codeCoverageIgnoreEnd */

/**
 * Interface SoapNamespaceInterface
 *
 * @package DMT\Soap
 */
interface SoapNamespaceInterface
{
    /**
     * @static array
     */
    const SOAP_NAMESPACES = [
        SOAP_1_1 => 'http://schemas.xmlsoap.org/soap/envelope/',
        // SOAP_1_2 => 'http://www.w3.org/2003/05/soap-envelope',
    ];
}
