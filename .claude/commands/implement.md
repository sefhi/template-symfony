---
allowed-tools: Bash(* !rm:*), WebFetch(domain:*), Agent(*)
description: "Execute a feature implementation pipeline: plan, create prd.md, implement, refactor, test and final review."
argument-hint: [feature_name]
---

## Execution vars
Pass these vars to the subagents:
- `feature_name` - the name of the feature to be implemented. $ARGUMENTS

# CRITICAL: This is a 5-step pipeline. You MUST execute ALL steps in order.
When this command is executed, you MUST run these 5 agents sequentially. Do NOT skip any step.

## Rules Reference

Consult the project rules in `.claude/rules/` for detailed conventions:

| Topic | Rule File |
|-------|-----------|
| **Architecture Overview** | `architecture.md` |
| **Coding Style** | `coding-style.md` |
| Domain Layer | `domain/domain.md`, `domain-entity.md`, `domain-repository.md`, `domain-value-object.md`, `domain-exception.md`, `domain-service.md` |
| Application Layer | `application/application.md`, `application-command.md`, `application-query.md` |
| Infrastructure Layer | `infrastructure/infrastructure.md`, `infrastructure-controller.md`, `infrastructure-repository.md` |
| Tests | `tests/tests.md`, `tests-unit.md`, `tests-functional.md`, `tests-mother.md` |

---

## STEP 1: Planning (REQUIRED)

**Say:** `[Claude Agent] Step 1/5: Activating Feature Planner...`

You MUST invoke the @feature-planner-agent agent to:
- Ask the user to describe the feature (if not already provided)
- Create a detailed implementation plan following DDD architecture
- Define API endpoints, Commands, Queries, Domain Events, and Value Objects
- Generate a Markdown prd.md plan ready for execution
- Create a plan file in `.claude/sessions/{feature_name}/prd.md` with the execution plan and its tasks
- Get user approval before proceeding to the next step

---

## STEP 2: Implementation (REQUIRED)

**Say:** `[Claude Agent] Step 2/5: Activating Feature Implementer...`

You MUST invoke the @feature-developer-agent agent to:
- Implement the plan task-by-task following layer order: Domain → Application → Infrastructure
- Use proper PHP types with `declare(strict_types=1)`
- Follow DDD patterns (Entities, Value Objects, Repository interfaces, Commands, Queries, Handlers)
- Use named constructors (`create()`/`make()`) for Entities
- Use Request DTOs with `#[Assert\*]` validation attributes
- Create Mother Objects for test data

**Do NOT proceed to Step 3 until all tasks are implemented.**

---

## STEP 3: Refactoring (REQUIRED)

**Say:** `[Claude Agent] Step 3/5: Activating Refactor Agent...`

You MUST invoke the @refactor-agent agent to:
- Review all changed files for code quality
- Ensure business logic is in Domain layer (not Controllers or Handlers)
- Ensure proper separation of concerns (Controller → Handler → Domain)
- Verify `final readonly class` for Commands, Queries, Handlers, Responses
- Verify `final class` for Entities and Controllers
- Replace magic strings with constants or enums
- Run quality checks: `make style && make test`

**Do NOT proceed to Step 4 until refactoring is complete and all checks pass. If there are errors, fix them and repeat Step 3.**

---

## STEP 4: Testing (REQUIRED)

**Say:** `[Claude Agent] Step 4/5: Activating Testing Agent...`

You MUST invoke the @unit-testing-agent agent to:
- Review test coverage with `make test/coverage`
- Add missing unit tests (happy path, error path, edge cases)
- Use PHPUnit with `#[Test]` attribute
- Follow test naming: `itShould{Action}{Condition}`
- Use GIVEN-WHEN-THEN comments
- Use Mother Objects for test data (not manual object creation)
- Use `createMock()` with `|MockObject` typing for dependencies
- Run full test suite: `make test`
- Run static analysis: `make static-analysis`

**Do NOT proceed to Step 5 until ALL tests pass and static analysis passes. If there are errors, go back to Step 3 and fix them.**

---

## STEP 5: Final Review (REQUIRED)

**Say:** `[Claude Agent] Step 5/5: Activating Final Reviewer...`

You MUST invoke the @code-reviewer-agent agent to:
- Verify all requirements are met
- Check DDD architecture compliance (layer dependencies)
- Verify security checklist (input validation via Request DTOs, auth, `#[\SensitiveParameter]`)
- Confirm test coverage meets standards
- Run all quality checks: `make style && make test`
- Provide final verdict: APPROVED or CHANGES REQUESTED

If CHANGES REQUESTED: go back to the appropriate step and fix issues.

---

## Final Summary (REQUIRED)

After completing all 5 steps, display:

```
## Feature Complete: [Name]

### Pipeline Summary
- Step 1: Planning - DONE
- Step 2: Implementation - DONE
- Step 3: Refactoring - DONE
- Step 4: Testing - DONE
- Step 5: Final Review - APPROVED

### Files Changed
- Created: X files
- Modified: Y files

### Tests
- Unit: X tests
- Functional: Y tests
- Status: All passing

### Quality Checks
- PHPStan: Passed
- PHP CS Fixer: Passed
```

**IMPORTANT:** Respond in the user's language throughout the process.

---

### Available Subagents Reference

| Subagent | Use Case |
|----------|----------|
| `feature-planner-agent` | Plan features with DDD architecture, tasks, and security considerations |
| `feature-developer-agent` | Implement PHP/Symfony features with DDD, validation, and tests |
| `refactor-agent` | Improve code quality without changing behavior |
| `unit-testing-agent` | Write comprehensive PHPUnit tests with proper coverage |
| `code-reviewer-agent` | Quality gate: verify requirements, security, and tests |

---

### Development Commands Reference

| Command | Purpose |
|---------|---------|
| `make test` | Run all PHPUnit tests |
| `make test/coverage` | Run tests with coverage report |
| `make lint` | Fix code style (PHP-CS-Fixer) |
| `make static-analysis` | PHPStan analysis (level: max) |
| `make style` | Run lint + static-analysis |
| `make migration/diff` | Generate migration from entity changes |
| `make migrate` | Run migrations |

**Single test file:**
```bash
docker compose exec webserver php bin/phpunit tests/Unit/Path/To/Test.php
docker compose exec webserver php bin/phpunit --filter testMethodName
```

---

### Layer Dependencies

- **Domain Layer** → No dependencies on other layers (pure business logic, NO Symfony/Doctrine imports)
- **Application Layer** → Can depend on Domain layer only
- **Infrastructure Layer** → Can depend on Application and Domain layers (Symfony, Doctrine here)

---

### PHP Standards

- `declare(strict_types=1)` in ALL files
- `final readonly class` for Commands, Queries, Handlers, Responses, Value Objects
- `final class` for Entities, Controllers
- Constructor property promotion with trailing commas
- Explicit null checks: `null === $entity` instead of `!$entity`
- Named arguments for constructors with 2+ parameters
- Getters without `get` prefix: `name()` not `getName()`

Respond in the user's language.