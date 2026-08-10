# Rules & AI Customizations for new-kasir Repository

## 1. Context & Branch Enforcement:
- **Active Branch Verification**: Every session start, run `git branch --show-current` to identify the active working branch.
- **Branch Restriction**: Never commit or make direct destructive edits on `main` or `master`. Always work on dedicated feature branches (e.g. `deva-branch`).
- **TODO Checking & Confirmation**:
  - Whenever working on `deva-branch`, always inspect `basic-knowledge/deva-branch/todo.md`.
  - Confirm pending tasks with the user before executing major changes.

## 2. Basic Knowledge & Log Code Protocol:
- **Documentation Primary Source**: Read `basic-knowledge/rule_ai.md` and `basic-knowledge/deva-branch/todo.md` for project architecture guidelines.
- **Immediate Logging**: Every code modification, file creation, migration run, or structural update MUST be logged immediately in `basic-knowledge/log_code.md` using the format:
  `YYYY-MM-DD | [TYPE] | Description | File(s) affected`

## 3. UI/UX & Layout Best Practices:
- **UI References**: Use layout patterns and styling from `resources/views/docs/` as reference models.
- **Theme Auto-Adaptation**: Ensure UI elements and charts dynamically adapt to `[data-theme="light"]` and `[data-theme="dark"]`.
- **Feedback & Feedback Mechanics**: Use `NexoraToast()` for toast alerts, `input-skeleton` and `btn-loading` for form submissions with minimum 400ms feedback latency.
- **Pagination & Filters**: Include `10`, `20`, `50`, `100`, `all` per-page dropdown options for tables with instant, interactive pagination.
