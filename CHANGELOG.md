# Changelog

## [Unreleased]

### Added

- Package scaffold, CI and tool configuration from `vo-gs1-doctrine`.
- ORM and ODM types for every string value object of `vo-finance` (25 per branch): securities identifiers, the payment
  card number, payment references and payment QR payloads. Names are `fin_<snake_name>`; the ORM column length is the
  longest value the class accepts, and a type with no bound or a bound above 4000 characters is a CLOB/TEXT column.
  `Orm\TypeRegistrar` and `Odm\TypeRegistrar` register them.
- `fin_payment_card_number` stores the full PAN; the README and the type docblocks warn that PCI DSS 4.0 requirement
  3.5.1 encryption at rest is the application's job. The README says that column lengths count characters (Oracle and
  SQL Server need character-length semantics) and that the GOST, SBP and UPI CLOB columns carry the 7089-character
  bound of vo-finance.
- Contract tests: every non-abstract value object of the package has exactly one type per branch, and the column lengths
  follow the library's bounds.
