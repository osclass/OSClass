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

     $aCurrency = View::newInstance()->_get('aCurrency') ;
     $typeForm  = View::newInstance()->_get('typeForm') ;

     if( $typeForm == 'add_post' ) {
         $title  = __('Add Currency') ;
         $submit = osc_esc_html( __('Add new currency') ) ;
     } else {
         $title  = __('Edit Currency') ;
         $submit = osc_esc_html( __('Update') ) ;
     }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="<?php echo str_replace('_', '-', osc_current_user_locale()) ; ?>">
    <head>
        <?php osc_current_admin_theme_path('head.php') ; ?>
    </head>
    <body>
        <?php osc_current_admin_theme_path('header.php') ; ?>
        <!-- container -->
        <div id="content">
            <?php osc_current_admin_theme_path( 'include/backoffice_menu.php' ) ; ?>
            <!-- right container -->
		    <div class="right">
                <div class="header_title">
                    <h1 class="currencies"><?php echo $title ; ?></h1>
                </div>
                <?php osc_show_admin_flash_messages() ; ?>
                <!-- currency-form form -->
                <div class="settings currency-form">
                    <form action="<?php echo osc_admin_base_url(true) ; ?>" method="post">
                        <input type="hidden" name="page" value="settings" />
                        <input type="hidden" name="action" value="currencies" />
                        <input type="hidden" name="type" value="<?php echo $typeForm ; ?>" />
                        <?php if( $typeForm == 'edit_post' ) { ?>
                        <input type="hidden" name="pk_c_code" value="<?php echo osc_esc_html($aCurrency['pk_c_code']) ; ?>" />
                        <?php } ?>
                        <fieldset>
                            <div class="input-line">
                                <label><?php _e('Currency Code') ; ?></label>
                                <div class="input">
                                    <input type="text" class="medium" name="pk_c_code" value="<?php echo osc_esc_html($aCurrency['pk_c_code']) ; ?>" <?php if( $typeForm == 'edit_post' ) echo 'disabled' ; ?>/>
                                    <p class="help-inline"><?php _e('It should be a three-character code') ; ?></p>
                                </div>
                            </div>
                            <div class="input-line">
                                <label><?php _e('Name') ; ?></label>
                                <div class="input">
                                    <input type="text" class="medium" name="s_name" value="<?php echo osc_esc_html($aCurrency['s_name']) ; ?>" />
                                </div>
                            </div>
                            <div class="input-line">
                                <label><?php _e('Description') ; ?></label>
                                <div class="input">
                                    <input type="text" class="xlarge" name="s_description" value="<?php echo osc_esc_html($aCurrency['s_description']) ; ?>" />
                                </div>
                            </div>
                            <div class="actions">
                                <input type="submit" value="<?php echo $submit ; ?>" />
                                <input type="button" onclick="location.href='<?php echo osc_admin_base_url(true) . '?page=settings&action=currencies' ; ?>'" value="<?php echo osc_esc_html( __('Cancel') ) ; ?>" />
                            </div>
                        </fieldset>
                    </form>
                </div>
                <!-- /currency-form form -->
            </div>
            <!-- /right container -->
        </div>
        <!-- /container -->
        <?php osc_current_admin_theme_path('footer.php') ; ?>
    </body>
</html>