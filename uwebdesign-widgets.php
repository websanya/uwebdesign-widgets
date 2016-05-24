<?php
/**
 * Plugin Name: uWebDesign Widgets
 * Plugin URI: https://github.com/websanya/uwebdesign-widgets
 * Description: Плагин с виджетами для комьюнити сайта uWebDesign.
 * Version: 1.0.1
 * Author: Alexander Goncharov
 * Author URI: https://websanya.ru
 * GitHub Plugin URI: https://github.com/websanya/uwebdesign-widgets
 * GitHub Branch: master
 */

/**
 * Creating the widget.
 */
class uwd_widget extends WP_Widget {

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
		}

		$query_args = array(
			'post_type'      => 'banner',
			'orderby'        => 'rand',
			'posts_per_page' => 1,
		);

		$query = new WP_Query( $query_args );

		while ( $query->have_posts() ) : $query->the_post();
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

//* Register and load the widget.
add_action( 'widgets_init', 'wpb_load_widget' );
function wpb_load_widget() {
	register_widget( 'uwd_widget' );
}