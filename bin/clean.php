<?php
/**
 * Script to clear specific directories while keeping certain assets intact.
 *
 * This script deletes all subdirectories in the 'assets' folder except for
 * 'assets/wpconstructor/{current-plugin}' and dist-vendor.
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

/**
 * Recursively delete a directory and all its contents
 *
 * @param string $dir Directory path to delete.
 */
function delete_directory( $dir ) {
	if ( ! is_dir( $dir ) ) {
		return;
	}

	$items = scandir( $dir );
	foreach ( $items as $item ) {
		if ( '.' === $item || '..' === $item ) {
			continue;
		}

		$path = $dir . '/' . $item;

		if ( is_dir( $path ) ) {
			delete_directory( $path ); // Recursively delete subdirectory.
		} else {
            // phpcs:ignore
			unlink( $path ); // Delete file.
		}
	}
    // phpcs:ignore
	rmdir( $dir ); // Remove the now-empty directory.
}

// Base plugin directory.
$plugin_dir = $plugin_root;

$plugin_slug_short = str_replace( 'wpconstructor-', '', basename( $plugin_root ) );

// Keep this folder intact.
$keep_folder = $plugin_dir . '/assets/wpconstructor/' . $plugin_slug_short;

// Delete all other subfolders in assets.
$assets = glob( $plugin_dir . '/assets/wpconstructor/*', GLOB_ONLYDIR );
foreach ( $assets as $asset_dir ) {
	if ( realpath( $asset_dir ) !== realpath( $keep_folder ) ) {
		delete_directory( $asset_dir );
        // phpcs:ignore
		echo "Deleted assets directory: $asset_dir\n";
	}
}

// Delete everything in dis-vendor.
$dist_vendor = $plugin_dir . '/dist-vendor';
if ( is_dir( $dist_vendor ) ) {
	delete_directory( $dist_vendor );
    // phpcs:ignore
	echo "Deleted dist-vendor directory: $dist_vendor\n";
}

// phpcs:ignore
echo "Cleanup complete. Plugin assets intact: $keep_folder\n";
