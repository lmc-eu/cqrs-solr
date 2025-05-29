<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;

class ApplicatorFactory
{
    /** @var ApplicatorInterface[] */
    private array $availableApplicators;

    /**
     * @param ApplicatorInterface[] $availableApplicators
     */
    public function __construct(iterable $availableApplicators)
    {
        $this->availableApplicators = [];
        foreach ($availableApplicators as $applicator) {
            $this->availableApplicators[] = $applicator;
        }
    }

    public static function getDefaultPriority(): int
    {
        return 1;
    }

    /**
     * @return ApplicatorInterface[]
     */
    public function getApplicators(EntityInterface $entity): array
    {
        return array_filter(
            $this->availableApplicators,
            function (ApplicatorInterface $applicator) use ($entity) {
                if ($applicator->supportEntity($entity)) {
                    $applicator->setEntity($entity);

                    return true;
                }

                return false;
            },
        );
    }
}
