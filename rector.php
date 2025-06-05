<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\CodeQuality\Rector\If_\CombineIfRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\Config\Level\CodeQualityLevel;
use Rector\Config\Level\CodingStyleLevel;
use Rector\Config\Level\DeadCodeLevel;
use Rector\Config\Level\TypeDeclarationLevel;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Array_\FirstClassCallableRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;

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

        // This replace arrays like "[$this, 'aMethod']" by the direct call
        // "$this->aMethod()" which is problematic (ie: in config or Twig
        // extensions)
        FirstClassCallableRector::class,

        // Cause "$e" is acceptable
        CatchExceptionNameMatchingTypeRector::class,

        // Encapsed string are IMO more lisible than sprintf!
        EncapsedStringsToSprintfRector::class,

        // Not a big fan a long line with a single if statement
        CombineIfRector::class,

        // I prefer comparing to null
        FlipTypeControlToUseExclusiveTypeRector::class,

        // This rule creates "errors" in CI but not locally
        // @see https://github.com/altercampagne/eventoj/actions/runs/13699190164/job/38308515651?pr=103
        NullToStrictStringFuncCallArgRector::class,
    ])
;
