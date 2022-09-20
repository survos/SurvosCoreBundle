<?php

namespace Survos\CoreBundle\Entity;

use Survos\CoreBundle\Entity\RouteParametersInterface;
use Survos\CoreBundle\Entity\RouteParametersTrait;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use function Symfony\Component\String\u;

/**
 * @deprecated implement RouteParametersInterface instead.
 */
abstract class SurvosCoreEntity implements RouteParametersInterface
{
    use RouteParametersTrait;
}
