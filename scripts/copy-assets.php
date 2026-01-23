<?php
/**
 * Copies files from packages to plugin directories.
 *
 * - dist/ => assets/ and lib/vendor/ (filtered by type, newer files only)
 *   Excludes dist/tolib/
 * - dist/tolib/ => lib/vendor/wpconstructor/packagename/ (all files)
 *
 * @package WPConstructor_Packages_Scripts_Copy
 */

if ( php_sapi_name() !== 'cli' ) {
	header( 'HTTP/1.1 403 Forbidden' );
	exit( 'This script can only be run from the command line.' );
}

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
function copy_files_newer_only(
	string $source,
	string $destination,
	?array $extensions = null,
	array $exclude_dirs = array(),
	$delete_src = false
): void {
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

					if ($delete_src){
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
$vendor_base = __DIR__ . '/../dist-vendor/' . $vendor_name;
$assets_base = __DIR__ . '/../assets/' . $vendor_name;

// Scan all packages.
$packages = glob( $vendor_base . '/*', GLOB_ONLYDIR );

foreach ( $packages as $package_path ) {
	$package_name = basename( $package_path );

	$src_path = $package_path . '/dist';

	copy_files_newer_only(
		$src_path,
		$assets_base . '/' . $package_name,
		array( 'js' ),
		array(),
		true
	);

	copy_files_newer_only(
		$src_path,
		$assets_base . '/' . $package_name,
		array( 'css' ),
		array(),
		true
	);

	copy_files_newer_only(
		$src_path,
		$assets_base . '/' . $package_name,
		array( 'png', 'jpg', 'jpeg', 'gif', 'svg' ),
		array(),
		true
	);

}

/**
 * Copy a file only if it doesn't exist at the destination
 * or if the source file is newer.
 *
 * @param string $file_path  Absolute path to the source file.
 * @param string $dest_path  Absolute path to the destination file.
 *
 * @return bool True if the file was copied, false if skipped.
 */
function copy_if_newer( $file_path, $dest_path ) {
	if ( ! file_exists( $file_path ) ) {
		return false; // Source does not exist.
	}

	// Ensure destination directory exists.
	$dest_dir = dirname( $dest_path );
	if ( ! is_dir( $dest_dir ) ) {
		// phpcs:ignore
		mkdir( $dest_dir, 0755, true );
	}

	// Copy if destination does not exist or source is newer.
	if ( ! file_exists( $dest_path ) || filemtime( $file_path ) > filemtime( $dest_path ) ) {
		copy( $file_path, $dest_path );
		return true;
	}

	return false; // Skipped because destination is up-to-date.
}


echo "All files copied successfully.\n";
