<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\WorkEntry\Application\Queries\ListWorkEntry;

use App\Sesame\WorkEntry\Application\Queries\ListWorkEntry\ListWorkEntryHandler;
use App\Sesame\WorkEntry\Application\Queries\ListWorkEntry\ListWorkEntryQuery;
use App\Sesame\WorkEntry\Application\Queries\ListWorkEntry\ListWorkEntryResponse;
use App\Sesame\WorkEntry\Domain\Collections\WorkEntries;
use App\Sesame\WorkEntry\Domain\Repositories\WorkEntryFindRepository;
use App\Shared\Domain\Criteria\FilterOperator;
use App\Shared\Domain\Criteria\OrderType;
use App\Shared\Domain\Criteria\OrderTypes;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Sesame\WorkEntry\Domain\Entities\WorkEntryMother;
use Tests\Unit\Shared\Domain\Criteria\CriteriaMother;
use Tests\Unit\Shared\Domain\Criteria\FilterMother;
use Tests\Unit\Shared\Domain\Criteria\FiltersMother;
use Tests\Unit\Shared\Domain\Criteria\OrderByMother;
use Tests\Unit\Shared\Domain\Criteria\OrderMother;
use Tests\Utils\Mother\MotherCreator;

final class ListWorkEntryHandlerTest extends TestCase
{
    private WorkEntryFindRepository|MockObject $workEntryFindRepository;
    private ListWorkEntryHandler $handler;

    protected function setUp(): void
    {
        $this->workEntryFindRepository = $this->createMock(WorkEntryFindRepository::class);
        $this->handler                 = new ListWorkEntryHandler(
            $this->workEntryFindRepository,
        );
    }

    #[Test]
    public function itShouldShowListWorkEntry(): void
    {
        // GIVEN

        $userId           = MotherCreator::id();
        $query            = new ListWorkEntryQuery($userId);
        $criteriaExpected = CriteriaMother::create(
            FiltersMother::create(
                [
                    FilterMother::fromPrimitives(
                        field: 'userId',
                        operator: FilterOperator::EQUAL->value,
                        value: $userId,
                    ),
                ]
            ),
            OrderMother::create(
                OrderByMother::create('createdAt'),
                OrderType::create(OrderTypes::DESC),
            )
        );

        $workEntriesExpected = WorkEntries::fromArray(
            [
                WorkEntryMother::random(['userId' => $userId]),
                WorkEntryMother::random(['userId' => $userId]),
            ],
        );

        $listWorkEntryResponseExpected = ListWorkEntryResponse::fromWorkEntries($workEntriesExpected);

        // WHEN

        $this->workEntryFindRepository
            ->expects(self::once())
            ->method('searchAllByCriteria')
            ->with($criteriaExpected)
            ->willReturn($workEntriesExpected);

        $result = ($this->handler)($query);

        // THEN

        self::assertInstanceOf(ListWorkEntryResponse::class, $result);
        self::assertEquals($listWorkEntryResponseExpected, $result);
    }
}
