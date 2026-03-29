<?php
/**
 * WPConstructor Backups Packages Script.
 *
 * This script serves to quickly make a backup of the packages.
 * Backups to the parent of WP root "packages-backups" directory.
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

$plugin_root = get_plugin_root();
$plugin_dir  = dirname( $plugin_root );
$wp_root_dir = get_wp_root( $plugin_root );

/**
 * Recursively copy a directory to another directory.
 *
 * @param string $src  Source directory.
 * @param string $dst  Destination directory.
 * @return void
 * @throws Exception When somthing went wrong.
 */
function backup_directory( string $src, string $dst ): void {
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

		$src_path = $src . DIRECTORY_SEPARATOR . $file;
		$dst_path = $dst . DIRECTORY_SEPARATOR . $file;

		if ( is_dir( $src_path ) ) {
			backup_directory( $src_path, $dst_path );
		} elseif ( ! copy( $src_path, $dst_path ) ) {
				// phpcs:ignore
				throw new Exception( "Failed to copy file: $src_path to $dst_path" );
		}
	}

	closedir( $dir );
}

$source_dir = $plugin_dir . '/packages/'; // Replace with your source folder.

// Generate timestamped destination folder.
$timestamp       = gmdate( 'Y-m-d-H-i-s' ); // YEAR-MONTH-DAY-HOUR-MINUTE-SECOND.
$destination_dir = $wp_root_dir . '/../packages-backups/' . $timestamp;

try {
	backup_directory( $source_dir, $destination_dir );
	// phpcs:ignore
	echo "Backup completed successfully to: $destination_dir\n";
} catch ( Exception $e ) {
	// phpcs:ignore
	echo 'Backup failed: ' . $e->getMessage() . "\n";
}
