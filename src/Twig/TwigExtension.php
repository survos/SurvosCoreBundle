<?php

namespace Survos\CoreBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, add ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('get_class', fn (object $obj) => $obj::class),
            new TwigFilter('json_pretty', fn (object|array $obj) => json_encode($obj, JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES)),
            new TwigFilter('short_class', fn (object $obj) => (new \ReflectionClass($obj))->getShortName()),
        ];
    }

    public function getFunctions(): array
    {
        return [
//            new TwigFunction('function_name', [::class, 'doSomething']),
        ];
    }
}
