<?php
require( dirname( __FILE__ ) . '/_cli_init.php' );

// Try to load WordPress !
try {
	if ( !defined( 'ABSPATH' ) )
		require_once (dirname( __FILE__ ) . '/../../../../wp-load.php');
} catch ( ErrorException $e ) {
	var_dump( $e->getMessage() );
	// Debug
	die( 'Configuration seems incorrect because WordPress is trying to do an HTTP redirect or display anything !' );
}

// List separator
$separator = '#--__--#';
$i = $j = $k = 0;
$lines = file(  dirname( __FILE__ ) . '/../data/file-replacements.txt' );
foreach ($lines as $line_num => $line) {
	$i++;
	$line_data = explode($separator, $line);
	var_dump($line_data);
	$line_data = array_map('trim', $line_data);
	
	if ( is_file($line_data[0]) ) {
		$j++;
		echo "rename( $line_data[0], $line_data[1] );";
		
		if ( rename( $line_data[0], $line_data[1] ) == true ) {
			$k++;
			unset($lines['$line_num']);
		}
	}
}
file_put_contents( dirname( __FILE__ ) . '/../data/file-replacements.txt', implode( PHP_EOL, $lines ) );

die(sprintf('Process results : %d lines, %d existing source files and %d renaming success operation !',$i, $j, $k));