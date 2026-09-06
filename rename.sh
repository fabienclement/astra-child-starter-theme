#!/usr/bin/env bash
#
# Renomme le thème enfant pour un projet neuf.
#
# Usage :   ./rename.sh <nom-court> <adresse-du-site-local>
# Exemple : ./rename.sh mon-site https://mon-site.local
#
# Le script reste dans le dépôt après usage : un projet change parfois de nom
# en cours de route. Il s'exclut lui-même du remplacement et du relevé final,
# parce qu'il porte le nom générique dans ses propres motifs de recherche.

set -euo pipefail

generique_titre="Astra Child Starter Theme"
generique_majuscules="ASTRA_CHILD_STARTER"
generique_snake="astra_child_starter"
generique_kebab="astra-child-starter"
generique_url="https://site.local"

if [ "$#" -ne 2 ]; then
	echo "Usage :   $0 <nom-court> <adresse-du-site-local>" >&2
	echo "Exemple : $0 mon-site https://mon-site.local" >&2
	exit 1
fi

nom="$1"
url="$2"

if ! [[ "$nom" =~ ^[a-z][a-z0-9]*(-[a-z0-9]+)*$ ]]; then
	echo "Le nom court s'écrit en minuscules, chiffres et tirets. « $nom » ne convient pas." >&2
	exit 1
fi

if ! [[ "$url" =~ ^https?:// ]]; then
	echo "L'adresse du site local commence par http:// ou https://. « $url » ne convient pas." >&2
	exit 1
fi

if ! git rev-parse --git-dir >/dev/null 2>&1; then
	echo "Ce script se lance depuis la racine d'un dépôt git." >&2
	exit 1
fi

snake="${nom//-/_}"
majuscules="$(printf '%s' "$snake" | tr '[:lower:]' '[:upper:]')"
titre="$(printf '%s' "$nom" | tr '-' ' ' | awk '{ for (i = 1; i <= NF; i++) $i = toupper(substr($i, 1, 1)) substr($i, 2) } 1')"

# BSD sed veut un argument de suffixe, GNU sed n'en veut pas.
if sed --version >/dev/null 2>&1; then
	sed_i=(sed -i)
else
	sed_i=(sed -i '')
fi

# `grep -I` écarte les fichiers binaires : screenshot.jpg fait sortir sed en
# erreur sur « illegal byte sequence ».
fichiers=()
while IFS= read -r fichier; do
	[ "$fichier" = "rename.sh" ] && continue
	[ -f "$fichier" ] || continue
	grep -Iq . "$fichier" || continue
	fichiers+=("$fichier")
done < <(git ls-files)

# L'ordre compte : le kebab long passe avant le court, sinon le Text Domain
# « astra-child-starter-theme » deviendrait « mon-site-theme ».
for fichier in "${fichiers[@]}"; do
	"${sed_i[@]}" \
		-e "s|${generique_titre}|${titre}|g" \
		-e "s|${generique_majuscules}|${majuscules}|g" \
		-e "s|${generique_snake}|${snake}|g" \
		-e "s|${generique_kebab}-theme|${nom}|g" \
		-e "s|${generique_kebab}|${nom}|g" \
		-e "s|${generique_url}|${url}|g" \
		"$fichier"
done

if [ -f "${generique_kebab}-theme.code-workspace" ]; then
	mv "${generique_kebab}-theme.code-workspace" "${nom}.code-workspace"
fi

# La version du thème repart à 1.0.0 : le projet neuf commence son propre
# décompte, et n'hérite pas de celui du starter.
"${sed_i[@]}" -e 's|^Version: .*|Version: 1.0.0|' style.css
"${sed_i[@]}" -e 's|^\(  "version": \)"[^"]*"|\1"1.0.0"|' package.json

# Le fichier de verrou porte la version deux fois pour le paquet lui-même, puis
# une fois par dépendance. Seules les deux premières occurrences sont les
# siennes, quelle que soit leur valeur.
if [ -f package-lock.json ]; then
	awk 'BEGIN { vues = 0 }
		/^ *"version": "/ && vues < 2 {
			sub(/"version": "[^"]*"/, "\"version\": \"1.0.0\"")
			vues++
		}
		1' package-lock.json >package-lock.json.tmp
	mv package-lock.json.tmp package-lock.json
fi

echo "Renommage fait : ${titre} (${nom}), site local ${url}."

# Le relevé final. Sans lui, le script déplacerait le risque d'oubli vers son
# propre auteur, sans le supprimer.
restes=()
while IFS= read -r fichier; do
	[ "$fichier" = "rename.sh" ] && continue
	[ -f "$fichier" ] || continue
	grep -Iq . "$fichier" || continue
	if grep -q -F -e "$generique_titre" -e "$generique_majuscules" -e "$generique_snake" -e "$generique_kebab" -e "$generique_url" "$fichier"; then
		restes+=("$fichier")
	fi
done < <(git ls-files)

# `git ls-files` rend l'index, qui garde l'ancien nom du fichier d'espace de
# travail après son renommage sur le disque. D'où le contrôle d'existence.
while IFS= read -r fichier; do
	[ -e "$fichier" ] || continue
	case "$fichier" in
	*"$generique_kebab"*) restes+=("$fichier (nom de fichier)") ;;
	esac
done < <(git ls-files)

if [ "${#restes[@]}" -ne 0 ]; then
	echo "Le nom générique subsiste dans :" >&2
	printf '  %s\n' "${restes[@]}" >&2
	exit 1
fi

echo "Relevé final : aucune occurrence du nom générique."
