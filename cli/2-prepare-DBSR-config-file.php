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

// Get sample config file
$config_data = file_get_contents( dirname( __FILE__ ) . '/../data/dbsr-config-sample.json' );
$config_data = json_decode( $config_data );

// Set current MYSQL settings
$config_data->options->PDO->username = DB_USER;
$config_data->options->PDO->password = DB_PASSWORD;
$config_data->options->PDO->database = DB_NAME;
$config_data->options->PDO->charset = DB_CHARSET;
$config_data->options->PDO->hostname = DB_HOST;

// List separator
$separator = '#--__--#';

$i = $j = $k = 0;
$lines = file( dirname( __FILE__ ) . '/../data/db-replacements.txt' );
foreach ( $lines as $line_num => $line ) {
	$i++;
	$line_data = explode( $separator, $line );
	$line_data = array_map('trim', $line_data);

	$config_data->search[] = $line_data[0];
	$config_data->replace[] = $line_data[1];
}

// Save config file
file_put_contents( dirname( __FILE__ ) . '/../data/dbsr-config.json', json_encode( $config_data ) );

die( sprintf( 'Config file writed ! %d replacements expected !', $i ) );
