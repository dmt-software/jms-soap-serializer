<?php

declare(strict_types=1);

namespace DMT\Test\Soap\Serializer\Fixtures;

use JMS\Serializer\Annotation as JMS;

/**
 * Class ListLanguagesWithPrefix
 *
 * @JMS\AccessType("public_method")
 * @JMS\XmlRoot("Language", namespace="http://xmpl-namespace.nl", prefix="ns")
 */
class LanguageWithPrefix extends Language
{
}