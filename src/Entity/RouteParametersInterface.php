<?php

namespace Survos\CoreBundle\Entity;

interface RouteParametersInterface
{
    public function getUniqueIdentifiers(): array;

    public function getrp(?array $addlParams = []): array;

    public static function getClassnamePrefix(string $class = null): string;
}
