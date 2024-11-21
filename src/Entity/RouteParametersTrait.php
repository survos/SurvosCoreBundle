<?php

namespace Survos\CoreBundle\Entity;

use Symfony\Component\Serializer\Attribute\Groups;

use function Symfony\Component\String\u;

trait RouteParametersTrait
{
//    public function getId(): int|string
//    {
//        throw new \Exception("you must implement getId() to use a default unique parameters in " . $this::class);
//    }

//    private const array UNIQUE_PARAMETERS = ['id' => 'id'];


    public function getUniqueIdentifiers(): array
    {
        if (defined('static::UNIQUE_PARAMETERS')) {
            $x = [];
            foreach (constant('static::UNIQUE_PARAMETERS') as $parameter => $getter) {
                $x[$parameter] = $this->{'get' . $getter}();
            }
            return $x;
        }

        if (!method_exists($this, 'getId')) {
            throw new \Exception("you must implement getId() to use a default unique parameters in " . $this::class);
        }
        return [strtolower((new \ReflectionClass($this))->getShortName()) . 'Id' => $this->getId()];
    }

    #[Groups(['rp', 'transitions', 'searchable'])]
    public function getRp(?array $addlParams = []): array
    {
        return array_merge($this->getUniqueIdentifiers(), $addlParams);
    }

    /** e.g. MemberDirectory to member_directory */
    public static function getClassnamePrefix(string $class = null): string
    {
        if (! $class) {
            $class = get_called_class();
        }
        $shortName = strtolower(u($x = (new \ReflectionClass($class))->getShortName())->snake()->lower()->ascii());

        //        $shortName = strtolower( (new \ReflectionClass(get_called_class()))->getShortName() );
        return $shortName;
    }
}
