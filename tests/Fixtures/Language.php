<?php

namespace DMT\Test\Soap\Serializer\Fixtures;

use DateTime;
use JMS\Serializer\Annotation as JMS;

#[JMS\AccessType(type: 'public_method')]
#[JMS\XmlNamespace(uri: 'http://xmpl-namespace.nl', prefix: '')]
#[JMS\XmlRoot(name: 'Language', namespace: 'http://xmpl-namespace.nl')]
class Language
{
    #[JMS\Type(name: 'string')]
    #[JMS\XmlElement(cdata: false, namespace: 'http://xmpl-namespace.nl')]
    protected string $name;

    #[JMS\Type(name: 'int')]
    #[JMS\XmlElement(cdata: false, namespace: 'http://xmpl-namespace.nl')]
    protected int $complexity;

    #[JMS\Type(name: 'DateTime<"Y-m-d">')]
    #[JMS\XmlElement(cdata: false, namespace: 'http://xmpl-namespace.nl')]
    protected DateTime $since;

    public function __construct(string $name, int $complexity, DateTime $since)
    {
        $this->setName($name);
        $this->setComplexity($complexity);
        $this->setSince($since);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getComplexity(): int
    {
        return $this->complexity;
    }

    public function setComplexity(int $complexity): void
    {
        $this->complexity = $complexity;
    }

    public function getSince(): DateTime
    {
        return $this->since;
    }

    public function setSince(DateTime $since): void
    {
        $this->since = $since;
    }
}
