<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       cwa.mx
 * @since      1.0.0
 *
 * @package    Pakke
 * @subpackage Pakke/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<input type="hidden" id="plugin_url" value="<?php echo esc_html( str_replace( __FILE__, '', plugin_dir_url( __FILE__ ) ) ) ?>">
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">

<!--    <h2>--><?php //echo esc_html(get_admin_page_title()); ?><!--</h2>-->
    <img width="300" id="logo-pakke" src="<?php echo esc_url( plugin_dir_url( __FILE__ ).'../img/logo-pakke.svg') ?>" alt="">
    <button class="button-primary" id="pakke-btn-portal">Portal del Cliente</button>
    <button class="button-primary" id="pakke-btn-ajustes"><a href="<?php echo esc_url( get_site_url()."/wp-admin/admin.php?page=wc-settings&tab=shipping&section=pakke_shipping") ?>">Ajustes</a></button>
    <style>
        .tab-content > .active {
            display: block;
        }
        .notice{
            display: none;
        }
        #pakke-btn-ajustes a{
            color: white !important;
        }
    </style>

    <style>
        body {
            color: #2c3e50;
            background: #ecf0f1;
        }
        h1 {
            margin: 0;
            line-height: 2;
            text-align: center;
        }
        h2 {
            margin: 0 0 0.5em;
            font-weight: normal;
        }
        input {
            position: absolute;
            opacity: 0;
            z-index: -1;
        }
        .row {
            display: flex;
        }
        .row .col {
            flex: 1;
        }
        .row .col:last-child {
            margin-left: 1em;
        }
        /* Accordion styles */
        .tabs {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 4px -2px rgba(0, 0, 0, 0.5);
        }
        .tab {
            width: 100%;
            color: white;
            overflow: hidden;
        }
        .tab-label {
            display: flex;
            justify-content: space-between;
            padding: 1em;
            background: white;
            font-weight: bold;
            cursor: pointer;
            color: black;
            /* Icon */
        }
        .tab-label:hover {
            background: #E40E20;
        }
        .tab-label::after {
            content: "\276F";
            width: 1em;
            height: 1em;
            text-align: center;
            transition: all 0.35s;
        }
        .tab-content {
            max-height: 0;
            padding: 0 1em;
            color: #2c3e50;
            background: white;
            transition: all 0.35s;
        }
        .tab-close {
            display: flex;
            justify-content: flex-end;
            padding: 1em;
            font-size: 0.75em;
            background: #2c3e50;
            cursor: pointer;
        }
        .tab-close:hover {
            background: #E40E20;
        }
        input:checked + .tab-label {
            background: #E40E20;
            color: white;
        }
        input:checked + .tab-label::after {
            transform: rotate(90deg);
        }
        input:checked ~ .tab-content {
            max-height: 100vh;
            padding: 1em;
        }

    </style>

    <ul class="nav nav-tabs mt-4" id="pakke-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="envio-tab" data-bs-toggle="tab" data-bs-target="#envio" type="button" role="tab" aria-controls="home" aria-selected="true">Envíos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="doc-tab" data-bs-toggle="tab" data-bs-target="#doc" type="button" role="tab" aria-controls="doc" aria-selected="false">Documentación</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#acerca" type="button" role="tab" aria-controls="acerca" aria-selected="false">Acerca de</button>
        </li>
    </ul>
    <div class="tab-content" id="pakke-TabContent">
        <div class="tab-pane fade show active" id="envio" role="tabpanel" aria-labelledby="envio-tab">
            <div class="wrap">
                <h2><?php _e( 'Guias', '' ); ?> </h2>

                <form method="post">
                    <input type="hidden" name="page" value="ttest_list_table">
                    <?php
                    $list_table = new Pakke_Guia_List();
                    $list_table->prepare_items();
                    $list_table->search_box( 'search', 'search_id' );
                    $list_table->display();
                    ?>
                </form>
            </div>
        </div>
        <div class="tab-pane fade" id="doc" role="tabpanel" aria-labelledby="doc-tab">
            <div class="row">
                <div class="col">
                    <div class="accordion accordion-flush mb-4" id="accordionDocumentacion">

                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="acerca" role="tabpanel" aria-labelledby="acerca-tab">

        </div>
    </div>
    </div>
    </div>
</div>