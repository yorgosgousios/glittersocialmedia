# Glitter Social Media

WordPress site for Glitter Social Media (glittersocialmedia.gr). It uses a custom classic theme with no page builder, and is bilingual: Greek is the default at `/`, English is at `/en/`, using Polylang.

## Local setup

Requires the shared Traefik proxy (`traefik_proxy` network, `*.local.dev`).

```sh
cp .env.example .env
make up        # start containers
make install   # WP + Polylang + Greek core translations, languages, pages, menus (safe to re-run)
make seed      # bilingual placeholder services, work, team, testimonials, posts
make unseed    # remove all placeholder content
```

- Site: https://glitter.local.dev (Greek) and https://glitter.local.dev/en/ (English)
- Admin: `/wp-admin` (`admin` / `admin`)
- Mail: https://mail-glitter.local.dev. Mailpit catches every email the site sends.
- WP-CLI: `make wp -- <command>`

## Checks

```sh
composer install && npm ci   # once
make lint                    # PHPCS (WordPress standards) + ESLint + Stylelint
make smoke                   # key pages in both languages return 200, PHP logs no errors
```

## Structure

```
theme/                  the Glitter theme (the only code we deploy)
  inc/                  setup, i18n (Polylang + Greeklish slugs), assets, post types,
                        meta boxes, customizer, brief form, helpers/stickers
  template-parts/       service/work/post cards, ticker, testimonials, CTA
  template-about.php    "About (team)" page template
  template-contact.php  "Contact (project brief)" page template
  languages/            glitter.pot, el.po → el.mo / el.l10n.php (Greek UI strings)
  assets/{css,js,fonts} plain CSS/JS, no build step; fonts self-hosted
local/mu-plugins/       local-only helpers (Mailpit SMTP, hide admin bar), not deployed
scripts/                install/setup (languages, pages, menus), seed/unseed, smoke test
.github/workflows/      CI and deploy
```

## Content editing

- **Services, Work, Team, Testimonials:** each has its own admin menu. Each item has a "Details" box (sticker, deliverables, client, results and so on) and a language. Use Polylang's "+" to create the translation.
- **Home:** the hero subtitle is the Home page excerpt and the intro is the Home page content, set per language. The headline and section titles are theme strings, translated in `theme/languages/el.po`.
- **Contact details and socials:** Appearance → Customize → Agency & social. Briefs go to that email (default `hey@glittersocialmedia.gr`).
- **Menus:** there's one menu per language. Polylang assigns them under Appearance → Menus.

## Translating theme strings

After changing any `__()` text in the theme:

```sh
make pot            # refresh theme/languages/glitter.pot
# update theme/languages/el.po from the POT (e.g. Poedit: Catalog → Update from POT) and translate
make translations   # compile el.mo + el.l10n.php
```

## CI/CD (GitHub Actions)

- **CI** (`ci.yml`) runs on every push and pull request. It does a PHP syntax check on 8.1 and 8.3, runs PHPCS, ESLint and Stylelint, and runs a fresh Docker install with seeding and the smoke test. Once all of that passes, it builds `glitter-theme.zip` as an artifact.
- **Deploy** (`deploy.yml`) runs after CI passes on `main`, or manually. It uploads `theme/` over FTPS with lftp (only changed files) to the Papaki hosting, then checks that the homepage returns 200. See INFRASTRUCTURE.txt for the full setup and the reasoning behind it. It stays off until `DEPLOY_ENABLED=true`. Setup notes are at the top of the file.

Only the theme is deployed. WordPress core, Polylang, uploads and the database live on the host.
