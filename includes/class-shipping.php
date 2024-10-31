<?php
/**
 * Created by PhpStorm.
 * User: pompi
 * Date: 23/05/21
 * Time: 12:34
 */

//
//class WC_PAKKE_SHIPPING extends WC_Shipping_Method {
//
//    public function __construct() {
//        $this->id                 = 'pakke_shipping'; // Id for your shipping method. Should be uunique.
//        $this->method_title       = __( 'Pakke' );  // Title shown in admin
//        $this->method_description = __( 'Envios con Pakke' ); // Description shown in admin
//
//        $this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
//        $this->title              = "Pakke"; // This can be added as an setting but for this example its forced.
//
//        $this->init();
//    }
//
//    public function init() {
//        // Load the settings API
//        $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
//        $this->init_settings(); // This is part of the settings API. Loads settings you previously init.
//
//        // Save settings in admin if you have any defined
//        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
//    }
//
//    public function calculate_shipping( $package = array() ) {
//
//        $rate = array(
//            'label' => $this->title,
//            'cost' => '10.00',
//            'calc_tax' => 'per_item'
//        );
//
//        // Register the rate
//        $this->add_rate( $rate );
//
//    }
//
//}
