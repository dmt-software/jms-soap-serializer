<?php

namespace DMT\Soap\Serializer;

use JMS\Serializer\Visitor\DeserializationVisitorInterface;
use JMS\Serializer\Visitor\Factory\DeserializationVisitorFactory;
use JMS\Serializer\XmlDeserializationVisitor;

/**
 * Class SoapDeserializationVisitorFactory
 *
 * @package DMT\Soap
 */
class SoapDeserializationVisitorFactory implements DeserializationVisitorFactory
{
    /**
     * @var bool
     */
    private $disableExternalEntities = true;

    /**
     * @return DeserializationVisitorInterface
     */
    public function getVisitor(): DeserializationVisitorInterface
    {
        return new XmlDeserializationVisitor($this->disableExternalEntities);
    }

    /**
     * @param bool $enable
     *
     * @return SoapDeserializationVisitorFactory
     */
    public function enableExternalEntities(bool $enable = true): self
    {
        $this->disableExternalEntities = !$enable;
        return $this;
    }
}