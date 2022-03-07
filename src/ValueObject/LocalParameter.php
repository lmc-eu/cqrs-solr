<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\ValueObject;

use Solarium\Core\Query\LocalParameters\LocalParameter as SolariumLocalParameter;
use Solarium\Core\Query\LocalParameters\LocalParameterInterface;

/** @internal */
class LocalParameter
{
    public static function withValue(string $type, string $value): LocalParameterInterface
    {
        if (array_key_exists($type, SolariumLocalParameter::PARAMETER_MAP)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Local Parameter has defined type "%s" and you should use it directly by LocalParameters::set... method.',
                    $type
                )
            );
        }

        return (new SolariumLocalParameter($type))->addValue(sprintf('%s=%s', $type, $value));
    }

    public static function withPlaceholder(string $type, string $placeholder): LocalParameterInterface
    {
        if (!str_starts_with($placeholder, '$')) {
            throw new \InvalidArgumentException('Local Parameter placeholder must start with $ sign.');
        }

        return self::withValue($type, $placeholder);
    }
}
