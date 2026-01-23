<?php
/**
 * Delets empty dirs in dist-vendor.
 *
 * @package WPConstructor_Scripts
 */

/**
 * Recursively delete all empty directories in a given folder
 *
 * @param string $dir Directory path.
 */
function delete_empty_dirs( $dir ) {
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
			// Recurse into subdirectory.
			delete_empty_dirs( $path );

			// Delete if now empty.
			if ( count( scandir( $path ) ) === 2 ) { // only . and ..
                // phpcs:disable
				rmdir( $path );
				echo "Deleted empty directory: $path\n";
                // phpcs:enable
			}
		}
	}
}

$folder = __DIR__ . '/../dist-vendor';
delete_empty_dirs( $folder );
