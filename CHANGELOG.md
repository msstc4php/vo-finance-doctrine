# Changelog

## [1.0.0] - unreleased

First stable release: from here on a breaking change needs 2.0.

**Backward compatibility (README, "Backward compatibility").** Type names (`fin_*`) and ORM column lengths are frozen
for 1.x; the lengths are vo-finance's `MAX_LENGTH` constants, frozen there too. vo-finance never refuses in a minor a
value an earlier 1.x release accepted, so stored rows keep hydrating across 1.x upgrades.

### Changed

- Requires `msstc4php/vo-finance ^1.0` (was `^0.2`) and `msstc4php/vo-base-doctrine ^1.3` (was `^1.2`).
- Every ORM string type takes its column length from the value object's public `MAX_LENGTH` instead of a literal; the
  contract test no longer reads private or `@internal` constants through reflection.
- `fin_valor` is `VARCHAR(12)` (was `VARCHAR(9)`): `Valor::MAX_LENGTH` is 12, room above the nine characters accepted.

### Upgrading from 0.x

- Run a schema diff once: only `fin_valor` widens from 9 to 12 characters. No type was renamed or removed, and no
  stored string changed (see vo-finance's 1.0.0 entry for its API changes).
- From vo-base-doctrine's deprecated `vo_credit_card_number`: map the column as `fin_payment_card_number` (also
  `VARCHAR(19)`) and the property as `PaymentCardNumber`; the stored strings are the same.
- No data check is needed before the migration. Widening `fin_valor` from 9 to 12 never truncates. A
  `vo_credit_card_number` row holds the same canonical digits `fin_payment_card_number` stores, and `PaymentCardNumber`
  accepts a superset of `CreditCardNumber` (10 to 19 digits against 12 to 19, Luhn in both), so every row hydrates.
  vo-finance 1.0 accepts every value 0.2.0 accepted, so 0.x rows of the other types load unchanged. On Oracle and SQL
  Server, check the character-length semantics of `fin_fisn`, `fin_epc_qr` and `fin_qr_bill` (README, "Characters, not
  bytes").

## [0.2.0] - 2026-11-23

### Changed

- Requires `msstc4php/vo-finance ^0.2` (was `^0.1`): vo-finance 0.2.0 relies on vo-base `^1.3` to validate payment payloads again on `unserialize()`. No change in this package's code.

## [0.1.0] - 2026-11-22

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
