<?php

namespace Survos\CoreBundle\Entity;

use JetBrains\PhpStorm\Deprecated;
use Symfony\Component\Serializer\Annotation\Groups;

trait RouteParametersTrait
{
    public function getUniqueIdentifiers(): array
    {
        return [strtolower( (new \ReflectionClass($this))->getShortName() ) . 'Id' => $this->getId()];
    }

    #[Groups(['rp','transitions'])]
    public function getrp(?array $addlParams=[]): array
    {
        return $this->rp($addlParams);
    }

}
