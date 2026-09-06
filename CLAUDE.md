# Astra Child Starter Theme

Ce dépôt est le point de départ d'un thème enfant d'Astra. Un projet neuf le copie, le renomme par `./rename.sh`, et n'y revient jamais. Ni le site, ni le thème parent Astra, ni ses extensions ne sont dans le dépôt.

Une amélioration trouvée sur un projet remonte à la main dans ce starter. Elle ne redescend pas vers les projets déjà partis.

## Où va le PHP

Chaque sujet prend son fichier dans `inc/`. `functions.php` ne porte qu'un `require_once` par fichier de `inc/`.

> POURQUOI : relevé le 03/09/2026 sur un thème enfant d'Astra plus ancien. Tout son PHP vit dans un `functions.php` de 1 563 lignes. Shortcodes, gestionnaires AJAX, colonnes d'admin et filtres oEmbed y sont mêlés. Personne n'a choisi cette forme. Le fichier l'a prise faute d'être découpé.

## Tarteaucitron se charge depuis `build/tarteaucitron/`

Tarteaucitron est la bibliothèque qui recueille le consentement aux cookies. `CopyWebpackPlugin`, le plugin de copie de webpack, dépose quatre de ses fichiers dans `build/tarteaucitron/`. `inc/tarteaucitron.php` enfile le script depuis ce dossier, dans l'en-tête et sans `defer`.

Un service se déclare dans `astra_child_starter_tarteaucitron_services()`. Les clés de service vivent dans le catalogue de la bibliothèque, `tarteaucitron.services.js`.

L'identifiant de mesure de Google Analytics se définit dans `wp-config.php`, sous la constante `ASTRA_CHILD_STARTER_GTAG_ID`. Le thème n'en livre aucun.

> POURQUOI un dossier à part : la bibliothèque déduit son propre dossier de la balise `<script>` qui la charge, par `document.currentScript`. Elle y cherche ensuite son fichier de langue, son catalogue de services et sa feuille de styles. Empaquetée, elle les chercherait là où ils ne sont pas. Relevé le 05/09/2026 dans son code source, en version 1.34.0.
>
> POURQUOI `CopyWebpackPlugin` plutôt qu'un `&&` dans le script npm : un fichier posé à la main dans `build/` ne survit pas au build suivant. Un fichier émis par le plugin y reste. Mesuré le 06/09/2026, en deux essais.

## Où va le CSS qui vise du code tiers

Un partiel Sass peut viser une classe d'Astra, d'une extension ou d'un bloc de WordPress. Ce partiel se nomme alors dans le bloc `overrides` de `.stylelintrc.json`. Ce bloc y éteint `selector-class-pattern` et `selector-id-pattern`.

Un rouge sur l'une de ces deux règles dit d'ajouter le partiel à ce bloc.

> POURQUOI : Tarteaucitron nomme ses classes et ses identifiants en camelCase, `tarteaucitronAllow` et `#tarteaucitronServices`. Élargir la regex pour eux reviendrait à y écrire le nom d'une bibliothèque. Relevé le 04/09/2026 sur un thème enfant d'Astra plus ancien. Tarteaucitron y produit 21 des 39 violations de `selector-class-pattern`, et 29 des 33 de `selector-id-pattern`.

## Le thème dépend d'une chose qu'il ne contient pas

`inc/enqueue.php` fait `include` de deux fichiers de `build/`, qui est gitignoré. Après un clone, `npm install` puis `npm run build` doivent tourner.

## Ce qui fait vert

`npm run lint`, puis `npm run build`. `npm run lint` appelle `composer lint`, donc `composer install` doit avoir tourné.

`composer lint` lance PHPCS puis PHPStan. PHPCS contrôle le style, PHPStan les types.

Ce dépôt n'a pas de suite de tests, et il ne tranche pas cette question pour un projet. Chaque projet décide, et écrit son POURQUOI dans son propre `CLAUDE.md`.

> POURQUOI le starter ne tranche pas : la règle par défaut veut qu'un changement de comportement s'accompagne d'un test. Un starter qui affirmerait le contraire installerait cette exception dans chaque projet neuf, sans que personne l'ait décidée.

## Le PHP suit les standards de WordPress

PHPCS contrôle ces standards, sur la configuration de `phpcs.xml.dist`. `composer format` corrige ce qui se corrige seul.

`phpcs.xml.dist` porte aussi le préfixe des globales, `astra_child_starter`, et le domaine de traduction, `astra-child-starter-theme`. `./rename.sh` réécrit les deux.

## Ce que rien ne contrôle

Les poignées passées à `wp_enqueue_style` et `wp_enqueue_script` portent `astra-child-starter`, avec des tirets.

Le seul CSS enfilé est `build/css/main.css`. `style.css` ne porte que son en-tête. Cet en-tête est le seul endroit qui porte la version du thème.

## Git

- Conventional Commits, sujet en anglais, à l'impératif, 72 caractères au plus.
- Le travail part de `main` sur une branche `feature/<nom-court>`. Il y revient par une pull request.
- **Une pull request se merge avec ses commits**, jamais en squash : l'historique est fait de commits de merge.
