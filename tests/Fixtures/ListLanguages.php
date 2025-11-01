<?php

namespace DMT\Test\Soap\Serializer\Fixtures;

use JMS\Serializer\Annotation as JMS;

#[JMS\AccessType(type: 'public_method')]
#[JMS\XmlNamespace(uri: 'http://xmpl-namespace.nl', prefix: '')]
#[JMS\XmlRoot(name: 'Languages', namespace: 'http://xmpl-namespace.nl')]
class ListLanguages
{
    /** @var array<Language> */
    #[JMS\XmlList(inline: true, entry: 'language', namespace: 'http://xmpl-namespace.nl')]
    #[JMS\Type(name: 'array<DMT\Test\Soap\Serializer\Fixtures\Language>')]
    protected array $languages;

    #[JMS\XmlElement(cdata: false, namespace: 'http://xmpl-namespace.nl')]
    #[JMS\Type(name: Count::class)]
    protected Count $count;

    public function getLanguages(): array
    {
        return $this->languages;
    }

    public function setLanguages(array $languages): void
    {
        $this->languages = $languages;
    }

    public function getCount(): Count
    {
        return $this->count;
    }

    public function setCount(Count $count): void
    {
        $this->count = $count;
    }
}
