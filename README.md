# WPConstructor Scripts

<p align="center">
  <img src="https://wpconstructor.com/assets/images/logos/wpconstructor-scripts.png?v=2" alt="WPConstructor Scripts Logo" width="350">
</p>

A collection of **Composer-driven PHP scripts** for building, backing up, and distributing WordPress plugins consistently and safely.  

---

## Why I Built WPConstructor Scripts

While building WordPress plugins, I kept repeating the same Composer scripts—copying assets, creating ZIPs, backing up files, and securing directories. It became repetitive and error-prone.

So I built WPConstructor Scripts: a reusable setup that handles all the common plugin build tasks in one place, instead of rewriting them every time.

---

## Installation

Install WPConstructor Scripts via Composer as a dev dependency:

```bash
composer require wpconstructor/scripts --dev
```

Then, add the following to your plugin’s `composer.json` under the `"scripts"` section:

```json
"scripts": {
    "build": "php vendor/wpconstructor/scripts/bin/run-build.php",
    "cfp": "php vendor/wpconstructor/scripts/bin/cfp.php",
    "backup:plugin": "php vendor/wpconstructor/scripts/bin/backup-plugin.php",
    "backup: all-plugins": "php vendor/wpconstructor/scripts/bin/backup-all-plugins.php"
}
```

Create a `build-zip.manifest.json` file in the root of your plugin. The `base` value should match your plugin's slug (the root directory inside the ZIP). Use the `include` array to specify which files and directories should be packaged, and `add-index-php` to automatically create `index.php` files in the listed directories for additional directory protection.

Example:
```json
{
    "base" : "wpcn-contact",
    "zip-file-name" : "wpcn-contact.zip",
    "include":[
        "/wpcn-contact.php",
        "/README.md",
        "/LICENSE.md",
        "/src/",
        "/docs/",
        "/assets/",
        "/vendor/wpconstructor/plugin-version/src/includes/plugin-version.php",
        "/vendor/autoload.php",
        "/vendor/composer/"
    ],
    "add-index-php":[
        "/"
    ]
}
```

---

## Usage Examples

### Build & Distribution

- **Build Plugin**
```bash
composer run build
```
Creates a distributable ZIP in `build/plugin-slug.zip`, as defined in `build-zip.manifest.json`.

---

### Maintenance Utilities

- **Check File Permissions**
```bash
composer run cfp
```
Scans your WordPress installation for files without write permissions.

---

### Backup Scripts

- **Backup Current Plugin**
```bash
composer run backup:plugin
```
Creates a backup of the current plugin in `wordpress-root/../plugin-backups`.  

- **Backup All Plugins**
```bash
composer run backup:all-plugins
```
Backs up all plugins to `wordpress-root/../all-plugins-backup`.  

---

## License

MIT License © 2026 by WPConstructor