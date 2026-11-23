# Integrations

- `msstc4php/vo-base-doctrine ^1.3` (abstract types) and `msstc4php/vo-finance ^1.0` (`dev-main as 1.0.0` in
  `composer-ci.json`). Both reached through their GitHub repositories. No direct vo-base requirement: the bridge uses
  no vo-base `Payload` class.
- Types: one per string value object of vo-finance (table in the README). Non-`ValueObject` classes of vo-finance
  (`QrBillAddress`, `MerchantAccount`, `DataObject`, `Internal\\IinRange`, enums) are not mapped.
- Column lengths: every ORM type returns `<ValueObject>::MAX_LENGTH` (public, frozen for 1.x in vo-finance); above
  4000 the text type. `QrBill::MAX_LENGTH` 997 is SIX's Swiss QR Code maximum (IG 2.3 section 6.2; Table 8's widest
  payload is 902); `Valor::MAX_LENGTH` 12 is headroom above the inferred nine; the EMVCo, GOST, SBP and UPI payloads
  carry 7089 and map to CLOB.
