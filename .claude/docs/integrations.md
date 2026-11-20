# Integrations

- `msstc4php/vo-base-doctrine ^1.2` (abstract types) and `msstc4php/vo-finance` (`^0.1`; `dev-main as 0.1.0` in
  `composer-ci.json` until vo-finance is tagged). Both reached through their GitHub repositories (no Packagist yet).
- Types: one per string value object of vo-finance (table in the README). Non-`ValueObject` classes of vo-finance
  (`QrBillAddress`, `MerchantAccount`, `DataObject`, `Internal\\IinRange`, enums) are not mapped.
- Column length decisions: constants where the library has them (`Fisn::MAX_LENGTH`, `EpcQr::MAX_LENGTH`,
  `JpqrCode::MAX_BYTES`, `EmvcoTlv::MAX_PAYLOAD_LENGTH`); `QrBill` 997 is SIX's published Swiss QR Code maximum (not
  quoted in vo-finance's FINANCE.md, the widest payload Table 8 allows is 902); `Gost56042Payload`,
  `SbpPaymentLink` and `UpiPaymentUri` have no bound in the library, so they are text, never refusing a valid value.
