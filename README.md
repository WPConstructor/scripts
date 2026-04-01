# WPConstructor Scripts

<p align="center">
  <img src="https://wpconstructor.com/assets/images/wpconstructor-scripts-logo.png?v=2" alt="WPConstructor Scripts Logo" width="350">
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
    "build:update": "php vendor/wpconstructor/scripts/scripts/build-vendor.php && php vendor/wpconstructor/scripts bin/copy-assets.php && php vendor/wpconstructor/scripts/scripts/add-index-php.php && npx @wpconstructor minify-assets assets",
    "build": "php vendor/wpconstructor/scripts/bin/run-build.php",
    "cfp": "php vendor/wpconstructor/scripts/bin/cfp.php",
    "clean": "php vendor/wpconstructor/scripts/bin/clean.php",
    "backup:packages": "php vendor/wpconstructor/scripts/bin/backup-packages.php",
    "backup:plugin": "php vendor/wpconstructor/scripts/bin/backup-plugin.php",
    "backup:all-plugins": "php vendor/wpconstructor/scripts/bin/backup-all-plugins.php"
}
```

---

## Usage Examples

### Asset & Security Helpers

- **Build Update**
```bash
composer run build:update
```
Updates plugin assets to the correct directories, creating nested folders like:  
`assets/wpconstructor/dashboard/images`.  

- **Add Index Files (included in build)**  
Automatically adds `index.php` to all asset directories to prevent directory listing.

---

### Build & Distribution

- **Build Plugin**
```bash
composer run build
```
Runs the full build process:
1. Copies vendor files to `dist-vendor`  
2. Removes empty directories  
3. Adds `index.php` files  
4. Creates a distributable ZIP in `dist/plugin-name-version-date.zip`  

- **Clean Project**
```bash
composer run clean
```
Removes empty directories and cleans build artifacts.

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

Optional:
```bash
composer run backup:all-plugins -- --only-wpconstr
```

- **Backup Packages Directory**
```bash
composer run backup:packages
```
Backs up the entire `packages` directory to `wordpress-root/../packages-backup`.

---

## One Workflow, Everywhere

With WPConstructor Scripts, every plugin build looks the same:

1. Assets copied  
2. Vendors trimmed  
3. Empty directories removed  
4. Index files added  
5. ZIP created  
6. Backups secured  

No guessing. No half-forgotten steps. No copy-pasting scripts between repos.  

---

## License

MIT License © 2026 by WPConstructor