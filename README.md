# Laravel Analytics Nova Tool

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sergeybruhin/laravel-analytics-nova-tool.svg)](https://packagist.org/packages/sergeybruhin/laravel-analytics-nova-tool)
[![PHP version](https://img.shields.io/packagist/dependency-v/sergeybruhin/laravel-analytics-nova-tool/php.svg)](https://packagist.org/packages/sergeybruhin/laravel-analytics-nova-tool)
[![License](https://img.shields.io/github/license/sergeybruhin/laravel-analytics-nova-tool.svg)](LICENSE.md)

The [Laravel Nova](https://nova.laravel.com) companion for
[`sergeybruhin/laravel-analytics`](https://github.com/sergeybruhin/laravel-analytics): Nova
resources for browsing visitors/sessions/pageviews/events, a couple of dashboard metrics, and a
filterable analytics report Tool — traffic trend, top pages, traffic sources and device breakdown,
all driven by one shared filter bar.

The data package stays free of any Nova dependency on purpose. Nova is a paid, credentialed
package, so putting Nova-facing code inside the core analytics package would make it uninstallable
for anyone without a licence and break its CI, which installs with no credentials at all. Hence two
packages:

```
sergeybruhin/laravel-analytics             visitors, sessions, pageviews, events (Postgres, "analytics" connection)
        ↑ required by
sergeybruhin/laravel-analytics-nova-tool   Resources, Metrics, Dashboard, filterable report Tool
```

## Requirements

| | |
|---|---|
| PHP | 8.2+ |
| Nova | 4 or 5 |
| `sergeybruhin/laravel-analytics` | ^0.0.1 |

## Installation

```bash
composer require sergeybruhin/laravel-analytics-nova-tool
```

Nova is not on Packagist, so this only works in an application that already has Nova's own
Composer repository configured — which every Nova application does. Until this package itself is
on Packagist, add a `vcs` repository first:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/sergeybruhin/laravel-analytics-nova-tool"
    }
]
```

then run the same `composer require` command above.

Nothing here is auto-discovered onto the Nova sidebar — Resources, the Dashboard and the Tool are
plain Nova classes you register yourself, same as every other resource in a typical Nova app:

```php
// app/Providers/NovaServiceProvider.php
use SergeyBruhin\NovaAnalytics\Dashboards\Analytics as AnalyticsDashboard;
use SergeyBruhin\NovaAnalytics\NovaAnalyticsTool;
use SergeyBruhin\NovaAnalytics\Resources\{Visitor, Session, Pageview, Event};

public function dashboards()
{
    return [
        (new AnalyticsDashboard)->canSee(fn ($request) => $request->user()?->isAdmin() ?? false),
    ];
}

public function tools()
{
    return [
        (new NovaAnalyticsTool)->canSee(fn ($request) => $request->user()?->isAdmin() ?? false),
    ];
}

// wherever your app registers Nova resources, e.g. Nova::resources([...]):
Nova::resources([Visitor::class, Session::class, Pageview::class, Event::class]);
```

## Resources

`Resources\{Visitor,Session,Pageview,Event}` — read-only by design (`authorizedToCreate/Update
/Delete/Replicate` all hard-return `false`; this data is written exclusively through the collect
endpoint's queue job in `laravel-analytics`). Drill-down works through real `BelongsTo`/`HasMany`
fields: `Visitor → Sessions/Pageviews/Events`, `Session → Visitor`, `Session → Pageviews/Events`,
`Pageview`/`Event → Session, Visitor`.

## Metrics & Dashboard

`Metrics\PageviewsTrend` (daily pageviews, bot sessions excluded) and `Metrics\TopTrafficSources`
(visitors by first-touch UTM source) are bundled into `Dashboards\Analytics`, a small
always-visible-range dashboard for a quick glance from the sidebar.

## The Tool

`NovaAnalyticsTool` registers its own sidebar page — a deeper report the Dashboard's fixed-range
metric cards can't express, since Nova gives each card its own independent range dropdown with no
way to share a filter across cards. One filter bar here drives four views at once:

- **Traffic trend** — daily pageviews + unique visitors, zero-filled so a quiet day still shows on
  the chart instead of leaving a gap.
- **Top pages** — most-viewed paths under the current filters.
- **Traffic sources** — sessions grouped by UTM source, medium and campaign (session-level, not
  first-touch, so it stays consistent with whatever date range is selected).
- **Device breakdown** — sessions grouped by device type, browser and OS.

Filter bar dimensions: date-range presets (today / yesterday / last 7 days / last 30 days / this
month / last month), UTM source/medium/campaign, device type/browser/OS, and a bot-traffic
include/exclude toggle (every view excludes bot sessions by default).

### HTTP surface

Everything the Tool's page talks to, under Nova's own domain and prefixed
`nova-vendor/laravel-analytics-nova-tool`. Every route sits behind Nova's authentication and the
package's own `Authorize` middleware, which defers to a `viewNovaAnalytics` gate — defined by the
host app, or allowed by default if the host hasn't defined it.

| Method | Path | Returns |
|---|---|---|
| `GET` | `/filter-options` | distinct UTM/device/browser/OS values for the current date range, plus the period preset list |
| `GET` | `/trend` | zero-filled daily pageviews + unique visitors |
| `GET` | `/top-pages` | top paths by views/unique visitors |
| `GET` | `/traffic-sources` | sessions grouped by UTM source/medium/campaign |
| `GET` | `/devices` | sessions grouped by device type/browser/OS |

All five accept the same query params: `period`, `utm_source`, `utm_medium`, `utm_campaign`,
`device_type`, `browser`, `os`, `include_bots` (`0`/`1`).

## Building the assets

`dist/` is committed, so installing the package needs no Node. To change the front end:

```bash
npm install
npm run prod
```

Standard Nova 4 `nova:tool` scaffold — Laravel Mix, Vue 3, Chart.js as a real bundled dependency
(Nova's own chart library, Chartist, isn't in the tool scaffold's webpack externals — only `vue`
is). Nova registers `Head`, `Heading`, `Card`, `SelectControl`, `CheckboxWithLabel` and friends
globally, so the Tool imports nothing from Nova's own JS.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for what changed in each release. This package follows
[semantic versioning](https://semver.org); while the version is below 1.0.0, routes, config keys
and response shapes may change in a minor release.

## Credits

- [Sergey Bruhin](https://github.com/sergeybruhin)
- [All contributors](https://github.com/sergeybruhin/laravel-analytics-nova-tool/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
