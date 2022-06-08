# Corebundle

Symfony Bundle with traits and interfaces common to many Survos Bundles

```bash
composer config repositories.survos_core '{"type": "path", "url": "/home/tac/survos/bundles/core-bundle"}'
composer req survos/core-bundle:*@dev
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
