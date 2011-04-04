<?php
/*
Plugin Name: Google Maps
Plugin URI: http://www.osclass.org/
Description: This plugin shows a Google Map on the location space of every item.
Version: 2.0
Author: OSClass & kingsult
Author URI: http://www.osclass.org/
Plugin update URI: http://www.osclass.org/files/plugins/google_maps/update.php
*/

    function google_maps_call_after_install() {
        $fields              = array() ;
        $fields["s_section"] = 'plugin-google_maps' ;
        $fields["s_name"]    = 'google_maps_key' ;
        $fields["s_value"]   = '' ;
        $fields["e_type"]    = 'STRING' ;

        $dao_preference = new Preference() ;
        $dao_preference->insert($fields) ;
        unset($dao_preference) ;
    }

    function google_maps_call_after_uninstall() {
        $dao_preference = new Preference() ;
        $dao_preference->delete( array("s_section" => "plugin-google_maps", "s_name" => "google_maps_key") ) ;
        unset($dao_preference) ;
    }

    function google_maps_admin() {
        osc_admin_render_plugin('google_maps/admin.php') ;
    }

    function google_maps_location() {
        $item = osc_item();
        if(osc_google_maps_key() != '' && $item['d_coord_lat'] != '' && $item['d_coord_long'] != '') {
            osc_google_maps_header();
            require 'map.php';
        }
    }

    // HELPER
    function osc_google_maps_key() {
        return(osc_get_preference('google_maps_key', 'plugin-google_maps')) ;
    }

    function osc_google_maps_header() {
        if(osc_google_maps_key() != '') {
            echo '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=' . osc_google_maps_key() . '" type="text/javascript"></script>';
        }
    }

    function insert_geo_location($catId, $itemId) {
        $aItem = Item::newInstance()->findByPrimaryKey($itemId);
        $sAddress = (isset($aItem['s_address']) ? $aItem['s_address'] : '');
        $sRegion = (isset($aItem['s_region']) ? $aItem['s_region'] : '');
        $sCity = (isset($aItem['s_city']) ? $aItem['s_city'] : '');
        $address = sprintf('%s, %s %s', $sAddress, $sRegion, $sCity);
        $response = osc_file_get_contents(sprintf('http://maps.google.com/maps/geo?q=%s&output=json&sensor=false&key=%s', urlencode($address), osc_google_maps_key()));
        $jsonResponse = json_decode($response);
        if (isset($jsonResponse->Placemark) && count($jsonResponse->Placemark[0]) > 0) {
            $coord = $jsonResponse->Placemark[0]->Point->coordinates;
            ItemLocation::newInstance()->update (array('d_coord_lat' => $coord[1]
                                                      ,'d_coord_long' => $coord[0])
                                                ,array('fk_i_item_id' => $itemId));
        }
    }

    // This is needed in order to be able to activate the plugin
    osc_register_plugin(__FILE__, 'google_maps_call_after_install') ;
    // This is a hack to show a Uninstall link at plugins table (you could also use some other hook to show a custom option panel)
    osc_add_hook(__FILE__."_uninstall", 'google_maps_call_after_uninstall') ;
    osc_add_hook(__FILE__."_configure", 'google_maps_admin') ;

    osc_add_hook('location', 'google_maps_location') ;

    osc_add_hook('item_form_post', 'insert_geo_location') ;
    osc_add_hook('item_edit_post', 'insert_geo_location') ;

?>