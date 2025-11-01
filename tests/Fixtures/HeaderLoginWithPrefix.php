<?php

declare(strict_types=1);

namespace DMT\Test\Soap\Serializer\Fixtures;

use JMS\Serializer\Annotation as JMS;

#[JMS\AccessType(type: 'public_method')]
#[JMS\XmlNamespace(uri: 'http://xmpl-namespace.nl', prefix: '')]
#[JMS\XmlRoot(name: 'HeaderAuthenticate', namespace: 'http://xmpl-namespace.nl', prefix: 'ns')]
class HeaderLoginWithPrefix extends HeaderLogin
{
}
