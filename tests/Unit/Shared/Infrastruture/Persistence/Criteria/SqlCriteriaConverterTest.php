<?php

namespace Tests\Unit\Shared\Infrastruture\Persistence\Criteria;

use App\Shared\Domain\Criteria\FilterOperator;
use App\Shared\Domain\Criteria\Filters;
use App\Shared\Domain\Criteria\OrderTypes;
use App\Shared\Infrastructure\Persistence\Criteria\SqlCriteriaConverter;
use Tests\Unit\Shared\Domain\Criteria\CriteriaMother;
use Tests\Unit\Shared\Domain\Criteria\FiltersMother;
use Tests\Unit\Shared\Domain\Criteria\OrderMother;
use PHPUnit\Framework\TestCase;

class SqlCriteriaConverterTest extends TestCase
{
    private SqlCriteriaConverter $sqlConverter;

    protected function setUp(): void
    {
        $this->sqlConverter = new SqlCriteriaConverter();
    }

    /** @test */
    public function itShouldConvertToSqlWithoutFilters(): void
    {
        // GIVEN

        $criteria = CriteriaMother::create(
            Filters::fromValues([]),
        );

        // WHEN

        $query = $this->sqlConverter->convert(['name', 'surname'], 'table', $criteria);

        // THEN

        self::assertEquals('SELECT name, surname FROM table', $query);
    }

    /** @test */
    public function itShouldConvertToSqlWithOneFilter(): void
    {
        // GIVEN

        $criteria = CriteriaMother::withOneFilter(
            'name',
            FilterOperator::EQUAL->value,
            'value'
        );

        // WHEN

        $query = $this->sqlConverter->convert(['name', 'surname'], 'table', $criteria);

        // THEN

        self::assertEquals("SELECT name, surname FROM table WHERE name = 'value'", $query);
    }

    /** @test */
    public function itShouldConvertToSqlWithFilters(): void
    {
        // GIVEN

        $criteria = CriteriaMother::create(
            Filters::fromValues(
                [
                    [
                        'field'    => 'name',
                        'operator' => FilterOperator::EQUAL->value,
                        'value'    => 'value',
                    ],
                    [
                        'field'    => 'surname',
                        'operator' => FilterOperator::EQUAL->value,
                        'value'    => 'value',
                    ],
                ]
            )
        );

        // WHEN

        $query = $this->sqlConverter->convert(['name', 'surname'], 'table', $criteria);

        // THEN

        self::assertEquals("SELECT name, surname FROM table WHERE name = 'value' AND surname = 'value'", $query);
    }

    /** @test */
    public function itShouldConvertToSqlSorted(): void
    {
        // GIVEN

        $criteria = CriteriaMother::create(
            FiltersMother::empty(),
            OrderMother::withOneSorted(
                'name',
                OrderTypes::ASC->value
            )
        );

        // WHEN

        $query = $this->sqlConverter->convert(['name', 'surname'], 'table', $criteria);

        // THEN

        self::assertEquals('SELECT name, surname FROM table ORDER BY name ASC', $query);
    }

    /** @test */
    public function itShouldConvertToSqlSortedAndFilters(): void
    {
        // GIVEN

        $criteria = CriteriaMother::create(
            Filters::fromValues(
                [
                    [
                        'field'    => 'name',
                        'operator' => FilterOperator::EQUAL->value,
                        'value'    => 'value',
                    ],
                    [
                        'field'    => 'surname',
                        'operator' => FilterOperator::EQUAL->value,
                        'value'    => 'value',
                    ],
                ]
            ),
            OrderMother::withOneSorted(
                'name',
                'ASC'
            )
        );

        // WHEN

        $query = $this->sqlConverter->convert(['name', 'surname'], 'table', $criteria);

        // THEN

        self::assertEquals("SELECT name, surname FROM table WHERE name = 'value' AND surname = 'value' ORDER BY name ASC", $query);
    }

    /** @test */
    public function itShouldConvertToSqlSortedAndFiltersAndFilterContain(): void
    {
        // GIVEN

        $criteria = CriteriaMother::create(
            Filters::fromValues(
                [
                    [
                        'field'    => 'name',
                        'operator' => FilterOperator::EQUAL->value,
                        'value'    => 'value',
                    ],
                    [
                        'field'    => 'surname',
                        'operator' => FilterOperator::CONTAINS->value,
                        'value'    => 'value',
                    ],
                ]
            ),
            OrderMother::withOneSorted(
                'name',
                'ASC'
            )
        );

        // WHEN

        $query = $this->sqlConverter->convert(['name', 'surname'], 'table', $criteria);

        // THEN

        self::assertEquals("SELECT name, surname FROM table WHERE name = 'value' AND surname LIKE '%value%' ORDER BY name ASC", $query);
    }

    /** @test */
    public function itShouldConvertToSqlLimitedAndPaginatedWithFilters(): void
    {
        // GIVEN

        $criteria = CriteriaMother::create(
            Filters::fromValues(
                [
                    [
                        'field'    => 'name',
                        'operator' => FilterOperator::EQUAL->value,
                        'value'    => 'value',
                    ],
                    [
                        'field'    => 'surname',
                        'operator' => FilterOperator::CONTAINS->value,
                        'value'    => 'value',
                    ],
                ]
            ),
            OrderMother::withOneSorted(
                'name',
                'ASC'
            )
        );

        $criteriaPaginated = CriteriaMother::criteriaPaginated(
            $criteria,
            10,
            2
        );

        $offset = $criteriaPaginated->getOffset();
        $limit  = $criteriaPaginated->getPageSize();

        // WHEN

        $query = $this->sqlConverter->convert(['name', 'surname'], 'table', $criteriaPaginated);

        // THEN

        self::assertEquals("SELECT name, surname FROM table WHERE name = 'value' AND surname LIKE '%value%' ORDER BY name ASC LIMIT $limit OFFSET $offset", $query);
    }

    /** @test */
    public function itShouldConvertToSqlLimitedAndPaginatedWithoutFilters(): void
    {
        // GIVEN

        $criteria = CriteriaMother::emptyPaginated(10, 2);

        $criteriaPaginated = CriteriaMother::criteriaPaginated(
            $criteria,
            10,
            2
        );

        $offset = $criteriaPaginated->getOffset();
        $limit  = $criteriaPaginated->getPageSize();

        // WHEN

        $query = $this->sqlConverter->convert(['id', 'name'], 'table', $criteriaPaginated);

        // THEN

        self::assertEquals('SELECT id, name FROM table LIMIT ' . $limit . ' OFFSET ' . $offset, $query);
    }

    /** @test */
    public function itShouldConvertToSqlWithOrderTypesAscByCursor(): void
    {
        // GIVEN

        $criteriaWithCursor = CriteriaMother::emptyCursor('createdAt', 'ASC', 2, '2021-01-01 00:00:00');
        $pageSize           = $criteriaWithCursor->getPageSize();

        // WHEN

        $query = $this->sqlConverter->convert(['id', 'name'], 'table', $criteriaWithCursor, ['createdAt' => 'created_at']);

        // THEN

        self::assertEquals("SELECT id, name FROM table WHERE created_at > '2021-01-01 00:00:00' ORDER BY created_at ASC LIMIT " . $pageSize, $query);
    }

    /** @test */
    public function itShouldConvertToSqlWithOrderTypesDescByCursor(): void
    {
        // GIVEN

        $criteria = CriteriaMother::create(
            Filters::fromValues(
                [
                    [
                        'field'    => 'name',
                        'operator' => FilterOperator::EQUAL->value,
                        'value'    => 'Peter',
                    ],
                    [
                        'field'    => 'surname',
                        'operator' => FilterOperator::CONTAINS->value,
                        'value'    => 'Parker',
                    ],
                ]
            ),
            OrderMother::withOneSorted(
                'sons',
                'DESC'
            ),
            10,
            null,
            '2'
        );
        $pageSize = $criteria->getPageSize();

        // WHEN

        $query = $this->sqlConverter->convert(['id', 'name', 'sons'], 'father', $criteria);

        // THEN

        self::assertEquals("SELECT id, name, sons FROM father WHERE name = 'Peter' AND surname LIKE '%Parker%' AND sons < '2' ORDER BY sons DESC LIMIT " . $pageSize, $query);
    }
}
