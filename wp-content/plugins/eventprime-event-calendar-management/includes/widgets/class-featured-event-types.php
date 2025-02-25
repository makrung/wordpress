<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('EventM_Featured_Type')){

    class EventM_Featured_Type extends WP_Widget {

        function __construct() {
            parent::__construct('eventm_featured_type', "EventPrime Featured Event-Types" , array( 'description' => 'Show list of featured event-types.' )
            );
        }

        public function widget( $args, $instance ) {
            $basic_functions = new Eventprime_Basic_Functions;
            wp_enqueue_style(
            'ep-widgets-style',
            plugin_dir_url(dirname(__DIR__)) . '/admin/css/ep-widgets-style.css',
                false, EVENTPRIME_VERSION
            );
            $event_types_text = $basic_functions->ep_global_settings_button_title('Event-Types');
            $featured_event_types =  sprintf( esc_html__( 'Featured %s', 'eventprime-event-calendar-management' ), $event_types_text );
            $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : $featured_event_types;
            $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
            $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
            if ( !$number ) {
                $number = 5;
            }

            $html = '<div class="widget widget_featured_events ep-widgets"><div class="widget-content">';
                $html .= '<h2 class="widget-title subheading heading-size-3">'.esc_attr( $title ).'</h2>';
                $event_types_controller = new Eventprime_Basic_Functions();
                $types = $event_types_controller->get_featured_event_types( $number );
               
                if( ! empty( $types->terms ) ){
                    $i = 0;
                    foreach ( $types->terms as $type ) {
                        $html .= '<div class="ep-featured-event-type ep-mw-wrap ep-widget-block-wrap ep-d-flex ep-align-items-center ep-p-2 ep-my-3 ep-shadow-sm ep-border ep-rounded-1 ep-bg-white">';
                            $title = $type->name;
                            $thumbnail_id = ( isset( $type->em_image_id ) && ! empty( $type->em_image_id ) ) ? $type->em_image_id : 0; 
                            $html .= '<div class="ep-fimage ep-di-flex">';
                            if (!empty($thumbnail_id)):
                                $html .= '<a href="'.esc_url( $type->event_type_url ).'"><img src="'.wp_get_attachment_image_src( $thumbnail_id, 'large' )[0].'" alt="'.__( 'Event Type Image', 'eventprime-event-calendar-management' ).'"></a>';
                            else:
                                $html .= '<a href="'.esc_url( $type->event_type_url ).'"><img src="'.plugin_dir_url(dirname(__DIR__)) .'admin/images/dummy_image.png" alt="'.__( 'Dummy Image', 'eventprime-event-calendar-management' ).'" ></a>';
                            endif;
                            $html .= '</div>';
                            $html .= '<div class="ep-fdata"><div class="ep-fname"><a href="'.esc_url( $type->event_type_url ).'">'.esc_attr( $type->name ).'</a></div>';              
                            $html .= '</div>';
                        $html .= '</div>';
                    }
                }else{
                    $html .= '<div class="ep-widgets-empty">'.esc_html__('No data found.','eventprime-event-calendar-management').'</div>';
                }
            $html .= '</div></div>';
            echo wp_kses_post($html);
        }

        public function form($instance) {
            $title = !empty( $instance['title'] ) ? $instance['title'] : '';
            $number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'eventprime-event-calendar-management' ); ?></label> 
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of event types to show:', 'eventprime-event-calendar-management' ); ?></label>
                <input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" step="1" min="1" value="<?php echo esc_attr( $number ); ?>" size="3" />
            </p>
            <?php 
        }

        // Updating widget replacing old instances with new
        public function update($new_instance, $old_instance) {

            $instance = array();
            $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
            $instance['number'] = (int) $new_instance['number'];
            return $instance;
        }

    }

}
