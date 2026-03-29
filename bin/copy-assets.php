<?php
/**
 * Copies assets files from vendor to plugin assets directories
 *
 * E.g. assets/wpconstructor/dashboard/images/logo.png.
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

$vendor_name = 'wpconstructor';

/**
 * Recursively copy files by extension or all files if $extensions is null,
 * excluding optional subdirectory.
 *
 * @param string        $source Source directory.
 * @param string        $destination Destination directory.
 * @param string[]|null $extensions Array of extensions to copy. Null = all files.
 * @param string[]      $exclude_dirs Relative paths to exclude.
 * @param bool          $delete_src Deletes source if true.
 */
function copy_files_newer_only( $source, $destination, $extensions = null, $exclude_dirs = array(), $delete_src = false ) {
	if ( ! is_dir( $source ) ) {
		return;
	}

	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $source, RecursiveDirectoryIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::SELF_FIRST
	);

	foreach ( $iterator as $file ) {
		if ( $file->isFile() ) {
			// Skip files in excluded directories.
			$relative_dir = str_replace( $source . '/', '', $file->getPath() );
			foreach ( $exclude_dirs as $exclude ) {
				if ( strpos( $relative_dir, $exclude ) === 0 ) {
					continue 2;
				}
			}

			$ext = strtolower( pathinfo( $file->getFilename(), PATHINFO_EXTENSION ) );

			if ( is_null( $extensions ) || in_array( $ext, $extensions, true ) ) {
				$relative_path = substr( $file->getPathname(), strlen( $source ) + 1 );
				$dest_path     = rtrim( $destination, '/' ) . '/' . $relative_path;

				// Ensure destination directory exists.
				$dest_dir = dirname( $dest_path );
				if ( ! is_dir( $dest_dir ) ) {
					// phpcs:ignore
					mkdir( $dest_dir, 0755, true );
				}

				// Copy only if missing or newer.
				if ( ! file_exists( $dest_path ) || filemtime( $file->getPathname() ) > filemtime( $dest_path ) ) {
					// phpcs:ignore
					copy( $file->getPathname(), $dest_path );
					// phpcs:ignore
					echo "Copied: {$file->getPathname()} -> $dest_path\n";

					if ( $delete_src ) {
						// phpcs:ignore
						unlink( $file->getPathname() );
						// phpcs:ignore
						echo "Deleted: {$file->getPathname()}\n";

					}
				} else {
					// phpcs:ignore
					echo "Skipped (up-to-date): {$file->getPathname()}\n";
				}
			}
		}
	}
}

/**
 * Base paths
 */
$dist_vendor_base = $plugin_root . '/dist-vendor/' . $vendor_name;
$assets_base      = $plugin_root . '/assets/' . $vendor_name;

// Scan all packages.
$packages = glob( $dist_vendor_base . '/*', GLOB_ONLYDIR );

foreach ( $packages as $package_path ) {
	$package_name = basename( $package_path );

	$src_path = $package_path . '/dist';

	// Copy js files from dist/.
	copy_files_newer_only(
		$src_path,
		$assets_base . '/' . $package_name,
		array( 'js' ),
		array(),
		true
	);

	// Copy css files from dist/.
	copy_files_newer_only(
		$src_path,
		$assets_base . '/' . $package_name,
		array( 'css' ),
		array(),
		true
	);

	// Copy image files from dist/.
	copy_files_newer_only(
		$src_path,
		$assets_base . '/' . $package_name,
		array( 'png', 'jpg', 'jpeg', 'gif', 'svg' ),
		array(),
		true
	);

}

echo "All files copied successfully.\n";
