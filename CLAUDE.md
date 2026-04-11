# PuntoPost PHP SDK

Official PHP SDK for the PuntoPost parcel delivery API. Published on Packagist as `puntopost/php-sdk`.

## Architecture

- `src/V1/` — SDK source code, versioned by API version
  - `AuthApi` — login (JWT)
  - `MerchantApi` — parcels (C2C, B2C, C2B), PUDOs, merchants, coverage
  - `Request/` — request DTOs with `toArray()` for the API body
  - `Response/` — response DTOs with `fromArray()` to parse API JSON
  - `Webhook/` — webhook event parser
- `tests/` — PHPUnit unit tests
- PHP 7.4 minimum — no enums, no named arguments, no union types, no readonly

## OpenAPI spec validation

`MockHttpClient` validates every request and response against `tests/fixtures/openapi.json` (the production OpenAPI spec). If a test uses mock data that doesn't match the spec, it fails with `InvalidArgumentException` showing the exact mismatch.

To update the spec: download from `https://back.puntopost.mx/api/doc/full.json` and replace `tests/fixtures/openapi.json`. Fix the nested `$ref` in `PudoLocationParcelResponse` if present.

## Commands

All commands run via Docker (no local PHP needed):

- `make install` — install dependencies
- `make check` — run ALL checks (ecs, phpstan, validate, lint, lint-latest, test, test-latest)
- `make test` — PHPUnit with PHP 7.4
- `make test-latest` — PHPUnit with PHP 8.5
- `make ecs` / `make ecs-fix` — code style check/fix
- `make phpstan` — static analysis
- `make lint` / `make lint-latest` — syntax check PHP 7.4 / 8.5

Always run `make check` before pushing. CI runs on pull requests.

## Releasing

This is a Packagist library. Packagist reads tags from GitHub to determine installable versions. The release process:

1. Merge the PR to `main`
2. Create a GitHub release with the new tag:
   ```bash
   gh release create v1.x.0 --target main --title "v1.x.0" --notes "release notes here"
   ```
   This creates the git tag automatically. Packagist picks it up within minutes.

Do NOT create tags without a release. Do NOT push branches to the remote after merging — delete them so Packagist only shows tags.

Follow semver: patch for fixes, minor for new fields/features, major for breaking changes.

## Rules

- Never commit `vendor/`, `.env`, or credentials
- Release notes should only describe changes visible to SDK consumers (new getters, breaking changes), not internal test/CI changes
- The `composer.json` has `"platform": {"php": "7.4.33"}` — this ensures dependency resolution targets PHP 7.4 even when running newer PHP locally
