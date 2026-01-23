<?php
/**
 * WPConstructor Backups Plugin Script.
 *
 * This script serves to quickly make a backup of the plugin development.
 * Backups to wp-content directory plugin-backups. Ignores .git.
 *
 * @package WPConstructor_Error_Manager_Development_Scripts
 */

$plugin_slug = basename( dirname( __DIR__ ) );
$exclude     = array( '.git', 'node_modules' );

$source_dir = __DIR__ . '/../';

// Generate timestamped destination folder.
$timestamp       = gmdate( 'Y-m-d-H-i-s' ); // YEAR-MONTH-DAY-HOUR-MINUTE-SECOND.
$destination_dir = __DIR__ . '/../../../plugin-backups/' . $plugin_slug . '-' . $timestamp;

/**
 * Recursively copy a directory to another directory.
 *
 * @param string $src  Source directory.
 * @param string $dst  Destination directory.
 * @return void
 * @throws Exception If error.
 */
function backup_directory( string $src, string $dst ): void {

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
			backup_directory( $src_path, $dst_path );
		} elseif ( ! copy( $src_path, $dst_path ) ) {
			// phpcs:ignore
			throw new Exception( "Failed to copy file: $src_path to $dst_path" );
		}
	}

	closedir( $dir );
}

try {
	backup_directory( $source_dir, $destination_dir );
	// phpcs:ignore
	echo "Plugin backup completed successfully to: $destination_dir\n";
} catch ( Exception $e ) {
	// phpcs:ignore
	echo 'Plugin backup failed: ' . $e->getMessage() . "\n";
}
