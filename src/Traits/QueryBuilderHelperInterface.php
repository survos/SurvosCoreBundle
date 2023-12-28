<?php

namespace Survos\CoreBundle\Traits;

interface QueryBuilderHelperInterface
{
    public function getCounts(string $field): array;

    public function findBygetCountsByField($field = 'marking', $filters = []): array;
}
