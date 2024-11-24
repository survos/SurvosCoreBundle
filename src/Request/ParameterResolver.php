<?php

namespace Survos\CoreBundle\Request;

use Doctrine\ORM\EntityManagerInterface;
use Survos\CoreBundle\Entity\RouteParametersInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

// use Zenstruck\Metadata; maybe someday

class ParameterResolver implements ValueResolverInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }


    /**
     * @return array<mixed>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        // get the argument type (e.g. BookingId)
        $argumentType = $argument->getType();

        if (!is_subclass_of($argumentType, RouteParametersInterface::class)) {
            return [];
        }

        if (defined($const=$argumentType.'::UNIQUE_PARAMETERS')) {
            foreach (constant($const) as $param => $getter) {
                if ($value = $request->attributes->get($param)) {
                    $x[$getter] = $value;
                }
            }
        } else {
            if (method_exists($argument, 'getUniqueIdentifiers')) {
                // @todo: use the method...
                $x = $argument->getUniqueIdentifiers();
            }
//            assert(false, $argumentType . " should declare a UNIQUE_PARAMETERS constant");
        }

        if (!empty($x)) {
            $repository = $this->entityManager->getRepository($argumentType);
            if ($entity = $repository->findOneBy($x)) {
                return [$entity];
            }
        }
        return [];
    }
}
