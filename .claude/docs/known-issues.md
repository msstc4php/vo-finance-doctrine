# Known issues

- Until `vo-finance` is tagged `v0.1.0`, `composer.json` (`^0.1`) does not resolve; only the `composer-ci.json` install
  (`dev-main as 0.1.0`) works, so a job that installs from `composer.json` fails.
- With zero types, Infection's `Foreach_` mutant of each `registerAll()` is equivalent and escapes; it goes away with
  the first types.

## Controller ruling, 2026-11-20 (Task 2 review round 1)

- The red CI jobs are accepted until Task 8 or the `vo-finance` v0.1.0 tag: Infection (covered MSI 66.67% with empty
  registrars), "PHPUnit without optional libraries", "ORM branch loads without doctrine/mongodb-odm" and "ODM branch loads
  without doctrine/orm and doctrine/dbal" (these install from `composer.json`, whose `msstc4php/vo-finance ^0.1` has no
  tag to resolve).
- `TypeRegistrarTest` asserts nothing while `TYPES` is empty (the loops run zero times); the tests become meaningful at
  Task 8, when the first types are registered.
