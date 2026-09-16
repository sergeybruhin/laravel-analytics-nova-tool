# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

While the version is below 1.0.0, routes, config keys and response shapes may change in a minor
release.

## [0.1.2] - 2026-09-16

### Fixed

- Replaced the `sergeybruhin/laravel-analytics` caret constraint with the range `>=0.0.2 <0.1`.
  Composer reads `^0.0.2` as `>=0.0.2 <0.0.3`, so every patch release of that package made this
  one uninstallable alongside it — 0.1.1 existed for no other reason than to widen the same
  constraint by one patch. The range ends that cycle: anything in the 0.0.x line resolves, and a
  0.1.0 release of the dependency (where the API may actually change) still needs a look.

## [0.1.1] - 2026-09-06

### Fixed

- Widened the `sergeybruhin/laravel-analytics` constraint from `^0.0.1` to `^0.0.2` — Composer's
  caret rule excluded `0.0.2` from the former, but the `Visitor`/`Session` `HasMany` relations
  these resources depend on only exist starting at that version.

## [0.1.0] - 2026-09-06

Initial release.

### Added

- Read-only Nova resources for `Visitor`, `Session`, `Pageview` and `Event`, with `BelongsTo`/
  `HasMany` drill-down between them.
- `PageviewsTrend` and `TopTrafficSources` metrics, bundled into an `Analytics` dashboard.
- A Nova Tool with its own sidebar page: a shared filter bar (date-range presets, UTM
  source/medium/campaign, device type/browser/OS, bot include/exclude) driving four views —
  traffic trend (zero-filled daily pageviews + unique visitors), top pages, traffic sources
  (session-level UTM breakdown, kept date-range-consistent rather than using first-touch
  attribution) and device breakdown.
- Five JSON endpoints under `nova-vendor/laravel-analytics-nova-tool`, gated by a
  `viewNovaAnalytics` gate that defaults to allowed when the host hasn't defined it.
- Vue 3 + Chart.js front end, built with the standard Nova 4 `nova:tool` scaffold (Laravel Mix);
  `dist/` is committed so installing the package needs no Node.

[0.1.1]: https://github.com/sergeybruhin/laravel-analytics-nova-tool/compare/v0.1.0...v0.1.1
[0.1.0]: https://github.com/sergeybruhin/laravel-analytics-nova-tool/releases/tag/v0.1.0
