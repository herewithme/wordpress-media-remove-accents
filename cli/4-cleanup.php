<?php
// Flush all files
$fp = fopen( dirname( __FILE__ ) . '/../data/db-replacements.txt', "w" );
fclose( $fp );

$fp = fopen( dirname( __FILE__ ) . '/../data/file-replacements.txt', "w" );
fclose( $fp );

$fp = fopen( dirname( __FILE__ ) . '/../data/dbsr-config.json', "w" );
fclose( $fp );