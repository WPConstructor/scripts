<?php
/**
 * Delets empty dirs in dist-vendor directory.
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
 * Recursively delete all empty directories in a given folder
 *
 * @param string $dir Directory path.
 * @param int    $counter The amount of deleted empty directories.
 *
 * @return int The amount of deleted (empty) directories.
 */
function delete_empty_dirs( $dir, $counter = 0 ) {
	if ( ! is_dir( $dir ) ) {
		return $counter;
	}

	$items = scandir( $dir );
	foreach ( $items as $item ) {
		if ( '.' === $item || '..' === $item ) {
			continue;
		}

		$path = $dir . DIRECTORY_SEPARATOR . $item;

		if ( is_dir( $path ) ) {
			// Recurse into subdirectory.
			$counter = delete_empty_dirs( $path, $counter );

			// Delete if now empty.
			if ( count( scandir( $path ) ) === 2 ) { // only . and ..
                // phpcs:disable
				rmdir( $path );
				++$counter;
				// phpcs:enable
			}
		}
	}
	return $counter;
}

$folder = $plugin_root . '/dist-vendor';
$amount = delete_empty_dirs( $folder );
// phpcs:ignore
echo "Deleted $amount empty directories.\n";