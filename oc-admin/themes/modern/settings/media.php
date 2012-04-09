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

     $maxPHPsize    = View::newInstance()->_get('max_size_upload') ;
     $imagickLoaded = extension_loaded('imagick') ;
     $aGD           = @gd_info() ;
     $freeType      = array_key_exists('FreeType Support', $aGD) ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="<?php echo str_replace('_', '-', osc_current_user_locale()) ; ?>">
    <head>
        <?php osc_current_admin_theme_path('head.php') ; ?>
        <link rel="stylesheet" media="screen" type="text/css" href="<?php echo osc_current_admin_theme_js_url('colorpicker/css/colorpicker.css') ; ?>" />
        <script type="text/javascript" src="<?php echo osc_current_admin_theme_js_url('colorpicker/js/colorpicker.js') ; ?>"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#colorpickerField').ColorPicker({
                    onSubmit: function(hsb, hex, rgb, el) { },
                    onChange: function (hsb, hex, rgb) {
                        $('#colorpickerField').val(hex) ;
                    }
                }) ;

                $('#watermark_none').bind('change', function() {
                    if( $(this).attr('checked') ) {
                        $('#watermark_text_box').hide() ;
                        $('#watermark_image_box').hide() ;
                    }
                }) ;

                $('#watermark_text').bind('change', function() {
                    if( $(this).attr('checked') ) {
                        $('#watermark_text_box').show() ;
                        $('#watermark_image_box').hide() ;
                        if( !$('input[name="keep_original_image"]').attr('checked') ) {
                            alert('<?php echo addslashes( __("It's highly recommended to have 'Keep original image' option active when you use watermarks.") ) ; ?>') ;
                        }
                    }
                }) ;

                $('#watermark_image').bind('change', function() {
                    if( $(this).attr('checked') ) {
                        $('#watermark_text_box').hide() ;
                        $('#watermark_image_box').show() ;
                        if( !$('input[name="keep_original_image"]').attr('checked') ) {
                            alert('<?php echo addslashes( __("It's highly recommended to have 'Keep original image' option active when you use watermarks.") ) ; ?>') ;
                        }
                    }
                }) ;

                $('input[name="keep_original_image"]').change(function() {
                    if( !$(this).attr('checked') ) {
                        if( !$('#watermark_none').attr('checked') ) {
                            alert('<?php echo addslashes( __("It's highly recommended to have 'Keep original image' option active when you use watermarks.") ) ; ?>') ;
                        }
                    }
                }) ;
            }) ;
        </script>
    </head>
    <body>
        <?php osc_current_admin_theme_path('header.php') ; ?>
        <!-- container -->
        <div id="content">
            <?php osc_current_admin_theme_path ( 'include/backoffice_menu.php' ) ; ?>
            <!-- right container -->
            <div class="right">
                <div class="header_title">
                    <h1 class="media"><?php _e('Media Settings') ; ?></h1>
                </div>
                <?php osc_show_admin_flash_messages() ; ?>
                <!-- media settings -->
                <div class="settings media">
                    <!-- media form -->
                    <form action="<?php echo osc_admin_base_url(true) ; ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="page" value="settings" />
                        <input type="hidden" name="action" value="media_post" />
                        <fieldset>
                            <h2><?php _e('Images sizes') ; ?></h2>
                            <p class="text"><?php _e('The sizes listed below determine the maximum dimensions in pixels to use when uploading a image. Format: <b>Width</b> x <b>Height</b>.') ; ?></p>
                            <div class="input-line">
                                <label><?php _e('Thumbnail size') ; ?></label>
                                <div class="input">
                                    <input type="text" class="small" name="dimThumbnail" value="<?php echo osc_esc_html( osc_thumbnail_dimensions() ) ; ?>" />
                                </div>
                            </div>
                            <div class="input-line">
                                <label><?php _e('Preview size') ; ?></label>
                                <div class="input">
                                    <input type="text" class="small" name="dimPreview" value="<?php echo osc_esc_html( osc_preview_dimensions() ) ; ?>" />
                                </div>
                            </div>
                            <div class="input-line">
                                <label><?php _e('Normal size') ; ?></label>
                                <div class="input">
                                    <input type="text" class="small"  name="dimNormal" value="<?php echo osc_esc_html( osc_normal_dimensions() ) ; ?>" />
                                </div>
                            </div>
                            <div class="input-line">
                                <label><?php _e('Original image') ; ?></label>
                                <div class="input">
                                    <label class="checkbox">
                                        <input type="checkbox" name="keep_original_image" value="1" <?php echo ( osc_keep_original_image() ? 'checked' : '' ) ; ?>/>
                                        <?php _e('Keep original image, unaltered after uploading.') ; ?>
                                        <p class="help-inline"><?php _e('It might ocuppy more space than usual.') ; ?></p>
                                    </label>
                                </div>
                            </div>
                            <h2><?php _e('Restrictions') ; ?></h2>
                            <div class="input-line">
                                <label><?php _e('Maximum size') ; ?></label>
                                <div class="input">
                                    <input type="text" class="medium" name="maxSizeKb" value="<?php echo osc_esc_html( osc_max_size_kb() ) ; ?>" />
                                    <p class="help-inline"><?php _e('Size in KB') ; ?></p>
                                    <div class="alert alert-inline alert-warning">
                                        <p><?php printf( __('Maximum size PHP configuration allows: %d KB'), $maxPHPsize ) ; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="input-line">
                                <label><?php _e('Allowed formats') ; ?></label>
                                <div class="input">
                                    <input type="text" class="medium" name="allowedExt" value="<?php echo osc_esc_html( osc_allowed_extension() ) ; ?>" />
                                    <p class="help-inline"><?php _e('For example: jpg, png, gif') ; ?></p>
                                </div>
                            </div>
                            <div class="input-line">
                                <label><?php _e('ImageMagick') ; ?></label>
                                <div class="input">
                                    <label class="checkbox">
                                        <input type="checkbox" name="use_imagick" value="1" <?php echo ( osc_use_imagick() ? 'checked' : '' ) ; ?> <?php if( !$imagickLoaded ) echo 'disabled' ; ?>/>
                                        <?php _e('Use ImageMagick instead of GD library') ; ?>
                                        <?php if( !$imagickLoaded ) { ?>
                                        <div class="alert alert-inline alert-error">
                                            <p><?php _e('ImageMagick library is not loaded') ; ?></p>
                                        </div>
                                        <?php } ?>
                                        <p class="help"><?php _e("It's faster and consume less resources than GD library.") ; ?></p>
                                    </label>
                                </div>
                            </div>
                            <h2><?php _e('Watermark') ; ?></h2>
                            <div class="input-line">
                                <label><?php _e('Watermark type'); ?></label>
                                <div class="input">
                                    <label class="radio">
                                        <input type="radio" id="watermark_none" name="watermark_type" value="none" <?php echo ( ( !osc_is_watermark_image() && !osc_is_watermark_text() ) ? 'checked="checked"' : '' ) ; ?> />
                                        <?php _e('None') ; ?>
                                    </label>
                                    <label class="radio">
                                        <input type="radio" id="watermark_text" name="watermark_type" value="text" <?php echo ( osc_is_watermark_text() ? 'checked="checked"' : '' ) ; ?> <?php echo ( $freeType ? '' : 'disabled' ) ; ?> />
                                        <?php _e('Text') ; ?>
                                        <?php if( !$freeType ) { ?>
                                        <div class="alert alert-inline alert-error">
                                            <p><?php printf( __('Freetype library is required. How to <a target="_blank" href="%s">install/configure</a>') , 'http://www.php.net/manual/en/image.installation.php' ) ; ?></p>
                                        </div>
                                        <?php } ?>
                                    </label>
                                    <label class="radio">
                                        <input type="radio" id="watermark_image" name="watermark_type" value="image" <?php echo ( osc_is_watermark_image() ? 'checked="checked"' : '' ) ; ?> />
                                        <?php _e('Image') ; ?>
                                    </label>
                                </div>
                            </div>
                            <div id="watermark_text_box" <?php echo ( osc_is_watermark_text() ? '' : 'style="display:none;"' ) ; ?>>
                                <h3><?php _e('Watermark Text Settings') ; ?></h3>
                                <div class="input-line">
                                    <label><?php _e('Text') ; ?></label>
                                    <div class="input">
                                        <input type="text" class="large" name="watermark_text" value="<?php echo osc_esc_html( osc_watermark_text() ) ; ?>" />
                                    </div>
                                </div>
                                <div class="input-line">
                                    <label><?php _e('Color') ; ?></label>
                                    <div class="input">
                                        <input type="text" maxlength="6" id="colorpickerField" class="small" name="watermark_text_color" value="<?php echo osc_esc_html( osc_watermark_text_color() ) ; ?>" />
                                    </div>
                                </div>
                                <div class="input-line">
                                    <label><?php _e('Position') ; ?></label>
                                    <div class="input">
                                        <select name="watermark_text_place" id="watermark_text_place">
                                            <option value="centre" <?php echo (osc_watermark_place() == 'centre') ? 'selected="true"' : '' ; ?>><?php _e('Centre') ; ?></option>
                                            <option value="tl" <?php echo (osc_watermark_place() == 'tl') ? 'selected="true"' : '' ; ?>><?php _e('Top Left') ; ?></option>
                                            <option value="tr" <?php echo (osc_watermark_place() == 'tr') ? 'selected="true"' : '' ; ?>><?php _e('Top Right') ; ?></option>
                                            <option value="bl" <?php echo (osc_watermark_place() == 'bl') ? 'selected="true"' : '' ; ?>><?php _e('Bottom Left') ; ?></option>
                                            <option value="br" <?php echo (osc_watermark_place() == 'br') ? 'selected="true"' : '' ; ?>><?php _e('Bottom Right') ; ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="watermark_image_box" <?php echo ( osc_is_watermark_image() ? '' : 'style="display:none;"' ) ; ?>>
                                <h3><?php _e('Watermark Image Settings') ; ?></h3>
                                <div class="input-line">
                                    <label><?php _e('Image'); ?></label>
                                    <div class="input">
                                        <label class="radio">
                                            <input type="file" name="watermark_image"/>
                                            <p class="help"><?php _e("OSClass doesn't check the watermark image size") ; ?></p>
                                        </label>
                                    </div>
                                </div>
                                <div class="input-line">
                                    <label><?php _e('Position') ; ?></label>
                                    <div class="input">
                                        <select name="watermark_image_place" id="watermark_image_place" >
                                            <option value="centre" <?php echo (osc_watermark_place() == 'centre') ? 'selected="true"' : '' ; ?>><?php _e('Centre') ; ?></option>
                                            <option value="tl" <?php echo (osc_watermark_place() == 'tl') ? 'selected="true"' : '' ; ?>><?php _e('Top Left') ; ?></option>
                                            <option value="tr" <?php echo (osc_watermark_place() == 'tr') ? 'selected="true"' : '' ; ?>><?php _e('Top Right') ; ?></option>
                                            <option value="bl" <?php echo (osc_watermark_place() == 'bl') ? 'selected="true"' : '' ; ?>><?php _e('Bottom Left') ; ?></option>
                                            <option value="br" <?php echo (osc_watermark_place() == 'br') ? 'selected="true"' : '' ; ?>><?php _e('Bottom Right') ; ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="actions">
                                <input type="submit" value="<?php echo osc_esc_html( __('Save changes') ) ; ?>" />
                            </div>
                        </fieldset>
                    </form>
                    <!-- /media form -->
                    <!-- regenerate images -->
                    <form action="<?php echo osc_admin_base_url(true); ?>" method="post">
                        <input type="hidden" name="action" value="images_post" />
                        <input type="hidden" name="page" value="settings" />
                        <fieldset>
                            <h2><?php _e('Regenerate images') ; ?></h2>
                            <p class="text">
                                <?php _e("You can regenerate your different image dimensions. If you have changed the dimension of thumbnails, preview or normal images, you might want to regenerate your images.") ; ?>
                            </p>
                            <div class="actions-nomargin">
                                <input type="submit" value="<?php echo osc_esc_html( __('Regenerate') ) ; ?>" />
                            </div>
                        </fieldset>
                    </form>
                    <!-- /regenerate images -->
                </div>
                <!-- /media settings -->
            </div>
            <!-- /right container -->
        </div>
        <!-- /container -->
        <?php osc_current_admin_theme_path('footer.php') ; ?>
    </body>
</html>