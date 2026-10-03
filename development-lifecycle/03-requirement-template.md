# 03 - Requirement Document Templates

Standard templates for requirements documents within `docs/working_docs/`: **Epic**, **Feature**, **Hotfix**, and **Case (Investigation)**.

---

## 🏛️ Template 1: EPIC (`docs/working_docs/epics/{epic-name}/{epic-name}_requirements.md`)

```markdown
# Epic: [Business Initiative Name]

**Epic ID:** EPIC-001  
**Owner / Domain Expert:** [Person's Name]  
**Status:** Draft / Validated / In Progress / Completed  
**Target Deadline:** YYYY-MM-DD (if applicable)  

## 1. Business Alignment & Justification
- **Primary Business Goal**: [Grow Revenue / Reduce Churn / Operational Efficiency]
- **Target KPIs**:
  - Baseline: [Current state, e.g., Occupancy rate 60%]
  - Target: [Post-release target, e.g., Occupancy rate 80%]
- **Supporting Evidence / Data**: [Support tickets, client contract requests, analytics data]

## 2. Problem Statement & Scope
- **Current Problem**: [Explanation of user pain points]
- **Expected Solution**: [Summary of new capability]
- **Core Entities**: [List of domain entities involved]

## 3. State & Entity Lifecycle Analysis
- **Initial Status**: [e.g., DRAFT / PENDING]
- **Valid States**: [DRAFT, ACTIVE, PAUSED, CANCELLED]
- **Transition Matrix**:
  - DRAFT -> ACTIVE (Trigger: Click Publish, Condition: Content is valid)
  - ACTIVE -> CANCELLED (Trigger: Click Cancel)

## 4. Use Cases & Acceptance Criteria
### Use Case 1: [Use Case Name]
- **Actor**: [User / Admin / System]
- **Preconditions**: [Initial conditions]
- **Main Flow**:
  1. User selects...
  2. System validates...
- **Acceptance Criteria**:
  - [ ] Given X, When Y, Then Z

## 5. Slicing Plan (Release Phases)
- **Slice 1 (MVP)**: [Core features ready for immediate testing]
- **Slice 2**: [Supporting features / advanced management]
- **Out of Scope (This Phase)**: [Intentionally deferred items]

## 6. Collateral Impact Analysis
- **Affected Components**: [Other modules/tables impacted]
- **Data Migration Needs**: [Yes/No, specify scenario if applicable]
- **Risks & Mitigations**: [Technical / business risks and preventative steps]

## 7. Definition of Done (DoD)
- [ ] Code strictly follows DDD & CQRS rules
- [ ] Domain unit tests pass 100%
- [ ] PHPStan 0 errors
- [ ] UAT accepted by Product Owner
```

---

## 🧩 Template 2: FEATURE (`docs/working_docs/features/{feature-name}/{feature-name}_requirements.md`)

```markdown
# Feature: [Feature Name]

**Parent Epic:** [Link to Parent Epic Document](../../epics/{epic-name}/{epic-name}_requirements.md)  
**Feature Scope:** [Which slice of the parent Epic is addressed]  
**Status:** Draft / In Development / Done  

## 1. Brief Context
Subordinate to the parent epic. This feature specifically accomplishes [specific feature goal].

## 2. Functional Specifications & Acceptance Criteria
### Scenario 1: [Scenario Name]
- **Given**: [Prerequisite conditions]
- **When**: [User/system action]
- **Then**: [Expected outcome]

## 3. Input & Output Data
- **Request Payload**: [Fields, data types, required validations]
- **Response Format**: [Resource response structure]

## 4. Testing & Definition of Done
- [ ] Handler & Entity unit tests
- [ ] HTTP Endpoint integration tests
- [ ] Architecture compliance review
```

---

## 🚨 Template 3: HOTFIX (`docs/working_docs/hotfixes/HF-YYYY-XXX-{slug}/hotfix_requirements.md`)

```markdown
# Hotfix: [Urgent Problem Description]

**Hotfix ID:** HF-2026-001  
**Severity:** Critical / High  
**Reported Date:** YYYY-MM-DD  
**Affected Users / Services:** [Who is affected by the error]  

## 1. Problem Description
[Symptoms occurring in production, error HTTP status, or stack traces]

## 2. Business & Operational Impact
- Estimated failed transactions: [Quantity]
- Direct impact: [Data loss / customer complaints]

## 3. Root Cause
[Technical cause of the bug, problematic file and line numbers]

## 4. Proposed Technical Solution
[Code repair plan]

## 5. Verification & Testing
- [ ] Local bug reproduction steps
- [ ] Fix verification
- [ ] Regression testing of related components

## 6. Rollback Plan
[Rapid steps to revert code/migrations if the hotfix triggers new side effects]
```

---

## 🔬 Template 4: CASE (Investigation Only) (`docs/working_docs/cases/CASE-YYYY-XXX-{slug}/case_report.md`)

> ⚠️ **NOTE**: This document is **STRICTLY FOR INCIDENT INVESTIGATION AND ANALYSIS**. This document **CONTAINS NO DIRECT CODE IMPLEMENTATION**. If investigation outcomes require technical remediation, create a separate Hotfix or Feature document.

```markdown
# Case: [Incident Investigation / Issue Analysis]

**Case ID:** CASE-2026-001  
**Incident Date:** YYYY-MM-DD  
**Investigation Status:** Open / In Progress / Closed  
**Investigator:** [Engineer / AI Agent Name]  

## 1. Incident Description
[Explanation of what happened and how the incident was initially detected]

## 2. Timeline of Events
- **10:00 UTC**: Incident initially detected in monitoring.
- **10:15 UTC**: Alert notification dispatched to the team.
- **10:45 UTC**: Log and database investigation initiated.

## 3. Investigation Findings
1. [Finding 1: error logs, data anomalies]
2. [Finding 2: slow queries or memory leaks]

## 4. Root Cause Analysis (5 Whys)
[Deep explanation of why the incident occurred]

## 5. Actionable Recommendations
1. **Immediate Action**: [Recommendation to create Hotfix HF-XXX]
2. **Long-Term Prevention**: [Recommendation to create Epic/Feature or add indexes/monitoring]
3. **Process Improvements**: [Update deployment SOP / migration checklist]
```
