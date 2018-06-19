<?php

namespace DMT\Soap\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\XmlSerializationVisitor;

/**
 * Class SoapSerializationVisitor
 *
 * @package DMT\Soap
 */
class SoapSerializationVisitor extends XmlSerializationVisitor implements SoapNamespaceInterface
{
    /**
     * @var int
     */
    protected $version = SOAP_1_1;

    /**
     * @param int $version
     *
     * @return SoapSerializationVisitor
     */
    public function setVersion(int $version = SOAP_1_1): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @param ClassMetadata $metadata
     * @param mixed $data
     * @param array $type
     * @param Context $context
     */
    public function startVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
        if ($this->document === null) {
            if (!isset($metadata->xmlRootName, $metadata->xmlRootNamespace)) {
                throw new RuntimeException('Missing XmlRootName or XmlRootNamespace for ' . $type['name']);
            }

            $this->document = parent::createDocument(null, null, false);

            $envelope = $this->addXmlElement($this->document, 'soap:Envelope', $this->getSoapNamespace());
            $body = $this->addXmlElement($envelope, 'soap:Body', $this->getSoapNamespace());
            $this->addXmlElement($body, $metadata->xmlRootName, $metadata->xmlRootNamespace);
        }

        parent::startVisitingObject($metadata, $data, $type, $context);
    }

    /**
     * Get the namespace for the current SOAP version.
     *
     * @return string
     * @throws InvalidArgumentException
     */
    protected function getSoapNamespace(): string
    {
        if (!array_key_exists($this->version, static::SOAP_NAMESPACES)) {
            throw new InvalidArgumentException('Unsupported SOAP version');
        }

        return static::SOAP_NAMESPACES[$this->version];
    }

    /**
     * Wraps XmlElement(s) that will hold the SOAP message.
     *
     * @param \DOMNode $parentNode
     * @param string $nodeName
     * @param string $namespace
     *
     * @return \DOMNode
     */
    protected function addXmlElement(\DOMNode $parentNode, string $nodeName, string $namespace): \DOMNode
    {
        $node = $parentNode->appendChild(
            ($parentNode->ownerDocument ?? $parentNode)->createElementNS($namespace, $nodeName)
        );
        $this->setCurrentNode($node);

        return $node;
    }
}
