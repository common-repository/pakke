<?php

/**
 * Fired during plugin deactivation
 *
 * @link       cwa.mx
 * @since      1.0.0
 *
 * @package    Pakke
 * @subpackage Pakke/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Pakke
 * @subpackage Pakke/includes
 * @author     CWA <info@cwa.mx>
 */
class Pakke_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
    public static function deactivate() {
        self::drop_db_guia();
        self::drop_db_guiasxorden();

    }
    public static function drop_db_guia() {
        global $wpdb;
        $table_name = $wpdb->prefix . "guia";
        $sql = "DROP TABLE IF EXISTS " . $table_name;
        $wpdb->query($sql);
    }
    public static function drop_db_guiasxorden() {
        global $wpdb;
        $table_name = $wpdb->prefix . "guiasxorden";
        $sql = "DROP TABLE IF EXISTS " . $table_name;
        $wpdb->query($sql);
    }

}
