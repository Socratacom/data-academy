<?php
/*
Plugin Name: Socrata In-person Learning Schedule
Plugin URI: http://socrata.com/
Description: This plugin manages In-person Learning schedule.
Version: 1.0
Author: Michael Church
Author URI: http://socrata.com/
License: GPLv2
*/

add_action( 'init', 'create_socrata_ipl_schedule' );
function create_socrata_ipl_schedule() {
	register_post_type( 'socrata_ipl_schedule',
	array(
		'labels' => array(
			'name' => 'IPL Schedule',
			'singular_name' => 'ipl schedule',
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
		'rewrite' => array('with_front' => false, 'slug' => 'ipl_schedule')
		)
	);
}

// MENU ICON
//Using Dashicon Font https://developer.wordpress.org/resource/dashicons
add_action( 'admin_head', 'add_socrata_ipl_schedule_icon' );
function add_socrata_ipl_schedule_icon() { ?>
  <style>
	#adminmenu .menu-icon-socrata_ipl_schedule div.wp-menu-image:before {
	  content: '\f338';
	}
  </style>
  <?php
}

// METABOXES
add_filter( 'rwmb_meta_boxes', 'socrata_ipl_schedule_register_meta_boxes' );
function socrata_ipl_schedule_register_meta_boxes( $meta_boxes ) {
	$prefix = 'ipl_schedule_';
	$meta_boxes[] = array(
		'title'         => 'Schedule Dates',   
		'post_types'    => 'socrata_ipl_schedule',
		'context'       => 'normal',
		'priority'      => 'high',
		'validation' => array(
      'rules'    => array(
        "{$prefix}startdate" => array(
            'required'  => true,
        ),
        "{$prefix}enddate" => array(
            'required'  => true,
        ),
      ),
    ),
		'fields' => array(
			// DATE
			array(
				'name'       => esc_html__( 'Start Date', 'ipl_schedule_' ),
				'id'         => "{$prefix}startdate",
				'type'       => 'date',				
        'timestamp'   => true, 
				'js_options' => array(					
          'numberOfMonths'  => 2,
          'showButtonPanel' => true,
				),
			),			
			// DATE
			array(
				'name'       => esc_html__( 'End Date', 'ipl_schedule_' ),
				'id'         => "{$prefix}enddate",
				'type'       => 'date',				
        'timestamp'   => true, 
				'js_options' => array(					
          'numberOfMonths'  => 2,
          'showButtonPanel' => true,
				),
			),      
      // TEXT
      array(
        'name'  => __( 'Custom Date', 'ipl_schedule_' ),
        'id'    => "{$prefix}custom_date",
        'desc' => __( 'Example: January', 'ipl_schedule_' ),
        'type'  => 'text',
        'clone' => false,
      ),
		)
	);

	$meta_boxes[] = array(
    'title'  => __( 'Schedule Meta' ),
    'post_types' => 'socrata_ipl_schedule',
    'context'    => 'normal',
    'priority'   => 'high',
    'fields' => array(      
      // TEXT
      array(
        'name'  => __( 'Region', 'ipl_schedule_' ),
        'id'    => "{$prefix}region",
        'desc' => __( 'Example: South West', 'ipl_schedule_' ),
        'type'  => 'text',
        'clone' => false,
      ),
      // URL
			array(
				'name' => esc_html__( 'Eventbrite URL', 'ipl_schedule_' ),
				'id'   => "{$prefix}eventbrite_url",
				'type' => 'url',
			),
    )
  );

  return $meta_boxes;
}

// Shortcode [ipl-schedule]
function socrata_ipl_schedule ( $atts, $content = null ) {
  ob_start();

	$today = strtotime('today UTC');
	$args = array(
		'post_type' => 'socrata_ipl_schedule',
		'post_status' => 'publish',
		'ignore_sticky_posts' => true,
		'meta_key' => 'ipl_schedule_startdate',
		'orderby' => 'meta_value_num',
		'order' => 'asc',
		'posts_per_page' => 100,
		'meta_query' => array(
			'relation' => 'AND',
			array(
			'key' => 'ipl_schedule_enddate',
			'value' => $today,
			'compare' => '>='
			)
		)
	);
	$myquery = new WP_Query($args);

	// The Loop
	if ( $myquery->have_posts() ) {
		echo '<table class="table"><tbody>';
		while ( $myquery->have_posts() ) { $myquery->the_post();
		$custom_date = rwmb_meta( 'ipl_schedule_custom_date' );
		$start_date = rwmb_meta( 'ipl_schedule_startdate' );
		$end_date = rwmb_meta( 'ipl_schedule_enddate' );
		$region = rwmb_meta( 'ipl_schedule_region' );
		$url = rwmb_meta( 'ipl_schedule_eventbrite_url' );
		?>

		<tr>
			<td class="align-middle">
				<?php if ( !empty ( $url ) ) { ?>
					<h4 class="mb-0 font-normal"><a href="<?php echo $ull;?>" target="_blank"><?php the_title(); ?><a></h4>
					<div class="mdc-text-blue-grey-400" style="font-size:14px;">
						<?php if ( !empty ( $custom_date ) ) { ?><?php echo $custom_date;?><?php } else { ?><?php echo date('M j', $start_date);?> - <?php echo date('M j', $end_date);?><?php } ?><?php if ( !empty ( $region ) ) echo ", $region ";?>						
					</div>
				<?php } else { ?>
					<h4 class="mb-0 font-normal"><?php the_title(); ?></h4>
					<div class="mdc-text-blue-grey-400" style="font-size:14px;">
						<?php if ( !empty ( $custom_date ) ) { ?><?php echo $custom_date;?><?php } else { ?><?php echo date('M j', $start_date);?> - <?php echo date('M j', $end_date);?><?php } ?><?php if ( !empty ( $region ) ) echo ", $region ";?>						
					</div>
				<?php } ?> 
			</td>
			<td class="align-middle text-right d-none d-sm-block"><?php if ( !empty ($url) ) { ?><a href="<?php echo $url;?>" target="_blank" class="btn btn-primary">Learn More</a> <?php } else { ?><span>Coming Soon</span><?php } ?></td>
		</tr>

		<?php

		}
		echo '</table></tbody>';
		wp_reset_postdata();
	} else { ?>
		<div class="alert alert-primary" role="alert">
			There are no scheduled events at this time.
		</div>
	<?php
	}

	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode( 'ipl-schedule', 'socrata_ipl_schedule' );
