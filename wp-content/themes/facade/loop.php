<?php
/**
 * The main posts loop.
 *
 */
?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php PC_Hooks::pc_pre_post_meta(); /* Framework hook wrapper */ ?>

		<div class="post-content">

			<?php PC_UTILITY::check_empty_post_title( get_the_ID() ); ?>

			<div class="post-meta">
				<?php PC_Hooks::pc_post_meta(); /* Framework hook wrapper */ ?>
				<p>
					<span class="author">By <?php the_author_posts_link(); ?></span><span class="post-date"> on <?php the_time('F jS, Y'); ?></span><span class="categories"> in <?php the_category(', '); ?></span> <?php the_tags( '<span class="tags">tags: ', ', ', '</span>'); ?>

					<?php global $post; ?>
					<?php if ( 'open' == $post->comment_status ) : ?>
						<span class="comments">| <?php comments_popup_link( __( 'Leave a Comment', 'presscoders' ), __( '1 Comment', 'presscoders' ), __( '% Comments', 'presscoders' ), '', ''); ?></span>
					<?php endif; ?>
				</p>
				<?php PC_Hooks::pc_after_post_meta(); /* Framework hook wrapper */ ?>
			</div><!-- .post-meta -->

			<?php if ( has_post_thumbnail() ) : ?>
			<div class="post-thumb">
			  <?php
			  $post_id = get_the_ID();
			  echo PC_Utility::get_responsive_slider_image( $post_id, 'post-thumbnail' ); /* Show post thumbnail image, if one exists. */
			  ?>
			</div> <!-- .post-thumb -->
			<?php endif; ?>

			<?php
				global $more;
				$more = 0;
				the_content( ' '.__( 'Read More', 'presscoders' ) );
				wp_link_pages( array( 'before' => '<div class="page-link">', 'after' => '</div>' ) );
			?>
		</div> <!-- .post-content -->

	</div> <!-- .post -->

<?php endwhile; // end of the loop. ?>

<?php if( get_next_posts_link() || get_previous_posts_link() ) : ?>
<div class="navigation">
	<div class="alignleft"><?php next_posts_link( __( '&laquo; Older Posts', 'presscoders' ) ) ?></div>
	<div class="alignright"><?php previous_posts_link( __( 'Newer Posts &raquo;', 'presscoders' ) ) ?></div>
</div>
<?php endif; ?>
