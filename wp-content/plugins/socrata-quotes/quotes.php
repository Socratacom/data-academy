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
		'title'         => 'Customer Info',   
		'post_types'    => 'socrata_quotes',
		'context'       => 'normal',
		'priority'      => 'high',
		'fields' => array(
			// TEXT
			array(
				'name'  => esc_html__( 'Author', 'quotes_' ),
				'id'    => "{$prefix}author",
				'type'  => 'text',
				'clone' => false,
			),
			// TEXT
			array(
				'name'  => esc_html__( 'Job Title', 'quotes_' ),
				'id'    => "{$prefix}job_title",
				'type'  => 'text',
				'clone' => false,
			),
			// IMAGE ADVANCED (WP 3.5+)
			array(
				'name'              => __( 'Headshot', 'quotes_' ),
				'id'                => "{$prefix}brand",
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

// Shortcode [logo-slider solution="SOLUTION SLUG" segment="SEGMENT SLUG" product="PRODUCT SLUG"]
function socrata_logo_slider($atts, $content = null) {
  extract( shortcode_atts( array(
	'solution' => '',
	'segment' => '',
	'product' => '',
  ), $atts ) );
  ob_start();
  ?>
  <div id="quotes">
	<div class="container">
	  <div class="customer-quotes">   
		<?php
		$args = array(
		'post_type' => 'socrata_quotes',  
		'solution' => $solution,
		'segment' => $segment,
		'product' => $product,
		'posts_per_page' => 100,
		'orderby' => 'date',
		'order'   => 'asc',
		);
		$myquery = new WP_Query($args);
		// The Loop
		while ( $myquery->have_posts() ) { $myquery->the_post(); 
		$logo = rwmb_meta( 'quotes_brand', 'size=medium' );
		$site = rwmb_meta( 'quotes_url' );
		$title = rwmb_meta( 'quotes_title' );
		?>

		<div class="text-center">
		  <div class="match-height" style="padding:0 15px;">
			<div class="sixteen-nine margin-bottom-15" style="background-image:url(<?php foreach ( $logo as $image ) { echo $image['url']; } ?>); background-size:contain; background-repeat:no-repeat; background-position:center center; position:relative;">
			  <?php if ( ! empty( $site ) ) { ?> <a href="<?php echo $site;?>" target="_blank" style="position:absolute; top:0; left:0; width:100%; height:100%;"></a> <?php } else {} ?>
			</div>
			<?php if ( ! empty( $title ) ) { ?>
			  <?php echo $title;?>
			<?php } else { ?>
			  <?php the_title();?>
			<?php } ?>
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
	  prevArrow: '<div class="toggle-left"><i class="fa slick-prev fa-long-arrow-left"></i></div>',
	  nextArrow: '<div class="toggle-right"><i class="fa slick-next fa-long-arrow-right"></i></div>',
	  autoplay: true,
	  autoplaySpeed: 5000,
	  speed: 800,
	  slidesToShow: 5,
	  slidesToScroll: 1,
	  accessibility:false,
	  dots:false,
	  responsive: [
		  {
			breakpoint: 992,
			settings: {
			  slidesToShow: 3,
			  slidesToScroll: 1
			}
		  },
		  {
			breakpoint: 768,
			settings: {
			  slidesToShow: 2,
			  slidesToScroll: 1
			}
		  },
		  {
			breakpoint: 480,
			settings: {
			  slidesToShow: 1,
			  slidesToScroll: 1
			}
		  }
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
add_shortcode('logo-slider', 'socrata_logo_slider');

// Shortcode [logo-slider-content solution="SOLUTION SLUG" segment="SEGMENT SLUG" product="PRODUCT SLUG"]
function socrata_logo_slider_content($atts, $content = null) {
  extract( shortcode_atts( array(
	'solution' => '',
	'segment' => '',
	'product' => '',
  ), $atts ) );
  ob_start();
  ?>
  <div id="quotes">
	<div class="container">
	  <div class="row">
		<div class="customer-quotes">   
		  <?php
		  $args = array(
		  'post_type' => 'socrata_quotes',  
		  'solution' => $solution,
		  'segment' => $segment,
		  'product' => $product,
		  'posts_per_page' => 100,
		  'orderby' => 'date',
		  'order'   => 'asc',
		  );
		  $myquery = new WP_Query($args);
		  // The Loop
		  while ( $myquery->have_posts() ) { $myquery->the_post(); 
		  $logo = rwmb_meta( 'quotes_brand', 'size=medium' );
		  $content = rwmb_meta( 'quotes_wysiwyg' );
		  $site = rwmb_meta( 'quotes_url' );
		  $title = rwmb_meta( 'quotes_title' );
		  ?>

			<div class="match-height" style="padding:0 15px;">
			  <div class="padding-30 margin-bottom-15" style="border-bottom:#d6d6d6 solid 1px;">
				<div class="sixteen-nine" style="background-image:url(<?php foreach ( $logo as $image ) { echo $image['url']; } ?>); background-size:contain; background-repeat:no-repeat; background-position:center center; position:relative;">
				  <?php if ( ! empty( $site ) ) { ?> <a href="<?php echo $site;?>" target="_blank" style="position:absolute; top:0; left:0; width:100%; height:100%;"></a> <?php } else {} ?>
				</div>
			  </div>
			  <div style="text-align: left;">
				<?php if ( ! empty( $title ) ) { ?>
				  <h5 class="margin-bottom-15"><?php echo $title;?></h5>
				<?php } else { ?>
				  <h5 class="margin-bottom-15"><?php the_title();?></h5>
				<?php } ?>                
				<?php echo $content;?>
				<?php if ( ! empty( $site ) ) { ?> <p><a href="<?php echo $site;?>" target="_blank">Visit Site</a></p> <?php } else {} ?>
			  </div>
			</div>

		  <?php
		  }
		  wp_reset_postdata();
		  ?>
		</div>
	  </div>
	</div>
  </div>

  <script type="text/javascript">
  $(document).ready(function(){
	$('.customer-quotes').slick({
	  arrows: true,
	  appendArrows: $('#quotes'),
	  prevArrow: '<div class="toggle-left"><i class="fa slick-prev fa-long-arrow-left"></i></div>',
	  nextArrow: '<div class="toggle-right"><i class="fa slick-next fa-long-arrow-right"></i></div>',
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
			}
		  },
		  {
			breakpoint: 768,
			settings: {
			  slidesToShow: 2,
			  slidesToScroll: 1
			}
		  },
		  {
			breakpoint: 480,
			settings: {
			  slidesToShow: 1,
			  slidesToScroll: 1
			}
		  }
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
add_shortcode('logo-slider-content', 'socrata_logo_slider_content');
