#!/usr/bin/env bash
#
# Éprouve rename.sh sur une copie des fichiers suivis du dépôt.
#
# Usage : tests/rename.test.sh

set -euo pipefail

racine="$(git rev-parse --show-toplevel)"
copie="$(mktemp -d)"
trap 'rm -rf "$copie"' EXIT

git -C "$racine" ls-files -z | tar --null -T - -C "$racine" -cf - | tar -xf - -C "$copie"
git -C "$copie" init -q
git -C "$copie" add -A

(cd "$copie" && ./rename.sh mon-site https://mon-site.local >/dev/null)

echecs=0
for chemin in .claude/vault tests; do
	if [ -e "$copie/$chemin" ]; then
		echo "rename.sh laisse $chemin à un projet neuf." >&2
		echecs=1
	fi
done

if [ "$echecs" -ne 0 ]; then
	exit 1
fi

echo "rename.sh : vert."
