<?php

namespace Survos\CoreBundle\Entity;

interface RouteParametersInterface
{
    public function getUniqueIdentifiers(): array;

    public function getRp(?array $addlParams = []): array;

    public static function getClassnamePrefix(string|null $class = null): string;
}
