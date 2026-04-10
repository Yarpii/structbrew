# Security First Scan Report (StructBrew)

**Date:** 2026-04-10  
**Scope:** Static code review + lightweight grep/lint checks in `/workspace/structbrew`  
**Focus:** XSS, SQL Injection, and baseline production-readiness risks

---

## Executive summary

This first-pass scan found **no immediately exploitable, confirmed SQL injection path** in controller code (queries are mostly prepared/parameterized), but found **multiple production security gaps** and **several potential injection/XSS-adjacent weaknesses** that should be remediated before go-live.

### Risk snapshot

- **Critical:** 0
- **High:** 1
- **Medium:** 2
- **Low:** 2

### Scope-based interpretation

For a custom framework and broad e-commerce scope, this is a **decent first-pass result** (no criticals, no confirmed active SQLi path), but production rollout should still be gated on fixing the high-risk hardening gap and re-testing.

---

## Findings

## 1) Missing baseline HTTP security headers (CSP/HSTS/anti-MIME/clickjacking)
**Severity:** High  
**Category:** Hardening / XSS impact reduction

### What I found
Response sending is centralized, but there is no enforced default header policy (for example `Content-Security-Policy`, `Strict-Transport-Security`, `X-Content-Type-Options`, `X-Frame-Options`/`frame-ancestors`, `Referrer-Policy`).

### Why this matters
Even when templates are mostly escaped, lack of CSP and related headers significantly increases blast radius of any future template bug, third-party script compromise, or HTML injection.

### Evidence
- `Response::send()` outputs only explicitly set headers; no security defaults are added. (`App/Core/Response.php`)

### Recommendation
- Add default security headers in a central middleware or `Response::send()`.
- Start with a report-only CSP, then enforce.

---

## 2) Host header trust used in SEO URL generation (possible host header poisoning)
**Severity:** Medium  
**Category:** Injection / cache poisoning / phishing

### What I found
SEO canonical/organization URLs derive from `$_SERVER['HTTP_HOST']` directly.

### Why this matters
If upstream proxy/web server host validation is weak, attacker-controlled Host headers can influence generated canonical/OG URLs, potentially enabling cache poisoning, phishing links, or SEO pollution.

### Evidence
- `Seo::currentUrl()` builds URL from `$_SERVER['HTTP_HOST']`.
- `Seo::organizationSchema()` uses `$_SERVER['HTTP_HOST']` for schema URLs.

### Recommendation
- Validate host against configured allow-list from store domain tables/config.
- Fall back to configured canonical host when mismatch occurs.

---

## 3) SQL injection posture: mostly safe, but query builder allows raw identifier composition
**Severity:** Medium (design-level risk)  
**Category:** SQL injection hardening

### What I found
The DB layer uses prepared statements for values (good), but it allows unvalidated raw identifiers in `select()`, `orderBy()`, `join()`, and arbitrary SQL in `whereRaw()`.

### Why this matters
Current controllers generally pass hardcoded columns, so no immediate exploit found. However, this is a risky API surface: if future code forwards request params into these methods, SQLi becomes likely.

### Evidence
- SQL builder methods directly concatenate identifiers/fragments.

### Recommendation
- Add allowlist validation for identifiers in builder methods.
- Restrict or deprecate `whereRaw()` for user-influenced flows.
- Add tests proving request sort/filter params cannot reach raw SQL without mapping.

---

## 4) Output-encoding gaps exist in some views (potential stored/reflected XSS if tainted data reaches them)
**Severity:** Low (currently contextual)  
**Category:** XSS

### What I found
Many views correctly call `htmlspecialchars()`, but some interpolations render variables directly in HTML text/attribute contexts.

### Why this matters
If any of these values become attacker-controlled via admin import, compromised DB, or future feature changes, XSS may be introduced.

### Evidence examples
- Direct value output without escaping in order history/date/status rendering (`App/Views/admin/orders/show.php`).
- Direct value output in multiple admin/account templates for status/date counters/classes.

### Recommendation
- Enforce “escape-by-default” template guideline.
- Add a CI rule/linter for raw `<?= $var ?>` in views (except strict allowlisted cases).

---

## 5) Dependency CVE visibility is limited
**Severity:** Low  
**Category:** CVE process

### What I found
Project is custom PHP framework with no Composer lock/dependency manifest in repo root, so package-level CVE scanning is currently limited.

### Why this matters
You cannot reliably attest to “no known CVEs” without an SBOM/dependency manifest and infra/runtime scanning.

### Recommendation
- Introduce a dependency manifest/SBOM process (application + container/OS packages).
- Add periodic CVE scans in CI/CD (app deps + base image + OS libs).

---

## Checks run (first pass)

- PHP syntax lint across PHP files.
- Grep-based review for:
  - Raw SQL execution patterns (`whereRaw`, `raw`, `query`, `statement`).
  - Output encoding patterns (`<?= ... ?>` vs `htmlspecialchars`).
  - CSRF and auth protections.

> Note: This was a **static first scan** (no running DAST, no authenticated crawling, no infra/container scan yet).

---

## Proposed next phase (recommended before production)

1. **SAST deep pass** with security ruleset (PHP sinks/sources/taint).
2. **DAST** against running staging environment (authenticated + admin flows).
3. **Infra/CVE scan** (container image, OS packages, TLS config, exposed ports).
4. **Secrets & config review** (`.env`, cookie flags, session policy, CORS, rate limits).
5. **Fix & verify loop**: patch highest risks, rerun scan, produce clean baseline report.
