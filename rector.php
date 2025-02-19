<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Array_\FirstClassCallableRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/public',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPhpSets()
    ->withTypeCoverageLevel(0)
    ->withDeadCodeLevel(0)
    ->withCodeQualityLevel(0)
    ->withSkip([
        // This rule systematically move "huge" block of code inside the
        // constructor. I found this less readable for entities.
        ClassPropertyAssignToConstructorPromotionRector::class,

        // This replace arrays like "[$this, 'aMethod']" by the direct call
        // "$this->aMethod()" which is problematic (ie: in config or Twig
        // extensions)
        FirstClassCallableRector::class,
    ])
;
