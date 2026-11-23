# CLAUDE.md

Guidance for Claude Code in this repository. Deep references live under `.claude/docs/`.

## What this is

`msstc4php/vo-finance-doctrine`: Doctrine ORM and MongoDB ODM types for `msstc4php/vo-finance` (securities
identifiers, payment cards, payment QR payloads). Built on the abstract types of `msstc4php/vo-base-doctrine`.
PHP >= 8.4. No Symfony dependency; `Orm` and `Odm` never see each other (`deptrac.yaml`).

## Common commands

Local install against the sibling checkouts (`../vo-base`, `../vo-base-doctrine`, `../vo-finance`):
`COMPOSER=composer-local.json composer update --ignore-platform-req=php`. In CI the install is `composer-ci.json`.

`composer-local.json` is gitignored. Create it from `composer-ci.json`: replace the three VCS repositories with path
repositories (`{"type": "path", "url": "../vo-base", "options": {"symlink": true}}`, same for `../vo-base-doctrine` and
`../vo-finance`) and require `msstc4php/vo-base-doctrine` and `msstc4php/vo-base` as `dev-main as 1.3.0` and
`msstc4php/vo-finance` as `dev-main as 0.2.0`.

- `make check`, `make test`, `make infection`, `make fix`.
- The ODM integration tests need MongoDB (`MONGODB_URI`, default `mongodb://127.0.0.1:27017`) and skip themselves
  otherwise.

## Standard

Tool configuration is byte-identical to the `php84` templates of `msstc4symfony/bundle-standard`; CI's `templates` job
enforces it. Do not edit `.php-cs-fixer.dist.php`, `rector.php`, `phpstan*.neon`, `phpunit.xml.dist`,
`infection.json5`, `Makefile` or `.gitignore` here. The hex layout of that standard does not apply.

## Adding a class of `vo-finance`

Every non-abstract value object that is a `ValueObject` needs one ORM and one ODM type named `fin_<snake_name>` (snake
case of the class short name; column length = the class's maximum length; above 4000 characters, or with no bound in
the library, the text type), registered in both `TypeRegistrar`s. Then:

- add a sample to `tests/Support/Samples.php::strings()` (name, canonical sample, length, at-max flag) and list text
  types in `TEXT_TYPES`;
- `EveryFinanceClassHasATypeTest` fails until both branches map the class, and its count assertion (25) must be bumped;
- add the bound to `ColumnLengthsFollowTheLibraryBoundsTest` when the class has a constant;
- classes that are not `ValueObject` (`QrBillAddress`, `MerchantAccount`, `DataObject`, enums, `Internal\`) get no type;
- update the README table.
