# King Ring Support Pages

WordPress plugin providing standalone layouts for:

- `/support/`
- `/contactus/`

## Automatic updates

The plugin checks the latest public GitHub Release in `w3009090/kingring-support-pages`.

For every update:

1. Increase the plugin header `Version` and `KINGRING_SUPPORT_PAGES_VERSION`.
2. Create a matching tag, for example `v2.1.0`.
3. Create a GitHub Release from that tag.
4. Attach the installable archive named exactly `kingring-support-pages.zip`.
5. Keep the repository public so WordPress can access the release without a token.

The ZIP must contain the `kingring-support-pages` directory at its root.
