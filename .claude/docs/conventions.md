# Conventions

- Type names `fin_<snake_name>` (`fin_isin`, `fin_payment_card_number`, `fin_emvco_merchant_qr`); `TypeRegistrarTest`
  checks the pattern.
- `registerAll()` iterates `types()`, not the constant: PHPStan reports a foreach over a literally empty constant
  (`foreach.emptyArray`) while the registrars are empty.
- ODM types cannot be built directly (see `vo-base-doctrine`): tests go through `Type::getType($name)` after
  `registerAll()`.
