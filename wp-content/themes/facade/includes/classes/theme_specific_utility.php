<?php

/**
 * Theme specific framework utility class.
 *
 * Contains theme specific general helper functions which are all static, so they can
 * be referenced without having to instantiate the class.
 *
 * @since 0.1.0
 */

/* Class may have already been defined in a child theme. */
if ( !class_exists('PC_TS_Utility') ) :

class PC_TS_Utility {

	/**
	 * PC_TS_Utility class constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		/* Setup class. */
		$this->setup();

		/* Add theme customizer color picker options for the current theme. */
		add_action( 'customize_register', array( $this, 'theme_customizer_register_color_styles' ) );
		add_action( 'wp_head', array( $this, 'add_theme_customizer_colors' ) );
	}

	/**
	 * Setup class.
	 *
	 * @since 0.1.0
	 */
	public function setup() {

	}

	/**
	 * Renders demo content upon successful theme activation.
	 *
	 * @since 0.1.0
	 */
	public static function theme_demo_default_content() {

		/* Add default widgets. */
		self::add_default_demo_widgets();
	}

	/**
	 * Add default widgets upon successful theme activation.
	 *
	 * @since 0.1.0
	 */
	public static function add_default_demo_widgets() {

	}

	/**
	 * Theme specific 'Blog Style Recent Posts' widget post loop.
	 *
	 * @since 0.1.0
	 */
    public static function blog_style_recent_posts_widget_loop( $args = array() ) {
	?>

		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
			<div class="post-content">

			<?php $post_title = get_the_title(); ?>
			<?php if ( !empty($post_title) ) : ?>
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			<?php else : ?>
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark">(no title)</a></h2>
			<?php endif; ?>

			<div class="post-meta">
			    <?php PC_Hooks::pc_post_meta(); /* Framework hook wrapper */ ?>
			    <p>
			    	<span class="author">By <?php the_author_posts_link(); ?></span><span class="post-date"> on <?php the_time('F jS, Y'); ?></span><span class="categories"> in <?php the_category(', '); ?></span> <?php the_tags( '<span class="tags">tags: ', ', ', '</span>'); ?>
			
			    	<?php global $post; ?>
			    	<?php if ( 'open' == $post->comment_status ) : ?>
						<span class="comments">| <?php comments_popup_link( __( 'Leave a Comment', 'presscoders' ), __( '1 Comment', 'presscoders' ), __( '% Comments', 'presscoders' ), '', ''); ?></span>
			    	<?php endif; ?>
			
			    </p>
			</div><!-- .post-meta -->

			<?php if( $args['spt'] ) { ?>

			<?php if ( has_post_thumbnail() ) : ?>
            <div class="post-thumb">
              <?php
			  $post_id = get_the_ID();
              echo PC_Utility::get_responsive_slider_image( $post_id, 'post-thumbnail' ); /* Show post thumbnail image, if one exists. */
              ?>
            </div> <!-- .post-thumb -->
			<?php endif; ?>

			<?php } ?>

                <?php
                    global $more;
                    $more = 0;
                    the_content( ' '.$args['read_more'] );
					wp_link_pages( array( 'before' => '<div class="page-link">', 'after' => '</div>' ) );
                ?>
            </div> <!-- .post-content -->

        </div> <!-- .post -->

    <?php
    }

	/**
	 * Theme specific portfolio slider rendering.
	 *
	 * @since 0.1.0
	 */
    public static function custom_portfolio_slider_li_structure( $args = array() ) {
	?>

		<li>
			<?php
				echo '<div class="portfolio-slide-content">';
				if(!empty($args['featured_image'])) echo $args['featured_image'];
				echo '<div class="overlay portfolio-slider-'.$args['post_id'].'">';
				echo $args['title']; //if($args['show_title'] == 0) echo $args['title'];
				if(!empty($args['content'])) echo '<p class="slide-content">'.PC_Utility::n_words( wp_strip_all_tags( $args['content']), 165 ).'</p>';
				echo '</div>';
				echo '</div>';
			?>
		</li>

    <?php
    }

	/**
	 * Add theme customizer defaults specific to the theme.
	 *
	 * @since 0.1.0
	 */
	public static function theme_specific_customizer_defaults() {

		global $pc_customizer_defaults;

		$color_picker_defaults = array (
			'pc-nav-links-color'			=> '',
			'pc-nav-hover-color'			=> '',
			'pc-header-footer-text-color'	=> ''
		);

		$pc_customizer_defaults = array_merge ( $pc_customizer_defaults, $color_picker_defaults );
	}

	/**
	 * Add color picker options in the theme customizer.
	 *
	 * @since 0.1.0
	 */
	public function theme_customizer_register_color_styles($wp_customize) {

		global $pc_customizer_defaults;

		/* Add individual theme customizer color picker options here. */
		$customizer_options = array(
			array(	'section'		=>	'colors',
					'section-label'	=>	__('Colors', 'presscoders'),
					'color-pickers'	=>	array(
						array(	'label'			=>	__( 'Header/Footer Text Color', 'presscoders' ),
								'name'			=>	'pc-header-footer-text-color',
								'transport'		=>	'postMessage',
								'priority'		=>	'2'
						),
						array(	'label'			=>	__( 'Header/Footer Link Color', 'presscoders' ),
								'name'			=>	'pc-nav-links-color',
								'transport'		=>	'postMessage',
								'priority'		=>	'4'
						),
						array(	'label'			=>	__( 'Header/Footer Link Hover Color', 'presscoders' ),
								'name'			=>	'pc-nav-hover-color',
								'transport'		=>	'refresh',
								'priority'		=>	'5'
						)
					)
			),
		);

		/* Get all currently registered sections. */
		$registered_sections = array_keys($wp_customize->sections());

		foreach( $customizer_options as $customizer_option ) {

			/* Check if section exists. Add it if not. */
			if( !in_array($customizer_option['section'], $registered_sections) ) {

				$wp_customize->add_section( $customizer_option['section'], array(
					'title'          => $customizer_option['section-label'],
					'priority'       => 35,
				) );
			}

			foreach( $customizer_option['color-pickers'] as $color_picker ) {

				/* Add setting. */
				$wp_customize->add_setting( $color_picker['name'], array(
					'default'        => $pc_customizer_defaults[$color_picker['name']],
					'transport'		 => $color_picker['transport']
				) );

				/* Add control. */
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $color_picker['name'], array(
					'label'    => $color_picker['label'],
					'section'  => $customizer_option['section'],
					'settings' => $color_picker['name'],
					'priority' => $color_picker['priority']
				) ) );
			}
		}

		if ( $wp_customize->is_preview() && ! is_admin() )
			add_action( 'wp_footer', array( $this, 'add_theme_specific_footer_js' ), 21);
	}

	/**
	 * Add theme specific JS to the previewer frame footer to alter HTML elements.
	 *
	 * @since 0.1.0
	 */
	public function add_theme_specific_footer_js() {

		global $customizer_css;

		?>
		<script type="text/javascript" defer="defer">
		( function( $ ){
		
		<?php
		foreach($customizer_css as $css) :

			if( is_array($css[1]) ) :
				foreach( $css[1] as $css_rule ) :
		?>
					wp.customize('<?php echo $css[0]; ?>',function( value ) {
						value.bind(function(to) {
							// Check if we need the !important attribute adding
							if( '<?php echo $css_rule[2]; ?>' == '') {
								$('<?php echo $css_rule[0]; ?>').css('<?php echo $css_rule[1]; ?>', to );
							}
							else {
								$('<?php echo $css_rule[0]; ?>').css('cssText', '<?php echo $css_rule[1]; ?>: ' + to + ' !important;' );
							}
						});
					});
		<?php
				endforeach;
			endif;

		endforeach;
		?>

		} )( jQuery )
		</script>
		<?php 
	}

	/**
	 * Add theme specific theme customizer color styles via 'wp_head' action hook.
	 *
	 * @since 0.1.0
	 */
	public function add_theme_customizer_colors() {

		global $pc_customizer_defaults;
		global $customizer_css;

		$color_picker_defaults = array (
			'pc-nav-links-color'			=> '',
			'pc-nav-hover-color'			=> '',
			'pc-header-footer-text-color'	=> ''
		);

		$customizer_css = array(
			array(	'pc-nav-links-color',
					array(
						array(	'.primary-menu .menu li a, #header-container nav .sub-menu li a, .primary-menu .menu li a, #site-title a, #before-content a, #before-content .slide-name, #header-widget-area a, footer a:link, footer a:visited, .secondary-menu li a',
								'color',
								'' ),
						array(	'#footer-widget-area .twtr-widget .twtr-tweet a, #footer-widget-area .twtr-widget .twtr-hd a, #body-container #footer-widget-area .twtr-widget h3, #body-container #footer-widget-area .twtr-widget h4 a',
								'color',
								' !important' )
					)
			),
			array(	'pc-nav-hover-color',
					array(
						array(	'#header-container a:hover, #header-container nav .sub-menu li a:hover, .secondary-menu a:hover, #before-content a:hover, footer a:hover, nav.primary-menu ul li.current_page_item > a, footer a:hover',
								'color',
								'' )
					)
			),
			array(	'pc-header-footer-text-color',
					array(
						array(	'#site-description, #header-widget-area, footer, #before-content, #footer-widget-area .twtr-widget .twtr-bd p',
								'color',
								' !important' )
					)
			)
		);

		$css_string = '';
		foreach( $customizer_css as $css ) {
			$cpt = $css[0]; /* Color picker name. */
			if( !get_theme_mod( $cpt ) ) /* If color picker option not set. */
				continue;
			$opt = get_theme_mod( $cpt, $pc_customizer_defaults[$cpt] ); /* Color picker options value. */
			if( !empty( $opt ) && $opt != "#" ) {
				if( is_array($css[1]) ) {
					foreach( $css[1] as $css_rule ) {
						$css_string .= $css_rule[0]." { ".$css_rule[1].": ".$opt.$css_rule[2]."; }\r\n";
					}
				}
			}
		}
if( !empty($css_string) ) {
echo "\r\n";
echo "<!-- ".PC_THEME_NAME." theme customizer color styles -->";
echo "<style type=\"text/css\">";
echo $css_string;
echo "</style>";
echo "\r\n";
}
	}

	/**
	 * Styles the custom header image displayed on the Appearance -> Header page.
	 *
	 * @since 0.1.0
	 */
	public static function admin_custom_header_page_style() {
	?>
		<style type="text/css">
		#pc-headimg img, .default-header img { border: 1px solid #dfdfdf; }
		</style>
	<?php
	}

	/**
	 * Outputs custom header image on the Appearance -> Header admin page.
	 * This callback overrides the default markup normally displayed there.
	 *
	 * @since 0.1.0
	 */
	public static function admin_custom_header_page_image() {

	$header_image = get_header_image();
	
	if ( ! empty( $header_image ) ) :

	?>
	<div id="pc-headimg">
		<img src="<?php echo esc_url( $header_image ); ?>" alt="" style="width:auto;" />
	</div>
	<?php
	
	else:

	?><p><em>(no image selected)</em></p><?php
	
	endif;
	}

	/**
	 * Styles the custom header image displayed on the Appearance -> Header page.
	 *
	 * @since 0.1.0
	 */
	public static function custom_content_slider_li_structure($args) {

		$featured_image = $args['featured_image'];
		$content = $args['content'];
		$title = $args['title'];

		// Don't render empty slides
		if( !(empty($featured_image) && empty($content) && empty($title)) ) :
		
		?>

		<li>
			<?php
			/* Don't render div.slide-media if no featured image. */
			if( !empty($featured_image) )
				echo '<div class="slide-media">'.$featured_image.'</div>';
			/* Don't render div.slide-content if no slide content. */
			if( !( empty($title) && empty($content) ) ) :
				echo '<div class="slide-content">';
				if(!empty($title)) echo $title;
				if(!empty($content)) echo $content;
				echo '</div>';
			endif;
			?>
		</li>

		<?php
		
		endif;

	}

	/**
	 * Add before content widget area in a custom location.
	 *
	 * The before content widget area is usually added to each page via PC_Hooks::pc_after_container().
	 * This method allows it to be placed on the page via another hook, and to change the .
	 *
	 * @since 0.1.0
	 */
	public function custom_front_page_before_content_location() {
		/* Display front page before content widgets if any defined in 'Front Page: Before Content' widget area. */
		add_action( 'pc_after_get_header', array( &$this, 'custom_front_page_before_content_render' ) );
	}

	/**
	 * Renders the custom before content widget area
	 *
	 * @since 0.1.0
	 */
	public function custom_front_page_before_content_render() {
        global $pc_is_front_page, $pc_home_page, $pc_page_on_front;

        if( $pc_is_front_page || ($pc_home_page && $pc_page_on_front == 0) ) {
            if ( is_active_sidebar( 'front-page-before-content-widget-area' ) ) :
                echo "<div id=\"before-content\">";
                echo "<div id=\"front-page-before-content-widget-area\" class=\"widget-area\">";
				dynamic_sidebar( 'front-page-before-content-widget-area' );
                echo "</div>";
				echo "</div>";
            endif;
        }
	}
}

endif;

?>