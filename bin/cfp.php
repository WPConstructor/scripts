<?php
/**
 * WPConstructor CFP (Check File Permissions) Script.
 *
 * Checks and reports file and directory permissions for a WordPress installation or
 * plugin to ensure correct security and functionality. Part of the WPConstructor Scripts
 * collection, helping developers identify and fix permission issues quickly.
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

$wp_root_dir = get_wp_root();

/**
 * Checks writable recursively.
 *
 * @param string $dir The root directory to check.
 * @param int    $counter The start counter of checked files.
 */
function check_writable( $dir, &$counter = 0 ) {
	$items = scandir( $dir );
	foreach ( $items as $item ) {
		if ( '-' === $item || '..' === $item ) {
			continue;
		}

		$path = $dir . '/' . $item;

		// Check directory.
		if ( is_dir( $path ) ) {
			// phpcs:ignore
			if ( ! is_writable( $path ) ) {
				// phpcs:ignore
				echo "Directory $path is NOT writable\n";
			}
			++$counter;
			// Recurse into subdirectory.
			check_writable( $path, $counter );

			// Check file.
			// phpcs:ignore
		} elseif ( ! is_writable( $path ) ) {
			// phpcs:ignore
			echo "File $path is NOT writable\n";
			++$counter;
		} else {
			++$counter;
		}
	}
	return $counter;
}

$total = check_writable( $wp_root_dir );
// phpcs:ignore
echo "Checked $total files/directories.\n";
