<?php

namespace DMT\Test\Soap\Serializer;

use DMT\Soap\Serializer\SoapMessageEventSubscriber;
use DOMDocument;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\XmlSerializationVisitor;
use Metadata\MetadataFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class SoapMessageEventSubscriberTest extends TestCase
{
    /**
     * @dataProvider provideMetadata
     *
     * @param ClassMetadata $metadata
     */
    public function testMissingXmlRoot(ClassMetadata $metadata)
    {
        $this->expectExceptionObject(new RuntimeException('Missing XmlRootName or XmlRootNamespace for ArrayObject'));

        $eventSubscriber = new SoapMessageEventSubscriber();
        $eventSubscriber->addMessage(
            new PreSerializeEvent(
                $this->getSerializationContext($metadata),
                new stdClass(),
                ['name' => 'ArrayObject']
            )
        );
    }

    public function provideMetadata(): array
    {
        $metadataFactory = function (array $metadataValues = []): ClassMetadata {
            /** @var ClassMetadata $classMetadata */
            $classMetadata = static::createMock(ClassMetadata::class);
            foreach ($metadataValues as $property => $value) {
                $classMetadata->{$property} = $value;
            }
            return $classMetadata;
        };

        return [
            [$metadataFactory()],
            [$metadataFactory(['xmlRootName' => 'Array'])],
            [$metadataFactory(['xmlRootNamespace' => 'http://xmpl-namepace.org'])],
        ];
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return SerializationContext
     */
    protected function getSerializationContext(ClassMetadata $metadata): SerializationContext
    {
        /** @var SerializationContext|MockObject $context */
        $context = static::createMock(SerializationContext::class);
        $context
            ->expects(static::any())
            ->method('getMetadataFactory')
            ->willReturnCallback(
                function () use ($metadata) {
                    $factory = static::createMock(MetadataFactory::class);
                    $factory
                        ->expects(static::any())
                        ->method('getMetadataForClass')
                        ->willReturn($metadata);

                    return $factory;
                }
            );

        $context
            ->expects(static::any())
            ->method('getVisitor')
            ->willReturnCallback(
                function () {
                    $visitor = new XmlSerializationVisitor();
                    $visitor->setCurrentNode((new DOMDocument())->createElement('soap:Body'));

                    return $visitor;
                }
            );

        $context
            ->expects(static::any())
            ->method('getDepth')
            ->willReturn(1);

        return $context;
    }
}
