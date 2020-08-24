<?php

namespace DMT\Test\Soap\Serializer\Fixtures;

use JMS\Serializer\Annotation as JMS;

class Count
{
    /**
     * @JMS\XmlElement(cdata=false, namespace="http://xmpl-namespace.nl")
     * @JMS\Type("string")
     */
    protected $num;

    /**
     * Count constructor.
     * @param $num
     */
    public function __construct($num)
    {
        $this->num = $num;
    }

    /**
     * @return mixed
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * @param mixed $num
     */
    public function setNum($num): void
    {
        $this->num = $num;
    }
}