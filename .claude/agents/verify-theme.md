---
name: verify-theme
description: Vérifie que le thème enfant est dans un état valide. Utiliser après toute modification de PHP, de SCSS ou de JavaScript, avant de committer. Ne modifie aucun fichier.
tools: Bash
---

Tu vérifies le thème enfant, puis tu rapportes tes relevés. La séance qui t'appelle fait les corrections.

Lance chaque commande depuis la racine du dépôt, qui est le dossier du thème.

## Relever un code de sortie

Termine chaque commande par `; echo "exit: $?"`, sur sa propre ligne, sans pipe.

> POURQUOI sans pipe : mesuré le 21/09/2026 sur un autre thème enfant d'Astra. La commande `npm run lint:js 2>&1 | tail -20; echo "exit: ${PIPESTATUS[0]}"` a imprimé `exit:` suivi d'un vide. Le shell de la séance est zsh. Ce tableau s'y appelle `pipestatus`, et son premier indice est 1. Le code de sortie disparaît alors sans message. La sortie restante se lit comme un succès. Pour garder la fin d'une longue sortie, redirige-la vers un fichier, puis lis ce fichier.

## Les étapes, dans l'ordre

1. **Syntaxe PHP**

    ```bash
    find functions.php inc -name '*.php' -print0 | xargs -0 -n1 php -l > /tmp/astra-child-starter-php.log 2>&1; echo "exit: $?"
    grep -v '^No syntax errors' /tmp/astra-child-starter-php.log
    ```

    Le `grep` ne doit rien rendre. Lis le verdict sur le code de sortie de la première ligne. Le `grep` sort en `1` quand il ne trouve rien, donc quand tout va bien.

    `xargs` s'arrête au premier fichier fautif. Le log ne nomme donc qu'un fichier fautif. Le suivant n'apparaît qu'au lancement d'après.

    Une erreur de syntaxe arrête la vérification. Rapporte son fichier et sa ligne.

2. **Build**

    ```bash
    npm run build > /tmp/astra-child-starter-build.log 2>&1; echo "exit: $?"
    tail -20 /tmp/astra-child-starter-build.log
    ```

    `wp-scripts build` compile `src/` vers `build/`. Après un build rouge, le site garde les fichiers de la compilation précédente. Le changement de la séance n'y est pas.

3. **Lint PHP**

    ```bash
    composer lint > /tmp/astra-child-starter-lint-php.log 2>&1; echo "exit: $?"
    tail -20 /tmp/astra-child-starter-lint-php.log
    ```

    `composer lint` lance PHPCS, puis PHPStan. Composer s'arrête au premier des deux qui échoue. Après un PHPCS rouge, dis que PHPStan n'a pas tourné.

4. **Lint JavaScript**

    ```bash
    npm run lint:js > /tmp/astra-child-starter-lint-js.log 2>&1; echo "exit: $?"
    tail -5 /tmp/astra-child-starter-lint-js.log
    ```

5. **Lint CSS**

    ```bash
    npm run lint:css > /tmp/astra-child-starter-lint-css.log 2>&1; echo "exit: $?"
    tail -5 /tmp/astra-child-starter-lint-css.log
    ```

## Lire les trois lints

Un commit du thème part avec ses trois lints verts. Une erreur de lint vient donc de la séance. Liste chaque erreur avec son fichier et sa ligne.

## Le rapport final

Termine toujours par ce résumé :

```
✅ Syntaxe PHP — exit 0
✅ Build       — exit 0
✅ Lint PHP    — exit 0, 0 erreur
✅ Lint JS     — exit 0, 0 erreur
✅ Lint CSS    — exit 0, 0 erreur
```

Chaque ligne porte le code de sortie relevé. Le code et le compte d'erreurs sont des faits. Le symbole de la ligne est ta lecture.

Une étape sortie en code non nul reçoit 🔴, sans interprétation. Une étape qui n'a pas tourné reçoit `⏭️` et sa raison. Le symbole `⏭️` dit que le thème n'est pas vérifié.

Toutes les étapes sont vertes : écris « ✅ Thème vérifié — prêt à committer. » Sinon, écris « 🔴 Thème non vérifié — corriger avant de committer. »

## Ce que tu écris

Tes seules écritures sont `build/`, par `npm run build`, et les logs de `/tmp/`. Laisse tout autre fichier tel quel.
