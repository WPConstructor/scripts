<?php
/**
 * Runs all build scripts.
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
get_plugin_root( false );

// Build scripts to run.
$scripts = array(
	'php vendor/wpconstructor/scripts/scripts/build-vendor.php',
	'php vendor/wpconstructor/scripts/bin/copy-assets.php',
	'php vendor/wpconstructor/scripts/scripts/delete-empty-dirs.php',
	'php vendor/wpconstructor/scripts/scripts/add-index-php.php',
	'npx wpconstr-minify assets',
	'php vendor/wpconstructor/scripts/bin/build-zip.php',
);

foreach ( $scripts as $command ) {
    // phpcs:ignore
	echo "Running: {$command}\n";

	$output = array();
	$return = 0;

    // phpcs:ignore
	exec( $command, $output, $return );

	if ( 0 !== $return ) {
		// phpcs:ignore
		echo implode( "\n", $output ) . "\n";
        // phpcs:ignore
		exit( "❌ Script failed: {$command}\n" );
	}

	// phpcs:ignore
	echo implode( "\n", $output ) . "\n";
    // phpcs:ignore
	echo "✔ Finished: {$command}\n\n";
}

echo "🎉 All build scripts completed successfully.\n";
