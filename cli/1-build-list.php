<?php
/*
 * Build for remove accents for existing medias
 */

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

function get_file_lines_counter( $file ) {
	$linecount = 0;
	$handle = fopen( $file, "r" );
	while ( !feof( $handle ) ) {
		$line = fgets( $handle, 4096 );
		$linecount = $linecount + substr_count( $line, PHP_EOL );
	}

	fclose( $handle );
	return $linecount;
}

function mb_basename($file) { 
	$r = explode('/',$file);
	return end($r); 
} 

function _remove_accents( $value ) {
	//$value = utf8_decode($value);
	$value = remove_accents($value);
	return $value;
}

// Get blogs list
global $wpdb;
$blogs = $wpdb->get_results( "SELECT * FROM $wpdb->blogs WHERE 1 = 1 AND deleted = 0 AND archived = '0' ORDER BY blog_id" ); // AND public = 1$
// List separator
$separator = '#--__--#';

// DB replacements
$db_replacements = dirname( __FILE__ ) . '/../data/db-replacements.txt';
$file_replacements = dirname( __FILE__ ) . '/../data/file-replacements.txt';

// Flush JSON files
$fp = fopen( $db_replacements, "w" );
fclose( $fp );
$fp = fopen( $file_replacements, "w" );
fclose( $fp );

$i = $j = 0;
foreach ( $blogs as $blog ) {
	switch_to_blog( $blog->blog_id );
	$i++;

	$upload_dir = wp_upload_dir();

	// Always append end flash
	$upload_dir['baseurl'] = trailingslashit( $upload_dir['baseurl'] );
	$upload_dir['basedir'] = trailingslashit( $upload_dir['basedir'] );

	$attachment_query = new WP_Query( array(
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'nopaging' => 'true',
		'post_status' => 'any',
		//'post_parent' => null, // any parent
		'fields' => 'ids'
		) );

	foreach ( $attachment_query->posts as $attachment_id ) {
		$j++;

		// Get partial file path
		$_wp_attached_file = get_post_meta( $attachment_id, '_wp_attached_file', true );
		
		// Get file month folder
		$month_folder = trailingslashit( dirname( $_wp_attached_file ) );

		// Get only filename
		$_wp_attached_file = mb_basename( $_wp_attached_file );
		
		// Try to remove accents from filename
		$_wp_attached_file_without_accents = _remove_accents( $_wp_attached_file );

		// No accents ? Skip to next media
		if ( $_wp_attached_file_without_accents == $_wp_attached_file ) {
			continue;
		}

		// Be sur new name is uniq
		$_wp_attached_file_without_accents = wp_unique_filename( $upload_dir['basedir'] . $month_folder, $_wp_attached_file_without_accents );

		// Add full URL to DB rename
		$before = $upload_dir['baseurl'] . $month_folder . $_wp_attached_file;
		$after = $upload_dir['baseurl'] . $month_folder . $_wp_attached_file_without_accents;
		file_put_contents( $db_replacements, $before . $separator . $after . PHP_EOL, FILE_APPEND | LOCK_EX );
		
		// Add filename to DB rename
		$before = $_wp_attached_file;
		$after = $_wp_attached_file_without_accents;
		file_put_contents( $db_replacements, $before . $separator . $after . PHP_EOL, FILE_APPEND | LOCK_EX );

		// Add to files rename
		$before = $upload_dir['basedir'] . $month_folder . $_wp_attached_file;
		$after = $upload_dir['basedir'] . $month_folder . $_wp_attached_file_without_accents;
		file_put_contents( $file_replacements, $before . $separator . $after . PHP_EOL, FILE_APPEND | LOCK_EX );

		// Get thumb data
		$_wp_attachment_metadata = get_post_meta( $attachment_id, '_wp_attachment_metadata', true );
		
		// Use previos filename without accent
		$_wp_attachment_metadata['file'] = $month_folder . $_wp_attached_file_without_accents;
		
		foreach ( $_wp_attachment_metadata['sizes'] as $size => $thumb_data ) {
			$thumb_file_with_accents = $thumb_data['file'];
			$thumb_file_without_accents = _remove_accents( $thumb_data['file'] );
			$_wp_attachment_metadata['sizes'][$size]['file'] = $thumb_file_without_accents;
			
			// Be sur new name is uniq
			$thumb_file_without_accents = wp_unique_filename( $upload_dir['basedir'] . $month_folder, $thumb_file_without_accents );

			// Add full URL to DB rename
			$before = $upload_dir['baseurl'] . $month_folder . $thumb_file_with_accents;
			$after = $upload_dir['baseurl'] . $month_folder . $thumb_file_without_accents;
			file_put_contents( $db_replacements, $before . $separator . $after . PHP_EOL, FILE_APPEND | LOCK_EX );
			
			// Add filename to DB rename
			$before = $thumb_file_with_accents;
			$after = $thumb_file_without_accents;
			file_put_contents( $db_replacements, $before . $separator . $after . PHP_EOL, FILE_APPEND | LOCK_EX );

			// Add to files rename
			$before = $upload_dir['basedir'] . $month_folder . $thumb_file_with_accents;
			$after = $upload_dir['basedir'] . $month_folder . $thumb_file_without_accents;
			file_put_contents( $file_replacements, $before . $separator . $after . PHP_EOL, FILE_APPEND | LOCK_EX );
		}
	}
}

die( sprintf( 'Building text files finished, %d files replacements and %d DB replacements for %d medias into %d blogs', get_file_lines_counter( $file_replacements ), get_file_lines_counter( $db_replacements ), $j, $i ) );
