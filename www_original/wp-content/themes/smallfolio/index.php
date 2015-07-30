<?php get_header(); ?>


<!--Theme options script-->
<?php
global $options;
foreach ($options as $value) {
	if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}?>



	<!--Content-->
	<div id="main">
	
	<div id="tagline">
		<h3 class="myclass">
		<?php if (is_date()) : ?>
		Archive: <?php the_time('F, Y'); ?>
		<?php else: ?>
		<?php single_cat_title(); ?>
		<?php endif; ?>
		</h3>
		</h3>
	</div>
			

	
			
	
	
	<?php 
	$catid = get_cat_id($bmb_portfolio_id);
	if (is_category_or_sub($catid)) { ?>
	<?php load_template( TEMPLATEPATH . '/portfolio.php'); ?>
			
		
			
		
	    <? } else { ?>
		
		<!--Content left part-->
		<div id="left_sidebar">
			<?php get_sidebar(); ?>
		</div>
		

		<!--Content right part-->
		<div id="content_blog">
			
			<!--Blog items-->
						<div id="blog_items">
							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
							<div class="blog_item">
									
								<?php if ( has_post_thumbnail() ) { ?>
								<a href="<?php the_permalink() ?>"><?php the_post_thumbnail(); ?></a>
								<?php } ?>
								
								
								<div class="blog_item_text">
									<h3><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
									<div class="storycontent">
										<?php the_excerpt(); ?>
									</div>
			
									<div class="blog_meta">
										<?php the_time('F jS, Y') ?> | <?php comments_popup_link(__('Comments (0)'), __('Comments (1)'), __('Comments (%)')); ?>
									</div>
								</div>
									
								<div class="clearboth"></div>
							</div>
						
							<?php comments_template(); // Get wp-comments.php template ?>
							<?php endwhile; else: ?>
							<p><?php _e('<h2>Sorry, no posts matched your criteria</h2>You might try to use our Site search <br>or try to browse the site with the main navigation menu'); ?></p>
							<?php endif; ?>
							<br />
							<div id="footer_nav"><?php posts_nav_link(' &nbsp;&nbsp;&nbsp;&nbsp; ', __('&laquo; Newer Posts'), __('Older Posts &raquo;')); ?></div>
						</div>
			
				
		</div>
		
		
		<!--Clearboth for IE-->
		<div id="clearboth"></div>
				
		<?php } ?>
			
	</div>
	<!--End Content-->

<?php get_footer(); ?>