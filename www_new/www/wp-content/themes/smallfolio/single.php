<?php get_header(); ?>

	<!--Content-->
	<div id="main">
	
		<!--Title-->
		<div id="tagline">
			<h3>
			<?php
			$category = get_the_category();
			$parent = get_cat_name($category[0]->category_parent);
			echo  $category[0]->cat_name;
			?>
			</h3>
		</div>
		
		<!--Content left part-->
		<div id="left_sidebar">
			<?php get_sidebar(); ?>
		</div>

		<!--Content right part-->
		<div id="content">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			<h2><?php the_title(); ?></h2>
			<?php the_content(); ?>
			<?php endwhile; endif; ?>
			
			<div id="comments_wrap">
			<?php comments_template(); ?>
			</div>
			
		</div>
		
		
		<!--Clearboth for IE-->
		<div id="clearboth"></div>
		
	
	</div>
	

<?php get_footer(); ?>