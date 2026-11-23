# Known issues

## CI state, 2026-11-23 (UTC)

- Infection covered MSI is 100%. The jobs that install from `composer.json` (`msstc4php/vo-finance ^1.0`) resolve only
  once vo-finance is tagged `v1.0.0`; tag vo-finance first.
- The ODM round trips need MongoDB and skip locally without it; only CI runs them.
- `ColumnLengthsFollowTheLibraryBoundsTest` reads the public `MAX_LENGTH` of each sample's class (no reflection on
  private or `@internal` constants since 1.0); every ORM type must have a row in `Samples::strings()`.
- `fin_payment_card_number` holds the clear PAN (owner's decision, final review CR-005): the README and both type
  docblocks carry the PCI DSS 4.0 requirement 3.5.1 warning; encryption at rest is the application's job.
- Column lengths count characters (CR-012): Oracle `VARCHAR2` and SQL Server `VARCHAR` without a UTF-8 collation count
  bytes, so accented FISN/EPC/QR-bill values can overflow; the README asks for character-length semantics.
- GOST, SBP and UPI payloads are bounded at 7089 characters like the EMVCo classes; their columns stay CLOB (7089 is
  above the 4000 limit).
- `Samples::strings()` can yield the same label key twice only by mistake; build lists from it with `foreach`, not
  `iterator_to_array()` plus `array_column()` (that silently dropped rows once).
