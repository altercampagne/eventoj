<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\Config\Level\CodeQualityLevel;
use Rector\Config\Level\CodingStyleLevel;
use Rector\Config\Level\DeadCodeLevel;
use Rector\Config\Level\TypeDeclarationLevel;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/public',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPhpSets()
    ->withComposerBased(twig: true, doctrine: true, phpunit: true, symfony: true)
    ->withTypeCoverageLevel(count(TypeDeclarationLevel::RULES))
    ->withDeadCodeLevel(count(DeadCodeLevel::RULES))
    ->withCodeQualityLevel(count(CodeQualityLevel::RULES))
    ->withCodingStyleLevel(count(CodingStyleLevel::RULES))
    ->withSkip([
        // This rule systematically move "huge" block of code inside the
        // constructor. I found this less readable for entities.
        ClassPropertyAssignToConstructorPromotionRector::class,

        // Cause "$e" is acceptable
        CatchExceptionNameMatchingTypeRector::class,

        // I prefer comparing to null
        FlipTypeControlToUseExclusiveTypeRector::class,
    ])
;
