<?php

namespace Survos\CoreBundle\Entity;

use Symfony\Component\Serializer\Annotation\Groups;

trait RouteParametersTrait
{
    public function getUniqueIdentifiers(): array
    {
        return [strtolower( (new \ReflectionClass($this))->getShortName() ) . 'Id' => $this->getId()];
    }

    #[Groups(['rp','transitions'])]
    public function getRP(?array $addlParams=[]): array
    {
        return array_merge($this->getUniqueIdentifiers(), $addlParams);
    }
}
