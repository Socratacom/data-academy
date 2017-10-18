<?php
/*
Plugin Name: Socrata Online Learning
Plugin URI: http://socrata.com/
Description: This plugin manages In-person Learning schedule.
Version: 1.0
Author: Michael Church
Author URI: http://socrata.com/
License: GPLv2
*/

add_action( 'init', 'create_socrata_online_learning' );
function create_socrata_online_learning() {
	register_post_type( 'socrata_online_learning',
	array(
		'labels' => array(
			'name' => 'online_learning',
			'singular_name' => 'online_learning',
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
		'rewrite' => array('with_front' => false, 'slug' => 'online_learning')
		)
	);
}

// MENU ICON
//Using Dashicon Font https://developer.wordpress.org/resource/dashicons
add_action( 'admin_head', 'add_socrata_online_learning_icon' );
function add_socrata_online_learning_icon() { ?>
  <style>
	#adminmenu .menu-icon-socrata_online_learning div.wp-menu-image:before {
	  content: '\f205';
	}
  </style>
  <?php
}

// METABOXES
add_filter( 'rwmb_meta_boxes', 'socrata_online_learning_register_meta_boxes' );
function socrata_online_learning_register_meta_boxes( $meta_boxes ) {
	$prefix = 'online_learning_';
	$meta_boxes[] = array(
		'title'         => 'Author Meta',   
		'post_types'    => 'socrata_online_learning',
		'context'       => 'normal',
		'priority'      => 'high',
		'fields' => array(
			// TEXT
			array(
				'name'  => esc_html__( 'Job Title', 'online_learning_' ),
				'id'    => "{$prefix}job_title",
				'type'  => 'text',
				'clone' => false,
			),
			// TEXT
			array(
				'name'  => esc_html__( 'Organization', 'online_learning_' ),
				'id'    => "{$prefix}organization",
				'type'  => 'text',
				'clone' => false,
			),
			// IMAGE ADVANCED (WP 3.5+)
			array(
				'name'              => __( 'Headshot', 'online_learning_' ),
				'id'                => "{$prefix}headshot",
				'desc'              => __( 'Minimum size 300x300 pixels.', 'online_learning_' ),
				'type'              => 'image_advanced',
				'max_file_uploads'  => 1,
			),
			// WYSIWYG/RICH TEXT EDITOR
			array(
				'name'    => esc_html__( 'Quote', 'online_learning_' ),
				'id'      => "{$prefix}quote",
				'type'    => 'wysiwyg',
				'raw'     => false,
				'options' => array(
				'textarea_rows' => 4,
				'teeny'         => false,
				'media_buttons' => false,
			),
		),
	),
);

  return $meta_boxes;
}

// Shortcode [online-courses solution="SOLUTION SLUG" segment="SEGMENT SLUG" product="PRODUCT SLUG"]
function socrata_online_learning_courses ( $atts, $content = null ) {
	extract ( shortcode_atts ( array (
		'category' => '',
	), $atts ) );
	ob_start();
	?>
	
	<div id="online_learning" class="carousel">
		<div class="container">
			<div class="customer-online_learning">
				<?php
				$args = array(
					'post_type' => 'socrata_online_learning',  
					'socrata_online_learning_cat' => $category,
					'posts_per_page' => 100,
					'orderby' => 'date',
					'order'   => 'asc',
				);
				$myquery = new WP_Query($args);
				// The Loop
				while ( $myquery->have_posts() ) { $myquery->the_post(); 
					$headshot = rwmb_meta( 'online_learning_headshot', 'size=post-thumbnail' );
					$quote = rwmb_meta( 'online_learning_quote' );
					$job_title = rwmb_meta( 'online_learning_job_title' );
					$organization = rwmb_meta( 'online_learning_organization' );
				?>

						<div class="quote match-height p-3">							
							<?php echo $quote;?>
							<div class="author">
								<?php if ( !empty ( $headshot ) ) {  
									foreach ( $headshot as $image ) { ?><img src="<?php echo $image['url']; ?>" class="headshot"> <?php } 
								}
								else { ?> 
									<img src="/wp-content/uploads/no-profile-image.jpg" class="headshot">
								<?php }
								?>
								<div class="author-meta"><span class="font-semi-bold"><?php the_title(); ?></span><?php if ( !empty ( $job_title ) ) { ?><br><?php echo $job_title;?><?php if ( !empty ( $organization ) ) { ?>, <?php echo $organization;?> <?php } ?> <?php } ?>
								</div>
							</div>
						</div>

				<?php
				}
				wp_reset_postdata();
				?>

			</div>
		</div>
	</div>



<?php
$content = ob_get_contents();
ob_end_clean();
return $content;
}
add_shortcode( 'online-courses', 'socrata_online_learning_courses' );
