<?php

namespace DMT\Soap\Serializer;

use DOMDocument;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\XmlSerializationVisitor;

/**
 * Class SoapHeaderEventSubscriber
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
        /** @var XmlSerializationVisitor $visitor */
        $visitor = $event->getVisitor();
        /** @var DOMDocument $document */
        $document = $visitor->getDocument();

        if (!$this->hasSoapHeader($document) && $context->getDepth() === 0) {
            /** @var ClassMetadata $metadata */
            $metadata = $context->getMetadataFactory()->getMetadataForClass(get_class($this->header));

            $visitor->setCurrentNode($document->firstChild);
            $visitor->setCurrentNode(
                $header = $document->firstChild->insertBefore(
                    $document->createElementNS($document->lookupNamespaceUri('soap'), 'Header'),
                    $document->firstChild->firstChild
                )
            );
            
            if ($metadata->xmlRootPrefix !== null) {
                $tagName = $metadata->xmlRootPrefix . ':' . $metadata->xmlRootName;
            } else {
                $tagName = $metadata->xmlRootName;
            }
           
            $visitor->setCurrentNode(
                $header->appendChild(
                    $document->createElementNS($metadata->xmlRootNamespace, $tagName)
                )
            );

            $context->getNavigator()->accept($this->header, null);
        }
    }

    /**
     * Check if the header is already added.
     *
     * @param DOMDocument $document
     *
     * @return bool
     */
    protected function hasSoapHeader(DOMDocument $document): bool
    {
        return $document->getElementsByTagNameNS($document->lookupNamespaceUri('soap'), 'Header')->length > 0;
    }
}
