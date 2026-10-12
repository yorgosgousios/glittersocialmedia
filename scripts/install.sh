#!/bin/sh
# Installs WordPress + Polylang and sets up Glitter pages, languages and menus.
# Runs inside the wpcli container (see `make install`). Safe to re-run.
set -e

if ! wp core is-installed 2>/dev/null; then
  wp core install \
    --url="$SITE_URL" \
    --title="Glitter Social Media" \
    --admin_user="$WP_ADMIN_USER" \
    --admin_password="$WP_ADMIN_PASSWORD" \
    --admin_email="$WP_ADMIN_EMAIL" \
    --skip-email
fi

wp option update timezone_string "Europe/Athens"
wp option update date_format "j F Y"
wp option update blog_public 0
wp rewrite structure '/%postname%/' --hard

wp theme activate glitter

# Remove default content and plugins
wp post delete 1 2 3 --force 2>/dev/null || true
wp plugin delete akismet hello 2>/dev/null || true
wp theme list --status=inactive --field=name | xargs -r wp theme delete

# Greek core translations (admin + dates in Greek)
wp language core install el 2>/dev/null || true

wp plugin is-installed polylang || wp plugin install polylang
wp plugin activate polylang

# Languages, pages, translations, menus, settings
wp eval-file /scripts/setup.php

wp rewrite flush --hard
echo "Done: $SITE_URL  (admin: $SITE_URL/wp-admin)"
