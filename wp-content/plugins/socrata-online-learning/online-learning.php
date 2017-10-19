<?php
/*
Plugin Name: Socrata Online Learning Courses
Plugin URI: http://socrata.com/
Description: This plugin manages In-person Learning schedule.
Version: 1.0
Author: Michael Church
Author URI: http://socrata.com/
License: GPLv2
*/

add_action( 'init', 'create_socrata_ol_courses' );
function create_socrata_ol_courses() {
	register_post_type( 'socrata_ol_courses',
	array(
		'labels' => array(
			'name' => 'OL Courses',
			'singular_name' => 'online learning courses',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New',
			'edit' => 'Edit',
			'edit_item' => 'Edit',
			'new_item' => 'New',
			'view' => 'View',
			'view_item' => 'View',
			'search_items' => 'Search',
			'not_found' => 'Not found',
			'not_found_in_trash' => 'Not found in Trash'
		),
		'public' => true,
		'menu_position' => 5,
		'supports' => array( 'title' ),
		'taxonomies' => array( '' ),
		'menu_icon' => '',
		'has_archive' => false,
		'rewrite' => array('with_front' => false, 'slug' => 'ol_courses')
		)
	);
}

// MENU ICON
//Using Dashicon Font https://developer.wordpress.org/resource/dashicons
add_action( 'admin_head', 'add_socrata_ol_courses_icon' );
function add_socrata_ol_courses_icon() { ?>
  <style>
	#adminmenu .menu-icon-socrata_ol_courses div.wp-menu-image:before {
	  content: '\f547';
	}
  </style>
  <?php
}

// METABOXES
add_filter( 'rwmb_meta_boxes', 'socrata_ol_courses_register_meta_boxes' );
function socrata_ol_courses_register_meta_boxes( $meta_boxes ) {
	$prefix = 'ol_courses_';
	$meta_boxes[] = array(
		'title'         => 'Course Meta',   
		'post_types'    => 'socrata_ol_courses',
		'context'       => 'normal',
		'priority'      => 'high',
		'fields' => array(			
			// TEXTAREA
			array(
				'name' => esc_html__( 'Short Description', 'ol_courses_' ),
				'desc' => esc_html__( 'Textarea description', 'ol_courses_' ),
				'id'   => "{$prefix}description",
				'type' => 'textarea',
				'cols' => 20,
				'rows' => 3,
			),			
			// URL
			array(
				'name' => esc_html__( 'Course URL', 'ol_courses_' ),
				'id'   => "{$prefix}course_url",
				'type' => 'url',
			),
		)
	);

  return $meta_boxes;
}