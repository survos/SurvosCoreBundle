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
                if (class_exists($getter)) {
                    dd($parameter, $getter);
                }
                $x[$parameter] = $this->{'get' . $getter}();
            }
            return $x;
        }

        if (!method_exists($this, 'getId')) {
            throw new \Exception("you must implement getId() to use a default unique parameters in " . $this::class);
        }
        $uniqueIds =  [strtolower((new \ReflectionClass($this))->getShortName()) . 'Id' => $this->getId()];
        dd($uniqueIds);
        return $uniqueIds;
    }

    #[Groups(['rp', 'transitions', 'searchable'])]
    public function getRp(?array $addlParams = []): array
    {
        return array_merge($this->getUniqueIdentifiers(), $addlParams);
    }

    /** e.g. MemberDirectory to member_directory */
    public static function getClassnamePrefix(string|null $class = null): string
    {
        if (! $class) {
            $class = get_called_class();
        }
        $shortName = strtolower(u($x = (new \ReflectionClass($class))->getShortName())->snake()->lower()->ascii());

        //        $shortName = strtolower( (new \ReflectionClass(get_called_class()))->getShortName() );
        return $shortName;
    }
}
