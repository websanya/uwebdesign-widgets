<?php
/**
 * Plugin Name: uWebDesign Widgets
 * Plugin URI: https://github.com/websanya/uwebdesign-widgets
 * Description: Плагин с виджетами для комьюнити сайта uWebDesign.
 * Version: 1.1.4
 * Author: Alexander Goncharov
 * Author URI: https://websanya.ru
 * GitHub Plugin URI: https://github.com/websanya/uwebdesign-widgets
 * GitHub Branch: master
 */

/**
 * Creating the widget.
 */
class uwd_widget_banner extends WP_Widget {

	function __construct() {
		parent::__construct(
			'uwd_banner_widget', //* Widget ID.
			'[uWebDesign] Баннер в сайдбар', //* Widget Title.
			array( 'description' => 'Случайным образом выбирает баннер и отображает его', ) //* Widget Description.
		);
	}

	//* Creating widget front-end. This is where the action happens.
	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', $instance['title'] );

		//* Before and after widget arguments are defined by themes.
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} else {
			$args['before_title'] = '<h5 class="widget-title widgettitle screen-reader-text">';
			$no_title             = true;
		}

		$query_args = array(
			'post_type'      => 'banner',
			'orderby'        => 'rand',
			'posts_per_page' => 1,
		);

		$query = new WP_Query( $query_args );

		while ( $query->have_posts() ) : $query->the_post();
			if ( ! empty( $no_title ) ) {
				echo $args['before_title'] . get_the_title() . $args['after_title'];
			}
			?>
			<a href="<?php the_field( 'banner_url' ); ?>" rel="nofollow">
				<img width="770" height="770" src="<?php the_field( 'banner_img_sidebar' ); ?>"
				     alt="<?php the_title(); ?>">
			</a>
			<?php
		endwhile;

		//* Reset Post Data.
		wp_reset_postdata();

		echo $args['after_widget'];

	}

	//* Widget Backend.
	public function form( $instance ) {

		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = '';
		}
		//* Widget admin form.
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo 'Заголовок'; ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
			       value="<?php echo esc_attr( $title ); ?>"/>
		</p>
		<?php
	}

	//* Updating widget replacing old instances with new.
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

}

/**
 * Creating the widget.
 */
class uwd_widget_themes extends WP_Widget {

	function __construct() {
		parent::__construct(
			'uwd_widget_themes', //* Widget ID.
			'[uWebDesign] Темы к подкасту в сайдбар', //* Widget Title.
			array( 'description' => 'Темы к крайнему подкасту в сайдбар', ) //* Widget Description.
		);
	}

	//* Creating widget front-end. This is where the action happens.
	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', $instance['title'] );

		$query_args = array(
			'post_type'      => 'post',
			'posts_per_page' => 1,
			'tax_query'      => array(
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => 'podcast-topics',
				),
			),
		);

		$query = new WP_Query( $query_args );
		$query->the_post();

		//* Before and after widget arguments are defined by themes.
		echo $args['before_widget'];
		$comments = ' <small class="uwd-widget-meta">(' . russian_comments( get_comments_number( get_the_ID() ), array(
				'комментарий',
				'комментария',
				'комментариев',
			) ) . ')</small>';
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $comments . $args['after_title'];
		} else {
			echo $args['before_title'] . 'Темы к ближайшему подкасту' . ' ' . $comments . ' ' . $args['after_title'];
		}

			?>
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'medium' ); ?>
			</a>
			<?php the_content(); ?>
			<p>
				<a href="<?php the_permalink(); ?>#respond">Предложить тему &rarr;</a>
			</p>
			<?php

		//* Reset Post Data.
		wp_reset_postdata();

		echo $args['after_widget'];

	}

	//* Widget Backend.
	public function form( $instance ) {

		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = '';
		}
		//* Widget admin form.
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo 'Заголовок'; ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
			       value="<?php echo esc_attr( $title ); ?>"/>
		</p>
		<?php
	}

	//* Updating widget replacing old instances with new.
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

}

//* Register and load the widget.
add_action( 'widgets_init', 'wpb_load_widget' );
function wpb_load_widget() {
	register_widget( 'uwd_widget_banner' );
	register_widget( 'uwd_widget_themes' );
}