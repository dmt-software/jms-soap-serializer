<?php

namespace DMT\Test\Soap\Serializer;

use DMT\Soap\Serializer\SoapDateHandler;
use DMT\Soap\Serializer\SoapNamespaceInterface;
use DMT\Soap\Serializer\SoapSerializationVisitor;
use DMT\Test\Soap\Serializer\Fixtures\Language;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class SoapSerializationVisitorTest
 *
 * @package DMT\Soap\Serializer
 */
class SoapSerializationVisitorTest extends TestCase
{
    /**
     * @var Serializer
     */
    protected $serializer;

    public function setUp()
    {
        AnnotationRegistry::registerUniqueLoader('class_exists');

        $this->serializer = SerializerBuilder::create()
            ->setSerializationVisitor(
                'soap',
                new SoapSerializationVisitor(
                    new SerializedNameAnnotationStrategy(
                        new IdenticalPropertyNamingStrategy()
                    )
                )
            )
            ->configureHandlers(
                function(HandlerRegistry $registry) {
                    $registry->registerSubscribingHandler(new SoapDateHandler());
                }
            )
            ->build();
    }

    /**
     * @dataProvider provideLanguage
     *
     * @param string $name
     * @param int $complexity
     * @param \DateTime $date
     */
    public function testSerialization(string $name, int $complexity, \DateTime $date)
    {
        $xml = simplexml_load_string($this->serializer->serialize(new Language($name, $complexity, $date), 'soap'));

        static::assertContains(SoapNamespaceInterface::SOAP_NAMESPACES[SOAP_1_1], $xml->getNamespaces());
        static::assertSame('Envelope', $xml->getName());
        static::assertSame('Body', $xml->xpath('/*[local-name()="Envelope"]/*')[0]->getName());

        $message = $xml->xpath('/*[local-name()="Envelope"]/*')[0]->children()[0];
        static::assertContains('http://xmpl-namespace.nl', $message->getNamespaces());
        static::assertSame($name, strval($message->name));
        static::assertSame($complexity, intval($message->complexity));
        static::assertSame($date->format('Y-m-d'), strval($message->since));
    }

    public function provideLanguage(): array
    {
        return [
            ['F#', 103, new \DateTime('2005-05-01')],
            ['JavaScript', 64, new \DateTime('1995-09-13')],
            ['Perl', 40, new \DateTime('1987-12-18')]
        ];
    }

    /**
     * @dataProvider provideMetadata
     *
     * @expectedException \JMS\Serializer\Exception\RuntimeException
     * @expectedExceptionMessage Missing XmlRootName or XmlRootNamespace for ArrayObject
     */
    public function testMissingXmlRoot(ClassMetadata $metadata)
    {
        $visitor = new SoapSerializationVisitor(new IdenticalPropertyNamingStrategy());
        $visitor->startVisitingObject($metadata, '', ['name' => 'ArrayObject'], SerializationContext::create());
    }

    public function provideMetadata(): array
    {
        $metadataFactory = function (array $metadataValues = []): ClassMetadata {
            /** @var ClassMetadata $classMetedata */
            $classMetedata = static::createMock(ClassMetadata::class);
            foreach ($metadataValues as $property => $value) {
                $classMetedata->{$property} = $value;
            }
            return $classMetedata;
        };

        return [
            [$metadataFactory()],
            [$metadataFactory(['xmlRootName' => 'Array'])],
            [$metadataFactory(['xmlRootNamespace' => 'http://xmpl-namepace.org'])],
        ];
    }

    /**
     * @expectedException \JMS\Serializer\Exception\InvalidArgumentException
     * @expectedExceptionMessage Unsupported SOAP version
     */
    public function testUnsupportedVersion()
    {
        $visitor = (
            new SoapSerializationVisitor(
                new IdenticalPropertyNamingStrategy()
            )
        )->setVersion(0);

        /** @var ClassMetadata $metadata */
        $metadata = $this->serializer->getMetadataFactory()->getMetadataForClass(Language::class);
        $visitor->startVisitingObject($metadata, '', [], SerializationContext::create());
    }
}
