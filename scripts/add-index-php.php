<?php
/**
 * Recursively add index.php files to dist-vendor and assets directories and subdirectories.
 *
 * This is useful for WordPress plugin/theme directories to prevent directory
 * browsing.
 *
 * @package WPConstructor\Utils
 */

if ( php_sapi_name() !== 'cli' ) {
	header( 'HTTP/1.1 403 Forbidden' );
	exit( 'This script can only be run from the command line.' );
}

/**
 * Recursively add index.php files to a directory and its subdirectories.
 *
 * @param string $directory Base directory.
 * @param string $index_content Optional content for index.php.
 */
function add_index_files( string $directory, string $index_content = "<?php\n// phpcs:ignoreFile\n// Silence is golden.\n" ): void {
	if ( ! is_dir( $directory ) ) {
		return;
	}

	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $directory, RecursiveDirectoryIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::SELF_FIRST
	);

	foreach ( $iterator as $item ) {
		if ( $item->isDir() ) {
			$index_file = $item->getPathname() . '/index.php';
			if ( ! file_exists( $index_file ) ) {
                // phpcs:ignore
				file_put_contents( $index_file, $index_content );
                // phpcs:ignore
				echo "Added index.php to: {$item->getPathname()}\n";
			}
		}
	}

	// Also add index.php to the base directory if missing.
	$base_index = rtrim( $directory, '/' ) . '/index.php';
	if ( ! file_exists( $base_index ) ) {
        // phpcs:ignore
		file_put_contents( $base_index, $index_content );
        // phpcs:ignore
		echo "Added index.php to base directory: {$directory}\n";
	}
}

$target_directory = __DIR__ . '/../dist-vendor';
add_index_files( $target_directory );

$target_directory = __DIR__ . '/../assets';
add_index_files( $target_directory );

echo "All index.php files added successfully.\n";
