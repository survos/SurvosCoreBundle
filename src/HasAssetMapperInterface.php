<?php

namespace Survos\CoreBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface HasAssetMapperInterface
{
    public function isAssetMapperAvailable(ContainerBuilder $container): bool;

    public function getPaths(): array;
}
