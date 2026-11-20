# Architecture

Two independent branches, no shared code (`deptrac.yaml`: `Orm` and `Odm` have no rules, so neither may see the other).

- `src/Orm/*Type.php`: `final` subclasses of `MSSTC4PHP\ValueObject\Doctrine\Orm\AbstractStringVoType` (value object
  class and column length). Above 4000 characters the text type is used, as in `vo-gs1-doctrine`.
- `src/Odm/*Type.php`: the same over `Odm\AbstractStringVoType`; no column length.
- `Orm\TypeRegistrar` / `Odm\TypeRegistrar`: `registerAll()` (idempotent) and `types()` (name => class); 25 types per
  branch, one per string value object of vo-finance. `Orm` types extend `AbstractStringVoType` with a
  column length, or `AbstractTextVoType` for the unbounded or over-4000 payloads (EMVCo-based QR payloads, GOST, SBP, UPI
  URI).
