<?php

osc_add_filter('admin_body_class', 'admin_modeCompact_class');
function admin_modeCompact_class($args){
    $compactMode = osc_get_preference('compact_mode','modern_admin_theme');
    if($compactMode == true){
        $args[] = 'compact';
    }
    return $args;
}
osc_add_hook('ajax_admin_compactmode','modern_compactmode_actions');
function modern_compactmode_actions(){
    $compactMode = osc_get_preference('compact_mode','modern_admin_theme');
    $modeStatus  = array('compact_mode'=>true);
    if($compactMode == true){
        $modeStatus['compact_mode'] = false;
    }
    osc_set_preference('compact_mode', $modeStatus['compact_mode'], 'modern_admin_theme');
    echo json_encode($modeStatus);
}

function admin_header_favicons() {
    $favicons   = array();
    $favicons[] = array(
        'rel'   => 'shortcut icon',
        'sizes' => '',
        'href'  => osc_current_admin_theme_url('images/favicon-48.png')
    );
    $favicons[] = array(
        'rel'   => 'apple-touch-icon-precomposed',
        'sizes' => '144x144',
        'href'  => osc_current_admin_theme_url('images/favicon-144.png')
    );
    $favicons[] = array(
        'rel'   => 'apple-touch-icon-precomposed',
        'sizes' => '114x114',
        'href'  => osc_current_admin_theme_url('images/favicon-114.png')
    );
    $favicons[] = array(
        'rel'   => 'apple-touch-icon-precomposed',
        'sizes' => '72x72',
        'href'  => osc_current_admin_theme_url('images/favicon-72.png')
    );
    $favicons[] = array(
        'rel'   => 'apple-touch-icon-precomposed',
        'sizes' => '',
        'href'  => osc_current_admin_theme_url('images/favicon-57.png')
    );

    $favicons = osc_apply_filter('admin_favicons', $favicons);
?>
        <!-- favicons
        ================================================== -->
<?php
    foreach($favicons as $f) { ?>
        <link <?php if($f['rel'] !== '') { ?>rel="<?php echo $f['rel']; ?>" <?php } if($f['sizes'] !== '') { ?>sizes="<?php echo $f['sizes']; ?>" <?php } ?>href="<?php echo $f['href']; ?>">
    <?php }
}
osc_add_hook('admin_header', 'admin_header_favicons');

function admin_footer_html() { ?>
    <div class="float-left">
        <?php printf(__('Thank you for using <a href="%s" target="_blank">OSClass</a>'), 'http://osclass.org/'); ?> -
        <a title="<?php _e('Documentation'); ?>" href="http://doc.osclass.org/" target="_blank"><?php _e('Documentation'); ?></a> &middot;
        <a title="<?php _e('Forums'); ?>" href="http://forums.osclass.org/" target="_blank"><?php _e('Forums'); ?></a>
    </div>
    <div class="float-right">
        <strong>OSClass <?php echo preg_replace('|.0$|', '', OSCLASS_VERSION); ?></strong>
    </div>
    <a id="ninja" href="" class="ico ico-48 ico-dash-white"></a>
    <div class="clear"></div>
    <form id="donate-form" name="_xclick" action="https://www.paypal.com/in/cgi-bin/webscr" method="post" target="_blank">
       <input type="hidden" name="cmd" value="_donations">
       <input type="hidden" name="business" value="info@osclass.org">
       <input type="hidden" name="item_name" value="OSClass project">
       <input type="hidden" name="return" value="<?php echo osc_admin_base_url(); ?>">
       <input type="hidden" name="currency_code" value="USD">
       <input type="hidden" name="lc" value="US" />
    </form>
    <!-- javascript
    ================================================== -->
    <script type="text/javascript">
        var $ninja = $('#ninja');

        $ninja.click(function(){
            jQuery('#donate-form').submit();
            return false;
        });
    </script><?php
}
osc_add_hook('admin_content_footer', 'admin_footer_html');

function admin_theme_js() { ?>
    <!-- scripts
    ================================================== -->
    <?php osc_load_scripts();
}
osc_add_hook('admin_header', 'admin_theme_js', 1);
function admin_theme_css() { ?>
    <!-- styles
    ================================================== -->
    <?php osc_load_styles();
}
osc_add_hook('admin_header', 'admin_theme_css', 2);

function printLocaleTabs($locales = null) {
    if($locales==null) { $locales = osc_get_locales(); }
    $num_locales = count($locales);
    if($num_locales>1) {
    echo '<div id="language-tab" class="ui-osc-tabs ui-tabs ui-widget ui-widget-content ui-corner-all"><ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">';
        foreach($locales as $locale) {
            echo '<li class="ui-state-default ui-corner-top"><a href="#'.$locale['pk_c_code'].'">'.$locale['s_name'].'</a></li>';
        }
    echo '</ul></div>';
    };
}
function printLocaleTitle($locales = null, $item = null) {
    if($locales==null) { $locales = osc_get_locales(); }
    if($item==null) { $item = osc_item(); }
    $num_locales = count($locales);
    foreach($locales as $locale) {
        echo '<div class="input-has-placeholder input-title-wide"><label for="title">' . __('Enter title here') . ' *</label>';
        $title = (isset($item) && isset($item['locale'][$locale['pk_c_code']]) && isset($item['locale'][$locale['pk_c_code']]['s_title'])) ? $item['locale'][$locale['pk_c_code']]['s_title'] : '' ;
        if( Session::newInstance()->_getForm('title') != "" ) {
            $title_ = Session::newInstance()->_getForm('title');
            if( $title_[$locale['pk_c_code']] != "" ){
                $title = $title_[$locale['pk_c_code']];
            }
        }
        $name = 'title'. '[' . $locale['pk_c_code'] . ']';
        echo '<input id="' . $name . '" type="text" name="' . $name . '" value="' . osc_esc_html(htmlentities($title, ENT_COMPAT, "UTF-8")) . '"  />' ;
        echo '</div>';
    }
}
function printLocaleTitlePage($locales = null,$page = null) {
    if($locales==null) { $locales = osc_get_locales(); }
    $aFieldsDescription = Session::newInstance()->_getForm("aFieldsDescription");
    $num_locales = count($locales);
    echo '<label for="title">' . __('Title') . ' *</label>';

    foreach($locales as $locale) {
        $title = '';
        if(isset($page['locale'][$locale['pk_c_code']])) {
            $title = $page['locale'][$locale['pk_c_code']]['s_title'];
        }
        if( isset($aFieldsDescription[$locale['pk_c_code']]) && isset($aFieldsDescription[$locale['pk_c_code']]['s_title']) &&$aFieldsDescription[$locale['pk_c_code']]['s_title'] != '' ) {
            $title = $aFieldsDescription[$locale['pk_c_code']]['s_title'];
        }
        $name = $locale['pk_c_code'] . '#s_title';

        echo '<div class="input-has-placeholder input-title-wide"><label for="title">' . __('Enter title here') . ' *</label>';
        echo '<input id="' . $name . '" type="text" name="' . $name . '" value="' . osc_esc_html(htmlentities($title, ENT_COMPAT, "UTF-8")) . '"  />' ;
        echo '</div>';
    }
}
function printLocaleDescription($locales = null, $item = null) {
    if($locales==null) { $locales = osc_get_locales(); }
    if($item==null) { $item = osc_item(); }
    $num_locales = count($locales);
    foreach($locales as $locale) {
        $name = 'description'. '[' . $locale['pk_c_code'] . ']';

        echo '<div><label for="description">' . __('Description') . ' *</label>';
        $description = (isset($item) && isset($item['locale'][$locale['pk_c_code']]) && isset($item['locale'][$locale['pk_c_code']]['s_description'])) ? $item['locale'][$locale['pk_c_code']]['s_description'] : '';
        if( Session::newInstance()->_getForm('description') != "" ) {
            $description_ = Session::newInstance()->_getForm('description');
            if( $description_[$locale['pk_c_code']] != "" ){
                $description = $description_[$locale['pk_c_code']];
            }
        }
        echo '<textarea id="' . $name . '" name="' . $name . '" rows="10">' . $description . '</textarea></div>' ;
    }
}
function printLocaleDescriptionPage($locales = null, $page = null) {
    if($locales==null) { $locales = osc_get_locales(); }
    $aFieldsDescription = Session::newInstance()->_getForm("aFieldsDescription");
    $num_locales = count($locales);

    foreach($locales as $locale) {
        $description = '';
        if(isset($page['locale'][$locale['pk_c_code']])) {
            $description = $page['locale'][$locale['pk_c_code']]['s_text'];
        }
        if( isset($aFieldsDescription[$locale['pk_c_code']]) && isset($aFieldsDescription[$locale['pk_c_code']]['s_text']) &&$aFieldsDescription[$locale['pk_c_code']]['s_text'] != '' ) {
            $description = $aFieldsDescription[$locale['pk_c_code']]['s_text'];
        }
        $name = $locale['pk_c_code'] . '#s_text';
        echo '<div><label for="description">' . __('Description') . ' *</label>';
        echo '<textarea id="' . $name . '" name="' . $name . '" rows="10">' . $description . '</textarea></div>' ;
    }
}

/* end of file */