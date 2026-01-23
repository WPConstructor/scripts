<?php
/**
 * Delete the dist-vendor folder recursively
 *
 * @package WPConstructor_Scripts
 */

$dir = __DIR__ . '/../dist-vendor';

/**
 * Recursively delete a directory
 *
 * @param string $dir Directory path.
 */
function delete_dir( $dir ) {
	if ( ! is_dir( $dir ) ) {
		return;
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

// Delete the folder.
delete_dir( $dir );

echo "dist-vendor folder deleted successfully.\n";

/**
 * Copy vendor folder to dist-vendor for production, skipping unnecessary files.
 */

$source      = __DIR__ . '/../vendor';        // original vendor folder.
$destination = __DIR__ . '/../dist-vendor'; // target folder.

/**
 * Recursively copy a directory while skipping unwanted files/folders.
 *
 * @param string $src Source path.
 * @param string $dst Destination path.
 */
function copy_vendor_for_production( $src, $dst ) {
	$skip_dirs     = array( 'tests', 'test', 'docs', 'examples', '.git', '.github', 'scripts', '.vscode' );
	$include_ext   = array( 'php', 'json' );
	$include_files = array( 'readme.md', 'license' );

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
			$lower_item = strtolower( $item );
			if ( in_array( $lower_item, $include_files, true ) ) {
				$do_copy = true;
			}

			// Only copy set extensions.
			$ext = pathinfo( $item, PATHINFO_EXTENSION );
			if ( in_array( $ext, $include_ext, true ) ) {
				$do_copy = true;
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

echo "vendor copied to dist-vendor successfully, production-ready.\n";
