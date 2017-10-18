<?php
/*
Plugin Name: Socrata Quotes
Plugin URI: http://socrata.com/
Description: This plugin manages quotes/testimonials.
Version: 1.0
Author: Michael Church
Author URI: http://socrata.com/
License: GPLv2
*/

add_action( 'init', 'create_socrata_quotes' );
function create_socrata_quotes() {
	register_post_type( 'socrata_quotes',
	array(
		'labels' => array(
			'name' => 'quotes',
			'singular_name' => 'quotes',
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
		'rewrite' => array('with_front' => false, 'slug' => 'quotes')
		)
	);
}

// TAXONOMIES
add_action( 'init', 'create_socrata_quotes_cat', 0 );
function create_socrata_quotes_cat() {
  register_taxonomy(
	'socrata_quotes_cat',
	'socrata_quotes',
	array(
	  'labels' => array(
		'name' => 'Quotes Category',
		'menu_name' => 'Category',
		'add_new_item' => 'Add New ',
		'new_item_name' => "New "
	  ),
	  'show_ui' => true,
	  'show_tagcloud' => false,
	  'hierarchical' => true,
	  'sort' => true,      
	  'args' => array( 'orderby' => 'term_order' ),
	  'show_admin_column' => true,
	  'rewrite' => array('with_front' => false, 'slug' => 'quotes-category'),
	)
  );
}

// MENU ICON
//Using Dashicon Font https://developer.wordpress.org/resource/dashicons
add_action( 'admin_head', 'add_socrata_quotes_icon' );
function add_socrata_quotes_icon() { ?>
  <style>
	#adminmenu .menu-icon-socrata_quotes div.wp-menu-image:before {
	  content: '\f205';
	}
  </style>
  <?php
}

// METABOXES
add_filter( 'rwmb_meta_boxes', 'socrata_quotes_register_meta_boxes' );
function socrata_quotes_register_meta_boxes( $meta_boxes ) {
	$prefix = 'quotes_';
	$meta_boxes[] = array(
		'title'         => 'Author Meta',   
		'post_types'    => 'socrata_quotes',
		'context'       => 'normal',
		'priority'      => 'high',
		'fields' => array(
			// TEXT
			array(
				'name'  => esc_html__( 'Job Title', 'quotes_' ),
				'id'    => "{$prefix}job_title",
				'type'  => 'text',
				'clone' => false,
			),
			// TEXT
			array(
				'name'  => esc_html__( 'Organization', 'quotes_' ),
				'id'    => "{$prefix}organization",
				'type'  => 'text',
				'clone' => false,
			),
			// IMAGE ADVANCED (WP 3.5+)
			array(
				'name'              => __( 'Headshot', 'quotes_' ),
				'id'                => "{$prefix}headshot",
				'desc'              => __( 'Minimum size 300x300 pixels.', 'quotes_' ),
				'type'              => 'image_advanced',
				'max_file_uploads'  => 1,
			),
			// WYSIWYG/RICH TEXT EDITOR
			array(
				'name'    => esc_html__( 'Quote', 'quotes_' ),
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

// Shortcode [logo-slider-content solution="SOLUTION SLUG" segment="SEGMENT SLUG" product="PRODUCT SLUG"]
function socrata_quote_slider ( $atts, $content = null ) {
	extract ( shortcode_atts ( array (
		'category' => '',
	), $atts ) );
	ob_start();
	?>
	
	<div id="quotes" class="carousel">
		<div class="container">
			<div class="customer-quotes">
				<?php
				$args = array(
					'post_type' => 'socrata_quotes',  
					'socrata_quotes_cat' => $category,
					'posts_per_page' => 100,
					'orderby' => 'date',
					'order'   => 'asc',
				);
				$myquery = new WP_Query($args);
				// The Loop
				while ( $myquery->have_posts() ) { $myquery->the_post(); 
					$headshot = rwmb_meta( 'quotes_headshot', 'size=post-thumbnail' );
					$quote = rwmb_meta( 'quotes_quote' );
					$job_title = rwmb_meta( 'quotes_job_title' );
					$organization = rwmb_meta( 'quotes_organization' );
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

	<script type="text/javascript">
		$(document).ready(function(){
			$('.customer-quotes').slick({
				arrows: true,
				appendArrows: $('#quotes'),
				prevArrow: '<div class="toggle-left d-xs-none d-sm-none"><i class="slick-prev icon-left-arrow"></i></div>',
				nextArrow: '<div class="toggle-right d-xs-none d-sm-none"><i class="slick-next icon-right-arrow"></i></div>',
				autoplay: true,
				autoplaySpeed: 5000,
				speed: 800,
				slidesToShow: 4,
				slidesToScroll: 1,
				accessibility:false,
				dots:false,
				responsive: [
					{
						breakpoint: 992,
						settings: {
						slidesToShow: 3,
						slidesToScroll: 1
					}},
					{
						breakpoint: 768,
						settings: {
						slidesToShow: 2,
						slidesToScroll: 1
					}},
					{
						breakpoint: 480,
						settings: {
						slidesToShow: 1,
						slidesToScroll: 1
					}}
				]
			});
			$('.customer-quotes').show();
		});
	</script>

<?php
$content = ob_get_contents();
ob_end_clean();
return $content;
}
add_shortcode( 'quote_slider', 'socrata_quote_slider' );
