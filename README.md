# Astra Child Starter Theme

Le point de départ d'un thème enfant d'Astra. Il porte sa chaîne de build, ses contrôles de style et de types, et son panneau de consentement aux cookies.

Un projet neuf part de ce dépôt, le renomme, et n'y revient jamais. Une amélioration trouvée sur un projet remonte ici à la main.

## Démarrer un projet

1. **Créer le dépôt.** Le bouton « Use this template » de GitHub crée le dépôt du projet. Le cloner ensuite dans le dossier `wp-content/themes/` du site.
2. **Renommer le thème.** Lancer `./rename.sh <nom-court> <adresse-du-site-local>`, par exemple `./rename.sh mon-site https://mon-site.local`. Le script réécrit le nom générique dans tous les fichiers texte suivis par git. Il remet ensuite la version du thème à 1.0.0. Il finit par un relevé, et sort en erreur si une occurrence subsiste.
3. **Installer les dépendances.** Lancer `npm install`, puis `composer install`.
4. **Compiler.** Lancer `npm run build`. Le dossier `build/` est gitignoré, et `inc/enqueue.php` en fait `include`. Sans cette étape, le thème affiche un avertissement aux seuls administrateurs.
5. **Activer le thème** dans l'administration de WordPress, sous Apparence puis Thèmes.

**Après le renommage, ce README et le `CLAUDE.md` parlent encore du starter.** Retirer de ce README son ouverture et la section « Démarrer un projet ». Reprendre le `CLAUDE.md` avec ce que le projet décide, la question des tests comprise.

Le script `rename.sh` reste dans le dépôt après usage. Un projet change parfois de nom en cours de route, et le script sert alors une seconde fois.

## Deux choses à poser à la main

**Le lien qui rouvre le panneau de consentement.** Le thème ne livre aucun point d'entrée. Un lien vers l'ancre `#tarteaucitron` rouvre le panneau. Ce lien se pose dans le pied de page, depuis le personnalisateur d'Astra. Sans lui, un visiteur qui a refusé ne peut plus revenir sur son choix.

**L'identifiant de mesure de Google Analytics.** Le thème n'en livre aucun. Le définir dans le `wp-config.php` du site :

```php
define( 'ASTRA_CHILD_STARTER_GTAG_ID', 'G-XXXXXXXXXX' );
```

Sans cette constante, le service `gtag` ne se déclare pas, et le panneau ne le propose pas. Le renommage réécrit le nom de cette constante avec celui du thème.

## Les commandes

`npm run start` compile en veille et lance Browser-sync, le serveur de développement, sur l'adresse du site local. `npm run build` compile pour la production.

`npm run lint` enchaîne ESLint, Stylelint et `composer lint`. Ce dernier lance PHPCS puis PHPStan. PHPCS contrôle le style du PHP, PHPStan ses types.

`npm run lint:style` et `composer format` corrigent ce qui se corrige seul.

## Ce que le starter porte

Le PHP est découpé par sujet dans `inc/`, et `functions.php` ne porte qu'un `require_once` par fichier.

Le Sass et le JavaScript portent chacun une preuve que la chaîne compile. Le Sass porte une règle sur une classe qui n'existe sur aucune page. Le JavaScript porte un `console.log`. Les deux se retirent dès que le projet écrit son propre code.

Tarteaucitron vient de npm et reste hors du paquet JavaScript. Le `CLAUDE.md` du dépôt dit pourquoi, et où se déclare un service.

## Ce que le projet décide lui-même

Le starter ne tranche pas la question des tests. Chaque projet décide, et écrit son POURQUOI dans son propre `CLAUDE.md`.
