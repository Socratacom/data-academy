<?php
/*
Plugin Name: Socrata Course Dashboard
Plugin URI: http://socrata.com
Description: This plugin adds visualizations to a dashbord that represents course data.
Version: 1.0
Author: Michael Church
Author URI: http://socrata.com/
License: GPLv2
*/

// REGISTER POST TYPE
add_action( 'init', 'create_course_dashboard' );

function create_course_dashboard() {
  register_post_type( 'course_dashboard',
    array(
      'labels' => array(
        'name' => 'Course Stats',
        'singular_name' => 'Course Stats',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Organization',
        'edit' => 'Edit Organization',
        'edit_item' => 'Edit Organization',
        'new_item' => 'New Organization',
        'view' => 'View',
        'view_item' => 'View Organization',
        'search_items' => 'Search',
        'not_found' => 'Not found',
        'not_found_in_trash' => 'Not found in Trash'
      ),
      'supports' => array('title'),
      'public' => true,
      'show_ui' => true,
			'menu_position' => 5,
			'menu_icon' => 'dashicons-chart-pie',
			'has_archive' => false,
    )
  );
}

// METABOXES
add_filter( 'rwmb_meta_boxes', 'course_dashboard_register_meta_boxes' );
function course_dashboard_register_meta_boxes( $meta_boxes ) {
	$prefix = 'stats_';
	$meta_boxes[] = array(
		'title'         => 'Organization Stats',   
		'post_types'    => 'course_dashboard',
		'context'       => 'normal',
		'priority'      => 'high',
		'fields' => array(			
			// NUMBER
			array(
				'name' => esc_html__( 'Registered Students', 'stats_' ),
				'id'   => "{$prefix}registered",
				'type' => 'number',
				'min'  => 0,
				'std'	=> 0,
			),
			// NUMBER
			array(
				'name' => esc_html__( 'Courses Completed', 'stats_' ),
				'id'   => "{$prefix}completed",
				'type' => 'number',
				'min'  => 0,
				'std'	=> 0,
			),
			// NUMBER
			array(
				'name' => esc_html__( 'Certificates Issued', 'stats_' ),
				'id'   => "{$prefix}certificates",
				'type' => 'number',
				'min'  => 0,
				'std'	=> 0,
			),
			// IMAGE ADVANCED (WP 3.5+)
      array(
        'name'             => __( 'Logo', 'stats_' ),
        'id'               => "{$prefix}logo",
        'type'             => 'image_advanced',
        'max_file_uploads' => 1,
      ),
		)
	);

  return $meta_boxes;
}


// Shortcode [stat-totals]
function stat_totals($atts, $content = null) {
  ob_start();


function registered_meta_values( $key = 'stats_registered', $type = 'course_dashboard', $status = 'publish' ) {

  global $wpdb;

  if( empty( $key ) )
      return;

  $r = $wpdb->get_col( $wpdb->prepare( "
      SELECT pm.meta_value FROM {$wpdb->postmeta} pm
      LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
      WHERE pm.meta_key = '%s' 
      AND p.post_status = '%s' 
      AND p.post_type = '%s'
  ", $key, $status, $type ) );

  return $r;
}

function completed_meta_values( $key = 'stats_completed', $type = 'course_dashboard', $status = 'publish' ) {

  global $wpdb;

  if( empty( $key ) )
      return;

  $r = $wpdb->get_col( $wpdb->prepare( "
      SELECT pm.meta_value FROM {$wpdb->postmeta} pm
      LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
      WHERE pm.meta_key = '%s' 
      AND p.post_status = '%s' 
      AND p.post_type = '%s'
  ", $key, $status, $type ) );

  return $r;
}

function certificates_meta_values( $key = 'stats_certificates', $type = 'course_dashboard', $status = 'publish' ) {

  global $wpdb;

  if( empty( $key ) )
      return;

  $r = $wpdb->get_col( $wpdb->prepare( "
      SELECT pm.meta_value FROM {$wpdb->postmeta} pm
      LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
      WHERE pm.meta_key = '%s' 
      AND p.post_status = '%s' 
      AND p.post_type = '%s'
  ", $key, $status, $type ) );

  return $r;
}

$reginput = implode( ',', registered_meta_values( 'stats_registered' ));
$regres = explode(',',$reginput);
$regresult = array_sum($regres);

$compinput = implode( ',', completed_meta_values( 'stats_completed' ));
$compres = explode(',',$compinput);
$compresult = array_sum($compres); 

$certsinput = implode( ',', certificates_meta_values( 'stats_certificates' ));
$certsres = explode(',',$certsinput);
$certsresult = array_sum($certsres); 

{ ?>

	<div class="col-sm-4 text-center">
		<h1 class="display-1 mdc-text-blue-500 mb-0"><?php echo $regresult;?></h1>
		<p>Total Students Registered</p>
	</div>
	<div class="col-sm-4 text-center">
		<h1 class="display-1 mdc-text-teal-500 mb-0"><?php echo $compresult;?></h1>
		<p>Total Courses Taken</p>
	</div>
	<div class="col-sm-4 text-center">
		<h1 class="display-1 mdc-text-green-500 mb-0"><?php echo $certsresult;?></h1>
		<p>Total Certificates Issued</p>
	</div>
	

<?php }






  $content = ob_get_contents();
  ob_end_clean();
  return $content;
}
add_shortcode('stat-totals', 'stat_totals');


// Shortcode [organization-visualisations]
function organization_visualisations($atts, $content = null) {
  ob_start();
  ?>



<?php
$args = array(
'post_type' => 'course_dashboard',
'posts_per_page' => -1,
'orderby' => 'date',
'order'   => 'asc',
);
$myquery = new WP_Query($args);
// The Loop
while ( $myquery->have_posts() ) { $myquery->the_post(); 
$registered = rwmb_meta( 'stats_registered' );
$completed = rwmb_meta( 'stats_completed' );
$certificates = rwmb_meta( 'stats_certificates' );
$id = get_the_ID();
?>


<div class="col-sm-4">
	<div class="card mb-4 match-height">
		<div class="card-header">
    	<h6 class="mb-0"><?php the_title();?></h6>
  	</div>
		<div id="<?php echo $id; ?>"></div>
		<script>
		var pieDiv = document.getElementById("<?php echo $id; ?>");
		var traceA = {
		  type: "pie",
		  values: [<?php echo $registered;?>, <?php echo $completed;?>, <?php echo $certificates;?>],
		  labels: ['Registered Students', 'Courses Completed', 'Certificates Issued'],
		  hole: 0.8,
		  direction: 'clockwise',

		  textinfo: 'none',
		  marker: {
		    colors: ['#03A9F4', '#009688', '#4CAF50'],
		  },
		  textfont: {
		    family: 'Roboto',
		    color: 'white',
		    size: 18
		  },
		  hoverlabel: {
		    bgcolor: 'black',
		    bordercolor: 'black',
		    padding: 10,
		    font: {
		      family: 'Roboto',
		      color: 'white',
		      size: 14,
		    }
		  }
		};
		var data = [traceA];
		var layout = {
		  showlegend: false,
		  height: 200,
			margin: {
				l: 30,
				r: 30,
				b: 30,
				t: 30,
				pad: 30
			},
			paper_bgcolor: '#37474F'
		};
		Plotly.plot(pieDiv, data, layout, {displayModeBar: false});
		</script>
		<div class="card-body">
			<ul class="list-group mb-0 stats">
				<li class="d-flex justify-content-between align-items-center"><span class="text-bold mdc-text-blue-500"><?php echo $registered; ?></span> <span>Students Registered</span></li>
				<li class="d-flex justify-content-between align-items-center"><span class="text-bold mdc-text-teal-500"><?php echo $completed; ?></span> <span>Courses Completed</span></li>
				<li class="d-flex justify-content-between align-items-center"><span class="text-bold mdc-text-green-500"><?php echo $certificates; ?></span> <span>Certificates Issued</span></li>
			</ul>
		</div>
	</div>
</div>



<?php
}
wp_reset_postdata();
?>






  <?php
  $content = ob_get_contents();
  ob_end_clean();
  return $content;
}
add_shortcode('organization-visualisations', 'organization_visualisations');