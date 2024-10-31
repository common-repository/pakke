<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       cwa.mx
 * @since      1.0.0
 *
 * @package    Pakke
 * @subpackage Pakke/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pakke
 * @subpackage Pakke/admin
 * @author     CWA <info@cwa.mx>
 */
class Pakke_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Pakke_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Pakke_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */


        wp_register_style($this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pakke-admin.css');
        wp_enqueue_style($this->plugin_name);
        wp_register_style($this->plugin_name.'bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.css');
        wp_enqueue_style($this->plugin_name.'bootstrap');
        wp_register_style($this->plugin_name.'datatables', plugin_dir_url( __FILE__ ) . 'css/dataTables.bootstrap.min.css');
        wp_enqueue_style($this->plugin_name.'datatables');
        wp_register_style($this->plugin_name.'font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.css');
        wp_enqueue_style($this->plugin_name.'font-awesome');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Pakke_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Pakke_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_register_script($this->plugin_name.'_script', plugin_dir_url( __FILE__ ) . 'js/pakke-admin.js',array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name.'_script');
        wp_register_script($this->plugin_name.'bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.js',array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name.'bootstrap');
        wp_register_script($this->plugin_name.'databootstrap', plugin_dir_url( __FILE__ ) . 'js/datatables.js',array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name.'databootstrap');
        wp_register_script($this->plugin_name.'datatables', plugin_dir_url( __FILE__ ) . 'js/dataTables.bootstrap.min.js',array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name.'datatables');

    }

    public function add_menu() {
        // add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        add_menu_page(
            "Pakke",  // Título de la página
            "Pakke",  // Literal de la opción
            "manage_options",  // Dejadlo tal cual
            'pakke-index',  // Slug
            array( $this, 'pakke_index' ),
            plugin_dir_url( __FILE__ ).'img/logo_cuadros_rojos.png'

        );
        // Se añade enlace en las optiones de Ajustes de WordPress
        add_options_page(
            "Pakke",  // Título de la página
            "Pakke",  // Literal de la opción
            "manage_options",  // Dejadlo tal cual
            $this->plugin_name, array($this, 'display_plugin_setup_page'));
    }
    public function pakke_index() {
        include_once 'partials/pakke-admin-display.php';

    }
    public function display_plugin_setup_page() {
        include_once 'partials/pakke-admin-display.php';
    }
    public function add_action_links( $links ) {
        $settings_link = array('<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Ajustes', $this->plugin_name) . '</a>',);
        return array_merge( $settings_link, $links );

    }
    public function validate($input) {

        $valid = array();


        $valid['key'] = (isset($input['key']) && !empty($input['key'])) ?  $input['key']: '';
        //return 1;
        return $valid;
    }
    public function options_update() {
        register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
    }

    public function pakke_connect_get() {
        $ruta=isset( $_POST['url'] ) ? sanitize_text_field( $_POST['url'] ) : '';


        $key = get_option('woocommerce_pakke_shipping_settings')['apikey'];


        $url = ' https://seller.pakke.mx/api/v1' . $ruta;



        $httpresponse = wp_remote_get( $url ,
            array( 'timeout' => 10,
                'headers' => array(
                    'Content-Type'=> 'application/json',
                    'Accept' => "application/json",
                    'Authorization'=> "$key"
                    )
            ));

        $response = json_decode($httpresponse['body'], true);


        echo json_encode($response);

        wp_die();
    }

    public function pakke_connect_portal() {


        $key = get_option('woocommerce_pakke_shipping_settings')['apikey'];
        $token='6eUCRMRFMknf2mfVJK2FfEIvhFVsjhKrBkg8LfJuEC1THQM77fVFCoko7jOTtRPR';

        $url = 'https://seller.pakke.mx/api/v1/Management/AuthToken/'.$key.'?access_token='.$token;


        $httpresponse = wp_remote_get( $url ,
            array( 'timeout' => 2000,

                'headers' => array(
                    'Content-Type'=> 'application/json',
                    'Accept' => "application/json",
                    'Authorization'=> "$key"
                )

            ));

        $response = json_decode($httpresponse['body'], true);


        echo json_encode($response);

        wp_die();
    }
    public function pakke_courier_img() {



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

//        foreach ($response['courierDetail'] as $k=> $c){
//                if($c->code==$courier_id){
//                    echo $c->logo;
//                    wp_die();
//                    break;
//                }
//
//        }

echo json_encode($response['courierDetail']);
wp_die();


    }
    public function statecode($urlcode,$zip){
        $key = get_option('woocommerce_pakke_shipping_settings')['apikey'];
        $httpresponse_code = wp_remote_get( $urlcode.''.$zip ,
            array( 'timeout' => 2000,
                'headers' => array(
                    'Content-Type'=> 'application/json',
                    'Accept' => "application/json",
                    'Authorization'=> "$key"
                )
            ));
        $response_code = json_decode($httpresponse_code['body'], true);
        return $response_code;
    }
    public function pakke_cotizar_guia()
    {
        $order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : '';
        $largo = isset( $_POST['largo'] ) ? sanitize_text_field( $_POST['largo'] ) : 0;
        $ancho = isset( $_POST['ancho'] ) ? sanitize_text_field( $_POST['ancho'] ) : 0;
        $alto = isset( $_POST['alto'] ) ? sanitize_text_field( $_POST['alto'] ) : 0;
        $peso = isset( $_POST['peso'] ) ? sanitize_text_field( $_POST['peso'] ) : 0;
        $order = wc_get_order($order_id);
        $origin_postcode = get_option('woocommerce_store_postcode');
        $postal_code = $order->shipping_postcode;
        

        $key = get_option('woocommerce_pakke_shipping_settings')['apikey'];
        $maxweight = 60;
        $maxwidth = 115;
        $maxheight = 115;
        $maxlength = 150;
        $url = ' https://seller.pakke.mx/api/v1/Shipments/rates';
        $response='';
        if ($peso <= $maxweight && $ancho <= $maxwidth && $alto <= $maxheight && $largo <= $maxlength && ($ancho*$alto*$largo/5000)<=$maxweight) {

            $body = [
                "ZipCodeFrom" => "$origin_postcode",
                "ZipCodeTo" => "$postal_code",
                "Parcel" => array(
                    "Weight" => $peso,
                    "Width" => $ancho,
                    "Height" => $alto,
                    "Length" => $largo
                ),
                "CouponCode" => null,
                "InsuredAmount" => 1000,
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
        }
        echo json_encode($response);
        wp_die();
    }

    public function pakke_connect_guia()

    {

        $order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : '';
        $largo = isset( $_POST['largo'] ) ? sanitize_text_field( $_POST['largo'] ) : 0;
        $ancho = isset( $_POST['ancho'] ) ? sanitize_text_field( $_POST['ancho'] ) : 0;
        $alto = isset( $_POST['alto'] ) ? sanitize_text_field( $_POST['alto'] ) : 0;
        $peso = isset( $_POST['peso'] ) ? sanitize_text_field( $_POST['peso'] ) : 0;
        $courier_id = isset($_POST['courier_id']) ? sanitize_text_field($_POST['courier_id']) : '';
        $service_id = isset($_POST['service_id']) ? sanitize_text_field($_POST['service_id']) : 0;
        if($courier_id=="FIJO"){
            $courier_id="";
            $service_id="";
        }
        $tipo_servicio = isset($_POST['tipo_servicio']) ? sanitize_text_field($_POST['tipo_servicio']) : '';
        $costo = isset($_POST['costo']) ? sanitize_text_field($_POST['costo'] ): '';


        $url = ' https://seller.pakke.mx/api/v1/Shipments';
        $urlcode = ' https://seller.pakke.mx/api/v1/ZipCodes/';

        $order = wc_get_order($order_id);
        $shipping_method = @array_shift($order->get_shipping_methods());
        $shipping_method_id = $shipping_method['method_id'];

        $CourierCode = '';
        $total_weight = 0;
        $total_width = 0;
        $total_height = 0;
        $total_length = 0;

        $product_list = "";

        foreach ($order->get_items() as $key => $item) {
            $product_list .= $item->get_name() . ",";
            $CourierCode = wc_get_order_item_meta($key, '_shipping_rate_id');
            // var_dump($CourierCode);
            $code = explode("%", $CourierCode)[0];
            $c_id = explode("%", $CourierCode)[1];
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


        $origin_postcode = get_option('woocommerce_pakke_shipping_settings')['cp'];
        $to_state = $this->statecode($urlcode, $order->shipping_postcode)['StateCode'];
        $store_state=get_option('woocommerce_pakke_shipping_settings')['estado'];
        $origin_address = get_option('woocommerce_pakke_shipping_settings')['direccion1'];
        $origin_city = get_option('woocommerce_pakke_shipping_settings')['ciudad'];
        $origin_name = get_option('woocommerce_pakke_shipping_settings')['name'];
        $origin_email = get_option('woocommerce_pakke_shipping_settings')['email'];
        $origin_phone1 = get_option('woocommerce_pakke_shipping_settings')['phone1'];
        $origin_phone2 = get_option('woocommerce_pakke_shipping_settings')['phone2'];
        $origin_colonia = get_option('woocommerce_pakke_shipping_settings')['colonia'];


        $user_id = get_post_meta($order_id, '_customer_user', true);
        $customer = new WC_Customer($user_id);
        $email = $customer->get_email();


        $body = [
            "CourierCode" => "$courier_id",
            "CourierServiceId" => "$service_id",
            "ResellerReference" => "PRUEBA_NO_RECOLECTAR",
            "Content" => substr($product_list, 0, 24),
            "AddressFrom" => array(
                "ZipCode" => "$origin_postcode",
                "State" => "$store_state",
                "City" => "$origin_city",
                "Neighborhood" => "$origin_colonia",
                "Address1" => $origin_address,
                "Address2" => "",
                "Residential" => 1
            ),
            "AddressTo" => array(
                "ZipCode" => "$order->shipping_postcode",
                "State" => "$to_state",
                "City" => "$order->shipping_city",
                "Neighborhood" => "$order->shipping_colonia",
                "Address1" => substr($order->shipping_address_1, 0, 29),
                "Address2" => "",
                "Residential" => 1
            ),
            "Parcel" => array(
                "Weight" => $peso,
                "Width" => $ancho,
                "Height" => $alto,
                "Length" => $largo
            ),
            "Sender" => array(
                "Name" => "$origin_name",
                "Phone1" => "$origin_phone1",
                "Phone2" => "$origin_phone2",
                "Email" => "$origin_email"
            ),
            "Recipient" => array(
                "Name" => "$order->shipping_first_name" . " $order->shipping_last_name",
                "CompanyName" => "$order->shipping_company",
                "Phone1" => "$order->billing_phone",
                "Email" => "$email"
            ),

        ];

        $body = wp_json_encode($body);
        $key = get_option('woocommerce_pakke_shipping_settings')['apikey'];

        $httpresponse = wp_remote_post($url,
            array('timeout' => 2000,
                'body' => $body,
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Accept' => "application/json",
                    'Authorization' => "$key"
                )

            ));

        $response = json_decode($httpresponse['body'], true);


        echo json_encode($response);

        global $wpdb;
        $table_name = $wpdb->prefix . "guiasxorden";
        if (!$response['error']) {

        $wpdb->insert(
            $table_name,
            array(
                'orden' => $order_id,
                'courier' => $courier_id,
                'servicio' => $response['CourierService'],
                'fecha_guia' => date('Y-m-d'),
                'costo' => $response['CoveredAmount'],
                'no_guia' => $response['TrackingNumber'],
                'descargar' => $response['Label'],

            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',


            )
        );
    }

        wp_die();
    }

function save_guia($order_id){

    $order = wc_get_order($order_id);
    $shipping_method = @array_shift($order->get_shipping_methods());
    $shipping_method_id = $shipping_method['method_id'];
    if($shipping_method_id=='pakke_shipping'){


        global $wpdb;


    $table_name = $wpdb->prefix . "guia";

        $wpdb->insert(
            $table_name,
            array(
                'pedido' => "$order_id",
                'status' => 'No procesado',
                'fecha_orden'=>$order->order_date,
                'fecha_guia' => '',
                'no_guia' => '',
                'courier' => '',
                'servicio' => '',
            ),
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',

            )
        );
    }

        wp_die();


}

    function pakke_update_guia(){

        $order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : '';


        $fecha_guia=isset( $_POST['fecha_guia'] ) ? sanitize_text_field( $_POST['fecha_guia'] ) : '';
        $no_guia=isset( $_POST['no_guia'] ) ? sanitize_text_field( $_POST['no_guia'] ) : '';
        $courier=isset( $_POST['courier'] ) ? sanitize_text_field( $_POST['courier'] ) : '';
        $servicio=isset( $_POST['servicio'] ) ? sanitize_text_field( $_POST['servicio'] ) : '';

        global $wpdb;
        $table_name = $wpdb->prefix . "guia";
        $wpdb->update(
            $table_name,
            array(
                'status'=>"Procesada",
                'fecha_guia'=>$fecha_guia,
                'no_guia'=>$no_guia,
                'courier'=>$courier,
                'servicio'=>$servicio,


            ),
            array( 'pedido' => $order_id ),
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',


            ),
            array( '%d' )
        );

        echo json_encode($order_id);




        wp_die();
    }
    public function pakke_cargar_guia(){
        $order_id = isset( $_POST['order_id'] ) ? sanitize_text_field( $_POST['order_id'] ) : '';
        global $wpdb;

        $response = array();

        $table_name = $wpdb->prefix . "guiasxorden";

        $response = $wpdb->get_results("SELECT * FROM ".$table_name." where orden=".$order_id);

        echo json_encode($response);

        wp_die();

    }




}
