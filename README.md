# SOAP Serializer

[![Build Status](https://travis-ci.org/dmt-software/jms-soap-serializer.svg?branch=master)](https://travis-ci.org/dmt-software/jms-soap-serializer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dmt-software/jms-soap-serializer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dmt-software/jms-soap-serializer/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/dmt-software/jms-soap-serializer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/dmt-software/jms-soap-serializer/?branch=master)

## Install
`composer require dmt-software/jms-soap-serializer`

## Usage

### Configure Serializer

```php
<?php
 
use DMT\Soap\Serializer\SoapDeserializationVisitorFactory;
use DMT\Soap\Serializer\SoapSerializationVisitorFactory;
use DMT\Soap\Serializer\SoapMessageEventSubscriber;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\SerializerBuilder;
 
$builder = SerializerBuilder::create()
    ->setSerializationVisitor('soap', new SoapSerializationVisitorFactory())
    ->setDeserializationVisitor('soap', new SoapDeserializationVisitorFactory())
    ->configureListeners(
        function (EventDispatcher $dispatcher) {
            $dispatcher->addSubscriber(
                new SoapMessageEventSubscriber()
            );
        }
    );

$serializer = $builder->build();
```

#### Enable (de)serialization of DateTime objects

```php
<?php
 
use DMT\Soap\Serializer\SoapDateHandler;
use JMS\Serializer\Handler\HandlerRegistry;

/** @var JMS\Serializer\SerializerBuilder $builder */
$builder->configureHandlers(
    function(HandlerRegistry $registry) {
        $registry->registerSubscribingHandler(new SoapDateHandler());
    }
);
```  

#### Configure Serializer with SoapHeader

```php
<?php
 
use DMT\Soap\Serializer\SoapHeaderInterface;
use DMT\Soap\Serializer\SoapHeaderEventSubscriber;
use DMT\Soap\Serializer\SoapMessageEventSubscriber;
use JMS\Serializer\EventDispatcher\EventDispatcher;
 
/** @var JMS\Serializer\SerializerBuilder $builder */
$builder->configureListeners(
    function (EventDispatcher $dispatcher) {
        $dispatcher->addSubscriber(
            new SoapMessageEventSubscriber()
        );
        /** @var SoapHeaderInterface $soapHeader */
        $dispatcher->addSubscriber(
            new SoapHeaderEventSubscriber($soapHeader)
        );
    }
);
```

#### Using SOAP 1.2

```php
<?php
 
use DMT\Soap\Serializer\SoapNamespaceInterface;
use DMT\Soap\Serializer\SoapSerializationVisitorFactory;

/** @var JMS\Serializer\SerializerBuilder $builder */
$builder->setSerializationVisitor(
    'soap',
    (new SoapSerializationVisitorFactory())
        ->setSoapVersion(SoapNamespaceInterface::SOAP_1_2)
);
```

### Using Serializer

#### Serialize SOAP Request 

```php
<?php
 
use JMS\Serializer\Serializer;

/** @var Message $requestMessage */
/** @var Serializer $serializer */
$request = $serializer->serialize($requestMessage, 'soap');

// $request = '<soap:Envelope ...><soap:Body><ns1:Message>...</ns1:Message></soap:Body></soap:Envelope>';
```

#### Deserialize SOAP Response

```php
<?php
 
use JMS\Serializer\Serializer;

/** @var Serializer $serializer */
$response = $serializer->deserialize('<env:Envelope ... </env:Envelope>', ResponseMessage::class, 'soap');

// $response instanceof ResponseMessage
```

### Debugging

#### Failing to make a request
When creating a SOAP message you must provide a XmlRoot and XmlRootNamespace. If you forgot to provide them an exception
is thrown "*Missing XmlRootName or XmlRootNamespace for ?YourSOAPRequest?". 
 
To fix this add the XmlRoot annotation to your configuration:
```php
<?php 
namespace Any\NS; 
 
use JMS\Serializer\Annotation as JMS;
 
/** 
 * @JMS\XmlRoot("YourSOAPRequest", namespace="http://ns-for-your-request")
 */
class YourSOAPRequest
{
    ...
}
```
or if you're using yaml configuration:
      
```yaml
Any\NS\YourSOAPRequest:
  ...
  xml_root_name: YourSOAPRequest
  xml_root_namespace: http://ns-for-your-request
  ...   
```
