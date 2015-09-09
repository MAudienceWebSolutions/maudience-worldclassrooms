<?php
/*
* Register the new widget classes here so that they show up in the widget list
*/
function load_widgets() {
	register_widget('LatestTweets');
	register_widget('ThemeWidgetLatestPost');
	register_widget('ThemeWidgetBanner');
	register_widget('ThemeWidgetTestimonial');
}
add_action('widgets_init', 'load_widgets');

/*
* Displays a block with latest tweets from particular user
*/
class LatestTweets extends ThemeWidgetBase {
	function LatestTweets() {
		$widget_opts = array(
			'classname' => 'theme-widget',
			'description' => 'Displays a block with your latest tweets'
		);
		$this->WP_Widget('theme-widget-latest-tweets', 'Latest Tweets', $widget_opts);
		$this->custom_fields = array(
			array(
				'name'=>'title',
				'type'=>'text',
				'title'=>'Title',
				'default'=>''
			),
			array(
				'name'=>'username',
				'type'=>'text',
				'title'=>'Username',
				'default'=>''
			),
			array(
				'name'=>'count',
				'type'=>'text',
				'title'=>'Number of Tweets to show',
				'default'=>'5'
			),
		);
	}
	
	/*
	* Called when rendering the widget in the front-end
	*/
	function front_end($args, $instance) {
		extract($args);
		$tweets = TwitterHelper::get_tweets($instance['username'], $instance['count']);
		if (!empty($tweets)) {
			if ($instance['title'])
				echo $before_title . $instance['title'] . $after_title;
		}
		?>
		<ul>
			<?php foreach ($tweets as $tweet): ?>
				<li><?php echo $tweet->tweet_text ?> - <span><?php echo $tweet->time_distance ?> ago</span></li>
			<?php endforeach ?>
		</ul>
		<?php
	}
}

/*
* An example widget
*/
class ThemeWidgetExample extends ThemeWidgetBase {
	/*
	* Register widget function. Must have the same name as the class
	*/
	function ThemeWidgetExample() {
		$widget_opts = array(
			'classname' => 'theme-widget', // class of the <li> holder
			'description' => __( 'Displays a block with title/text' ) // description shown in the widget list
		);
		// Additional control options. Width specifies to what width should the widget expand when opened
		$control_ops = array(
			//'width' => 350,
		);
		// widget id, widget display title, widget options
		$this->WP_Widget('theme-widget-example', 'Theme Widget - Example', $widget_opts, $control_ops);
		$this->custom_fields = array(
			array(
				'name'=>'title', // field name
				'type'=>'text', // field type (text, textarea, integer etc.)
				'title'=>'Title', // title displayed in the widget form
				'default'=>'Hello World!' // default value
			),
			array(
				'name'=>'text',
				'type'=>'textarea',
				'title'=>'Content', 
				'default'=>'Lorem Ipsum dolor sit amet'
			),
		);
	}
	
	/*
	* Called when rendering the widget in the front-end
	*/
	function front_end($args, $instance) {
		extract($args);
		if ($instance['title'] != '') {
			echo $before_title . $instance['title'] . $after_title;
		}
		?>
		<p><?php echo $instance['text'];?></p>
		<?php
	}
}

class ThemeWidgetLatestPost extends ThemeWidgetBase {
	/*
	* Register widget function. Must have the same name as the class
	*/
	function ThemeWidgetLatestPost() {
		$widget_opts = array(
			'classname' => 'theme-latest-post', // class of the <li> holder
			'description' => __( 'Displays the latest blog post' ) // description shown in the widget list
		);
		// Additional control options. Width specifies to what width should the widget expand when opened
		$control_ops = array(
			//'width' => 350,
		);
		// widget id, widget display title, widget options
		$this->WP_Widget('theme-widget-latest_post', 'Theme Widget - What\'s New', $widget_opts, $control_ops);
		$this->custom_fields = array(
			array(
				'name'=>'title', // field name
				'type'=>'text', // field type (text, textarea, integer etc.)
				'title'=>'Title', // title displayed in the widget form
				'default'=>'WHAT’S NEW' // default value
			),
		);
	}
	
	/*
	* Called when rendering the widget in the front-end
	*/
	function front_end($args, $instance) {
		extract($args);
		if ($instance['title'] != '') {
			echo $before_title . $instance['title'] . $after_title;
		}

		query_posts('posts_per_page=1');

		if (have_posts()) {
			the_post();

			the_excerpt();
		}		

		wp_reset_query();
	}
}

class ThemeWidgetBanner extends ThemeWidgetBase {
	/*
	* Register widget function. Must have the same name as the class
	*/
	function ThemeWidgetBanner() {
		$widget_opts = array(
			'classname' => 'banner', // class of the <li> holder
			'description' => __( 'Displays a Banner Image with Link' ) // description shown in the widget list
		);
		// Additional control options. Width specifies to what width should the widget expand when opened
		$control_ops = array(
			//'width' => 350,
		);
		// widget id, widget display title, widget options
		$this->WP_Widget('theme-widget-banner', 'Theme Widget - Banner', $widget_opts, $control_ops);
		$this->custom_fields = array(
			array(
				'name'=>'image', // field name
				'type'=>'text', // field type (text, textarea, integer etc.)
				'title'=>'Image', // title displayed in the widget form
				'default'=> get_bloginfo('stylesheet_directory') . '/images/join-team.png' // default value
			),
			array(
				'name'=>'link',
				'type'=>'text',
				'title'=>'Link', 
				'default'=>'#'
			),
		);
	}
	
	/*
	* Called when rendering the widget in the front-end
	*/
	function front_end($args, $instance) {
		?>
		<a href="<?php echo $instance['link'] ?>" class="join">
			<img src="<?php echo $instance['image'] ?>" alt="" />
		</a>
		<?php
	}
}

class ThemeWidgetTestimonial extends ThemeWidgetBase {
	/*
	* Register widget function. Must have the same name as the class
	*/
	function ThemeWidgetTestimonial() {
		$widget_opts = array(
			'classname' => 'theme-widget-testimonial', // class of the <li> holder
			'description' => __( 'Displays a random testimonial' ) // description shown in the widget list
		);
		// Additional control options. Width specifies to what width should the widget expand when opened
		$control_ops = array(
			//'width' => 350,
		);
		// widget id, widget display title, widget options
		$this->WP_Widget('theme-widget-testimonial', 'Theme Widget - Testimonial', $widget_opts, $control_ops);
		$this->custom_fields = array(
			array(
				'name'=>'title', // field name
				'type'=>'text', // field type (text, textarea, integer etc.)
				'title'=>'Title', // title displayed in the widget form
				'default'=>'Client Testimonials' // default value
			),
		);
	}
	
	/*
	* Called when rendering the widget in the front-end
	*/
	function front_end($args, $instance) {
		extract($args);
		if ($instance['title'] != '') {
			echo $before_title . $instance['title'] . $after_title;
		}

		$testimonial = get_posts('numberposts=1&orderby=rand&post_type=testimonial');

		if ($testimonial) {
			?>
			<p>"<?php echo $testimonial[0]->post_content; ?>"</p>
			<p class="author">-<?php echo $testimonial[0]->post_title; ?></p>
			<?php
		}
	}
}

?>