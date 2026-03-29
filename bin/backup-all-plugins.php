<?php
/**
 * WPConstructor Backups All Plugins Script.
 *
 * This script serves to quickly make a backup of all plugins.
 * Backups to the parent of WP root all-plugins-backups directory.
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

$only_webconstr = false;

// Parse command-line arguments.
foreach ( $argv as $arg ) {
	if ( strpos( $arg, '--only-wpconstr' ) === 0 ) {
		$only_webconstr = true;
		echo "Only backing up wpconstructor plugins.\n";
		break;
	}
}

$exclude = array( '.git', 'node_modules' );

// Generate timestamped destination folder.
$timestamp = gmdate( 'Y-m-d-H-i-s' ); // YEAR-MONTH-DAY-HOUR-MINUTE-SECOND.

$wp_root_dir = get_wp_root( $plugin_dir );

$plugins_source_dir = $plugin_dir;
$destination_dir    = $wp_root_dir . '/../all-plugins-backups/all-plugins-' . $timestamp;

$mu_plugins_source_dir = $plugin_dir . '/../mu-plugins';

/**
 * Recursively copy a directory to another directory.
 *
 * @param string $src  Source directory.
 * @param string $dst  Destination directory.
 * @param bool   $only_webconstr True to only copy plugins starting with wepconstructor-.
 * @return void
 * @throws Exception If error.
 */
function backup_directory( $src, $dst, $only_webconstr = false ) {

	global $exclude;

	if ( ! is_dir( $src ) ) {
		// phpcs:ignore
		throw new Exception( "Source directory does not exist: $src" );
	}

	if ( ! is_dir( $dst ) ) {
		// phpcs:ignore
		if ( ! mkdir( $dst, 0755, true ) ) {
		// phpcs:ignore
			throw new Exception( "Failed to create destination directory: $dst" );
		}
	}

	$dir = opendir( $src );
	if ( ! $dir ) {
		// phpcs:ignore
		throw new Exception( "Failed to open source directory: $src" );
	}

	// phpcs:ignore
	while ( ( $file = readdir( $dir ) ) !== false ) {
		if ( '.' === $file || '..' === $file ) {
			continue;
		}
		foreach ( $exclude as $ex ) {
			if ( $ex === $file ) {
				continue 2;
			}
		}
		$src_path = $src . DIRECTORY_SEPARATOR . $file;
		$dst_path = $dst . DIRECTORY_SEPARATOR . $file;

		if ( is_dir( $src_path ) ) {
			if ( $only_webconstr ) {
				$d = str_replace( '\\', '/', $src_path );
				if ( strpos( $d, 'plugins/wpconstructor-' ) !== false ) {
					backup_directory( $src_path, $dst_path, $only_webconstr );
				}
			} else {
				backup_directory( $src_path, $dst_path, $only_webconstr );
			}
		} elseif ( ! copy( $src_path, $dst_path ) ) {
			// phpcs:ignore
			throw new Exception( "Failed to copy file: $src_path to $dst_path" );
		}
	}

	closedir( $dir );
}

try {
	backup_directory( $plugins_source_dir, $destination_dir . '/plugins', $only_webconstr );
	// phpcs:ignore
	echo "Backup all plugins completed successfully to: $destination_dir\n";
} catch ( Exception $e ) {
	// phpcs:ignore
	echo 'Backup all plugins failed: ' . $e->getMessage() . "\n";
}

if ( file_exists( $mu_plugins_source_dir ) ) {
	try {
		backup_directory( $mu_plugins_source_dir, $destination_dir . '/mu-plugins' );
		// phpcs:ignore
		echo "Backup all mu-plugins completed successfully to: $destination_dir/mu-plugins\n";
	} catch ( Exception $e ) {
		// phpcs:ignore
		echo 'Backup all mu-plugins failed: ' . $e->getMessage() . "\n";
	}
} else {
	echo "No mu-plugin directory found!\n";
}

/**
 * ZIP the backuped directory.
 */


$root = $destination_dir . '/';

$zip_file = $destination_dir . '.zip';

// Make it Windows compatible.
$zip_file = str_replace( '\\', '/', $zip_file );

// Initialize ZIP.
$zip = new ZipArchive();
if ( $zip->open( $zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE ) !== true ) {
    // phpcs:ignore
	die( "Cannot create ZIP file at $zip_file\n" );
}

// Recursive iterator for all files.
$iterator = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $root, RecursiveDirectoryIterator::SKIP_DOTS ),
	RecursiveIteratorIterator::SELF_FIRST
);

foreach ( $iterator as $file ) {
	$file_path     = $file->getPathname();
	$relative_path = str_replace( str_replace( '\\', '/', $root ), '', str_replace( '\\', '/', $file_path ) );

	// Add files to ZIP with top-level folder.
	if ( $file->isDir() ) {
		$zip->addEmptyDir( "$relative_path" );
	} else {
		$zip->addFile( $file_path, "$relative_path" );
	}
}

$zip->close();
