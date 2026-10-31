# Codex Plugin - King Ring Support Pages

Current version: **2.5.2**.

[Полные правила разработки, проверки и публикации плагина](PLUGIN_RULES.md)

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
