<?php

namespace DMT\Test\Soap\Serializer\Fixtures;

use JMS\Serializer\Annotation as JMS;

class Count
{
    #[JMS\XmlElement(cdata: false, namespace: 'http://xmpl-namespace.nl')]
    protected mixed $num;

    public function __construct(mixed $num)
    {
        $this->num = $num;
    }

    public function getNum(): mixed
    {
        return $this->num;
    }

    public function setNum(mixed $num): void
    {
        $this->num = $num;
    }
}
