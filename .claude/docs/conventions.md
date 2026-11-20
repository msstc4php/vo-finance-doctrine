# Conventions

- Type names `fin_<snake_name>` from the class short name (`fin_isin`, `fin_payment_card_number`,
  `fin_merchant_presented_qr`, `fin_gost56042_payload`); `TypeRegistrarTest` checks the pattern. No two classes of
  vo-finance share a short name, so no regional prefix is needed (`Security\Upi` is `fin_upi`, `UpiPaymentUri` is
  `fin_upi_payment_uri`).
- Samples live in `tests/Support/Samples.php`; `strings()` rows are `[name, sample, length, bad stored value, atMax]`,
  length 0 meaning a text type without a bound. `longestQrBill()` is every Table 8 element at its maximum (902
  characters, CRLF); it proves the 997 column, which is SIX IG 2.3 section 6.2.
- Integration entities are `tests/Support/{Orm,Odm}/Payment.php` (card, Swiss QR-bill with CRLF, EMVCo payload).
- ODM types cannot be built directly (see `vo-base-doctrine`): tests go through `Type::getType($name)` after
  `registerAll()`.
