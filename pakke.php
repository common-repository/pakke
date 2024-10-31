<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              pakke.com
 * @since             1.0.1
 * @package           Pakke
 *
 * @wordpress-plugin
 * Plugin Name:       Pakke
 * Plugin URI:        pakke.com
 * Description:       Envios con Pakke.
 * Version:           1.0.2
 * Author:            PAKKE
 * Author URI:        pakke.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pakke
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PAKKE_VERSION', '1.0.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pakke-activator.php
 */
function activate_pakke() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-pakke-activator.php';
    Pakke_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pakke-deactivator.php
 */
function deactivate_pakke() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-pakke-deactivator.php';
    Pakke_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pakke' );
register_deactivation_hook( __FILE__, 'deactivate_pakke' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pakke.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pakke() {

    $plugin = new Pakke();
    $plugin->run();


    function pakke_shipping_method() {
        if ( ! class_exists( 'Pakke_Shipping_Method') ) {
            class Pakke_Shipping_Method extends WC_Shipping_Method {

                public function __construct($instance_id = 0 ) {
                    $this->id                 = 'pakke_shipping'; // Id for your shipping method. Should be uunique.
                    $this->instance_id          = absint( $instance_id );
                    $this->method_title       = __( 'Pakke' );  // Title shown in admin
                    $this->method_description = __( '¡Todo lo que necesitas para tus envios, en un solo lugar!' ); // Description shown in admin

                    $this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
                    $this->title              = "Pakke"; // This can be added as an setting but for this example its forced.
                    $this->supports             = array(
                        'shipping-zones',
                        'settings',
                        'instance-settings',
                        'instance-settings-modal',

                    );
//                    $this->availability = 'including';
//                    $this->countries = array(
//                        'MX',
//
//                    );
                     $this->init();

                }

                public function init() {
                    // Load the settings API
                    $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
                    $this->init_settings(); // This is part of the settings API. Loads settings you previously init.

                    // Save settings in admin if you have any defined
                    add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
                }
                function init_form_fields()
                {
                    $key = get_option('woocommerce_pakke_shipping_settings')['apikey'];


                    $url = 'https://middleware.pakke.mx/courier/details';


                    $httpresponse = wp_remote_get($url,
                        array('timeout' => 10,
                            'headers' => array(
                                'Content-Type' => 'application/json',
                                'Accept' => "application/json",
                                'Authorization' => "$key"
                            )
                        ));

                    if ($httpresponse){
                        $response = json_decode($httpresponse['body'], true);


                    $this->form_fields = array(


                        'apikey' => array(
                            'title' => __('API KEY', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'API Key de su cuenta pakke'
                        ),
                        'apistatus' => array(
                            'title' => __('API Status', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'API Status de su cuenta pakkee'
                        ),
                        'leyend_customer_info' => array(
                            'title' => __('La siguiente información ayudará a Pakke a contactarte si llegara a ocurrir algún problema.', 'pakke'),
                            'type' => 'title',
                            'class' => 'pakke-woo-settings-text'
                        ),

                        'name' => array(
                            'title' => __('Nombre del vendedor', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'Nombre vendedor'
                        ),
                        'email' => array(
                            'title' => __('Email de contacto', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'Email vendedor'
                        ),
                        'phone1' => array(
                            'title' => __('Telefono de contacto', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'Telefono de contacto'
                        ),
                        'phone2' => array(
                            'title' => __('Telefono celular', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'Telefono celular'
                        ),
                        'tipo' => array(
                            'type' => 'select',
                            'options' => array(
                                'fijo' => __('Monto Fijo', 'pakke'),
                                'dinamico' => __('Dinamico', 'pakke'),

                            ),
                            'class' => 'radio_pakke',
                            'desc_tip' => 'Tipo Costo de envio',
                            'title' => __("Tipo Costo de envio"),


                        ),
                        'fijo' => array(
                            'title' => __('Costo fijo', 'pakke'),
                            'type' => 'number',
                            'desc_tip' => 'Costo fijo',
                            'default' => 0,
                            'class' => 'fijo_pakke',
                        ),
                        'extra' => array(
                            'title' => __('Margen extra (Opcional)', 'pakke'),
                            'type' => 'number',
                            'desc_tip' => 'Margen extra (Opcional)',
                            'default' => 0,
                            'class' => 'extra_pakke',
                        ),
                        'title_service_type' => array(
                            'title' => __('Tipo de servicio para cotización dinámica', 'pakke'),
                            'type' => 'title',
                            'class' => 'pakke-woo-settings-title'
                        ),
                        'leyend_service_type' => array(
                            'title' => __('Los tipos de servicio que ofrece Pakke ayudarán a los clientes a seleccionar el tipo de servicio por el cual quieren que sea enviados sus productos.', 'pakke'),
                            'type' => 'title',
                            'class' => 'pakke-woo-settings-text'
                        ),
                        'standard_check' => array(
                            'title' => __('standard', 'pakke'),
                            'type' => 'checkbox',

                            'class' => 'pakke-woo-settings-check'
                        ),
                        'standard_input' => array(
                            'title' => __('', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'Standard',
                            'class' => 'pakke-woo-settings-checktext'
                        ),
                        'express_check' => array(
                            'title' => __('express', 'pakke'),
                            'type' => 'checkbox',

                            'class' => 'pakke-woo-settings-check'
                        ),
                        'express_input' => array(
                            'title' => __('', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'express',
                            'class' => 'pakke-woo-settings-checktext'
                        ),
                        'sameday_check' => array(
                            'title' => __('sameday', 'pakke'),
                            'type' => 'checkbox',

                            'class' => 'pakke-woo-settings-check'
                        ),
                        'sameday_input' => array(
                            'title' => __('', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'sameday',
                            'class' => 'pakke-woo-settings-checktext'
                        ),
                        'nextday_check' => array(
                            'title' => __('nextday', 'pakke'),
                            'type' => 'checkbox',

                            'class' => 'pakke-woo-settings-check'
                        ),
                        'nextday_input' => array(
                            'title' => __('', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'nextday',
                            'class' => 'pakke-woo-settings-checktext'
                        ),
                        'title_service_type1' => array(
                            'title' => __('Direccion de origen', 'pakke'),
                            'type' => 'title',
                            'class' => 'pakke-woo-settings-title'
                        ),
                        'leyend_service_type1' => array(
                            'title' => __('Esta direccion nos ayudara a estimar las cotizaciones de tus envios', 'pakke'),
                            'type' => 'title',
                            'class' => 'pakke-woo-settings-text'
                        ),
                        'direccion1' => array(
                            'title' => __('Direccion 1', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'Direccion 1'
                        ),
//                        'direccion2' => array(
//                            'title' => __('Direccion 2', 'pakke'),
//                            'type' => 'text',
//                            'desc_tip' => 'Direccion 2'
//                        ),

                        'cp' => array(
                            'title' => __('CP', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'CP'
                        ),
                        'colonia' => array(
                            'title' => __('Colonia', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'Colonia'
                        ),
                        'ciudad' => array(
                            'title' => __('Ciudad', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'Ciudad'
                        ),

                        'estado' => array(
                            'title' => __('Estado', 'pakke'),
                            'type' => 'text',
                            'desc_tip' => 'Estado'
                        ),


                    );
                    if ($response['courierDetail']) {
                        $this->form_fields['title_service_type2'] = array(
                            'title' => __('Administracion de paqueterias', 'pakke'),
                            'type' => 'title',
                            'class' => 'pakke-woo-settings-title'
                        );
                        $this->form_fields['leyend_service_type2'] = array(
                            'title' => __('A continuacion puedes administrar con que paqueterias quieres trabajar', 'pakke'),
                            'type' => 'title',
                            'class' => 'pakke-woo-settings-text'
                        );


                        foreach ($response['courierDetail'] as $k => $c) {

                            $this->form_fields[$c['code']] = array(
                                'title' => __($k, 'pakke'),
                                'type' => 'checkbox',

                                'class' => 'pakke-woo-settings-check'
                            );
                        }
                    }
                }
                }
                public function get_instance_form_fields() {
                    return parent::get_instance_form_fields();
                }
                public function is_available( $package ) {
                    $is_available = true;
                    return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package, $this );
                }
                public function calculate_weight($package = array()) {

                    $_parcels = array();
                    $_length = 0;
                    $_height = 0;
                    $_width = 0;
                    $_weight = 0;
                    $_qty = 0;

                    foreach ( $package['contents'] as $item_id => $values ) {
                        if(!empty($values['data'])) {
                            $_Q = $values['quantity'];
                            $product = $values['data'];
                            $tmpLength = 0;
                            $tmpHeight = 0;
                            $tmpWidth = 0;

                            $thisParcel = null;

                            $_weight = (float)$product->get_weight();
                            $_qty = (float)$_Q;
                            $tmpLength = (float)$product->get_length();
                            $tmpHeight = (float)$product->get_height();
                            $tmpWidth = (float)$product->get_width();


                            $weightUnit = get_option('woocommerce_weight_unit');
                            $dimensionUnit = get_option('woocommerce_dimension_unit');

                            $originalWeight = $_weight;

                            if ($dimensionUnit === 'cm' && $tmpHeight < 1) {
                                $tmpHeight = 1;
                            }

                            if ($weightUnit === 'g') {
                                $originalWeight /= 1000;
                                $weightUnit = 'kg';
                            }

                            if ($weightUnit === 'oz') {
                                $originalWeight /= 35.274;
                                $weightUnit = 'kg';
                            }

                            $endWeight = round($originalWeight, 1);

                            if ($endWeight <= 0) {
                                $endWeight = 0.1;
                                $weightUnit = 'kg';
                            }

                            $thisParcel = array(
                                'quantity' => $_qty,
                                'weight' => $endWeight,
                                'weight_unit' => $weightUnit,
                                'length' => $tmpLength,
                                'height' => $tmpHeight,
                                'width' => $tmpWidth,
                                'dimension_unit' => $dimensionUnit,
                            );
                            $_parcels[] = $thisParcel;

                        }
                    }

                    return $_parcels;
                }


                public function calculate_shipping( $package = array() )
                {

                    $origin_postcode = get_option('woocommerce_store_postcode');
                    $postal_code = $package['destination']['postcode'];
                    $_parcels = $this->calculate_weight($package);

                    $key = get_option('woocommerce_pakke_shipping_settings')['apikey'];
                    $maxweight = 60;
                    $maxwidth = 115;
                    $maxheight = 115;
                    $maxlength = 150;
                    $url = ' https://seller.pakke.mx/api/v1/Shipments/rates';
                    $url2='https://middleware.pakke.mx/courier/details';
                   if ($_parcels[0]["weight"] <= $maxweight && $_parcels[0]["width"] <= $maxwidth && $_parcels[0]["height"] <= $maxheight && $_parcels[0]["length"] <= $maxlength && ($_parcels[0]["width"]*$_parcels[0]["height"]*$_parcels[0]["length"]/5000)<=$maxweight) {

                    $body = [
                        "ZipCodeFrom" => "$origin_postcode",
                        "ZipCodeTo" => "$postal_code",
                        "Parcel" => array(
                            "Weight" => $_parcels[0]["weight"],
                            "Width" => $_parcels[0]["width"],
                            "Height" => $_parcels[0]["height"],
                            "Length" => $_parcels[0]["length"]
                        ),
                        "CouponCode" => null,

                    ];


                    $body = wp_json_encode($body);
                    $httpresponse = wp_remote_post($url,
                        array('timeout' => 10,
                            'body' => $body,
                            'headers' => array(
                                'Content-Type' => 'application/json',
                                'Accept' => "application/json",
                                'Authorization' => "$key"
                            )

                        ));

                    $response = json_decode($httpresponse['body'], true);
//                    echo json_encode($body);
//                    echo "///";


//                echo json_encode($response);
                    if ($response["Pakke"]) {
                        $standard=false;
                        $express=false;
                        $nextday=false;
                        $sameday=false;
                        $tipo = get_option('woocommerce_pakke_shipping_settings')['tipo'];
                        $extra = get_option('woocommerce_pakke_shipping_settings')['extra'];
                        $fijo = get_option('woocommerce_pakke_shipping_settings')['fijo'];
                        if(!$extra)
                            $extra=0;
                        foreach ($response["Pakke"] as $r) {
                            if(get_option('woocommerce_pakke_shipping_settings')[$r["CourierCode"]]=='no')
                                continue;



                            $type_service='';

                            if($r["typeService"]=='standard' ) {

                                if(get_option('woocommerce_pakke_shipping_settings')['standard_check']=='no' || $standard)
                                    continue;
                                else{
                                    $type_service=get_option('woocommerce_pakke_shipping_settings')['standard_input'];
                                    $standard=true;
                                }


                            }
                            if($r["typeService"]=='express' ) {
                                if(get_option('woocommerce_pakke_shipping_settings')['express_check']=='no' || $express)
                                    continue;
                                else {
                                    $type_service = get_option('woocommerce_pakke_shipping_settings')['express_input'];
                                    $express=true;
                                }
                            }
                            if($r["typeService"]=='sameDay' ) {
                                if(get_option('woocommerce_pakke_shipping_settings')['sameday_check']=='no' || $sameday)
                                    continue;
                                else{
                                    $sameday=true;
                                    $type_service=get_option('woocommerce_pakke_shipping_settings')['sameday_input'];}
                            }
                            if($r["typeService"]=='nextDay' ) {
                                if(get_option('woocommerce_pakke_shipping_settings')['nextday_check']=='no' || $nextday)
                                    continue;
                                else{
                                    $nextday=true;
                                    $type_service=get_option('woocommerce_pakke_shipping_settings')['nextday_input'];}
                            }





                           if($tipo=='dinamico') {

                               $rate = array(
                                   'id' => $r["CourierCode"] . "%".$r["CourierServiceId"]."%". str_replace(" ","_",$r["CourierServiceName"]).'%'.$r["TotalPrice"] ,
                                   'label' => $type_service,
                                   'cost' => $r["TotalPrice"] + $extra,

                               );

                               $this->add_rate($rate);
                          }
                           if($tipo=='fijo'){
                               if ($r["BestOption"]) {
                               $rate = array(
                                   'id' => 'FIJO',
                                   'label' => "Costo por envio",
                                   'cost' => $fijo,

                               );
                                   $this->add_rate($rate);
                                   break;
                           }

                            }
                       }

                    }
                }
                }





            }
        }
    }
    add_action('woocommerce_shipping_init','Pakke_Shipping_Method');

    function add_pakke_shipping_method($methods) {
        $methods['pakke_shipping'] = 'pakke_shipping_method';
        return $methods;
    }
    add_filter('woocommerce_shipping_methods', 'add_pakke_shipping_method');



    function wpdocs_enqueue_custom_admin_style() {
        wp_register_style( 'custom_wp_admin_css', plugin_dir_url( __FILE__ ) . '/admin/css/pakke-admin.css', false, '1.0.0' );
        wp_enqueue_style( 'custom_wp_admin_css' );
        wp_register_script( 'custom_wp_admin_js', plugin_dir_url( __FILE__ ) . '/admin/js/pakke-admin.js', false, '1.0.0' );
        wp_enqueue_script( 'custom_wp_admin_js' );
    }
    add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style' );

    // Add a custom metabox only for shop_order post type (order edit pages)
    add_action( 'add_meta_boxes', 'add_meta_boxesws' );
    function add_meta_boxesws()
    {
        add_meta_box( 'custom_order_meta_box', __( 'Envío con Pakke' ),
            'custom_metabox_content', 'shop_order', 'normal', 'high');
    }

     function pakke_courier_img_prev($id) {



        $key = get_option('woocommerce_pakke_shipping_settings')['apikey'];


        $url = 'https://middleware.pakke.mx/courier/details';



        $httpresponse = wp_remote_get( $url ,
            array( 'timeout' => 10,
                'headers' => array(
                    'Content-Type'=> 'application/json',
                    'Accept' => "application/json",
                    'Authorization'=> "$key"
                )
            ));

        $response = json_decode($httpresponse['body'], true);

        foreach ($response['courierDetail'] as $k=> $c){
                if($c->code==$id){
                    return $c->logo;

                }

        }

       return false();


    }

    function custom_metabox_content(){
        $post_id =sanitize_text_field($_GET['post']);
        if(! $post_id ) return; // Exit
        $order = wc_get_order($post_id);

// Iterating through order shipping items
        foreach( $order->get_items( 'shipping' ) as $item_id => $item ){
            $shipping_method_total       = $item->get_total();

        }
        $total_weight = 0;
        $total_width = 0;
        $total_height = 0;
        $total_length = 0;
        foreach( $order->get_items() as $item_id => $item ){
            $shipping_service            = wc_get_order_item_meta( $item_id, '_shipping_rate_id', true );
            $product = $item->get_product();
            $quantity = $item->get_quantity();
            $weight = $product->get_weight();
            $width = $product->get_width();
            $height = $product->get_height();
            $length = $product->get_length();
            $total_weight += floatval($weight * $quantity);
            $total_width += floatval($width * $quantity);
            $total_height += floatval($height * $quantity);
            $total_length += floatval($length * $quantity);
        }

        $logo="";
        $key = get_option('woocommerce_pakke_shipping_settings')['apikey'];
        $url = 'https://middleware.pakke.mx/courier/details';
        $httpresponse = wp_remote_get( $url ,
            array( 'timeout' => 10,
                'headers' => array(
                    'Content-Type'=> 'application/json',
                    'Accept' => "application/json",
                    'Authorization'=> "$key"
                )
            ));
        $response = json_decode($httpresponse['body'], true);

        foreach ($response['courierDetail'] as $c){
            
            if($c['code']==explode('%',$shipping_service)[0]){

                $logo= $c['logo'];

            }

        }
               ?>
<!--        <p><a href="?post=--><?php //echo $post_id; ?><!--&action=edit&abc=--><?php //echo $value; ?><!--" class="button">--><?php //_e('Return Shipment'); ?><!--</a></p>-->
        <style>
            .medidas-div,.medidas-div-prev{
                display: flex;
                flex-wrap: wrap;
            }
            .medidas{
                width: 18%;
                margin: 1%;
                display: flex;
                align-content: center;
            }
            .medidas-prev{
                width: 8%;
                margin: 1%;
                /*display: flex;*/
                align-content: center;
            }
            #pakke-btn-cotizar{
                padding-top: 2%;
            }
            .medidas-prev h4{
                font-size: 13px;
            }
        </style>
        <h2>Cotización previa  </h2>
        <div class="medidas-div-prev">
            <div class="medidas-prev">
                <label >Largo (cm)</label>
                <input type="hidden" id="pakke_largo_prev" name="pakke_largo_prev" value="<?php echo esc_html($total_length)  ?>">
                <h4><?php echo esc_html($total_length)  ?></h4>
            </div>
            <div class="medidas-prev">
                <label >Ancho (cm)</label>
                <input type="hidden" id="pakke_ancho_prev" name="pakke_ancho_prev" value="<?php echo esc_html($total_width)  ?>">
                <h4><?php echo esc_html($total_width)  ?></h4>
            </div>
            <div class="medidas-prev">
                <label >Alto (cm)</label>
                <input type="hidden" id="pakke_alto_prev" name="pakke_alto_prev" value="<?php echo esc_html($total_height)  ?>">
                <h4><?php echo esc_html($total_height)  ?></h4>
            </div>
            <div class="medidas-prev">
                <label >Peso (kg)</label>
                <input type="hidden" id="pakke_peso_prev" name="pakke_peso_prev" value="<?php echo esc_html($total_weight)  ?>">
                <h4><?php echo esc_html($total_weight)  ?></h4>
            </div>
            <div class="medidas-prev">
                <label >Servicio</label>
                <input type="hidden" id="pakke_courierid_prev" name="pakke_courierid_prev" value="<?php echo esc_html(explode('%',$shipping_service)[0])  ?>">
                <?php if(explode('%',$shipping_service)[0]!="FIJO"){?>
                <img src="<?php echo  esc_url($logo) ?>" width="100px" alt="">
            <?php } else {?>
            <img src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'admin/img/logo-pakke.svg') ?>" width="100px" alt="">
        <?php } ?>
            </div>
            <div class="medidas-prev">
                <label >Tipo servicio</label>
                <input type="hidden" id="pakke_serviceid_prev" name="pakke_serviceid_prev" value="<?php echo esc_html(explode('%',$shipping_service)[1])  ?>">
        <?php if(explode('%',$shipping_service)[0]!="FIJO"){?>
                <h4><?php echo esc_html(str_replace("_"," ",explode('%',$shipping_service)[2]))   ?></h4>
        <?php } else {?>
            <h4>---</h4>
        <?php } ?>
            </div>
            <div class="medidas-prev">
                <label >Cotizado</label>
        <?php if(explode('%',$shipping_service)[0]!="FIJO"){?>
                <h4>$ <?php echo esc_html(explode('%',$shipping_service)[3])   ?> MXN</h4>
        <?php } else {?>
            <h4>---</h4>
        <?php } ?>
            </div>
            <div class="medidas-prev">
                <label >Cobrado</label>
                <h4>$ <?php echo esc_html($shipping_method_total)   ?> MXN</h4>
            </div>
            <div class="medidas-prev">
                <a class="button button-primary" id="pakke-btn-guia-prev"><?php echo esc_html('Generar Guia'); ?></a>
                <img  width="20px" height="20px"  class="load-guia" src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'admin/img/ajax-loader.gif') ?>" alt="">
            </div>
        </div>
        <h2>Cotización manualmente  </h2>
        <div class="medidas-div">
            <div class="medidas">
                <label for="pakke_largo">Largo (cm)</label>
                <input type="number" id="pakke_largo" name="pakke_largo">
            </div>
            <div class="medidas">
                <label for="pakke_ancho">Ancho (cm)</label>
                <input type="number" id="pakke_ancho" name="pakke_ancho">
            </div>
            <div class="medidas">
                <label for="pakke_alto">Alto (cm)</label>
                <input type="number" id="pakke_alto" name="pakke_alto">
            </div>
            <div class="medidas">
                <label for="pakke_peso">Peso (kg)</label>
                <input type="number" id="pakke_peso" name="pakke_peso">
            </div>
            <div class="medidas">
            <a class="button button-primary" id="pakke-btn-cotizar"><?php _e('Cotizar'); ?></a>
                <img  width="20px" height="20px"  class="load-cotizar" src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'admin/img/ajax-loader.gif') ?>" alt="">
            </div>
        </div>
        <input type="hidden" id="pakke-order-id" value="<?php echo esc_html($post_id) ?>">

        <input type="hidden" id="check-icon" value="<?php echo esc_url(plugin_dir_url( __FILE__ ).'admin/img/check.png') ?>">
        <div class="tabla-cotizar" style="display: none">
            <table class="table-striped">
                <thead>
                <th>COURIER</th>
                <th></th>
                <th></th>
                <th>TIPO DE SERVICIO</th>
                <th>TIEMPO ESTIMADO</th>
                <th>FECHA</th>
                <th >PRECIO</th>

                </thead>
                <tbody class="tabla-cuerpo">

                </tbody>
            </table>
            <div class="leyenda" >
                <img width="20px" height="20px" src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'admin/img/check.png') ?>" alt="">
                <p>Mejor Opción de Envío</p>
            </div>
            <div class="botones-guia">
            <a class="button" id="pakke-btn-cancelar"><?php _e('Cancelar'); ?></a>
            <a class="button button-primary" id="pakke-btn-guia"><?php _e('Generar Guia'); ?></a>
                <img  width="20px" height="20px"  class="load-guia" src="<?php echo esc_url(plugin_dir_url( __FILE__ ).'admin/img/ajax-loader.gif') ?>" alt="">

            </div>
        </div>
        <div class="alert alert-danger" id="error-guia"></div>
        <div class="alert alert-success" id="exito-guia"></div>

        <div class="tabla-guias">
        <H2>Guias generadas</H2>
            <table class="table-striped">
                <thead>
                <th>COURIER</th>

                <th>TIPO DE SERVICIO</th>
                <th>FECHA DE GENERACION</th>
                <th>COSTO</th>
                <th >NO. DE GUIA</th>
                <th >DESCARGAR</th>

                </thead>
                <tbody class="tabla-cuerpo-guias">

                </tbody>
            </table>

        </div>
        <?php


    }


    // Custom function that get the chosen shipping method details for a cart item
    function get_cart_item_shipping_method( $cart_item_key ){
        $chosen_shippings = WC()->session->get( 'chosen_shipping_methods' ); // The chosen shipping methods

        foreach( WC()->cart->get_shipping_packages() as $id => $package ) {
            $chosen = $chosen_shippings[$id]; // The chosen shipping method
            if( isset($package['contents'][$cart_item_key]) && WC()->session->__isset('shipping_for_package_'.$id) ) {
                return WC()->session->get('shipping_for_package_'.$id)['rates'][$chosen];
            }
        }
    }

// Save shipping method details in order line items (product) as custom order item meta data
    add_action( 'woocommerce_checkout_create_order_line_item', 'add_order_line_item_custom_meta', 10, 4 );
    function add_order_line_item_custom_meta( $item, $cart_item_key, $values, $order ) {
        // Load shipping rate for this item
        $rate = get_cart_item_shipping_method( $cart_item_key );

        // Set custom order item meta data
        $item->update_meta_data( '_shipping_rate_id', $rate->id ); // the shipping rate ID

    }



//----------------------------tabla--------------------------


if ( ! class_exists ( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * List table class
 */
class Pakke_Guia_List extends \WP_List_Table {

    function __construct() {
        parent::__construct( array(
            'singular' => 'Guia',
            'plural'   => 'Guias',
            'ajax'     => false
        ) );
    }

    function get_table_classes() {
        return array( 'widefat', 'fixed', 'striped', $this->_args['plural'] );
    }

    /**
     * Message to show if no designation found
     *
     * @return void
     */
    function no_items() {
        _e( 'No se han generado guias', '' );
    }

    /**
     * Default column values if no callback found
     *
     * @param  object  $item
     * @param  string  $column_name
     *
     * @return string
     */
    function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'pedido':
                return $item->pedido;

            case 'status':
                return $item->status;

            case 'fecha_orden':
                return $item->fecha_orden;

            case 'fecha_guia':
                return $item->fecha_guia;

            case 'no_guia':
                return $item->no_guia;

            case 'courier':
                return $item->courier;

            case 'servicio':
                return $item->servicio;

            default:
                return isset( $item->$column_name ) ? $item->$column_name : '';
        }
    }

    /**
     * Get the column names
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'           => '<input type="checkbox" />',
            'pedido'      => __( 'Pedido', '' ),
            'status'      => __( 'Status', '' ),
            'fecha_orden'      => __( 'Fecha', '' ),
            'fecha_guia'      => __( 'Fecha Guia', '' ),
            'no_guia'      => __( 'No de Guia', '' ),
            'courier'      => __( 'Courier', '' ),
            'servicio'      => __( 'Servicio', '' ),

        );

        return $columns;
    }

    /**
     * Render the designation name column
     *
     * @param  object  $item
     *
     * @return string
     */
    function column_pedido( $item ) {

        $actions           = array();
//        $actions['edit']   = sprintf( '<a href="%s" data-id="%d" title="%s">%s</a>', admin_url( 'admin.php?page=&action=edit&id=' . $item->id ), $item->id, __( 'Procesar', '' ), __( 'Procesar', '' ) );
     //   $actions['delete'] = sprintf( '<a href="%s" class="submitdelete" data-id="%d" title="%s">%s</a>', admin_url( 'admin.php?page=pakke-index&action=delete&id=' . $item->id ), $item->id, __( 'Borrar', '' ), __( 'Borrar', '' ) );

        return sprintf( '<a href="%1$s"><strong>%2$s</strong></a> %3$s', admin_url( 'post.php?post='.$item->pedido.'&action=edit'), $item->pedido, $this->row_actions( $actions ) );
    }

    /**
     * Get sortable columns
     *
     * @return array
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'pedido' => array( 'pedido', true ),
            'status' => array( 'pedido', true ),
            'fecha_orden' => array( 'fecha_orden', true ),
            'fecha_guia' => array( 'fecha_guia', true ),
            'no_guia' => array( 'no_guia', true ),

            'courier' => array( 'pedido', true ),
            'servicio' => array( 'pedido', true ),
        );

        return $sortable_columns;
    }

    /**
     * Set the bulk actions
     *
     * @return array
     */
    function get_bulk_actions() {
//        $actions = array(
//            'procesar'  => __( 'Procesar', '' ),
//        );
//        return $actions;
    }

    /**
     * Render the checkbox column
     *
     * @param  object  $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="Guia_id[]" value="%d" />', $item->id
        );
    }

    /**
     * Set the views
     *
     * @return array
     */
    public function get_views_() {
        $status_links   = array();
        $base_link      = admin_url( 'admin.php?page=pakke-index' );

        foreach ($this->counts as $key => $value) {
            $class = ( $key == $this->page_status ) ? 'current' : 'status-' . $key;
            $status_links[ $key ] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', add_query_arg( array( 'status' => $key ), $base_link ), $class, $value['label'], $value['count'] );
        }

        return $status_links;
    }

    /**
     * Prepare the class items
     *
     * @return void
     */
    function prepare_items() {

        $columns               = $this->get_columns();
        $hidden                = array( );
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $per_page              = 10;
        $current_page          = $this->get_pagenum();
        $offset                = ( $current_page -1 ) * $per_page;
        $this->page_status     = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '2';

        // only ncessary because we have sample data
        $args = array(
            'offset' => $offset,
            'number' => $per_page,
        );

        if ( isset( $_REQUEST['orderby'] ) && isset( $_REQUEST['order'] ) ) {
            $args['orderby'] = sanitize_text_field( $_REQUEST['orderby']);
            $args['order']   = sanitize_text_field($_REQUEST['order'] );
        }
        if(isset($_REQUEST['action']) && $_REQUEST['action']=='delete'){
            global $wpdb;
            $id=sanitize_text_field($_REQUEST['id']);
            $table_name = $wpdb->prefix . "guia";

            $response= $wpdb->delete(
                $table_name,
                array( 'id' => $id ),
                array( '%d' )
            );
        }

        $this->items  = _get_all_Guia( $args );

        $this->set_pagination_args( array(
            'total_items' => _get_Guia_count(),
            'per_page'    => $per_page
        ) );
    }
}
//------------------------------end tabla-------------------
function _get_all_Guia( $args = array() ) {
    global $wpdb;

    $defaults = array(
        'number'     => 20,
        'offset'     => 0,
        'orderby'    => 'id',
        'order'      => 'DESC',
    );

    $args      = wp_parse_args( $args, $defaults );
    $cache_key = 'Guia-all';
    $items     = wp_cache_get( $cache_key, '' );

    if ( false === $items ) {
        $items = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'guia ORDER BY ' . $args['orderby'] .' ' . $args['order'] .' LIMIT ' . $args['offset'] . ', ' . $args['number'] );

        wp_cache_set( $cache_key, $items, '' );
    }

    return $items;
}

/**
 * Fetch all Guia from database
 *
 * @return array
 */
function _get_Guia_count() {
    global $wpdb;

    return (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'guia' );
}

/**
 * Fetch a single Guia from database
 *
 * @param int   $id
 *
 * @return array
 */
function _get_Guia( $id = 0 ) {
    global $wpdb;

    return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . 'guia WHERE id = %d', $id ) );
}

// Hook in
    add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );

// Our hooked in function – $fields is passed via the filter!
    function custom_override_checkout_fields( $fields ) {
        $fields['shipping']['shipping_colonia'] = array(
            'label'     => __('Colonia', 'woocommerce'),
            'placeholder'   => _x('Colonia', 'placeholder', 'woocommerce'),
            'required'  => true,
            'class'     => array('form-row-wide'),
            'clear'     => true
        );

        return $fields;
    }

    /**
     * Display field value on the order edit page
     */

    add_action( 'woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );

    function my_custom_checkout_field_display_admin_order_meta($order){
        echo '<p><strong>'.__('Colonia').':</strong> ' . get_post_meta( $order->get_id(), '_shipping_colonia', true ) . '</p>';
    }
}



run_pakke();
