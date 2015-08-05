<?php
global $options;
foreach ($options as $value) {
	if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_settings( $value['id'] ); }
}?>


<?php get_header(); ?>

<div id="main">
	
	
	<div id="showcase">
	
	
	<?php switch ($bmb_slider) {
	case  "Nivo Slider":?>
		
		<style>
		.nivo-caption
		{
			visibility: hidden;
		}
		</style>
			
		<div id="slider">
			<?php 
			$get_showcase_id = get_cat_id($bmb_showcase_cat);
			query_posts('cat='.$get_showcase_id); ?>
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('slider'); ?></a>
			<?php endwhile; ?>
			<?php endif; ?>
			<?php wp_reset_query(); ?>
		</div>
	
	
	
		<?php break; ?>	
		<?php case "Coin Slider":?>
		
		<!--Coin Slider-->
		
		<div id="coin-slider">
			<?php 
			$get_showcase_id = get_cat_id($bmb_showcase_cat);
			query_posts('cat='.$get_showcase_id); ?>
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('slider'); ?></a>
			<?php endwhile; ?>
			<?php endif; ?>
			<?php wp_reset_query(); ?>
		</div>
				
		<?php break; ?>
		<?php }?>
	
	</div>
	
	
	<!--Tagline-->
	
	<div id="tagline">
		<h4><?php echo stripslashes($bmb_tagline);?></h4>
	</div>
	
	
	<!--3 Columns-->
	
	<div id="index_articles">
		<ul>
			<li class="left">
			<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar("Mainpage Left") ) : else : ?>
			Widget "Mainpage left" goes here
			<?php endif; ?>
			</li>
			
			
			<li class="center">
			<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar("Mainpage Middle") ) : else : ?>
			Widget "Mainpage middle" goes here
			<?php endif; ?>
			</li>
			
			
			<li class="right">
			<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar("Mainpage Right") ) : else : ?>
			Widget "Mainpage right" goes here
			<?php endif; ?>
			

			</li>
		</ul>
	</div>

	
</div>

<?php get_footer(); ?>