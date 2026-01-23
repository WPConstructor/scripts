<?php
// phpcs:ignoreFile
/**
 * Recursively copy a directory to another directory.
 *
 * @param string $src  Source directory
 * @param string $dst  Destination directory
 * @return void
 * @throws Exception
 */
function backup_directory( string $src, string $dst ): void {
	if ( ! is_dir( $src ) ) {
		throw new Exception( "Source directory does not exist: $src" );
	}

	if ( ! is_dir( $dst ) ) {
		if ( ! mkdir( $dst, 0755, true ) ) {
			throw new Exception( "Failed to create destination directory: $dst" );
		}
	}

	$dir = opendir( $src );
	if ( ! $dir ) {
		throw new Exception( "Failed to open source directory: $src" );
	}

	while ( ( $file = readdir( $dir ) ) !== false ) {
		if ( $file === '.' || $file === '..' ) {
			continue;
		}

		$srcPath = $src . DIRECTORY_SEPARATOR . $file;
		$dstPath = $dst . DIRECTORY_SEPARATOR . $file;

		if ( is_dir( $srcPath ) ) {
			backup_directory( $srcPath, $dstPath );
		} elseif ( ! copy( $srcPath, $dstPath ) ) {
				throw new Exception( "Failed to copy file: $srcPath to $dstPath" );
		}
	}

	closedir( $dir );
}

$sourceDir = __DIR__ . '/../../packages/'; // Replace with your source folder

// Generate timestamped destination folder
$timestamp      = date( 'Y-m-d-H-i-s' ); // YEAR-MONTH-DAY-HOUR-MINUTE-SECOND
$destinationDir = __DIR__ . '/../../packages-backups/' . $timestamp;

try {
	backup_directory( $sourceDir, $destinationDir );
	echo "Backup completed successfully to: $destinationDir\n";
} catch ( Exception $e ) {
	echo 'Backup failed: ' . $e->getMessage() . "\n";
}
