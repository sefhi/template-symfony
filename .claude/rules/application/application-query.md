---
paths: src/*Context/Application/Query/**/*.php
---

# Application Layer - Queries & Query Handlers

This rule applies to Query and QueryHandler files in the Application layer.

## Queries

Queries are read-only request objects that define what data is needed.

Examples: 

```php
<?php

declare(strict_types=1);

namespace App\{Context}Context\Application\Queries\{Action}{Entity};

class {Action}{Entity}ByIdQuery implements Query
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}
```

### Query Rules

- **Location**: `Application/Queries/{Entity}/`
- **Naming**: `Find{Entity}sQuery.php` (always plural, filters determine results)
- **Permissions**: Implement permission interfaces (e.g., `{Context}Permissible`)
- **Properties**: Use `private readonly` with getters (allows modifying QueryParams in constructor)
- **QueryParams**: Accept QueryParams for filtering/pagination (including by ID)

### Query Naming Conventions

| Pattern | Use Case | Example |
|---------|----------|---------|
| `Find{Entity}sQuery` | Collection with filters (including by ID) | `FindComplaintsQuery` |
| `FindCount{Entity}sQuery` | Count entities | `FindCountComplaintsQuery` |
| `Find{Entity}{Relation}sQuery` | Related entities | `FindComplaintCommentsQuery` |

## Query Handlers

Query handlers execute read operations and return data.

**Important**: All queries use `querySearch()` which returns a `QueryResponse`. If no results are found, return an empty QueryResponse (not an exception).

```php
<?php

declare(strict_types=1);

namespace App\{Context}Context\Application\Query\{Entity};

use App\{Context}Context\Domain\Model\{Entity}\{Entity}ViewRepository;
use Sesame\Ddd\Domain\QueryResponse;

class Find{Entity}sQueryHandler
{
    public function __construct(
        private readonly {Entity}ViewRepository $viewRepository,
    ) {
    }

    public function handleFind{Entity}sQuery(Find{Entity}sQuery $query): QueryResponse
    {
        return $this->viewRepository->querySearch($query->queryParams());
    }
}
```

### Query Handler Rules

- **Location**: `Application/Query/{Entity}/`
- **Naming**: `{QueryName}Handler.php` (e.g., `FindComplaintsQueryHandler.php`)
- **Method naming**: `handle{QueryName}({Query} $query)` (e.g., `handleFindComplaintsQuery`)
- **Return type**: `QueryResponse` from `Sesame\Ddd\Domain\QueryResponse`
- **Dependencies**: Use ViewRepository for all queries
- **No exceptions for not found**: Return empty QueryResponse if no results

### Query Handler Patterns

**Collection Query with Filters**:
```php
public function handleFind{Entity}sQuery(Find{Entity}sQuery $query): QueryResponse
{
    $queryParams = $query->queryParams();

    $allowedIds = $this->coreClient->allowedIds($query->entityReference());
    $queryParams->addParam({Entity}QueryParams::COMPANY_IDS, ['in' => $allowedIds]);

    return $this->viewRepository->querySearch($queryParams);
}
```

**Count Query**:
```php
public function handleFindCount{Entity}sQuery(FindCount{Entity}sQuery $query): QueryResponse
{
    $count = $this->viewRepository->queryCount($query->queryParams());
    return new QueryResponse(['count' => $count]);
}
```

## ViewRepository Methods

| Method | Use Case |
|--------|----------|
| `querySearch($queryParams)` | All queries with filters - returns empty QueryResponse if no results |
| `queryCount($queryParams)` | Count entities matching criteria |
