<?php
/*
Plugin Name: Include Tax
Version: 1.0
Description: Add Custom Taxonomy to search queries.
Author: Hibou
Author URI: http://private.hibou-web.com
Plugin URI: http://private.hibou-web.com
Text Domain: include-tax
Domain Path: /languages
*/

function custom_search_where( $where ){
	global $wpdb;

	$customs = array('custom_field1', 'custom_field2', 'custom_field3');

	if ( is_search() && get_search_query() ) {
		$where .= "OR ((t.name LIKE '%" . get_search_query() . "%' OR t.slug LIKE '%" . get_search_query() . "%') AND {$wpdb->posts}.post_status = 'publish') OR (({$wpdb->postmeta}.meta_key = 'textfield_common') AND ({$wpdb->postmeta}.meta_value  LIKE '%" . get_search_query() . "%')) OR (({$wpdb->postmeta}.meta_key = 'textfield_common') AND ({$wpdb->postmeta}.meta_value  LIKE '%" . get_search_query() . "%'))";

		//$where .= " OR (({$wpdb->postmeta}.meta_key = 'textfield_common') AND ({$wpdb->postmeta}.meta_value  LIKE '%" . get_search_query() . "%'))";
	}

	return $where;
}

function custom_search_join( $join ){
	global $wpdb;
	if ( is_search() && get_search_query() ) {
		$join .= "
		INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
		 LEFT JOIN {$wpdb->term_relationships} tr ON {$wpdb->posts}.ID = tr.object_id INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id=tr.term_taxonomy_id INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id";

		//$join .= " LEFT JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id ";
	}

	return $join;
}

function custom_search_groupby($groupby){
	global $wpdb;
	// we need to group on post ID
	$groupby_id = "{$wpdb->posts}.ID";
	if(!is_search() || strpos($groupby, $groupby_id) !== false || !get_search_query()) return $groupby;

	// groupby was empty, use ours
	if(!strlen(trim($groupby))) return $groupby_id;

	// wasn't empty, append ours
	return $groupby.", ".$groupby_id;
}

add_filter('posts_where','custom_search_where', 1,1 );
add_filter('posts_join', 'custom_search_join', 1, 1 );
add_filter('posts_groupby', 'custom_search_groupby', 1, 1 );

