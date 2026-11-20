# Known issues

- Until `vo-finance` is tagged `v0.1.0`, `composer.json` (`^0.1`) does not resolve; only the `composer-ci.json` install
  (`dev-main as 0.1.0`) works, so a job that installs from `composer.json` fails.

## CI state, 2026-11-20 (UTC)

- Infection covered MSI is 100%. The jobs that install from `composer.json` stay red until `vo-finance` is tagged
  `v0.1.0`: "PHPUnit without optional libraries", "ORM branch loads without doctrine/mongodb-odm" and "ODM branch loads
  without doctrine/orm and doctrine/dbal" (`msstc4php/vo-finance ^0.1` has no tag to resolve).
- The ODM round trips need MongoDB and skip locally without it; only CI runs them.
- `ReflectionClassConstant` reads the private bounds (`Fisn::MAX_LENGTH`, `EpcQr::MAX_LENGTH`, `JpqrCode::MAX_BYTES`) in
  `ColumnLengthsFollowTheLibraryBoundsTest`; renaming one of them fails that test, which is the point.
