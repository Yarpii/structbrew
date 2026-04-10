#!/usr/bin/env bash
set -euo pipefail

# Lists potentially unsafe short echo tags in views for manual review.
# Allowlist common safe patterns to reduce noise.
rg -n "<\?=" App/Views \
  | rg -v "htmlspecialchars|\(int\)|\(float\)|number_format|json_encode|date\(|Session::csrfToken\(|__\(" \
  || true
