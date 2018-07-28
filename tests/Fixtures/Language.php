<?php

namespace DMT\Test\Soap\Serializer\Fixtures;

use JMS\Serializer\Annotation as JMS;

/**
 * Class Language
 *
 * @JMS\AccessType("public_method")
 * @JMS\XmlNamespace(uri="http://xmpl-namespace.nl", prefix="")
 * @JMS\XmlRoot("Language", namespace="http://xmpl-namespace.nl")
 */
class Language
{
    /**
     * @JMS\XmlElement(cdata=false, namespace="http://xmpl-namespace.nl")
     * @JMS\Type("string")
     *
     * @var string
     */
    protected $name;

    /**
     * @JMS\XmlElement(cdata=false, namespace="http://xmpl-namespace.nl")
     * @JMS\Type("integer")
     *
     * @var int
     */
    protected $complexity;

    /**
     * @JMS\XmlElement(cdata=false, namespace="http://xmpl-namespace.nl")
     * @JMS\Type("DateTime<'Y-m-d'>")
     *
     * @var \DateTime
     */
    protected $since;

    /**
     * Language constructor.
     *
     * @param string $name The programming language name.
     * @param int $complexity The complexity measured by the number of reserved words or keywords.
     * @param \DateTime $since
     */
    public function __construct(string $name, int $complexity, \DateTime $since)
    {
        $this->setName($name);
        $this->setComplexity($complexity);
        $this->setSince($since);
    }


    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getComplexity(): ?int
    {
        return $this->complexity;
    }

    /**
     * @param int $complexity
     */
    public function setComplexity(int $complexity): void
    {
        $this->complexity = $complexity;
    }

    /**
     * @return \DateTime
     */
    public function getSince(): \DateTime
    {
        return $this->since;
    }

    /**
     * @param \DateTime $since
     */
    public function setSince(\DateTime $since): void
    {
        $this->since = $since;
    }
}
