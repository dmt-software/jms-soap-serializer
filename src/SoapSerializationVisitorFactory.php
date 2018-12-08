<?php

namespace DMT\Soap\Serializer;

use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Visitor\Factory\SerializationVisitorFactory;
use JMS\Serializer\Visitor\SerializationVisitorInterface;
use JMS\Serializer\XmlSerializationVisitor;

/**
 * CLass SoapSerializationVisitorFactory
 *
 * @package DMT\Soap
 */
final class SoapSerializationVisitorFactory implements SerializationVisitorFactory, SoapNamespaceInterface
{
    /**
     * @var string
     */
    private $defaultVersion = '1.0';

    /**
     * @var string
     */
    private $defaultEncoding = 'UTF-8';

    /**
     * @var bool
     */
    private $formatOutput = true;

    /**
     * @var int
     */
    private $soapVersion = self::SOAP_1_1;

    public function getVisitor(): SerializationVisitorInterface
    {
        $visitor = new XmlSerializationVisitor(
            $this->formatOutput,
            $this->defaultEncoding,
            $this->defaultVersion
        );

        $envelope = $visitor->createRoot(null, 'Envelope', $this->getSoapNamespace(), 'soap');

        $visitor->setCurrentNode($this->addXmlElement($envelope, 'Body', $this->getSoapNamespace()));

        return $visitor;
    }

    public function setSoapVersion(int $soapVersion): self
    {
        $this->soapVersion = $soapVersion;
        return $this;
    }

    public function setDefaultVersion(string $version): self
    {
        $this->defaultVersion = $version;
        return $this;
    }

    public function setDefaultEncoding(string $encoding): self
    {
        $this->defaultEncoding = $encoding;
        return $this;
    }

    public function setFormatOutput(bool $formatOutput): self
    {
        $this->formatOutput = $formatOutput;
        return $this;
    }

    /**
     * Get the namespace for the current SOAP version.
     *
     * @return string
     * @throws InvalidArgumentException
     */
    protected function getSoapNamespace(): string
    {
        if (!array_key_exists($this->soapVersion, static::SOAP_NAMESPACES)) {
            throw new InvalidArgumentException('Unsupported SOAP version');
        }

        return static::SOAP_NAMESPACES[$this->soapVersion];
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

        return $node;
    }
}