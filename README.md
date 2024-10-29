# Corebundle

Symfony Bundle with interfaces, traits, models and services needed by more than one Survos components.  For example, Model\Column is used by grid, api-grid and simple-datatables.  RouteParametersInterface is used by tree and the griid bundles.


https://github.com/symfony/symfony/issues/20083


```bash
composer req survos/core-bundle
```

```php
<?php
// src/Entity/Foo.php
namespace App\Entity;

use Survos\CoreBundle\Entity\RouteParametersInterface;
use Survos\CoreBundle\Entity\RouteParametersTrait;

class Foo implements RouteParametersInterface
{
use RouteParametersTrait;

public function getUniqueParams(): array { 
    return ['fooId' => $this->getFooCode()];
}
```

Now use .rp in twig and ->getRp() in php as part of generating a route
```twig
<a href="{{ path('foo_show', foo.rp) }}">Show</a>
```

Combined with survos/maker-bundle, create a param converter

```bash
bin/console survos:make:param-converter Foo
```

## Helper Tasks

echo "SYMFONY_DEPRECATIONS_HELPER=weak" >> .env
