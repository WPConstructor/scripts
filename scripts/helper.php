<?php
/**
 * Helper functions for the WPConstructor Scripts.
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
 * Checks if running in CLI and exits if not.
 *
 * @return void
 */
function check_if_cli() {
	if ( php_sapi_name() !== 'cli' ) {
		header( 'HTTP/1.1 403 Forbidden' );
		exit( 'This script can only be run from the command line.' );
	}
}

/**
 * Gets plugin root.
 *
 * @param bool $add_slash True (default) to add slash.
 *
 * @return string Absolute path to the plugin root.
 */
function get_plugin_root( $add_slash = true ) {
	$current_dir = getcwd();
	$plugin_dir  = dirname( $current_dir );

	if ( 'plugins' !== basename( $plugin_dir ) ) {
		exit( 'Wrong current dir. Please be sure your current dir is in the plugin root!' . "\n" );
	}
	if ( $add_slash ) {
		$current_dir .= '/';
	}
	return $current_dir;
}

/**
 * Get the WordPress root directory (directory containing wp-load.php)
 *
 * @param string $start_dir The start dir.
 *
 * @return string The absolute path to the WordPress root
 */
function get_wp_root( $start_dir = null ) {
	if ( null === $start_dir ) {
		$start_dir = getcwd();
	} else {
		// Start from the in $start_dir set directory.
		$dir = $start_dir;
	}

	// Limit the search to avoid infinite loops (optional, e.g., 10 levels).
	$max_levels = 10;
	$level      = 0;

	while ( $level < $max_levels ) {
		if ( file_exists( $dir . '/wp-load.php' ) ) {
			return $dir;
		}

		$parent = dirname( $dir );
		if ( $parent === $dir ) {
			// Reached the root filesystem.
			break;
		}

		$dir = $parent;
		++$level;
	}

	// Exit if wp-load.php was not found.
	exit( 'Error: WordPress root directory not found.' );
}
