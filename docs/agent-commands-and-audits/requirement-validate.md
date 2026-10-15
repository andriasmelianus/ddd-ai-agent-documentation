---
description: Validates requirement documents (epics, features, hotfixes, cases)
proactive: false
triggers:
  - "validate requirement"
  - "review requirement"
  - "/requirement-validate"
---

# Universal AI Agent Command: Requirement Validation

Prompt for AI Agents to validate the completeness of requirement documents located under `docs/working_docs/`.

---

## 🧭 Core Principle: "Analysis Informs, Never Blocks"
- Evaluate documents to discover risks, logical loopholes, and hidden dependencies.
- Never block the user; provide clear information and empower the user to decide.

---

## 🔍 Validation Flow by Document Type:

### 1. Detect Requirement Type:
- If the path contains `/epics/` ➔ Execute **Full Validation**.
- If the path contains `/features/` ➔ Execute **Lightweight Validation (check reference to parent epic)**.
- If the path contains `/hotfixes/` ➔ Execute **Problem-Focused & Rollback Validation**.
- If the path contains `/cases/` ➔ Execute **Investigation Validation (NO IMPLEMENTATION CODE PERMITTED)**.

### 2. Inspection Checklist (For Epics & Features):
1. **Business Alignment**: Are business goals and KPIs (or experiment hypotheses) explicitly defined?
2. **Entities & CRUD Cycle**: Have Create, Read, Update, Delete, and List operations been evaluated?
3. **State & Transition Analysis**: Are initial status, all valid states, transition triggers, and conditions fully specified?
4. **Inverse Operations**: Have counterpart actions (e.g., cancel, deactivate, reject) been considered?
5. **Collateral Impact**: Does this modification affect currently active modules, endpoints, database schemas, or reporting?
6. **Slicing & MVP**: Is the scope overly broad? If so, is the release phase breakdown (MVP vs. subsequent phases) logical?

---

## 📄 Validation Result Output Format

AI Agents must present analysis output in the following format:

```markdown
# Requirement Validation Report: [Document Name]

**Detected Type:** Epic / Feature / Hotfix / Case  
**Evaluation Status:** Ready for Implementation / Needs Clarification  

### 1. Potentially Missing Use Cases
| Missing Use Case | Reason Needed | Priority | Question for Stakeholders |
|---|---|---|---|
| [Action] | [Reason] | Must / Should | [Question] |

### 2. State Machine & Transition Evaluation
- **Initial Status**: [Defined / Undefined]
- **Transition Loopholes**: [Explain if any dangling states exist without exit paths]

### 3. Collateral Impact Analysis
- **Affected Components**: [List of affected modules/tables]
- **Potential Risks**: [Technical risks / regressions in existing features]

### 4. Slicing & MVP Evaluation
- [Recommendation on whether the feature size is appropriate or should be partitioned into Slice 1, Slice 2]

### 5. Open Clarification Questions
1. [Specific questions requiring user feedback]
```
