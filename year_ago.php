<?php
/* 
    Plugin Name: A Year Ago Today 
    Plugin URI: http://www.jerimiannwalker.com
    Description: This plugin displays links to the most popular post or posts from one year ago on the sidebar. Popularity is determined by comment count.
    Author: Jerimi 
    Version: 1.0.2
    Author URI: http://www.jerimiannwalker.com/
    License: GPLv2 or later
    */


add_action('widgets_init', 'jaw_year_ago_register_widgets');
 
function jaw_year_ago_register_widgets() {  
  register_widget('jaw_year_ago_my_info');
} 

class jaw_year_ago_my_info extends WP_Widget {
	function jaw_year_ago_my_info(){
		$widget_ops=array(
		'classname' => 'jaw_year_ago_widget_class',
		'description' => 'This plugin displays links to the most popular post or posts from one year ago on the sidebar.'
		);
		$this ->WP_Widget('jaw_year_ago_my_info', 'A Year Ago Today', $widget_ops);
	}
	
	function form($instance){
		$defaults = array('title' => 'A Year Ago Today', 'number_of_posts' => 1);
		$instance = wp_parse_args( (array) $instance, $defaults);
		$title=$instance['title'];
		$number_of_posts=$instance['number_of_posts'];
		?>
		<p>Title: <input class="widefat" name="<?php echo $this -> get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p>Number of Posts: <input class="widefat" name="<?php echo $this -> get_field_name ('number_of_posts'); ?>" type="text" value="<?php echo esc_attr($number_of_posts); ?>" /></p>
		<?php }
		
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title']= strip_tags( $new_instance['title']);
		echo is_int($new_instance['number_of_posts']);
		if ((int) $new_instance['number_of_posts'] == $new_instance['number_of_posts'] && (int) $new_instance['number_of_posts'] > 0){
			$instance['number_of_posts'] = $new_instance['number_of_posts'];
		}
		else {
			$instance['number_of_posts'] = 1;
		}
		return $instance;
	}
	
	function widget($args, $instance){
		extract($args);
		$title=apply_filters( 'widget_title', $instance['title']);
		
		echo $before_widget;
		if (empty($instance['number_of_posts'])){
			$number_of_posts=1;
		}
		else {
			$number_of_posts=$instance['number_of_posts'];			
		}
		$current_month = date_i18n('m'); 
		$current_day = date_i18n('j');
		$lastyear = date('Y')-1; 
		$custom_loop = new WP_Query();
		$custom_loop -> query('showposts=' . $number_of_posts . '&year=' . $lastyear . '&monthnum=' . $current_month . '&day=' . $current_day . '&orderby=comment_count');
		if ( $custom_loop->have_posts() ) {?>
		<?php echo $before_title . $title . $after_title; ?>
		<ul>
		<?php 
		while ( $custom_loop->have_posts() ) {
			$custom_loop->the_post();
			echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
		}
		wp_reset_query();
		echo '</ul>';
		}
		echo $after_widget;
	}

}