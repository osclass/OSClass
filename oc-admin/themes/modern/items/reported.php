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

    function customPageHeader(){ ?>
        <h1><?php _e('Reported listings') ; ?>
            <a href="#" class="btn ico ico-32 ico-help float-right"></a>
       </h1>
<?php
    }
    osc_add_hook('admin_page_header','customPageHeader');

    function customPageTitle($string) {
        return sprintf(__('Reported listings &raquo; %s'), $string);
    }
    osc_add_filter('admin_title', 'customPageTitle');

    //customize Head
    function customHead() { ?>
        <script type="text/javascript">
            // autocomplete users
            $(document).ready(function(){
                // check_all bulkactions
                $("#check_all").change(function(){
                    var isChecked = $(this+':checked').length;
                    $('.col-bulkactions input').each( function() {
                        if( isChecked == 1 ) {
                            this.checked = true;
                        } else {
                            this.checked = false;
                        }
                    });
                });
                
                // dialog delete
                $("#dialog-item-delete").dialog({
                    autoOpen: false,
                    modal: true,
                    title: '<?php echo osc_esc_js( __('Delete listing') ); ?>'
                });
            });
            
            // dialog delete function
            function delete_dialog(item_id) {
                $("#dialog-item-delete input[name='id[]']").attr('value', item_id);
                $("#dialog-item-delete").dialog('open');
                return false;
            }
        </script>
        <?php
    }
    osc_add_hook('admin_header','customHead');

    $aData      = __get('aItems') ;
    $url_spam   = __get('url_spam') ;
    $url_bad    = __get('url_bad') ;
    $url_rep    = __get('url_rep') ;
    $url_off    = __get('url_off') ;
    $url_exp    = __get('url_exp') ;
    $url_date   = __get('url_date') ;

    $sort       = Params::getParam('sort');
    $direction  = Params::getParam('direction');

    osc_current_admin_theme_path( 'parts/header.php' ) ; ?>
<h2 class="render-title"><?php _e('Manage reported listings') ; ?></h2>
<div class="relative">
    <div id="listing-toolbar">
        <div class="float-right">
            <?php if($sort!='date') { ?>
            <a id="btn-reset-filters" class="btn btn-red" href="<?php echo osc_admin_base_url(true); ?>?page=items&action=items_reported"><?php _e('Reset filters'); ?></a>
            <?php } ?>
        </div>
    </div>
    <form class="" id="datatablesForm" action="<?php echo osc_admin_base_url(true) ; ?>" method="post">
        <input type="hidden" name="page" value="items" />
        <input type="hidden" name="action" value="bulk_actions" />
        <div id="bulk-actions">
            <label>
                <select id="bulk_actions" name="bulk_actions" class="select-box-extra">
                    <option value=""><?php _e('Bulk actions') ; ?></option>
                    <option value="delete_all"><?php _e('Delete') ; ?></option>
                    <option value="clear_all"><?php _e('Clear All') ; ?></option>
                    <option value="clear_spam_all"><?php _e('Clear Spam') ; ?></option>
                    <option value="clear_bad_all"><?php _e('Clear Missclassified') ; ?></option>
                    <option value="clear_dupl_all"><?php _e('Clear Duplicated') ; ?></option>
                    <option value="clear_expi_all"><?php _e('Clear Expired') ; ?></option>
                    <option value="clear_offe_all"><?php _e('Clear Offensive') ; ?></option>
                    <?php $onclick_bulkactions= 'onclick="javascript:return confirm(\'' . osc_esc_js( __('You are doing bulk actions. Are you sure you want to continue?') ) . '\')"' ; ?>
                </select> <input type="submit" <?php echo $onclick_bulkactions; ?> id="bulk_apply" class="btn" value="<?php echo osc_esc_html( __('Apply') ) ; ?>" />
            </label>
        </div>
        <div class="table-contains-actions">
            <table class="table" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th class="col-bulkactions"><input id="check_all" type="checkbox" /></th>
                        <th class="col-title"><?php _e('Title') ; ?></th>
                        <th><?php _e('User') ; ?></th>
                        <th class="<?php if($sort=='spam'){ if($direction=='desc'){ echo 'sorting_desc'; } else { echo 'sorting_asc'; } } ?>">
                            <a id="order_spam" href="<?php echo $url_spam; ?>"><?php _e('Spam') ; ?></a>
                        </th>
                        <th class="<?php if($sort=='bad'){ if($direction=='desc'){ echo 'sorting_desc'; } else { echo 'sorting_asc'; } } ?>">
                            <a id="order_bad" href="<?php echo $url_bad; ?>"><?php _e('Misclassified') ; ?>
                        </th>
                        <th class="<?php if($sort=='rep'){ if($direction=='desc'){ echo 'sorting_desc'; } else { echo 'sorting_asc'; } } ?>">
                            <a id="order_rep" href="<?php echo $url_rep; ?>"><?php _e('Duplicated') ; ?>
                        </th>
                        <th class="<?php if($sort=='exp'){ if($direction=='desc'){ echo 'sorting_desc'; } else { echo 'sorting_asc'; } } ?>">
                            <a id="order_exp" href="<?php echo $url_exp; ?>"><?php _e('Expired') ; ?>
                        </th>
                        <th class="<?php if($sort=='off'){ if($direction=='desc'){ echo 'sorting_desc'; } else { echo 'sorting_asc'; } } ?>">
                            <a id="order_off" href="<?php echo $url_off; ?>"><?php _e('Offensive') ; ?>
                        </th>
                        <th class="col-date <?php if($sort=='date'){ if($direction=='desc'){ echo 'sorting_desc'; } else { echo 'sorting_asc'; } } ?>">
                            <a id="order_date" href="<?php echo $url_date; ?>"><?php _e('Date') ; ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                <?php if(count($aData['aaData'])>0) { ?>
                <?php foreach( $aData['aaData'] as $array) { ?>
                    <tr>
                    <?php foreach($array as $key => $value) { ?>
                        <?php if( $key==0 ) { ?>
                        <td class="col-bulkactions">
                        <?php } else { ?>
                        <td>
                        <?php } ?>
                        <?php echo $value; ?>
                        </td>
                    <?php } ?>
                    </tr>
                <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="9" class="text-center">
                        <p><?php _e('No data available in table') ; ?></p>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <div id="table-row-actions"></div> <!-- used for table actions -->
        </div>
    </form>
</div>
<?php 
    osc_show_pagination_admin($aData);
?>
<form id="dialog-item-delete" method="get" action="<?php echo osc_admin_base_url(true); ?>" id="display-filters" class="has-form-actions">
    <input type="hidden" name="page" value="items" />
    <input type="hidden" name="action" value="delete" />
    <input type="hidden" name="id[]" value="" />
    <div class="form-horizontal">
        <div class="form-row">
            <?php _e('Are you sure you want to delete this listing?'); ?>
        </div>
        <div class="form-actions">
            <div class="wrapper">
            <a class="btn" href="javascript:void(0);" onclick="$('#dialog-item-delete').dialog('close');"><?php _e('Cancel'); ?></a>
            <input id="item-delete-submit" type="submit" value="<?php echo osc_esc_html( __('Delete') ); ?>" class="btn btn-red" />
            </div>
        </div>
    </div>
</form>
<?php osc_current_admin_theme_path( 'parts/footer.php' ) ; ?>