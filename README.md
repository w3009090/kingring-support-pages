# Codex Plugin - King Ring Support Pages

Current version: **2.6.0**.

[Полные правила разработки, проверки и публикации плагина](PLUGIN_RULES.md)

[Правила работы со страницами WordPress](PAGE_RULES.md)

[Download the installable WordPress ZIP](downloads/CodexPlugin.zip)

Upload this ZIP under **Plugins > Add New Plugin > Upload Plugin** and replace
the installed plugin when prompted. Edit page content in **King Ring Support**
in the WordPress admin menu. Settings are grouped into **Features** (checkboxes)
and **Content** (page text, links and images) tabs. Both tabs share one Save Changes
button; switching tabs preserves unsaved edits.

This version includes editable Support and Contact pages, a subject selector,
an optional order number, file attachments, privacy agreement, ticket numbers,
automatic acknowledgement emails, and logo/main-image selection. FAQ is removed.
Existing repository changes to the plugin name and automatic updates are retained.
It also includes built-in switches for disabling WordPress blog and comment
features, replacing the separate Disable Blog and Disable Comments plugins.
The switches are enabled by default under **Plugins → Codex Plugin → Settings**
and **King Ring Support → Plugin Features**. After version 2.5.1 is active, the separate plugins can be
deactivated and deleted. If WordPress is configured to show latest posts on
the homepage, the built-in blog switch sends that request to `/support/`, so
no static homepage setting or warning is required.

WordPress plugin providing standalone layouts for:

- `/support/`
- `/contactus/`
- Missing URLs (HTTP 404), with the same header, footer and responsive layout.

Version 2.5.3 replaces the active theme's legacy error page with a branded
"Page not found" screen and links to Support and Contact. Missing URLs remain
HTTP 404; no pages are created or redirected and existing page settings are preserved.

Version 2.5.4 adds a safe integration mode for the unified `kingring-store`
WooCommerce theme. Support and Contact keep their routes, editable content and
form behavior, but use the store's shared header, footer, palette and responsive
system. The store theme keeps ownership of its homepage and 404 page.

Version 2.5.5 completes visual acceptance of the integration: it keeps the
Privacy consent visible, replaces the old marketplace icon and unrelated hero
with store-owned ring imagery, removes the empty embedded-map gap, and uses the
consistent 1-business-day reply promise.

Version 2.5.6 uses a real WooCommerce product image for the Shop card instead
of shrinking a landscape lifestyle banner into the square card media area.

Version 2.5.7 restores the original King Ring Support composition inside the
unified store shell: full-width support hero, centered introduction, one
segmented three-action panel and the closing brand quote. Store Header, Footer,
account and catalog routes remain shared with WooCommerce.

Version 2.5.8 replaces the Amazon mark in the internal Shop card with a real
WooCommerce ring image, so the restored Support layout no longer suggests that
the unified-store link opens a different marketplace.

Version 2.5.9 restores the two original Amazon support actions requested for
the unified store: `Rate Your Order` opens Amazon order history and `Visit the
Store` opens the King Ring Amazon storefront with its Amazon icon.

Version 2.6.0 completes the Contact transfer into the unified store. It restores
the centered source-page introduction, unboxed form/details layout, original
field rhythm and the map section that the earlier store integration hid.

## GitHub auto-updates

The plugin checks the latest public GitHub Release in:

`w3009090/kingring-support-pages`

For every update:

1. Increase the plugin `Version` and `KINGRING_SUPPORT_PAGES_VERSION`.
2. Create a matching Git tag, for example `v2.1.0`.
3. Create a GitHub Release from that tag.
4. Attach the installable plugin archive named exactly:

   `CodexPlugin.zip`

The ZIP must contain the `kingring-support-pages` directory at its root.
WordPress will then display the release through its standard plugin updater.
