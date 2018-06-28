<?php

namespace DMT\Soap\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\SerializationContext;

/**
 * Class EventSubscriber
 *
 * @package DMT\Soap
 */
class SoapHeaderEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var SoapHeaderInterface
     */
    protected $header;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            [
                'event' => 'serializer.post_serialize',
                'method' => 'addSoapHeader',
                'format' => 'soap',
            ],
        ];
    }

    /**
     * EventSubscriber constructor.
     *
     * @param SoapHeaderInterface $header
     */
    public function __construct(SoapHeaderInterface $header)
    {
        $this->header = $header;
    }

    /**
     * @param ObjectEvent $event
     */
    public function addSoapHeader(ObjectEvent $event)
    {
        /** @var SerializationContext $context */
        $context = $event->getContext();
        /** @var SoapSerializationVisitor $visitor */
        $visitor = $event->getVisitor();
        /** @var \DOMDocument $document */
        $document = $visitor->getDocument();

        if ($context->getDepth() === 0 && !$this->hasSoapHeader($document)) {
            $visitor->setCurrentNode($document->firstChild);
            $visitor->setCurrentNode(
                $document->firstChild->insertBefore(
                    $document->createElementNS($document->lookupNamespaceUri('soap'), 'Header'),
                    $document->firstChild->firstChild
                )
            );
            $context->getNavigator()->accept($this->header, null, $context);
        }
    }

    /**
     * Check if the header is already added.
     *
     * @param \DOMDocument $document
     *
     * @return bool
     */
    protected function hasSoapHeader(\DOMDocument $document): bool
    {
        return $document->getElementsByTagNameNS($document->lookupNamespaceUri('soap'), 'Header')->length > 0;
    }
}
