<?php
/**
 * The Template for displaying all single campaigns.
 *
 * @package Modified Fundify for Palootoo
 * @since Palootoo 1.5
 */

global $campaign;

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); $campaign = atcf_get_campaign( $post->ID ); ?>

		<?php locate_template( array( 'campaign/title.php' ), true ); ?>
		
		<div id="content" class="post-details">
			<div class="container">
				
				<?php do_action( 'atcf_campaign_before', $campaign ); ?>
				
				<?php locate_template( array( 'searchform-campaign.php' ), true ); ?>
				<?php locate_template( array( 'campaign/campaign-sort-tabs.php' ), true ); ?>

				<?php locate_template( array( 'campaign/project-details.php' ), true ); ?>

				<aside id="sidebar">
					<?php locate_template( array( 'campaign/location-info.php' ), true ); ?>
					<?php locate_template( array( 'campaign/author-info.php' ), true ); ?>

					
				</aside>

				<div id="main-content">
					<?php locate_template( array( 'campaign/meta.php' ), true ); ?>
					<?php locate_template( array( 'campaign/share.php' ), true ); ?>

					<div class="entry-content inner campaign-tabs">
						<div id="description">
							<?php the_content(); ?>
						</div>

						<?php locate_template( array( 'campaign/updates.php' ), true ); ?>

						<?php comments_template(); ?>

						<?php locate_template( array( 'campaign/backers.php' ), true ); ?>
					</div>
				</div>

			</div>
		</div>

	<?php endwhile; ?>

<?php get_footer(); ?>
