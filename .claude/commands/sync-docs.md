---
allowed-tools: Bash(* !rm:*), Read(*), Glob(*), Grep(*), Write(*), Edit(*)
description: "Synchronize documentation for a specific bounded context after code changes."
argument-hint: [context-name]
---

# Documentation Sync Command

Synchronize the documentation for the specified bounded context with its current code state.

## Usage

```
/sync-docs subtitle
/sync-docs template
/sync-docs bookstore
```

## Context Parameter

The context name should match the directory name in `src/`:
- `subtitle` → `src/Subtitle/`
- `template` → `src/Template/`
- `bookstore` → `src/BookStore/`

---

## Execution Steps

**Say:** `[Claude Agent] Synchronizing documentation for context: $ARGUMENTS`

### Step 1: Validate Context

First, verify the context exists:

1. Check if `src/{Context}/` directory exists
2. Check if `docs/contexts/{context}/README.md` exists
3. If documentation doesn't exist, suggest running `/implement` first or offer to create initial documentation

### Step 2: Detect Code Changes

Analyze the current state of the context:

1. **List all components** in the context:
   ```
   src/{Context}/**/Domain/Entities/*.php        → Entities
   src/{Context}/**/Domain/ValueObjects/*.php    → Value Objects
   src/{Context}/**/Domain/Exceptions/*.php      → Exceptions
   src/{Context}/**/Application/Commands/**/*.php → Commands
   src/{Context}/**/Application/Queries/**/*.php  → Queries
   src/{Context}/**/Infrastructure/Api/**/*Controller.php → Endpoints
   ```

2. **Compare with documented components** in `docs/contexts/{context}/README.md`

3. **Identify gaps**:
   - New components not in documentation
   - Documented components no longer in code
   - Modified components (check signatures, behavior)

### Step 3: Update Documentation

For each gap found:

**New Entity/Value Object:**
- Add to Domain Model section
- Add to Core Concepts table

**New Command/Query:**
- Add new Use Case section with:
  - Command/Query name
  - Flow description
  - Example request/response

**New Endpoint:**
- Add row to API Endpoints table
- Include in relevant Use Case section

**Removed Component:**
- Remove from documentation
- Check for orphaned references

**Modified Component:**
- Update relevant sections
- Verify examples still accurate

### Step 4: Report Changes

Output a summary:

```markdown
## Documentation Sync Complete: {Context}

### Changes Detected
| Type | Component | Action |
|------|-----------|--------|
| Entity | NewEntity | ✅ Added to docs |
| Command | OldCommand | ✅ Removed from docs |
| Endpoint | POST /api/new | ✅ Added to docs |

### Documentation Updated
- File: `docs/contexts/{context}/README.md`
- Sections modified: X
- Last sync: {timestamp}

### No Changes Needed
(if applicable)
The documentation is already in sync with the code.
```

---

## What to Document

### For New Entities
```markdown
### Domain Model (add)

{NewEntity} (Entity/Aggregate Root)
├── id: UuidInterface
├── property: Type - Description
└── ...
```

### For New Endpoints
```markdown
### API Endpoints (add row)

| METHOD | /api/path | Description | Auth |
```

### For New Use Cases
```markdown
### {UseCaseName}: {Action}

**Command/Query**: `{CommandName}`

**Flow**:
1. Step 1
2. Step 2

**Example Request**:
\`\`\`bash
curl ...
\`\`\`

**Example Response**:
\`\`\`json
{...}
\`\`\`
```

### For New Exceptions
```markdown
### Error Handling (add row)

| ExceptionName | HTTP Code | When |
```

---

## Rules Reference

Consult the project rules in `.claude/rules/` for understanding component patterns:

| Layer | Rule Files |
|-------|------------|
| Domain | `domain/domain.md`, `domain-entity.md`, `domain-value-object.md` |
| Application | `application/application.md`, `application-command.md`, `application-query.md` |
| Infrastructure | `infrastructure/infrastructure.md`, `infrastructure-controller.md` |

---

## Error Handling

**Context not found:**
```
Error: Context '{context}' not found.
Available contexts: subtitle, template, bookstore, health
```

**Documentation not found:**
```
Warning: No documentation found for context '{context}'.
Would you like to generate initial documentation? (This will analyze the context and create docs/contexts/{context}/README.md)
```

---

## Examples

### Sync after adding a new endpoint
```
/sync-docs subtitle
```
Output:
```
## Documentation Sync Complete: Subtitle

### Changes Detected
| Type | Component | Action |
|------|-----------|--------|
| Endpoint | PATCH /api/subtitles/{id}/time-shift | ✅ Added to docs |
| Command | TimeShiftSubtitleCommand | ✅ Added to docs |

### Documentation Updated
- File: docs/contexts/subtitle/README.md
- Sections modified: 2 (API Endpoints, Use Cases)
```

### Sync with no changes
```
/sync-docs template
```
Output:
```
## Documentation Sync Complete: Template

### No Changes Needed
The documentation is already in sync with the code.
- Last checked: 2024-01-18 15:30:00
```

---

Respond in the user's language.
