<?php
/**
 * FeedBurner Subscribe widget class.
 * Display info on how to subscribe by feed. If a FeedBurner URL is available
 * it also offers an email box.
 *
 * @since 0.1
 */
class FeedBurner_Subscribe_Widget extends WP_Widget {
  
	/**
	 * Widget setup.
	 */
  function __construct() {
   /* Widget settings. */
   $widget_ops = array( 'description' => __('Displays controls for subscribing via FeedBurner.', 'feedburner_subscribe_description') );
  
   // /* Create the widget. */
   parent::__construct(false, $name = __('Feedburner Subscribe', 'feedburner_subscribe'), $widget_ops);
  }

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

    $feedburner_settings = get_option('feedburner_settings');
    $catslug = $GLOBALS['wp']->query_vars['category_name'];
    // FIXME not always the way to get the category URL
    $wp_feed = "/category/$catslug/feed/";
    $fb_feedurl = $feedburner_settings['feedburner_category_'.$catslug];
    $fb_key = basename($fb_feedurl);
    
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$name = $instance['name'];
		$sex = $instance['sex'];
		$show_sex = isset( $instance['show_sex'] ) ? $instance['show_sex'] : false;

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

    $desc = (empty($catslug)) ? "all" : single_cat_title("", false);
    
    // FIXME style by stylesheet
    echo "Subscribe to <strong>$desc</strong> posts ";

    if (empty($fb_feedurl)) {
      echo "<a href=\"$wp_feed\">by feed</a>.";
    } else {
      echo "<a href=\"$wp_feed\">by feed</a> or by email,";      
      // FIXME move this CSS into a stylesheet that can be overridden
      echo <<<EOD
<form class="feedburner_subscribe_email_form" style="text-align:center;"
  action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow"
  onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=$fb_key', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
  <input type="email" required="required" placeholder="Email address" style="width:140px" name="email"/>
  <input type="hidden" value="$fb_key" name="uri"/>
  <input type="hidden" name="loc" value="en_US"/>
  <input type="submit" value="Subscribe" />
  </form>
EOD;
    }

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['name'] = strip_tags( $new_instance['name'] );

		/* No need to strip tags for sex and show_sex. */
		$instance['sex'] = $new_instance['sex'];
		$instance['show_sex'] = $new_instance['show_sex'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Example', 'example'), 'name' => __('John Doe', 'example'), 'sex' => 'male', 'show_sex' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

	<?php
	}
}

