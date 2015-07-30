<div id="content_portfolio">

	<ul class="gallery">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?> 
		
		
		
		<li class="zoom">
			<?php if (get_post_meta($post->ID, 'youtube', true)) {
				$videourl = get_post_meta($post->ID, 'youtube', true);
				echo '<div class="zoom zoom_v"><a href="'.$videourl.'" title="'.get_the_title().'" class="youtube thumb">';
				the_post_thumbnail(portfolio);
				echo '</a></div>';
			} else if (get_post_meta($post->ID, 'vimeo', true)) {	
				$videourl = get_post_meta($post->ID, 'vimeo', true);
				echo '<div class="zoom zoom_v"><a href="'.$videourl.'" title="'.get_the_title().'" class="vimeo thumb">';
				the_post_thumbnail(portfolio);
				echo '</a></div>';
			} else {
				$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), array( 720,405 ), false, '' );
				echo '<div class="zoom"><a href="'.$src[0].'" class="thumb zoom" rel="group">';
				the_post_thumbnail(portfolio);
				echo '</a></div>';
			} ?>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		</li>
		

		
		<?php endwhile; else: ?>  
		
		<li>
			<h2>Woops...</h2>  
			<p>Sorry, no posts we're found.</p>  
		</li>
		
	<?php endif; ?>
	</ul>
	
	<div id="footer_nav"><?php posts_nav_link(' &nbsp;&nbsp;&nbsp;&nbsp; ', __('&laquo; Newer Posts'), __('Older Posts &raquo;')); ?></div>
	
</div>
			
		
			
		
	