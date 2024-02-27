<?php

namespace DMT\Test\Soap\Serializer;

use DMT\Soap\Serializer\SoapDateHandler;
use DMT\Soap\Serializer\SoapDeserializationVisitorFactory;
use DMT\Soap\Serializer\SoapMessageEventSubscriber;
use DMT\Soap\Serializer\SoapSerializationVisitorFactory;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

trait SoapSerializerSetUpTrait
{
    /**
     * @var Serializer
     */
    protected $serializer;

    public function setUp(): void
    {
        $this->serializer = SerializerBuilder::create()
            ->setSerializationVisitor('soap', new SoapSerializationVisitorFactory())
            ->setDeserializationVisitor('soap', new SoapDeserializationVisitorFactory())
            ->setPropertyNamingStrategy(
                new SerializedNameAnnotationStrategy(
                    new IdenticalPropertyNamingStrategy()
                )
            )
            ->configureListeners(
                function (EventDispatcher $dispatcher) {
                    $dispatcher->addSubscriber(
                        new SoapMessageEventSubscriber()
                    );
                }
            )
            ->configureHandlers(
                function (HandlerRegistry $registry) {
                    $registry->registerSubscribingHandler(new SoapDateHandler());
                }
            )
            ->build();
    }
}
