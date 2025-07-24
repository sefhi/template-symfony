<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Application\Queries\ListWorkEntry;

use App\Sesame\WorkEntry\Domain\Repositories\WorkEntryFindRepository;
use App\Shared\Domain\Bus\Query\QueryHandler;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter;
use App\Shared\Domain\Criteria\FilterField;
use App\Shared\Domain\Criteria\FilterOperator;
use App\Shared\Domain\Criteria\Filters;
use App\Shared\Domain\Criteria\FilterValue;
use App\Shared\Domain\Criteria\Order;
use App\Shared\Domain\Criteria\OrderBy;
use App\Shared\Domain\Criteria\OrderType;
use App\Shared\Domain\Criteria\OrderTypes;
use Ramsey\Uuid\Uuid;

final readonly class ListWorkEntryHandler implements QueryHandler
{
    public function __construct(private WorkEntryFindRepository $workEntryFindRepository)
    {
    }

    public function __invoke(ListWorkEntryQuery $query): ListWorkEntryResponse
    {
        $userId = Uuid::fromString($query->userId);

        $filter = Filter::create(
            field: FilterField::fromString('userId'),
            operator: FilterOperator::EQUAL,
            value: FilterValue::fromString($userId->toString()),
        );

        $criteria = Criteria::create(
            Filters::fromArray([$filter]),
            Order::create(
                OrderBy::fromString('createdAt'),
                OrderType::create(OrderTypes::DESC)
            ),
        );

        $result = $this->workEntryFindRepository->searchAllByCriteria($criteria);

        return ListWorkEntryResponse::fromWorkEntries($result);
    }
}
