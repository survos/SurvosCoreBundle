<?php

namespace Survos\CoreBundle\Entity;

interface RouteParametersInterface
{
    public function getUniqueIdentifiers(): array;
    public function getrp(?array $addlParams=[]): array;
    static public function getPrefix(string $class = null): string;

}
