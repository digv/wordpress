<?php
function remove_more_link_scroll( $link ) {
	$link = preg_replace( '|#more-[0-9]+|', '', $link );
	return $link;
}
add_filter( 'the_content_more_link', 'remove_more_link_scroll' );
 
/* Load Press Coders theme framework class. */
if( file_exists( get_template_directory().'/api/classes/api.php' ) ) {
	require_once( get_template_directory().'/api/classes/api.php' );
}

if( !class_exists( 'PC_Main_Theme' ) ) :

class PC_Main_Theme extends PC_Framework {

	public function __construct($theme_name) {
		/* Call parent construtor manually to make both constructors fire. */
		parent::__construct($theme_name);

		/* Add theme support for framework features. */
		add_action( 'after_setup_theme', array( &$this, 'theme_support' ) );
	}

	/* Add support for theme features. */
	public function theme_support() {

		/** WORDPRESS BUILT-IN SUPPORTED THEME FEATURES **/

		/* Note: Use '%2$s' instead of '%s' to reference a child theme image. */
		$bg_defaults = array(
			'default-color' => '',
			'default-image' => PC_THEME_ROOT_URI.'/images/vertical-stripe-white.png'
		);

		add_theme_support( 'automatic-feed-links' );			/* Add posts and comments RSS feed links to head. */
		add_theme_support( 'post-thumbnails' );					/* Use the post thumbnails feature. */
		add_theme_support( 'custom-background', $bg_defaults );	/* A simple uploader to change the site background image. */

		add_editor_style();								/* Post/page editor style sheet to match site styles. */

		/** FRAMEWORK SUPPORTED THEME FEATURES **/

		add_theme_support( 'pc-custom-header' );	/* Wrapper for the WordPress Custom Header feature. */
		add_theme_support( 'theme-options-page' );	/* Display a theme options page. */
		add_theme_support( 'hf-code-insert' );		/* Custom header/footer code insert, plus flexible footer links. */
		add_theme_support( 'shortcodes' );			/* Include all framework shortcodes. */
		add_theme_support( 'fancybox' );			/* Include Fancybox lightbox. */
		add_theme_support( 'superfish' );			/* Load Superfish jQuery menu. */
		add_theme_support( 'modernizr' );			/* Load Modernizr library. */
		add_theme_support( 'fitvids' );				/* Responsive video resizing. */
		add_theme_support( 'homepage-bar' );		/* Adds the custom homepage bar to the theme. */
		
		/* Include specified framework widgets. */
		add_theme_support( 'pc_widgets', 'twitter-feed',
											'theme-recent-posts',
											'blog-style-recent-posts',
											'color-scheme-switcher',
											'info-box-font-icons'
		);

		/* Add array of menu location labels, or leave 2nd parameter blank for a single default menu. */
		add_theme_support( 'custom-menus', array( 'Primary Navigation', 'Top Menu' ) );

		/* Add array of theme color schemes. */
		add_theme_support( 'color-schemes',  array( __( 'Default', 'presscoders' ) => 'default',
													__( 'Blue', 'presscoders' ) => 'blue' ) );

		/* ADDITIONAL THEME FEATURES */

		/* Default thumbnail size for post thumbnails. */
		set_post_thumbnail_size( 650, 200, true );

		/* Add an extra custom thumbnail for the single Portfolio page carousel thumb size. */
		add_image_size( 'single-pf-carousel', 100, 100, true );
	}

} /* End class definition */

/* Create theme class instance */
global $pc_theme_object;
$pc_theme_object = new PC_Main_Theme( 'Facade' );

endif; /* Endif class definition and instantiation. */

?>
