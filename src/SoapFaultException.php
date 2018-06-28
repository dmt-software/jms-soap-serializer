<?php

namespace DMT\Soap\Serializer;

/**
 * Class SoapFaultException
 *
 * @package DMT\Soap
 */
class SoapFaultException extends \RuntimeException
{
    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $reason;

    /**
     * @var string
     */
    public $node;

    /**
     * @var array
     */
    public $detail;

    /**
     * SoapFaultException constructor.
     *
     * @param string $code
     * @param string $reason
     * @param string|null $node
     * @param array|null $detail
     */
    public function __construct(string $code, string $reason, string $node = null, array $detail = null)
    {
        $this->code = $code;
        $this->reason = $reason;
        $this->node = $node;
        $this->detail = $detail;

        $previous = null;
        if (class_exists(\SoapFault::class)) {
            $code = preg_replace(
                ['~^Receiver~', '~^Sender~', '~^DataEncodingUnknown~'],
                ['Server', 'Client', 'Client'],
                $this->code
            );
            $previous = new \SoapFault(...func_get_args());
        }

        parent::__construct($reason, 0, $previous);
    }

    public function getFaultCode(): string
    {
        return $this->code;
    }
}
