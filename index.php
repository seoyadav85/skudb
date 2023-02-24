<?php 
// Silence is Golden 
?>

<?php
/*
Plugin Name: Weather Widget Plugin
Description: Show weather details in a widget
Version: 0.17
*/

// Register and load the widget
function weather_load_widget() {
    register_widget( 'weather_widget' );
}
add_action( 'widgets_init', 'weather_load_widget' );

// The widget Class
class weather_widget extends WP_Widget {

  function __construct() {
    parent::__construct(

      // Base ID of your widget
      'weather_widget',

      // Widget name will appear in UI
      __('Weather Widget', 'weather_widget_domain'),

      // Widget description
      array( 'description' => __( 'Show Weather Details in a Widget', 'weather_widget_domain' ), )
    );
  }

  // Creating widget front-end view
  public function widget( $args, $instance ) {
    $title = apply_filters( 'widget_title', $instance['title'] );

    //Only show to me during testing - replace the Xs with your IP address if you want to use this
    //if ($_SERVER['REMOTE_ADDR']==="xxx.xxx.xxx.xxx") {

      // before and after widget arguments are defined by themes
      echo $args['before_widget'];
      if ( ! empty( $title ) ) echo $args['before_title'] . $title . $args['after_title'];

      // This is where you run the code and display the output
      $curl = curl_init();
      $url = "https://climacell-microweather-v1.p.rapidapi.com/weather/nowcast?fields=epa_aqi&lat=34.0201598&lon=-118.692597";

      curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
          "x-rapidapi-host: climacell-microweather-v1.p.rapidapi.com",
          "x-rapidapi-key: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
        ),
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        //Only show errors while testing
        //echo "cURL Error #:" . $err;
      } else {
        //The API returns data in JSON format, so first convert that to an array of data objects
        $responseObj = json_decode($response);

        //Gather the air quality value and timestamp for the first and last elements
        $firstEPAAQI = "<strong>".$responseObj[0]->epa_aqi->value."</strong>";
        $firstTime = date("Y-m-d H:i:s T",strtotime($responseObj[0]->observation_time->value));
        $numResults = count($responseObj);
        $lastEPAAQI = "<strong>".$responseObj[$numResults-1]->epa_aqi->value."</strong>";
        $lastTime = date("Y-m-d H:i:s T",strtotime($responseObj[$numResults-1]->observation_time->value));

        //This is the content that gets populated into the widget on your site
        echo "The <a href='https://www.airnow.gov/index.cfm?action=aqibasics.aqi'>air quality EPA score</a> ".
              "is currently $firstEPAAQI at $firstTime and is forecasted to be $lastEPAAQI at $lastTime <br>";
      }

      echo $args['after_widget'];

    //} // end check for IP address for testing
  } // end public function widget

  // Widget Backend - this controls what you see in the Widget UI
  //  For this example we are just allowing the widget title to be entered
  public function form( $instance ) {
    if ( isset( $instance[ 'title' ] ) ) {
      $title = $instance[ 'title' ];
    } else {
      $title = __( 'New title', 'wpb_widget_domain' );
    }
    // Widget admin form
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <?php
  }

  // Updating widget - replacing old instances with new
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
    return $instance;
  }
} // Class weather_widget ends here
?>