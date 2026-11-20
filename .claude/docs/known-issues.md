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
- `fin_payment_card_number` holds the clear PAN (owner's decision, final review CR-005): the README and both type
  docblocks carry the PCI DSS 4.0 requirement 3.5.1 warning; encryption at rest is the application's job.
- Column lengths count characters (CR-012): Oracle `VARCHAR2` and SQL Server `VARCHAR` without a UTF-8 collation count
  bytes, so accented FISN/EPC/QR-bill values can overflow; the README asks for character-length semantics.
- Since vo-finance's final-review fix, GOST, SBP and UPI payloads are bounded at 7089 characters like the EMVCo classes;
  their columns stay CLOB (7089 is above the 4000 limit).
