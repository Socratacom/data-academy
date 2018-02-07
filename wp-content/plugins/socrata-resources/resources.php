<?php
/*
Plugin Name: Socrata Resources
Plugin URI: http://socrata.com/
Description: This plugin manages resources.
Version: 1.0
Author: Michael Church
Author URI: http://socrata.com/
License: GPLv2
*/

add_action( 'init', 'create_socrata_resources' );
function create_socrata_resources() {
	register_post_type( 'socrata_resources',
	array(
		'labels' => array(
			'name' => 'Resources',
			'singular_name' => 'resources',
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
		'menu_icon' => 'dashicons-admin-page',
		'has_archive' => false,
		'rewrite' => array('with_front' => false, 'slug' => 'resources')
		)
	);
}

// METABOXES
add_filter( 'rwmb_meta_boxes', 'socrata_resources_register_meta_boxes' );
function socrata_resources_register_meta_boxes( $meta_boxes ) {
	$prefix = 'resources_';
	$meta_boxes[] = array(
		'title'         => 'RESOURCE DETAILS',   
		'post_types'    => 'socrata_resources',
		'context'       => 'normal',
		'priority'      => 'high',
		'validation' => array(
			'rules'  => array(
				"{$prefix}url" => array(
					'required'  => true,
				),
			),
		),
		'fields' => array(
			// TEXT
			array(
				'name'  => 'Source',
				'id'    => "{$prefix}source",
				'desc' => 'ex: Socrata.com or Author Name',
				'type'  => 'text',
				'clone' => false,
			),
			// URL
			array(
				'name' => 'URL',
				'id'   => "{$prefix}url",
				'desc' => 'Link to resource.',
				'type' => 'url',
			),
			// TEXT
			array(
				'name'  => 'Link Text',
				'id'    => "{$prefix}link_text",
				'desc' => 'ex: Read More',
				'type'  => 'text',
				'clone' => false,
			),
			// IMAGE ADVANCED (WP 3.5+)
			array(
				'name'              => 'Feature Image',
				'id'                => "{$prefix}thumbnail",
				'desc'              => 'Minimum size 760x420 pixels.',
				'type'              => 'image_advanced',
				'max_file_uploads'  => 1,
			),
		),
	);

  return $meta_boxes;
}

// Shortcode [resource-list]
function socrata_resource_list ( $atts, $content = null ) {
	ob_start();
	?>

	<div class="row">
		<?php
		$args = array(
		'post_type' => 'socrata_resources',
		'posts_per_page' => 100,
		'orderby' => 'date',
		'order'   => 'desc',
		);
		$myquery = new WP_Query($args);
		// The Loop
		while ( $myquery->have_posts() ) { $myquery->the_post(); 
		$url = rwmb_meta( 'resources_url' );
		$source = rwmb_meta( 'resources_source' );
		$linktext = rwmb_meta( 'resources_link_text' );
		$feature = rwmb_meta( 'resources_thumbnail', 'size=medium' );
		?>

			<?php if ( !empty ( $feature ) ) { ?> 

			<div class="col-sm-6 col-lg-4">
				<div class="card mb-4 match-height">
					<div class="sixteen-nine" style="background-image:url(<?php foreach ( $feature as $image ) { echo $image['url']; } ?>); background-size:cover; background-repeat:no-repeat; background-position:center; position:relative;"><a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" style="position: absolute; top:0; left:0; width:100%; height:100%"></a></div>
					<div class="card-body">
						<?php if ( !empty ( $source ) ) { ?>
							<h4 class="mb-0"><?php the_title(); ?></h4>
							<p class="mb-0"><small><i><?php echo $source; ?></i></small></p>
						<?php } else { ?>
							<h4 class="mb-0"><?php the_title(); ?></h4>
						<?php } ?>			
					</div>
					<div class="card-footer">
						<?php if ( !empty ( $linktext ) ) { ?>
							<a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-link"><?php echo $linktext; ?></a>
						<?php } else { ?>
							<a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-link">More Information</a>
						<?php } ?>
					</div>
				</div>
			</div>

			<?php } else { ?>

			<div class="col-sm-6 col-lg-4">
				<div class="card mb-4 mdc-bg-blue-grey-500 match-height">
					<div class="card-body">
						<?php if ( !empty ( $source ) ) { ?>
							<h4 class="mb-0 mt-3 text-white"><?php the_title(); ?></h4>
							<p class="mb-0 text-white"><small><i><?php echo $source; ?></i></small></p>
						<?php } else { ?>
							<h4 class="mb-0 mt-3 text-white"><?php the_title(); ?></h4>
						<?php } ?>			
					</div>
					<div class="card-footer">
						<?php if ( !empty ( $linktext ) ) { ?>
							<div class="btn btn-link"><span class="text-white"><?php echo $linktext; ?></span></div>
						<?php } else { ?>
							<div class="btn btn-link"><span class="text-white">More Information</span></div>
						<?php } ?>
					</div>
					<a href="<?php echo $url; ?>" target="_blank" rel="noopener noreferrer" style="position: absolute; top:0; left:0; width:100%; height: 100%;"></a>
				</div>
			</div>

			<?php } ?>

		<?php
		}
		wp_reset_postdata();
		?>				
	</div>

<?php
$content = ob_get_contents();
ob_end_clean();
return $content;
}
add_shortcode( 'resource-list', 'socrata_resource_list' );
