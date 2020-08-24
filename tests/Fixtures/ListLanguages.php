<?php

namespace DMT\Test\Soap\Serializer\Fixtures;

use JMS\Serializer\Annotation as JMS;
use DMT\Test\Soap\Serializer\Fixtures\Count;

/**
 * Class ListLanguages
 *
 * @JMS\AccessType("public_method")
 * @JMS\XmlNamespace(uri="http://xmpl-namespace.nl", prefix="")
 * @JMS\XmlRoot("Languages", namespace="http://xmpl-namespace.nl")
 */
class ListLanguages
{
    /**
     * @JMS\XmlList(inline=true, entry="language", namespace="http://xmpl-namespace.nl")
     * @JMS\Type("array<DMT\Test\Soap\Serializer\Fixtures\Language>")
     *
     * @var Language[]
     */
    protected $languages;

    /**
     * @JMS\XmlElement(cdata=false, namespace="http://xmpl-namespace.nl")
     * @JMS\Type("DMT\Test\Soap\Serializer\Fixtures\Count")
     *
     * @var Count
     */
    protected $count;

    /**
     * @return Language[]
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * @param Language[] $languages
     */
    public function setLanguages(array $languages): void
    {
        $this->languages = $languages;
    }

    /**
     * @return \DMT\Test\Soap\Serializer\Fixtures\Count
     */
    public function getCount(): \DMT\Test\Soap\Serializer\Fixtures\Count
    {
        return $this->count;
    }

    /**
     * @param \DMT\Test\Soap\Serializer\Fixtures\Count $count
     */
    public function setCount(\DMT\Test\Soap\Serializer\Fixtures\Count $count): void
    {
        $this->count = $count;
    }
}
