# msstc4php/vo-finance-doctrine

Doctrine ORM (DBAL) and MongoDB ODM types for [`msstc4php/vo-finance`](https://github.com/msstc4php/vo-finance), built on
[`msstc4php/vo-base-doctrine`](https://github.com/msstc4php/vo-base-doctrine). PHP >= 8.4, no Symfony dependency.

25 types per branch, one per string value object of `vo-finance` (securities identifiers, the payment card number, the
payment references and the payment QR payloads). The value-typed parts of a payload (`QrBillAddress`, `MerchantAccount`,
`DataObject`) and the backed enums (`CardScheme`, `PixKeyType`, `QrBillReferenceType`, ...) need no type: map enums with
`enumType`.

## Install

Not on Packagist yet; register the GitHub repositories:

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/msstc4php/vo-base" },
        { "type": "vcs", "url": "https://github.com/msstc4php/vo-base-doctrine" },
        { "type": "vcs", "url": "https://github.com/msstc4php/vo-finance" },
        { "type": "vcs", "url": "https://github.com/msstc4php/vo-finance-doctrine" }
    ],
    "require": {
        "msstc4php/vo-finance-doctrine": "^0.1"
    }
}
```

Runtime requirements: `msstc4php/vo-finance`, `msstc4php/vo-base-doctrine ^1.2`; `doctrine/dbal` (ORM) or
`doctrine/mongodb-odm` (ODM) is yours.

## Usage

Bare Doctrine: call `Orm\TypeRegistrar::registerAll()` (or `Odm\TypeRegistrar::registerAll()`) once at bootstrap.
Symfony: map `TypeRegistrar::types()` under `doctrine.dbal.types` (ORM) or `doctrine_mongodb.odm.types` (ODM).

```php
#[ORM\Column(type: 'fin_qr_bill')]
private QrBill $qrBill;
```

## Type names and lengths

Every name is `fin_` plus the snake_case of the class short name (`PaymentCardNumber` is `fin_payment_card_number`,
`Gost56042Payload` is `fin_gost56042_payload`). The ORM column is `VARCHAR(n)`, `n` being the characters of the longest
value the class accepts. A type with no bound in the library, or a bound above 4000 characters (the VARCHAR limit of SQL
Server and Oracle), maps to vo-base-doctrine's `AbstractTextVoType`, a CLOB/TEXT column; a TEXT column cannot carry a
plain index on every platform (MySQL needs a prefix length), so index a derived key instead. The ODM field is a BSON
string without a length.

On write a type accepts only its own value-object class: an `Isin` given to `fin_mic` throws `InvalidArgumentException`.
A stored value that no longer parses throws the value object's own exception on read, not a DBAL `ConversionException`.

Values are stored as the value object holds them: card numbers without spaces, an EPC or Swiss QR-bill payload with its
line separators (LF or CRLF) untouched, full unmasked (`masked()` is for display).

**Collation.** Identifiers and payloads are case-sensitive in part (Pix keys, QR-bill text, payment links). Under a
case-insensitive collation two values that differ only in case collide in a unique index; give such columns a binary
collation (MySQL `utf8mb4_bin`).

| Type name | Value object | Column length | Length derived from |
|---|---|---|---|
| `fin_isin` | `Security\Isin` | 12 | ISO 6166, fixed 12 characters |
| `fin_upi` | `Security\Upi` | 12 | `QZ` + 10 characters |
| `fin_figi` | `Security\Figi` | 12 | fixed 12 characters |
| `fin_cusip` | `Security\Cusip` | 9 | fixed 9 characters |
| `fin_fisn` | `Security\Fisn` | 35 | `Fisn::MAX_LENGTH` = 35 |
| `fin_mic` | `Security\Mic` | 4 | fixed 4 characters |
| `fin_sedol` | `Security\Sedol` | 7 | fixed 7 characters |
| `fin_wkn` | `Security\Wkn` | 6 | fixed 6 characters |
| `fin_valor` | `Security\Valor` | 9 | 1 to 9 characters |
| `fin_cfi` | `Security\Cfi` | 6 | fixed 6 letters |
| `fin_lei` | `Security\Lei` | 20 | fixed 20 characters |
| `fin_payment_card_number` | `Card\PaymentCardNumber` | 19 | 10 to 19 digits, spaces and hyphens stripped |
| `fin_qr_reference` | `Reference\QrReference` | 27 | fixed 27 digits, stored compact |
| `fin_creditor_reference` | `Reference\CreditorReference` | 25 | `RF` + 2 + up to 21, stored compact |
| `fin_epc_qr` | `PaymentQr\Epc\EpcQr` | 331 | `EpcQr::MAX_LENGTH` = 331 |
| `fin_jpqr_code` | `PaymentQr\Japan\JpqrCode` | 512 | `JpqrCode::MAX_BYTES` = 512 (bytes, so at most 512 characters) |
| `fin_qr_bill` | `PaymentQr\Swiss\QrBill` | 997 | SIX IG 2.3 section 6.2, 997 characters including separators; the longest payload Table 8 allows is 902 characters |
| `fin_merchant_presented_qr` | `PaymentQr\Emvco\MerchantPresentedQr` | CLOB (bound 7089) | `EmvcoTlv::MAX_PAYLOAD_LENGTH` |
| `fin_pix_br_code` | `PaymentQr\Brazil\PixBrCode` | CLOB (bound 7089) | `EmvcoTlv::MAX_PAYLOAD_LENGTH` |
| `fin_prompt_pay_qr` | `PaymentQr\Thailand\PromptPayQr` | CLOB (bound 7089) | `EmvcoTlv::MAX_PAYLOAD_LENGTH` |
| `fin_duit_now_qr` | `PaymentQr\Malaysia\DuitNowQr` | CLOB (bound 7089) | `EmvcoTlv::MAX_PAYLOAD_LENGTH` |
| `fin_khqr_code` | `PaymentQr\Cambodia\KhqrCode` | CLOB (bound 7089) | `EmvcoTlv::MAX_PAYLOAD_LENGTH` |
| `fin_gost56042_payload` | `PaymentQr\Ru\Gost56042Payload` | CLOB (no bound) | the library sets no length bound |
| `fin_sbp_payment_link` | `PaymentQr\Ru\SbpPaymentLink` | CLOB (no bound) | the library sets no length bound |
| `fin_upi_payment_uri` | `PaymentQr\India\UpiPaymentUri` | CLOB (no bound) | the library sets no length bound |

## License

MIT, see [`LICENSE`](LICENSE).
