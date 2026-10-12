#!/bin/sh
# Smoke test: key pages return 200 and PHP logs no errors.
# Talks to the wordpress container directly over the compose network, so it
# works locally and in CI (no Traefik/TLS needed). Run from the repo root: `make smoke`.
set -eu

. ./.env
NETWORK="${PROJECT_NAME}_default"

fetch() {
  docker run --rm --network "$NETWORK" curlimages/curl:8.11.1 \
    -s -o /dev/null -w '%{http_code}' \
    -H "Host: ${PROJECT_BASE_URL}" -H 'X-Forwarded-Proto: https' \
    "http://wordpress$1"
}

fail=0
for path in / /en/ /services/ /en/services/ /work/ /en/work/ /sxetika/ /en/about/ /nea/ /en/journal/ /epikoinonia/ /en/contact/ /politiki-aporritou/ /en/privacy-policy/ '/?s=test'; do
  code=$(fetch "$path")
  echo "$code $path"
  [ "$code" = 200 ] || fail=1
done

code=$(fetch /this-page-does-not-exist/)
echo "$code /this-page-does-not-exist/ (expect 404)"
[ "$code" = 404 ] || fail=1

log=$(docker compose exec -T wordpress sh -c 'cat wp-content/debug.log 2>/dev/null || true')
if [ -n "$log" ]; then
  echo "PHP debug.log is not empty:"
  echo "$log"
  fail=1
fi

[ "$fail" = 0 ] && echo "Smoke test passed." || { echo "Smoke test FAILED."; exit 1; }
