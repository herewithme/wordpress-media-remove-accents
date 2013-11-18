<?php
// PHP Configuration
error_reporting( E_ALL ^ E_NOTICE );
@ini_set( 'display_startup_errors', '1' );
@ini_set( 'display_errors', '1' );
@ini_set( 'memory_limit', '512M' );
@ini_set( 'max_execution_time', -1 );
if ( function_exists( 'ignore_user_abort' ) )
	ignore_user_abort( 1 );
if ( function_exists( 'set_time_limit' ) )
	set_time_limit( 0 );

// List separator
$separator = '#--__--#';
$i = $j = $k = 0;
$lines = file(  dirname( __FILE__ ) . '/../data/file-replacements.txt' );
foreach ($lines as $line_num => $line) {
	$i++;
	$line_data = explode($separator, $line);
	
	if ( is_file($line_data[0]) ) {
		$j++;
		echo "rename( $line_data[0], $line_data[1] );";
		
		//if ( rename( $line_data[0], $line_data[1] ) == true ) {
		//	$k++;
		//}
	}
}

die(sprintf('Process results : %d lines, %d existing source files and %d renaming success operation !',$i, $j, $k));