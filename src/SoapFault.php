<?php

namespace DMT\Soap\Serializer;

/**
 * Class SoapFault
 *
 * @package DMT\Soap
 */
class SoapFault extends \RuntimeException
{
    /**
     * @var string
     */
    protected $faultCode;

    /**
     * @var array
     */
    protected $fault;

    /**
     * SoapFault constructor.
     *
     * @param string $reason
     * @param string $code
     * @param array $fault
     */
    public function __construct(string $reason = null, string $code = null, array $fault = [])
    {
        parent::__construct($reason, 0);

        $this->faultCode = $code;
        $this->fault = $fault;
    }

    /**
     * Get the fault code.
     *
     * @return string
     */
    public function getFaultCode(): ?string
    {
        return $this->faultCode;
    }

    /**
     * Access properties to mimic original SoapFault behaviour.
     *
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        return array_key_exists($name, $this->fault) ? $this->fault[$name] : null;
    }
}
