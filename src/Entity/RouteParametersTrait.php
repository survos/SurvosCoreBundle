<?php

namespace Survos\CoreBundle\Entity;

use JetBrains\PhpStorm\Deprecated;
use Symfony\Component\Serializer\Annotation\Groups;
use function Symfony\Component\String\u;

trait RouteParametersTrait
{
//    public function getUniqueIdentifiers(): array
//    {
//        return [strtolower( (new \ReflectionClass($this))->getShortName() ) . 'Id' => $this->getId()];
//    }

    #[Groups(['rp','transitions'])]
    public function getrp(?array $addlParams=[]): array
    {
        return array_merge($this->getUniqueIdentifiers(), $addlParams);
    }

    static public function getPrefix(string $class = null): string
    {
        if (!$class) {
            $class = get_called_class();
        }
        $shortName = strtolower(u( $x = (new \ReflectionClass($class))->getShortName() )->snake()->lower()->ascii());

//        $shortName = strtolower( (new \ReflectionClass(get_called_class()))->getShortName() );
        return $shortName;
    }


}
