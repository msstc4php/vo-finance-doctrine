# Known issues

- Until `vo-finance` is tagged `v0.1.0`, `composer.json` (`^0.1`) does not resolve; only the `composer-ci.json` install
  (`dev-main as 0.1.0`) works, so a job that installs from `composer.json` fails.
- With zero types, Infection's `Foreach_` mutant of each `registerAll()` is equivalent and escapes; it goes away with
  the first types.
