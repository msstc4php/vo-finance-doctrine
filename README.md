# msstc4php/vo-finance-doctrine

Doctrine ORM (DBAL) and MongoDB ODM types for [`msstc4php/vo-finance`](https://github.com/msstc4php/vo-finance), built on
[`msstc4php/vo-base-doctrine`](https://github.com/msstc4php/vo-base-doctrine). PHP >= 8.4, no Symfony dependency.

**Status: stable, 1.x.** From 1.0.0 on a breaking change needs 2.0; see [Backward compatibility](#backward-compatibility).

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
        "msstc4php/vo-finance-doctrine": "^1.0"
    }
}
```

Runtime requirements: `msstc4php/vo-finance ^1.0`, `msstc4php/vo-base-doctrine ^1.3`; `doctrine/dbal` (ORM) or
`doctrine/mongodb-odm` (ODM) is yours.

## Usage

Bare Doctrine: call `Orm\TypeRegistrar::registerAll()` (or `Odm\TypeRegistrar::registerAll()`) once at bootstrap.
Symfony: map `TypeRegistrar::types()` under `doctrine.dbal.types` (ORM) or the top-level `doctrine_mongodb.types` (ODM).

```php
#[ORM\Column(type: 'fin_qr_bill')]
private QrBill $qrBill;
```

## Type names and lengths

Every name is `fin_` plus the snake_case of the class short name (`PaymentCardNumber` is `fin_payment_card_number`,
`Gost56042Payload` is `fin_gost56042_payload`). The ORM column is `VARCHAR(n)`, `n` being the class's public
`MAX_LENGTH`, the stored-value bound vo-finance freezes for 1.x (characters). A type with no bound in the library, or a bound above 4000 characters (Oracle's `VARCHAR2`
holds 4000 bytes, SQL Server's `NVARCHAR` 4000 characters), maps to vo-base-doctrine's `AbstractTextVoType`, a
CLOB/TEXT column; a TEXT column cannot carry a plain index on every platform (MySQL needs a prefix length), so index a
derived key instead. The ODM field is a BSON string without a length.

**Characters, not bytes.** `n` counts characters, and an FISN, an EPC payload or a QR-bill payload may hold accented
letters, which take two bytes in UTF-8. Oracle sizes `VARCHAR2(n)` in bytes by default and SQL Server `VARCHAR(n)`
without a UTF-8 collation too, so such a value can overflow the column (ORA-12899). Use character-length semantics
(Oracle `NLS_LENGTH_SEMANTICS=CHAR`, SQL Server `NVARCHAR` or a UTF-8 collation) or a text type there.

On write a type accepts only its own value-object class: an `Isin` given to `fin_mic` throws `InvalidArgumentException`.
A stored value that no longer parses throws the value object's own exception on read, not a DBAL `ConversionException`.

Values are stored as the value object holds them: card numbers without spaces, an EPC or Swiss QR-bill payload with its
line separators (LF or CRLF) untouched, full unmasked (`masked()` is for display).

> **PCI DSS.** `fin_payment_card_number` stores the full PAN in clear text. PCI DSS 4.0 requirement 3.5.1 requires a
> stored PAN to be unreadable (truncation, tokenisation, an index token or strong cryptography). This type does none
> of that: encryption at rest and the PCI scope of the database are the application's job. Prefer storing a token, or
> only `masked()` / `last4()` in a plain column, and use this type only inside a PCI-scoped, encrypted store.

**Collation.** Identifiers and payloads are case-sensitive in part (Pix keys, QR-bill text, payment links). Under a
case-insensitive collation two values that differ only in case collide in a unique index; give such columns a binary
collation (MySQL `utf8mb4_bin`).

| Type name | Value object | Column length | Length derived from |
|---|---|---|---|
| `fin_isin` | `Security\Isin` | 12 | `Isin::MAX_LENGTH`, fixed 12 characters |
| `fin_upi` | `Security\Upi` | 12 | `Upi::MAX_LENGTH`, `QZ` + 10 characters |
| `fin_figi` | `Security\Figi` | 12 | `Figi::MAX_LENGTH`, fixed 12 characters |
| `fin_cusip` | `Security\Cusip` | 9 | `Cusip::MAX_LENGTH`, fixed 9 characters |
| `fin_fisn` | `Security\Fisn` | 35 | `Fisn::MAX_LENGTH` = 35 |
| `fin_mic` | `Security\Mic` | 4 | `Mic::MAX_LENGTH`, fixed 4 characters |
| `fin_sedol` | `Security\Sedol` | 7 | `Sedol::MAX_LENGTH`, fixed 7 characters |
| `fin_wkn` | `Security\Wkn` | 6 | `Wkn::MAX_LENGTH`, fixed 6 characters |
| `fin_valor` | `Security\Valor` | 12 | `Valor::MAX_LENGTH` = 12: 1 to 9 characters accepted today, headroom above the inferred bound |
| `fin_cfi` | `Security\Cfi` | 6 | `Cfi::MAX_LENGTH`, fixed 6 letters |
| `fin_lei` | `Security\Lei` | 20 | `Lei::MAX_LENGTH`, fixed 20 characters |
| `fin_payment_card_number` | `Card\PaymentCardNumber` | 19 | `PaymentCardNumber::MAX_LENGTH`, 10 to 19 digits, spaces and hyphens stripped |
| `fin_qr_reference` | `Reference\QrReference` | 27 | `QrReference::MAX_LENGTH`, fixed 27 digits, stored compact |
| `fin_creditor_reference` | `Reference\CreditorReference` | 25 | `CreditorReference::MAX_LENGTH`, `RF` + 2 + up to 21, stored compact |
| `fin_epc_qr` | `PaymentQr\Epc\EpcQr` | 331 | `EpcQr::MAX_LENGTH` = 331 |
| `fin_jpqr_code` | `PaymentQr\Japan\JpqrCode` | 512 | `JpqrCode::MAX_LENGTH` = `MAX_BYTES` = 512 (bytes, so at most 512 characters) |
| `fin_qr_bill` | `PaymentQr\Swiss\QrBill` | 997 | `QrBill::MAX_LENGTH`, SIX IG 2.3 section 6.2, 997 characters including separators; the longest payload Table 8 allows is 902 characters |
| `fin_merchant_presented_qr` | `PaymentQr\Emvco\MerchantPresentedQr` | CLOB (bound 7089) | `MAX_LENGTH` = 7089 |
| `fin_pix_br_code` | `PaymentQr\Brazil\PixBrCode` | CLOB (bound 7089) | `MAX_LENGTH` = 7089 |
| `fin_prompt_pay_qr` | `PaymentQr\Thailand\PromptPayQr` | CLOB (bound 7089) | `MAX_LENGTH` = 7089 |
| `fin_duit_now_qr` | `PaymentQr\Malaysia\DuitNowQr` | CLOB (bound 7089) | `MAX_LENGTH` = 7089 |
| `fin_khqr_code` | `PaymentQr\Cambodia\KhqrCode` | CLOB (bound 7089) | `MAX_LENGTH` = 7089 |
| `fin_gost56042_payload` | `PaymentQr\Ru\Gost56042Payload` | CLOB (bound 7089) | `MAX_LENGTH` = 7089, the shared abuse bound |
| `fin_sbp_payment_link` | `PaymentQr\Ru\SbpPaymentLink` | CLOB (bound 7089) | `MAX_LENGTH` = 7089, the shared abuse bound |
| `fin_upi_payment_uri` | `PaymentQr\India\UpiPaymentUri` | CLOB (bound 7089) | `MAX_LENGTH` = 7089, the shared abuse bound |

Store and compare a payment payload by its full string: the column holds it as the value object does. Values that
reach the same account through different payloads are different rows; index a key you derive yourself (an IBAN, a Pix
key) when you need that. A column for "any payment QR" (one type resolving through vo-finance's `PaymentQrFactory`)
does not exist yet; it can come later as an additional type.

## Backward compatibility

The type names (`fin_*`) and the ORM column lengths are frozen for 1.x: a minor release never changes the SQL
declaration, so a schema diff never comes from an upgrade within 1.x. The lengths are vo-finance's `MAX_LENGTH`
constants, which vo-finance freezes too; a type for a new vo-finance class comes in a minor release.

vo-finance never refuses in a minor a value an earlier 1.x release accepted, so a stored value keeps hydrating across
1.x upgrades; a check that gets a source later is reported by a `hasValid…()` method there. Rows written by 0.x keep
loading too: vo-finance 1.0 accepts every value 0.2.0 accepted. Only a row written outside the types that the value
object refuses throws the value object's own exception on read.

Upgrading from 0.x: `fin_valor` is `VARCHAR(12)` (was 9), so a schema diff widens that column once; every other
declaration is unchanged. From vo-base-doctrine's deprecated `vo_credit_card_number`: map the column as
`fin_payment_card_number` (`VARCHAR(19)`); the stored strings are the same. No data check is needed before either
step (see the CHANGELOG).

## License

MIT, see [`LICENSE`](LICENSE).
