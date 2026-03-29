<?php
/**
 * Build ZIP package for a WordPress plugin.
 *
 * This script collects only the required plugin files, prepares the
 * distribution directory, and creates a versioned ZIP archive ready
 * for release. It is intended to be executed from the command line
 * and is usually triggered as part of the Composer build process.
 *
 * @package    WPConstructor\Scripts
 * @copyright  2026 by WPConstructor
 * @author     WPConstructor <https://wpconstructor.com/contact>
 * @license    MIT (https://opensource.org/licenses/MIT)
 * @link       https://wpconstructor.com/codes/wpconstructor-scripts
 * @version    1.0.0 
 * @since      1.0.0 
 */

/**
 * Requires the helper.php file.
 */
require_once __DIR__ . '/../scripts/helper.php';

check_if_cli();

$plugin_root = get_plugin_root( false );
$plugin_dir  = dirname( $plugin_root );

/**
 * Extract the plugin version from a plugin main file without WordPress.
 *
 * @param string $file_path Absolute path to the plugin's main PHP file.
 * @return string|null The version string or null if not found.
 */
function extract_plugin_version( $file_path ) {
	if ( ! file_exists( $file_path ) ) {
		return null;
	}

	//phpcs:ignore
	$contents = file_get_contents( $file_path );

	if ( false === $contents ) {
		return null;
	}

	// Limit to the first 8KB like WordPress does.
	$header = substr( $contents, 0, 8192 );

	// Regex for: Version: X.Y.Z.
	$pattern = '/^\s*\*?\s*Version:\s*(.+)$/mi';

	if ( preg_match( $pattern, $header, $matches ) ) {
		$version = trim( $matches[1] );

		return '' !== $version ? $version : null;
	}

	return null;
}

$plugin_slug = basename( $plugin_root );

$plugin_version = extract_plugin_version( $plugin_root . '/' . $plugin_slug . '.php' );
if ( null === $plugin_version ) {
    // phpcs:ignore
    die( "Could not extract plugin version.\n" );
}

$root     = $plugin_root . '/';
$dist_dir = $root . 'dist';

$zip_file = $dist_dir . '/' . $plugin_slug . '-' . $plugin_version . '_' . gmdate( 'Y-m-d-H-i-s' ) . '.zip';

// Make it Windows compatible.
$zip_file = str_replace( '\\', '/', $zip_file );

// Ensure dist folder exists.
if ( ! is_dir( $dist_dir ) ) {
    // phpcs:ignore
	mkdir( $dist_dir, 0755, true );
}

// phpcs:ignore
if ( ! is_writable( $dist_dir ) ) {
	die( "Dist directory is not writable!\n" );
}

// Initialize ZIP.
$zip = new ZipArchive();
if ( $zip->open( $zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE ) !== true ) {
    // phpcs:ignore
	die( "Cannot create ZIP file at $zip_file\n" );
}

// Files/directories to exclude.
$exclude = array(
	'vendor',
	'node_modules',
	'scripts',
	'tests',
	'dist',
	'.git',
	'.github',
	'composer.json',
	'composer.lock',
	'package.json',
	'package-lock.json',
	'phpunit.xml',
	'.gitignore',
	'.gitattributes',
	'README.md',
	'LICENSE.md',
	'phpunit.xml',
	'phpunit-10.xml',
);

// Recursive iterator for all files.
$iterator = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $root, RecursiveDirectoryIterator::SKIP_DOTS ),
	RecursiveIteratorIterator::SELF_FIRST
);

foreach ( $iterator as $file ) {
	$file_path     = $file->getPathname();
	$relative_path = str_replace( str_replace( '\\', '/', $root ), '', str_replace( '\\', '/', $file_path ) );

	// Skip excluded files/directories.
	foreach ( $exclude as $skip ) {
		if ( strpos( $relative_path, '/' ) === false ) {
			$skip = str_replace( '//', '', $skip );
		}
		if ( $relative_path === $skip || strpos( $relative_path, $skip . '/' ) === 0 ) {
			continue 2;
		}
	}

	if ( strpos( $relative_path, 'dist-vendor' ) === 0 ) {
		$relative_path = 'vendor' . substr( $relative_path, strlen( 'dist-vendor' ) );
	}

	// Add files to ZIP with top-level folder.
	if ( $file->isDir() ) {
		$zip->addEmptyDir( "$plugin_slug/$relative_path" );
	} else {
		$zip->addFile( $file_path, "$plugin_slug/$relative_path" );
	}
}

$zip->close();

// phpcs:ignore
echo "✔ Plugin ZIP created successfully: $zip_file\n";
