<?php if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */

    class CAdminAdmins extends AdminSecBaseModel
    {
        //specific for this class
        private $adminManager ;

        function __construct()
        {
            parent::__construct() ;

            if( $this->isModerator() ) {
                if(($this->action!='edit' && $this->action!='edit_post') || Params::getParam('id')!='' && Params::getParam('id') != osc_logged_admin_id()) {
                    osc_add_flash_error_message(_m("You don't have enough permissions"), 'admin');
                    $this->redirectTo(osc_admin_base_url());
                }
            }

            //specific things for this class
            $this->adminManager = Admin::newInstance() ;
        }

        //Business Layer...
        function doModel()
        {
            parent::doModel() ;

            switch($this->action) {
                case('add'):        // callin add view
                                    $this->_exportVariableToView( 'admin', null ) ;
                                    $this->doView('admins/frm.php') ;
                break ;
                case('add_post'):   if( defined('DEMO') ) {
                                        osc_add_flash_warning_message( _m("This action cannot be done because is a demo site"), 'admin') ;
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins') ;
                                    }
                                    // adding a new admin
                                    $sPassword = Params::getParam('s_password', false, false) ;
                                    $sName     = Params::getParam('s_name') ;
                                    $sEmail    = Params::getParam('s_email') ;
                                    $sUserName = Params::getParam('s_username') ;
                                    $bModerator = Params::getParam('b_moderator')==0?0:1;

                                    // cleaning parameters
                                    $sPassword = strip_tags($sPassword) ;
                                    $sPassword = trim($sPassword) ;
                                    $sName     = strip_tags($sName) ;
                                    $sName     = trim($sName) ;
                                    $sEmail    = strip_tags($sEmail) ;
                                    $sEmail    = trim($sEmail) ;
                                    $sUserName = strip_tags($sUserName) ;
                                    $sUserName = trim($sUserName) ;
                                    
                                    // Checks for legit data
                                    if( !osc_validate_email($sEmail, true) ) {
                                        osc_add_flash_warning_message( _m("Email invalid"), 'admin') ;
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins&action=add') ;
                                    }
                                    if( !osc_validate_username($sUserName) ) {
                                        osc_add_flash_warning_message( _m("Username invalid"), 'admin');
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins&action=add') ;
                                    }
                                    if( $sName == '' ) {
                                        osc_add_flash_warning_message( _m("Name invalid"), 'admin') ;
                                        $this->redirectTo(osc_admin_base_url(true).'?page=admins&action=add') ;
                                    }
                                    if( $sPassword == '' ) {
                                        osc_add_flash_warning_message( _m("Password invalid"), 'admin') ;
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins&action=add') ;
                                    }
                                    $admin = $this->adminManager->findByEmail($sEmail) ;
                                    if( $admin ) {
                                        osc_add_flash_warning_message( _m("Email already in use"), 'admin') ;
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins&action=add') ;
                                    }
                                    $admin = $this->adminManager->findByUsername($sUserName) ;
                                    if( $admin ) {
                                        osc_add_flash_warning_message( _m("Username already in use"), 'admin') ;
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins&action=add') ;
                                    }

                                    $array = array(
                                        's_password'    =>  sha1($sPassword),
                                        's_name'        =>  $sName,
                                        's_email'       =>  $sEmail,
                                        's_username'    =>  $sUserName,
                                        'b_moderator'   =>  $bModerator
                                    ) ;

                                    $isInserted = $this->adminManager->insert($array) ;

                                    if( $isInserted ) {
                                        osc_add_flash_ok_message( _m('The admin has been added'), 'admin') ;
                                    } else {
                                        osc_add_flash_error_message( _m('There have been an error adding a new admin'), 'admin') ;
                                    }
                                    $this->redirectTo(osc_admin_base_url(true).'?page=admins') ;
                break ;
                case('edit'):       // calling edit admin view
                                    $adminEdit = null ;
                                    $adminId   = Params::getParam('id') ;

                                    if( $adminId != '' ) {
                                        $adminEdit = $this->adminManager->findByPrimaryKey((int) $adminId) ;
                                    } elseif( Session::newInstance()->_get('adminId') != '') {
                                        $adminEdit = $this->adminManager->findByPrimaryKey( Session::newInstance()->_get('adminId') ) ;
                                    }

                                    if( count($adminEdit) == 0 ) {
                                        osc_add_flash_error_message( _m('There is no admin admin with this id'), 'admin') ;
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins') ;
                                    }

                                    $this->_exportVariableToView("admin", $adminEdit) ;
                                    $this->doView('admins/frm.php') ;
                break ;
                case('edit_post'):  if( defined('DEMO') ) {
                                        osc_add_flash_warning_message( _m("This action cannot be done because is a demo site"), 'admin') ;
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins') ;
                                    }
                                    // updating a new admin
                                    $iUpdated = 0 ;
                                    $adminId  = Params::getParam('id') ;

                                    $sPassword    = Params::getParam('s_password', false, false) ;
                                    $sPassword2   = Params::getParam('s_password2', false, false) ;
                                    $sOldPassword = Params::getParam('old_password', false, false) ;
                                    $sName        = Params::getParam('s_name') ;
                                    $sEmail       = Params::getParam('s_email') ;
                                    $sUserName    = Params::getParam('s_username') ;
                                    $bModerator   = Params::getParam('b_moderator')==0?0:1;

                                    // cleaning parameters
                                    $sPassword   = strip_tags($sPassword) ;
                                    $sPassword   = trim($sPassword) ;
                                    $sPassword2  = strip_tags($sPassword2) ;
                                    $sPassword2  = trim($sPassword2) ;
                                    $sName       = strip_tags($sName) ;
                                    $sName       = trim($sName) ;
                                    $sEmail      = strip_tags($sEmail) ;
                                    $sEmail      = trim($sEmail) ;
                                    $sUserName   = strip_tags($sUserName) ;
                                    $sUserName   = trim($sUserName) ;

                                    // Checks for legit data
                                    if( !osc_validate_email($sEmail, true) ) {
                                        osc_add_flash_warning_message( _m("Email invalid"), 'admin') ;
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins&action=edit&id=' . $adminId) ;
                                    }
                                    if( !osc_validate_username($sUserName) ) {
                                        osc_add_flash_warning_message( _m("Username invalid"), 'admin');
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins&action=edit&id=' . $adminId) ;
                                    }
                                    if( $sName == '' ) {
                                        osc_add_flash_warning_message( _m("Name invalid"), 'admin') ;
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins&action=edit&id=' . $adminId) ;
                                    }

                                    $aAdmin = $this->adminManager->findByPrimaryKey($adminId) ;

                                    if( count($aAdmin) == 0 ) {
                                        osc_add_flash_error_message( _m("This admin doesn't exist"), 'admin') ;
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins') ;
                                    }

                                    if( $aAdmin['s_email'] != $sEmail ) {
                                        if($this->adminManager->findByEmail( $sEmail ) ) {
                                            osc_add_flash_warning_message( _m('Existing email'), 'admin') ;
                                            $this->redirectTo(osc_admin_base_url(true).'?page=admins&action=edit&id=' . $adminId) ;
                                        }
                                    }

                                    if( $aAdmin['s_username'] != $sUserName ) {
                                        if( $this->adminManager->findByUsername( $sUserName ) ) {
                                            osc_add_flash_warning_message( _m('Existing username'), 'admin') ;
                                            $this->redirectTo(osc_admin_base_url(true) . '?page=admins&action=edit&id=' . $adminId) ;
                                        }
                                    }

                                    $conditions = array('pk_i_id' => $adminId) ;
                                    $array      = array() ;

                                    if(osc_logged_admin_id()==$adminId) {
                                        if($sOldPassword != '' ) {
                                            if( $sPassword=='' ) {
                                                osc_add_flash_warning_message( _m("Password invalid"), 'admin') ;
                                                $this->redirectTo(osc_admin_base_url(true) . '?page=admins&action=edit&id=' . $adminId) ;
                                            } else {
                                                $firstCondition  = ( sha1($sOldPassword) == $aAdmin['s_password'] ) ;
                                                $secondCondition = ( $sPassword == $sPassword2 ) ;
                                                if( $firstCondition && $secondCondition ) {
                                                    $array['s_password'] = sha1($sPassword) ;
                                                } else {
                                                    osc_add_flash_warning_message( _m("The password couldn't be updated. Passwords don't match"), 'admin') ;
                                                    $this->redirectTo(osc_admin_base_url(true) . '?page=admins&action=edit&id=' . $adminId) ;
                                                }
                                            }
                                        }
                                    } else {
                                        if( $sPassword!='') {
                                            if($sPassword == $sPassword2) {
                                                $array['s_password'] = sha1($sPassword) ;
                                            } else {
                                                osc_add_flash_warning_message( _m("The password couldn't be updated. Passwords don't match"), 'admin') ;
                                                $this->redirectTo(osc_admin_base_url(true) . '?page=admins&action=edit&id=' . $adminId) ;
                                            }
                                        }
                                    }

                                    if($adminId!=osc_logged_admin_id()) {
                                        $array['b_moderator'] = $bModerator;
                                    }
                                    
                                    $array['s_name']     = Params::getParam('s_name') ;
                                    $array['s_username'] = $sUserName ;
                                    $array['s_email']    = $sEmail ;

                                    $iUpdated = $this->adminManager->update($array, $conditions) ;

                                    if( $iUpdated > 0 ) {
                                        osc_add_flash_ok_message( _m('The admin has been updated'), 'admin') ;
                                    }

                                    if( $this->isModerator() ) {
                                        $this->redirectTo(osc_admin_base_url(true)) ;
                                    } else {
                                        $this->redirectTo(osc_admin_base_url(true).'?page=admins') ;
                                    }
                break ;
                case('delete'):     if( defined('DEMO') ) {
                                        osc_add_flash_warning_message( _m("This action cannot be done because is a demo site"), 'admin') ;
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins') ;
                                    }
                                    // deleting and admin
                                    $isDeleted = false ;
                                    $adminId   = Params::getParam('id') ;
 
                                    if( !is_array($adminId) ) {
                                        osc_add_flash_error_message( _m("The admin id isn't in the correct format"), 'admin') ;
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins') ;
                                    }

                                    // Verification to avoid an administrator trying to remove to itself
                                    if( in_array(Session::newInstance()->_get('adminId'), $adminId) ) {
                                        osc_add_flash_error_message( _m("The operation hasn't been completed. You're trying to remove yourself!"), 'admin') ;
                                        $this->redirectTo(osc_admin_base_url(true) . '?page=admins') ;
                                    }

                                    $isDeleted = $this->adminManager->deleteBatch( $adminId ) ;

                                    if( $isDeleted ) {
                                        osc_add_flash_ok_message( _m('The admin has been deleted correctly'), 'admin') ;
                                    } else {
                                        osc_add_flash_error_message( _m('The admin couldn\'t be deleted'), 'admin') ;
                                    }
                                    $this->redirectTo(osc_admin_base_url(true) . '?page=admins') ;
                break ;
                default:            
                                    if( Params::getParam('iDisplayLength') == '' ) {
                                        Params::setParam('iDisplayLength', 10 ) ;
                                    }

                                    $p_iPage      = 1;
                                    if( is_numeric(Params::getParam('iPage')) && Params::getParam('iPage') >= 1 ) {
                                        $p_iPage = Params::getParam('iPage');
                                    }
                                    Params::setParam('iPage', $p_iPage);

                                    $admins = $this->adminManager->listAll() ;
                                    
                                    // pagination
                                    $start = ($p_iPage-1) * Params::getParam('iDisplayLength');
                                    $limit = Params::getParam('iDisplayLength');
                                    $count = count( $admins );

                                    $displayRecords = $limit;
                                    if( ($start+$limit ) > $count ) {
                                        $displayRecords = ($start+$limit) - $count;
                                    }
                                    // ----
                                    $aData = array() ;
                                    $max = ($start+$limit);
                                    if($max > $count) $max = $count;
                                    for($i = $start; $i < $max; $i++) {
                                    
                                        $admin = $admins[$i];
                                    
                                        $options = array();
                                        $options[] = '<a href="' . osc_admin_base_url(true) . '?page=admins&action=edit&amp;id='  . $admin['pk_i_id'] . '">' . __('Edit') . '</a>';
                                        $options[] = '<a onclick="javascript:return confirm(\'' . osc_esc_js(__('This action cannot be undone. Are you sure you want to continue?')) . '\');" href="' . osc_admin_base_url(true) . '?page=admins&action=delete&amp;id[]=' . $admin['pk_i_id'] . '">' . __('Delete') . '</a>';
                                        $auxOptions = '<ul>'.PHP_EOL ;
                                        foreach( $options as $actual ) {
                                            $auxOptions .= '<li>'.$actual.'</li>'.PHP_EOL;
                                        }
                                        $actions = '<div class="actions">'.$auxOptions.'</div>'.PHP_EOL ;
                                        
                                        $row = array() ;
                                        $row[] = '<input type="checkbox" name="id[]" value="' . $admin['pk_i_id'] . '" />' ;
                                        $row[] = $admin['s_username'] . $actions ;
                                        $row[] = $admin['s_name'] ;
                                        $row[] = $admin['s_email'] ;

                                        $aData[] = $row ;
                                    }
                                    $array['iTotalRecords']         = $displayRecords;
                                    $array['iTotalDisplayRecords']  = count($admins);
                                    $array['iDisplayLength']        = $limit;
                                    $array['aaData'] = $aData;

                                    $page  = (int)Params::getParam('iPage');
                                    if(count($array['aaData']) == 0 && $page!=1) {
                                        $total = (int)$array['iTotalDisplayRecords'];
                                        $maxPage = ceil( $total / (int)$array['iDisplayLength'] ) ;

                                        $url = osc_admin_base_url(true).'?'.$_SERVER['QUERY_STRING'];

                                        if($maxPage==0) {
                                            $url = preg_replace('/&iPage=(\d)+/', '&iPage=1', $url) ;
                                            $this->redirectTo($url) ;
                                        }

                                        if($page > 1) {   
                                            $url = preg_replace('/&iPage=(\d)+/', '&iPage='.$maxPage, $url) ;
                                            $this->redirectTo($url) ;
                                        }
                                    }
                                            
                                    $this->_exportVariableToView('aAdmins', $array) ;
                                    // calling manage admins view
                                    $this->doView('admins/index.php') ;
                break ;
            }
        }

        //hopefully generic...
        function doView($file)
        {
            osc_current_admin_theme_path($file) ;
            Session::newInstance()->_clearVariables();
        }
    }

    /* file end: ./oc-admin/admins.php */
?>