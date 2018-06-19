<?php

namespace DMT\Soap\Serializer;

use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\XmlDeserializationVisitor;

/**
 * Class SoapDeserializationVisitor
 *
 * @package DMT\Soap
 */
class SoapDeserializationVisitor extends XmlDeserializationVisitor implements SoapNamespaceInterface
{
    /**
     * @param string $data
     *
     * @return mixed|\SimpleXMLElement
     * @throws SoapFault
     */
    public function prepare($data)
    {
        $element = parent::prepare($data);

        $version = array_search(current($element->getNamespaces()), static::SOAP_NAMESPACES);
        if (!$version) {
            throw new InvalidArgumentException('Unsupported SOAP version');
        }

        $messages = $element->xpath('*[local-name()="Body"]/*');
        if (count($messages) === 1) {
            $data = $messages[0];
        }

        if ($data->getName() === 'Fault') {
            $this->throwSoapFault($data);
        }

        return $data;
    }

    /**
     * @param \SimpleXMLElement $fault
     *
     * @throws SoapFault
     */
    protected function throwSoapFault(\SimpleXMLElement $fault)
    {
        $version = array_search(current($fault->getNamespaces()), static::SOAP_NAMESPACES);

        $reason = $code = null;
        if ($version === SOAP_1_1) {
            $reason = $fault->faultstring;
            $code = trim(strstr($fault->faultcode, ":") ?? $fault->faultcode, ':');
        }

        throw new SoapFault($reason, $code, $this->faultToArray($fault));
    }

    /**
     * @param \SimpleXMLElement $element
     *
     * @return array
     */
    protected function faultToArray(\SimpleXMLElement $element): array
    {
        return array_map(
            function (\SimpleXMLElement $element) {
                return $element->children() ? $this->faultToArray($element) : strval($element);
            },
            iterator_to_array($element->children())
        );
    }
}
