<?php

namespace Survos\CoreBundle\Entity;

interface RouteParametersInterface
{
    public function getUniqueIdentifiers(): array;
    public function getRP(?array $addlParams=[]): array;

}
