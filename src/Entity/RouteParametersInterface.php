<?php

namespace Survos\CoreBundle\Entity;

interface RouteParametersInterface
{
    public function getUniqueIdentifiers(): array;

    public function getrp(?array $addlParams = []): array;

    public static function getPrefix(string $class = null): string;
}
