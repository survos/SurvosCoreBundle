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
            new TwigFilter('json_pretty', $this->json_pretty(...), ['is_safe' => ['html']]),

            new TwigFilter('short_class', fn (object $obj) => (new \ReflectionClass($obj))->getShortName()),
        ];
    }

    public function getFunctions(): array
    {
        return [
//            new TwigFunction('function_name', [::class, 'doSomething']),
        ];
    }

    public function json_pretty(object|array|null $value, ?string $wrapper='pre', array $wrapperAttributes=[]): string
    {
        if (is_string($value)) {
            $data = json_decode($value);
        } else {
            $data = $value;
        }
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($wrapper) {
            // @todo: attributes
            $json = sprintf("<%s>%s</%s>", $wrapper, $json, $wrapper);
        }
        return $json;
    }

}
