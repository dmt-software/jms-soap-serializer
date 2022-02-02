<?php

declare(strict_types=1);

namespace DMT\Test\Soap\Serializer\Fixtures;

use JMS\Serializer\Annotation as JMS;

/**
 * Class HeaderLogin
 *
 * @JMS\AccessType("public_method")
 * @JMS\XmlNamespace(uri="http://xmpl-namespace.nl", prefix="")
 * @JMS\XmlRoot("HeaderAuthenticate", namespace="http://xmpl-namespace.nl", prefix="ns")
 */
class HeaderLoginWithPrefix extends HeaderLogin
{
}