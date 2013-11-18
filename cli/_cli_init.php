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

if ( defined( 'STDIN' ) ) {
	if ( count( $argv ) == 3 ) {
		$domain = $argv[1];
		$path = $argv[2];
	} else {
		die( 'Missing some params' );
	}

	if ( empty( $domain ) || empty( $path ) ) {
		die( 'Missing domain or path' );
	}

	// Fake WordPress, build server array
	$_SERVER = array(
		'SERVER_PROTOCOL' => 'http/1.1',
		'SERVER_PORT' => 80,
		'HTTP_HOST' => $domain,
		'SERVER_NAME' => $domain,
		'REQUEST_URI' => $path . 'index.php',
		'REQUEST_METHOD' => 'GET',
		'SCRIPT_NAME' => 'index.php',
		'SCRIPT_FILENAME' => 'index.php',
		'PHP_SELF' => $path . 'index.php'
	);
}