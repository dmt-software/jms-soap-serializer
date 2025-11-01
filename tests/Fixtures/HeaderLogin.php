<?php

namespace DMT\Test\Soap\Serializer\Fixtures;

use DMT\Soap\Serializer\SoapHeaderInterface;
use JMS\Serializer\Annotation as JMS;

#[JMS\AccessType(type: 'public_method')]
#[JMS\XmlNamespace(uri: 'http://xmpl-namespace.nl', prefix: '')]
#[JMS\XmlRoot(name: 'HeaderAuthenticate', namespace: 'http://xmpl-namespace.nl')]
class HeaderLogin implements SoapHeaderInterface
{
    #[JMS\Type(name: "string")]
    #[JMS\XmlElement(cdata: false)]
    protected string $username;

    #[JMS\Type(name: "string")]
    #[JMS\XmlElement(cdata: false)]
    protected string $password;

    public function __construct(string $username, string $password)
    {
        $this->setUsername($username);
        $this->setPassword($password);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
