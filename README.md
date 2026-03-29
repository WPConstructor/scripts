# WPConstructor Scripts

A collection of **Composer-driven PHP scripts** for building, backing up, and distributing WordPress plugins consistently and safely.  

---

## Why I Built WPConstructor Scripts

While working on multiple WordPress plugins, I noticed a pattern: every project ended up needing the same Composer scripts. Copying them around, maintaining small differences, and fixing bugs in multiple places quickly became annoying—and error-prone.  

I wanted one reliable, reusable setup that handled the boring but critical stuff every plugin needs: copying assets, backing things up, building distributable ZIPs, and keeping WordPress secure.  

That’s how **WPConstructor Scripts** was born.  

---

## The Problem: Repeating the Same Work

When building plugins with Composer, you often end up reinventing the wheel:

- Copying assets into the right directories  
- Preparing vendor files for distribution  
- Creating plugin ZIPs with only the required files  
- Making backups before builds or updates  
- Ensuring no directories are exposed without an `index.php`  

Instead of duplicating effort, I extracted everything into a single reusable scripts package.  

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