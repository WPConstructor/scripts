<?php
/**
 * Recursively add index.php files to dist-vendor and assets directories and subdirectories.
 *
 * This is useful for WordPress plugin/theme directories to prevent directory
 * browsing.
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
$plugin_root = get_plugin_root( false );

/**
 * Recursively add index.php files to a directory and its subdirectories.
 *
 * @param string $directory Base directory.
 * @param string $index_content Optional content for index.php.
 *
 * @return int The amount of added index.php
 */
function add_index_files( string $directory, string $index_content = "<?php\n// phpcs:ignoreFile\n// Silence is golden.\n" ) {
	if ( ! is_dir( $directory ) ) {
		return 0;
	}

	$counter = 0;

	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $directory, RecursiveDirectoryIterator::SKIP_DOTS ),
		RecursiveIteratorIterator::SELF_FIRST
	);

	foreach ( $iterator as $item ) {
		if ( $item->isDir() ) {
			$index_file = $item->getPathname() . '/index.php';
			if ( ! file_exists( $index_file ) ) {
				++$counter;
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
		++$counter;
        // phpcs:ignore
		file_put_contents( $base_index, $index_content );
        // phpcs:ignore
		echo "Added index.php to base directory: {$directory}\n";
	}

	return $counter;
}

$target_directory = $plugin_root . '/dist-vendor';
$amount           = add_index_files( $target_directory );
// phpcs:ignore;
echo "Added $amount index.php files to dist-vendor directory.\n";

$target_directory = $plugin_root . '/assets';
$amount           = add_index_files( $target_directory );

// phpcs:ignore;
echo "Added $amount index.php files to assets directory.\n";
