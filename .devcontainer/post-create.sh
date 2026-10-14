#!/usr/bin/env bash
set -euo pipefail

workspace="$1"

for f in "$workspace"/.devcontainer/php/*.ini; do
  ln -sf "$f" "/usr/local/etc/php/conf.d/$(basename "$f")"
done

echo 'alias a="php artisan"' >> ~/.bashrc

composer setup