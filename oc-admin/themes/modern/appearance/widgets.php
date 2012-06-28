<?php
    /**
     * OSClass – software for creating and publishing online classified advertising platforms
     *
     * Copyright (C) 2010 OSCLASS
     *
     * This program is free software: you can redistribute it and/or modify it under the terms
     * of the GNU Affero General Public License as published by the Free Software Foundation,
     * either version 3 of the License, or (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
     * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
     * See the GNU Affero General Public License for more details.
     *
     * You should have received a copy of the GNU Affero General Public
     * License along with this program. If not, see <http://www.gnu.org/licenses/>.
     */

    $info = __get("info") ;
    osc_add_hook('admin_page_header','customPageHeader');
    function customPageHeader(){ ?>
        <h1><?php _e('Appearance') ; ?></h1>
    <?php
    }
    function customPageTitle($string) {
        return sprintf(__('Appearance &raquo; %s'), $string);
    }
    osc_add_filter('admin_title', 'customPageTitle');

    osc_current_admin_theme_path( 'parts/header.php' ) ; ?>
<div id="appearance-page">
    <div class="appearance">
        <h2 class="render-title"><?php _e('Manage Widgets'); ?> </h2>
    </div></div></div> <!-- -->
        <div class="grid-system">
            <?php foreach($info['locations'] as $location) { ?>
                <div class="grid-row grid-50">
                    <div class="row-wrapper">
                        <div class="widget-box">
                            <div class="widget-box-title"><h3><?php printf( __('Section: %s'), $location ) ; ?> &middot; <a href="<?php echo osc_admin_base_url(true); ?>?page=appearance&amp;action=add_widget&amp;location=<?php echo $location ; ?>" class="btn float-right"><?php _e('Add HTML widget') ; ?></a></h3></div>
                            <div class="widget-box-content">
                                <?php $widgets = Widget::newInstance()->findByLocation($location) ; ?>
                                <?php if( count($widgets) > 0 ) {
                                    $countEvent = 1; ?>
                                    <table class="table" cellpadding="0" cellspacing="0">
                                        <tbody>
                                        <?php foreach($widgets as $w) { ?>
                                            <tr<?php if($countEvent%2 == 0){ echo ' class="even"';} if($countEvent == 1){ echo ' class="table-first-row"';} ?>>
                                                <td><?php echo __('Widget'). ' ' . $w['pk_i_id']; ?></td>
                                                <td><?php printf( __('Description: %s'), $w['s_description'] ) ; ?></td>
                                                <td>
                                                    <?php printf('<a href="%1$s?page=appearance&amp;action=edit_widget&amp;id=%2$s&amp;location=%3$s">' . __('Edit') .'</a>', osc_admin_base_url(true), $w['pk_i_id'], $location); ?>
                                                    <?php printf('<a href="%s?page=appearance&amp;action=delete_widget&amp;id=%d">' . __('Delete') .'</a>', osc_admin_base_url(true), $w['pk_i_id']) ; ?></td>
                                            </tr>
                                        <?php
                                        $countEvent++;
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="clear"></div>
        </div>
    </div>
</div>
<div class="grid-system">
            <div class="grid-row grid-100">
                <div class="row-wrapper">
<?php osc_current_admin_theme_path( 'parts/footer.php' ) ; ?>