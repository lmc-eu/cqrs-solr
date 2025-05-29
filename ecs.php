<?php

declare(strict_types=1);

use Lmc\CodingStandard\Set\SetList;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitTestAnnotationFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRootFiles()
    ->withSets([
        SetList::ALMACAREER,
    ])
    ->withConfiguredRule(
        PhpUnitTestAnnotationFixer::class,
        ['style' => 'annotation'],
    )
    ->withConfiguredRule(
        'Lmc\CodingStandard\Sniffs\Naming\ClassNameSuffixByParentSniff',
        [
            'extraParentTypesToSuffixes' => ['*ApplicatorInterface' => 'Applicator'],
        ],
    )
    ->withSkip([
        'SlevomatCodingStandard\Sniffs\Exceptions\ReferenceThrowableOnlySniff.ReferencedGeneralException' => ['tests/Exception/*.php'],
    ]);
