<?php

   /*
   Plugin Name: Toddlers Shortcodes
   Plugin URI: http://unfamo.us
   Description: Shortcodes for the Toddlers Theme.
   Version: 2.0
   Author: Unfamous Themes
   Author URI: http://unfamo.us
   License: GPL2
   */

/*/////////////////////////////////////////////////////////////////
// GRID
/////////////////////////////////////////////////////////////////*/

function shortcode_row( $atts, $content = null ) {
	extract(shortcode_atts(array(
        'class'     => ''
    ), $atts));
   return '<div class="row shortcoderow '. $class .'">' . do_shortcode($content) . '</div>';
}
add_shortcode('row', 'shortcode_row');

function shortcode_one_half( $atts, $content = null ) {
	extract(shortcode_atts(array(
        'class'     => ''
    ), $atts));
   return '<div class="col-md-6 '. $class .'">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_half', 'shortcode_one_half');

function shortcode_one_third( $atts, $content = null ) {
	extract(shortcode_atts(array(
        'class'     => ''
    ), $atts));
   return '<div class="col-md-4 '. $class .'">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_third', 'shortcode_one_third');

function shortcode_one_fourth( $atts, $content = null ) {
	extract(shortcode_atts(array(
        'class'     => ''
    ), $atts));
   return '<div class="col-md-3 '. $class .'">' . do_shortcode($content) . '</div>';
}
add_shortcode('one_fourth', 'shortcode_one_fourth');

function shortcode_two_third( $atts, $content = null ) {
	extract(shortcode_atts(array(
        'class'     => ''
    ), $atts));
   return '<div class="col-md-8 '. $class .'">' . do_shortcode($content) . '</div>';
}
add_shortcode('two_third', 'shortcode_two_third');

function shortcode_three_fourth( $atts, $content = null ) {
	extract(shortcode_atts(array(
        'class'     => ''
    ), $atts));
   return '<div class="col-md-9 '. $class .'">' . do_shortcode($content) . '</div>';
}
add_shortcode('three_fourth', 'shortcode_three_fourth');

function shortcode_clear( $atts, $content = null) {
	extract(shortcode_atts(array(
        'class'     => ''
    ), $atts));
	return '<div class="clear"></div>';
}
add_shortcode('clear', 'shortcode_clear');

/*/////////////////////////////////////////////////////////////////
// MAP - Used with permission from DTBAKER
/////////////////////////////////////////////////////////////////*/

/**
 * Class dtbaker_Widget_Google_Map and dtbaker_Shortcode_Google_Map
 * Easily create a Google Map on any WordPress post/page (with an insert map button).
 * Easily create a Google Map in any Widget Area.
 * Author: dtbaker@gmail.com
 * Copyright 2014
 */


class dtbaker_Widget_Google_Map extends WP_Widget{
	/** constructor */
    function dtbaker_Widget_Google_Map() {
        $widget_ops = array(
            'description' => __('Use this to display a Google Map in a Widget Area.', 'boutique')
        );
        parent::__construct(false, __('Google Map', 'dtbaker'), $widget_ops );
    }
    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );
        $title = isset($instance['title']) ? $instance['title'] : '';
        // CHANGE COLOUR TO BLUE
        $before_widget = str_replace('orange', 'blue', $before_widget);
        echo $before_widget;
        echo $title ? ($before_title . $title . $after_title) : '';
	    // fire our shortcode below to generate map output.
	    $shortcode = dtbaker_Shortcode_Google_Map::get_instance();
	    echo $shortcode->dtbaker_shortcode_gmap($instance, isset($instance['innercontent']) ? $instance['innercontent'] : '');
        echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	    return array_merge($old_instance, $new_instance);
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $title = esc_attr(isset($instance['title']) ? $instance['title'] : '');
        ?>
	    <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'dtbaker'); ?>
	        <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title);?>">
	    </label></p>
		<?php
	    // pull the same fields in from our mce popup below:
	    $shortcode = dtbaker_Shortcode_Google_Map::get_instance();
	    foreach($shortcode->fields as $field){
		    ?>
	        <p><label for="<?php echo $this->get_field_id($field['name']); ?>"><?php echo $field['label']; ?>
	        <?php switch($field['mce_type']){
			    case 'listbox':
					$current_val = isset($instance[$field['name']])?$instance[$field['name']]:$field['default'];
					?>
				    <select name="<?php echo $this->get_field_name($field['name']); ?>">
					    <?php foreach($field['values'] as $key=>$val){ ?>
					    <option value="<?php echo esc_attr($key);?>"<?php echo esc_attr($current_val == $key ? ' selected':'');?>><?php echo $val;?></option>
					    <?php } ?>
				    </select>
				    <?php
			        break;
		        case 'textbox':
			        ?>
			        <input type="text" name="<?php echo esc_attr($this->get_field_name($field['name'])); ?>" value="<?php echo isset($instance[$field['name']])?$instance[$field['name']]:$field['default'];?>">
			        <?php
			        break;
		    } ?>
		    </label></p>
	        <?php
	    }
    }
}


class dtbaker_Shortcode_Google_Map{
    private static $instance = null;
    public static function get_instance() {
        if ( ! self::$instance )
            self::$instance = new self;
        return self::$instance;
    }

	public function init(){
		// comment this 'add_action' out to disable shortcode backend mce view feature
		add_action( 'admin_init', array( $this, 'init_plugin' ), 20 );
        add_shortcode('google_map', array($this,'dtbaker_shortcode_gmap'));
		add_action('widgets_init', create_function('', 'return register_widget("dtbaker_Widget_Google_Map");'));
	}
    public function init_plugin() {
        add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );
        add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ), 100 );
	    add_action( 'wp_ajax_dtbaker_mce_gmap_button', array( $this, 'wp_ajax_dtbaker_mce_gmap_button' ) );
	    if ( current_user_can('edit_posts') || current_user_can('edit_pages') ){
		    add_filter("mce_external_plugins", array($this, 'mce_plugin'));
		    add_filter("mce_buttons", array($this, 'mce_button'));
	    }
    }
	// front end shortcode displaying:
	public function dtbaker_shortcode_gmap($atts=array(), $innercontent='', $code='') {
		if(!isset($atts['address']))return;
		static $map_id=0;
		$map_id++;
		$defaults = array();
		foreach($this->fields as $field){
			$defaults[$field['name']] = $field['default'];
		}
	    extract(shortcode_atts($defaults, $atts));
	    ob_start();
		$template_file = locate_template('google_map.php');
		if(!$template_file) {
			?>
			<div id="googlemap<?php echo (int)$map_id; ?>" class="googlemap" style="height:<?php echo (int)$height; ?>px;"></div>
			<div class="clear"></div>
			<?php if ( $enlarge_button ) { ?>
				<div class="map_buttons">
					<a href="http://maps.google.com/maps?q=<?php echo htmlspecialchars( urlencode( $address ) ); ?>"
					   target="_blank"><?php _e( 'Enlarge Map', 'boutique' ); ?></a>
				</div>
			<?php } ?>
			<?php if ( $map_id == 1 ) { ?>
				<script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=false"></script>
			<?php } ?>
			<script type="text/javascript">
				(function ($) {
					var geocoder;
					var map;
					var query = "<?php echo esc_js(addcslashes($address,'"'));?>";
					function initialize() {
						geocoder = new google.maps.Geocoder();
						var myOptions = {
							zoom: <?php echo (int)$zoom;?>,
							scrollwheel: false,
							/*styles: [
								{"featureType": "water", "stylers": [{"visibility": "on"}, {"color": "#acbcc9"}]},
								{"featureType": "landscape", "stylers": [{"color": "#f2e5d4"}]},
								{
									"featureType": "road.highway",
									"elementType": "geometry",
									"stylers": [{"color": "#c5c6c6"}]
								},
								{
									"featureType": "road.arterial",
									"elementType": "geometry",
									"stylers": [{"color": "#e4d7c6"}]
								},
								{
									"featureType": "road.local",
									"elementType": "geometry",
									"stylers": [{"color": "#fbfaf7"}]
								},
								{
									"featureType": "poi.park",
									"elementType": "geometry",
									"stylers": [{"color": "#c5dac6"}]
								},
								{"featureType": "administrative", "stylers": [{"visibility": "on"}, {"lightness": 33}]},
								{
									"featureType": "poi.park",
									"elementType": "labels",
									"stylers": [{"visibility": "on"}, {"lightness": 20}]
								},
								{"featureType": "road", "stylers": [{"lightness": 20}]}
							],*/
							controls: {
								map_type: {
									type: ['roadmap', 'satellite', 'hybrid'],
									position: 'top_right',
									style: 'dropdown_menu'
								},
								overview: {opened: false},
								pan: false,
								rotate: false,
								scale: false,
								street_view: {position: 'top_right'},
								zoom: {
									position: 'top_left',
									style: 'large'
								}
							},
							mapTypeId: google.maps.MapTypeId.ROADMAP
						};
						map = new google.maps.Map(document.getElementById("googlemap<?php echo (int)$map_id;?>"), myOptions);
						codeAddress();
					}

					function codeAddress() {
						var address = query;
						geocoder.geocode({'address': address}, function (results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
								var marker = new google.maps.Marker({
									map: map,
									position: results[0].geometry.location
								});
								<?php if(strlen($innercontent)){ ?>
								var infowindow = new google.maps.InfoWindow({
									content: unescape("<?php echo str_replace('+',' ',(preg_replace('/\s+/',' ',addcslashes($innercontent,'"'))));?>")
								});
								google.maps.event.addListener(marker, 'click', function () {
									infowindow.open(map, marker);
								});
								infowindow.open(map, marker);
								<?php } ?>
								map.setCenter(marker.getPosition());
								setTimeout(function () {
									map.panBy(0, -50);
								}, 10);
							} else {
								alert("Geocode was not successful for the following reason: " + status);
							}
						});
					}

					$(function () {
						initialize();
					});
				}(jQuery));
			</script>
		<?php
		}else{
			include($template_file);
		}
	    return preg_replace("#\s+#", ' ', ob_get_clean());
	}

	public function wp_ajax_dtbaker_mce_gmap_button(){
		header("Content-type: text/javascript");
		?>
		( function() {
		    tinymce.PluginManager.add( 'dtbaker_mce_gmap', function( editor, url ) {
		        editor.addButton( 'dtbaker_mce_gmap_button', {
		            text: false,
		            icon: 'icon dashicons-location-alt',
		            onclick: function() {
		                wp.mce.google_map.popupwindow(editor);
		            }
		        } );
		    } );
		} )();
		<?php
		die();
	}
	public function mce_plugin($plugin_array){
		$plugin_array['dtbaker_mce_gmap'] = admin_url('admin-ajax.php?action=dtbaker_mce_gmap_button');
		return $plugin_array;
	}
	public function mce_button($buttons){
        array_push($buttons, 'dtbaker_mce_gmap_button');
		return $buttons;
	}
    /**
     * Outputs the view inside the wordpress editor.
     */
    public function print_media_templates() {
        if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
            return;
        ?>
        <script type="text/html" id="tmpl-editor-boutique-gmap">
			<div class="googlemap_placeholder" style="height:{{ data.height }}px;"><span>Google Map Will Display Here: <br/> {{ data.address }} <br/> {{ data.innercontent }}</span></div>
		</script>
	    <style type="text/css">
		    i.mce-i-icon {
				font: 400 20px/1 dashicons;
				padding: 0;
				vertical-align: top;
				speak: none;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				margin-left: -2px;
				padding-right: 2px
			}
	    </style>
        <?php
    }
    public function admin_print_footer_scripts() {
        if ( ! isset( get_current_screen()->id ) || get_current_screen()->base != 'post' )
            return;
        ?>
	    <script type="text/javascript">
		    (function($){
			    var media = wp.media, shortcode_string = 'google_map';
			    wp.mce = wp.mce || {};
			    wp.mce.google_map = {
				    shortcode_data: {},
					View: {
						template: media.template( 'editor-boutique-gmap' ),
						postID: $('#post_ID').val(),
						initialize: function( options ) {
							this.shortcode = options.shortcode;
							wp.mce.google_map.shortcode_data = this.shortcode;

						},
						getHtml: function() {
							var options = this.shortcode.attrs.named;
							options['innercontent'] = this.shortcode.content;
							return this.template(options);
						}
					},
				    edit: function( node ) {
						var data = window.decodeURIComponent( $( node ).attr('data-wpview-text') );
					    console.debug(this);
					    var values = this.shortcode_data.attrs.named;
						values['innercontent'] = this.shortcode_data.content;
					    console.log(values);

					    wp.mce.google_map.popupwindow(tinyMCE.activeEditor, values);
						//$( node ).attr( 'data-wpview-text', window.encodeURIComponent( shortcode ) );
					},
				    // this is called from our tinymce plugin, also can call from our "edit" function above
				    // wp.mce.google_map.popupwindow(tinyMCE.activeEditor, "bird");
				    popupwindow: function(editor, values, onsubmit_callback){
					    if(!values)values={};
					    if(typeof onsubmit_callback != 'function'){
						    onsubmit_callback = function( e ) {
		                        // Insert content when the window form is submitted (this also replaces during edit, handy!)
							    var s = '[' + shortcode_string;
							    for(var i in e.data){
								    if(e.data.hasOwnProperty(i) && i != 'innercontent'){
									    s += ' ' + i + '="' + e.data[i] + '"';
								    }
							    }
							    s += ']';
							    if(typeof e.data.innercontent != 'undefined'){
								    s += e.data.innercontent;
								    s += '[/' + shortcode_string + ']';
							    }
		                        editor.insertContent( s );
		                    };
					    }
		                editor.windowManager.open( {
		                    title: 'Google Map',
		                    body: [<?php
			                    // build array based on our $fields
			                    $js_fields = array();
			                    $field_count = 0;
			                    foreach($this->fields as $field){
			                        ?>
			                        {
				                        type: '<?php echo esc_js($field['mce_type']);?>',
				                        name: '<?php echo esc_js($field['name']);?>',
				                        label: '<?php echo esc_js($field['label']);?>',
					                    value: typeof values['<?php echo esc_js($field['name']);?>'] != 'undefined' ? values['<?php echo esc_js($field['name']);?>'] : '<?php echo $field['default'];?>'
				                        <?php if(isset($field['values']) && is_array($field['values'])){
				                            $values = array();
				                            foreach($field['values'] as $key=>$val){
				                                $values[] = array(
					                                'text' => $val,
					                                'value' => ".".$key,
				                                );
				                            }
				                            ?>
				                            ,values: <?php echo json_encode($values);?>
				                        <?php } ?>
			                        }
			                        <?php
			                        $field_count++;
			                        echo ($field_count<count($this->fields)) ? ',' : '';
			                    }
			                    ?>
		                    ],
		                    onsubmit: onsubmit_callback
		                } );
				    }
				};
			    wp.mce.views.register( shortcode_string, wp.mce.google_map );
			}(jQuery));
	    </script>

        <?php
    }

	public $fields = array(
		array(
			'name' => 'address',
			'mce_type' => 'textbox',
			'label' => 'Address',
			'default' => 'Sydney, Australia',
		),
		array(
			'name' => 'height',
			'mce_type' => 'textbox',
			'label' => 'Height',
			'default' => '400',
		),
		array(
			'name' => 'zoom',
			'mce_type' => 'textbox',
			'label' => 'Map Zoom (1-20)',
			'default' => '15',
		),
		array(
			'name' => 'enlarge_button',
			'mce_type' => 'listbox',
			'label' => 'Enlarge Button',
			'default' => '1',
			'values' => array(
				1 => 'Yes',
				0 => 'No',
			),
		),
		array(
			'name' => 'innercontent',
			'mce_type' => 'textbox',
			'label' => 'Popup',
			'default' => '',
		),
	);
}

dtbaker_Shortcode_Google_Map::get_instance()->init();



/*////////////////////////////////////////////////////////////////
// RECENT POSTS
/////////////////////////////////////////////////////////////////*/

add_shortcode('recent-posts', 'unf_recent_posts');
function unf_recent_posts($atts, $cont = null) {
    extract(shortcode_atts(array(
        'title'     => '',
        'pause'     => '',
    ), $atts));
	$str = "";
	$str .= '
	<div class="unf-recent-posts ">
		';
		if($title){$str .= '<h3 class="recent-post-title">'.$title.'</h3>';};
		$str .= '
		<div class="recent-post-pagination hidden-xs"></div>
		<div id="recent-post-loop" class="swiper-container swiper-container-recent">
			<div class="swiper-wrapper">';

			$formats = new WP_Query( array(
				'post_type' => 'post', // if the post type is post
				'posts_per_page' => 3,
    			'tax_query' => array(
			        array(
			            'taxonomy' => 'post_format',
			            'field' => 'slug',
			            'terms' => array( 'post-format-aside', 'post-format-quote', 'post-format-video', 'post-format-link', 'post-format-chat', 'post-format-image' ),
			            'operator' => 'NOT IN'
			        )
			    )
			));

			if( $formats->have_posts() ) : while( $formats->have_posts() ) : $formats->the_post();
		    $str .= '
		    	<div class="swiper-slide">
					<div class="news-item row clearfix">';
						if ( has_post_thumbnail() ) {
						$str .='
						<div class="col-sm-3 column">
							<div class="post-image">
								<a href="'.get_permalink().'">'. get_the_post_thumbnail() .'</a>
							</div>
						</div>';
						};
						$str .= '
						<div class="';
					    if ( has_post_thumbnail() ) {
					    	$str .=	'col-sm-9';
					    } else {
							$str .=	'col-sm-12';
					    };

					    $str .= ' column">
						    	<h4><a href="'.get_permalink().'">'.get_the_title().'</a></h4>

						    	<p class="byline vcard post-meta">
									<time class="updated" datetime="'.get_the_time('Y-m-j').'">'.get_the_time(get_option('date_format')).'
									</time>

									<i class="icon icon-star"></i>
									<span class="cats">'.get_the_category_list(', ').'
									</span>
									<span class="sticky-ind pull-right">
										<i class="icon icon-pin"></i>
									</span>
								</p>';

						if(strlen(get_the_excerpt()) >= 130){
							$str .= '<span class="post-exc">'.substr(get_the_excerpt(), 0,130).'...</span>';
						} else {
							$str .= '<span class="post-exc">'.substr(get_the_excerpt(), 0,130).'</span>';
						}
						$str .= '</div>
					</div>
				</div>
		    ';
		    endwhile;
			endif;
			wp_reset_query();
			$str .= '
			</div>
		</div>
	</div>';
	$str.="

<script>
jQuery(document).ready(function($) {
'use strict';
	$(function(){
			var mySwiper = new Swiper ('.swiper-container-recent', {
		    pagination: '.recent-post-pagination',
		    keyboardControl: true,
		    paginationClickable: true,
		    autoplay:5000,
		    speed:500,
		    mode:'horizontal',
		    loop: true,
		    calculateHeight: true,
		});
	})
});
</script>
";

   return $str;
}



/*////////////////////////////////////////////////////////////////
// FIX Spacing
/////////////////////////////////////////////////////////////////*/

function wpex_fix_shortcodes($content){
    $array = array (
        '<p>[' => '[',
        ']</p>' => ']',
        ']<br />' => ']'
    );

    $content = strtr($content, $array);
    return $content;
}
add_filter('the_content', 'wpex_fix_shortcodes');