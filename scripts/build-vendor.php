<?php
/**
 * Builds the dist-vendor directory, used for the zip build vendor directory.
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
require_once __DIR__ . '/helper.php';

check_if_cli();
$plugin_root     = get_plugin_root( false );
$dist_vendor_dir = $plugin_root . '/dist-vendor';

/**
 * Recursively delete a directory
 *
 * @param string $dir Directory path.
 */
function delete_dir( $dir ) {
	if ( ! is_dir( $dir ) ) {
		return false;
	}

	$items = scandir( $dir );
	foreach ( $items as $item ) {
		if ( '.' === $item || '..' === $item ) {
			continue;
		}

		$path = $dir . DIRECTORY_SEPARATOR . $item;
		if ( is_dir( $path ) ) {
			delete_dir( $path );
		} else {
            // phpcs:ignore
			unlink( $path );
		}
	}

    // phpcs:ignore
	rmdir( $dir );
}

if ( is_dir( $dist_vendor_dir ) ) {

	// Delete the folder.
	delete_dir( $dist_vendor_dir );

	echo "dist-vendor folder deleted successfully.\n";

}

/**
 * Copy vendor folder to dist-vendor for production, skipping unnecessary files.
 */

$source      = $plugin_root . '/vendor';        // original vendor folder.
$destination = $dist_vendor_dir;                // target folder.

/**
 * Recursively copy a directory while skipping unwanted files/folders.
 *
 * @param string $src Source path.
 * @param string $dst Destination path.
 */
function copy_vendor_for_production( $src, $dst ) {
	$skip_dirs     = array( 'tests', 'test', 'docs', 'examples', '.git', '.github', 'scripts', '.vscode' );
	$include_ext   = array( 'php', 'json', 'js', 'css', 'png', 'jpg', 'jpeg', 'gif', 'svg' );
	$include_files = array();
	$exclude_files = array( 'composer.json', 'composer.lock' );

	if ( ! is_dir( $dst ) ) {
        // phpcs:ignore
		mkdir( $dst, 0755, true );
	}

	$items = scandir( $src );
	foreach ( $items as $item ) {
		if ( '.' === $item || '..' === $item ) {
			continue;
		}

		$src_path = $src . DIRECTORY_SEPARATOR . $item;
		$dst_path = $dst . DIRECTORY_SEPARATOR . $item;

		if ( is_dir( $src_path ) ) {
			// Skip unwanted directories.
			if ( in_array( strtolower( $item ), $skip_dirs, true ) ) {
				continue;
			}
			copy_vendor_for_production( $src_path, $dst_path );
		} else {
			$do_copy = false;

			// Check if wanted file.
			if ( in_array( $item, $include_files, true ) ) {
				$do_copy = true;
			}

			// Only copy set extensions.
			$ext = pathinfo( $item, PATHINFO_EXTENSION );
			if ( in_array( $ext, $include_ext, true ) ) {
				$do_copy = true;
			}

			// Check if exclude file.
			if ( in_array( $item, $exclude_files, true ) ) {
				continue;
			}

			if ( ! $do_copy ) {
				continue;
			}

			copy( $src_path, $dst_path );
		}
	}
}

// Run the copy.
copy_vendor_for_production( $source, $destination );

echo "Vendor copied to dist-vendor successfully, production-ready.\n";
