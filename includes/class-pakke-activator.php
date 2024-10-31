<?php

/**
 * Fired during plugin activation
 *
 * @link       cwa.mx
 * @since      1.0.0
 *
 * @package    Pakke
 * @subpackage Pakke/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Pakke
 * @subpackage Pakke/includes
 * @author     CWA <info@cwa.mx>
 */
class Pakke_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        self::create_db_guia();
        self::create_db_guiasxorden();

    }
    public static function create_db_guia() {
        global $wpdb;
        $table_name = $wpdb->prefix . "guia";
        $charset_collate = $wpdb->get_charset_collate();

        $sql[] = "CREATE TABLE " . $table_name . " ( 
    id int(11) UNSIGNED NOT NULL AUTO_INCREMENT, 
    pedido text,
    status text,
    fecha_orden date DEFAULT '0000-00-00', 
    fecha_guia date ,
    no_guia text,
    courier text,
    servicio text,
    
   PRIMARY KEY (id) ) $charset_collate";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        dbDelta( $sql );
    }
    public static function create_db_guiasxorden() {
        global $wpdb;
        $table_name = $wpdb->prefix . "guiasxorden";
        $charset_collate = $wpdb->get_charset_collate();

        $sql[] = "CREATE TABLE " . $table_name . " ( 
    id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    orden int,
    courier text,
    servicio text,    
    fecha_guia date ,
    costo text,
    no_guia text,
    descargar text,
   PRIMARY KEY (id) ) $charset_collate";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        dbDelta( $sql );
    }
}
