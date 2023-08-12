<?php

declare(strict_types=1);

namespace Lsv\BallDontLie\Utils;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as BaseSerializer;

final readonly class Serializer
{
    public static function getSerializer(): BaseSerializer
    {
        $reflectionExtractor = new ReflectionExtractor();
        $phpDocExtractor = new PhpDocExtractor();

        $propertyTypeExtractor = new PropertyInfoExtractor(
            typeExtractors: [$phpDocExtractor, $reflectionExtractor],
        );

        $normalizers = [
            new BackedEnumNormalizer(),
            new ArrayDenormalizer(),
            new DateTimeNormalizer(),
            new ObjectNormalizer(
                nameConverter: new CamelCaseToSnakeCaseNameConverter(),
                propertyTypeExtractor: $propertyTypeExtractor
            ),
        ];

        $encoders = [
            new JsonEncoder(),
        ];

        return new BaseSerializer($normalizers, $encoders);
    }
}
