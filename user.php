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

class CWebUser extends WebSecBaseModel
{

    function __construct() {
        parent::__construct() ;
    }

    //Business Layer...
    function doModel() {
        switch( $this->action ) {
            case('dashboard'):      //dashboard...
                                    $max_items = (Params::getParam('max_items')!='')?Params::getParam('max_items'):5;
                                    $aItems = Item::newInstance()->findByUserID(Session::newInstance()->_get('userId'), 0, $max_items);//Item::newInstance()->listWhere("fk_i_user_id = ".Session::newInstance()->_get('userId'));
                                    //calling the view...
                                    $this->_exportVariableToView('items', $aItems) ;
                                    $this->_exportVariableToView('max_items', $max_items) ;
                                    $this->doView('user-dashboard.php') ;
            break ;
            case('profile'):        //profile...
                                    $user = User::newInstance()->findByPrimaryKey( Session::newInstance()->_get('userId') ) ;
                                    $aCountries = Country::newInstance()->listAll() ;
                                    $aRegions = array() ;
                                    if( $user['fk_c_country_code'] != '' ) {
                                        $aRegions = Region::newInstance()->getByCountry( $user['fk_c_country_code'] ) ;
                                    } elseif( count($aCountries) > 0 ) {
                                        $aRegions = Region::newInstance()->getByCountry( $aCountries[0]['pk_c_code'] ) ;
                                    }
                                    $aCities = array() ;
                                    if( $user['fk_i_region_id'] != '' ) {
                                        $aCities = City::newInstance()->listWhere("fk_i_region_id = %d" ,$user['fk_i_region_id']) ;
                                    } else if( count($aRegions) > 0 ) {
                                        $aCities = City::newInstance()->listWhere("fk_i_region_id = %d" ,$aRegions[0]['pk_i_id']) ;
                                    }
                                    
                                    //calling the view...
                                    $this->_exportVariableToView('countries', $aCountries) ;
                                    $this->_exportVariableToView('regions', $aRegions) ;
                                    $this->_exportVariableToView('cities', $aCities) ;
                                    $this->_exportVariableToView('user', $user) ;
                                    $this->doView('user-profile.php') ;
            break ;
            case('profile_post'):   //profile post...
                                    $userId = Session::newInstance()->_get('userId') ;

                                    require_once LIB_PATH . 'osclass/UserActions.php' ;
                                    $userActions = new UserActions(false) ;
                                    $success = $userActions->edit( $userId ) ;

                                    // This has been moved to special area (only password changes)
                                    /*if( $success == 1 ) {
                                        osc_add_flash_message( _m('Passwords don\'t match') ) ;
                                    } else {*/
                                        osc_add_flash_message( _m('Your profile has been updated successfully') ) ;
                                    //}

                                    $this->redirectTo( osc_user_profile_url() ) ;
            break ;
            case('alerts'):         //alerts
                                    $aAlerts = Alerts::newInstance()->getAlertsFromUser( Session::newInstance()->_get('userId') ) ;
                                    $user = User::newInstance()->findByPrimaryKey( Session::newInstance()->_get('userId'));
                                    foreach($aAlerts as $k => $a) {
                                        $search = osc_unserialize(base64_decode($a['s_search'])) ;
                                        $search->limit(0, 3) ;
                                        $aAlerts[$k]['items'] = $search->search() ;
                                    }

                                    $this->_exportVariableToView('alerts', $aAlerts) ;
                                    View::newInstance()->_reset('alerts') ;
                                    $this->_exportVariableToView('user', $user) ;
                                    $this->doView('user-alerts.php') ;
            break;
            case('change_email'):           //change email
                                            $this->doView('user-change_email.php') ;
            break;
            case('change_email_post'):      //change email post
                                            if(!preg_match("/^[_a-z0-9-\+]+(\.[_a-z0-9-\+]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", Params::getParam('new_email'))) {
                                                osc_add_flash_message( _m('The specified e-mail is not valid')) ;
                                                $this->redirectTo( osc_change_user_email_url() ) ;
                                            } else {
                                                $user = User::newInstance()->findByEmail(Params::getParam('new_email'));
                                                if(!isset($user['pk_i_id'])) {
                                                    if( osc_user_validation_enabled() )
                                                    {
                                                        $userEmailTmp = array() ;
                                                        $userEmailTmp['fk_i_user_id'] = Session::newInstance()->_get('userId') ;
                                                        $userEmailTmp['s_new_email'] = Params::getParam('new_email') ;
                                                        
                                                        UserEmailTmp::newInstance()->insertOrUpdate($userEmailTmp) ;

                                                        $code = osc_genRandomPassword(30) ;
                                                        $date = date('Y-m-d H:i:s') ;

                                                        $userManager = new User() ;
                                                        $userManager->update (
                                                            array( 's_pass_code' => $code, 's_pass_date' => $date, 's_pass_ip' => $_SERVER['REMOTE_ADDR'] )
                                                            ,array( 'pk_i_id' => Session::newInstance()->_get('userId') )
                                                        );

                                                        $locale = osc_current_user_locale() ;
                                                        $aPage = Page::newInstance()->findByInternalName('email_new_email') ;
                                                        if(isset($aPage['locale'][$locale]['s_title'])) {
                                                            $content = $aPage['locale'][$locale] ;
                                                        } else {
                                                            $content = current($aPage['locale']) ;
                                                        }

                                                        if (!is_null($content)) {
                                                            $validation_url = osc_change_user_email_confirm_url( Session::newInstance()->_get('userId'), $code ) ;

                                                            $words = array() ;
                                                            $words[] = array('{USER_NAME}', '{USER_EMAIL}', '{WEB_URL}', '{WEB_TITLE}', '{VALIDATION_LINK}', '{VALIDATION_URL}') ;
                                                            $words[] = array(Session::newInstance()->_get('userName'), Params::getParam('new_email'), osc_base_url(), osc_page_title(), '<a href="' . $validation_url . '" >' . $validation_url . '</a>', $validation_url) ;
                                                            $title = osc_mailBeauty($content['s_title'], $words) ;
                                                            $body = osc_mailBeauty($content['s_text'], $words) ;

                                                            $params = array(
                                                                'subject' => $title
                                                                ,'to' => Params::getParam('new_email')
                                                                ,'to_name' => Session::newInstance()->_get('userName')
                                                                ,'body' => $body
                                                                ,'alt_body' => $body
                                                            ) ;
                                                            osc_sendMail($params) ;
                                                            osc_add_flash_message( _m('We have sent you an e-mail. Follow the instructions to validate the changes')) ;
                                                        } else {
                                                            osc_add_flash_message( _m('We tried to sent you an e-mail, but it failed. Please, contact the administrator')) ;
                                                        }
                                                        $this->redirectTo( osc_user_profile_url() ) ;

                                                    } else {
                                                        
                                                        User::newInstance()->update(
                                                            array( 's_email' => Params::getParam('new_email') )
                                                            ,array( 'pk_i_id' => Params::getParam('userId') )
                                                        ) ;
                                                        osc_add_flash_message( _m('Your email has been changed successfully')) ;
                                                        $this->redirectTo( osc_user_profile_url() ) ;

                                                    }
                                                } else {
                                                    osc_add_flash_message( _m('The specified e-mail is already in use')) ;
                                                    $this->redirectTo( osc_change_user_email_url() ) ;
                                                }
                                            }
            break;
            case('change_password'):        //change password
                                            $this->doView('user-change_password.php') ;
            break;
            case 'change_password_post':    //change password post
                                            $user = User::newInstance()->findByPrimaryKey( Session::newInstance()->_get('userId') ) ;

                                            if( $user['s_password'] != sha1( Params::getParam('password') ) ) {
                                                osc_add_flash_message( _m('Current password doesn\'t match')) ;
                                                $this->redirectTo( osc_change_user_password_url() ) ;
                                            } elseif( !Params::getParam('new_password') ) {
                                                osc_add_flash_message( _m('Passwords can\'t be empty')) ;
                                                $this->redirectTo( osc_change_user_password_url() ) ;
                                            } elseif( Params::getParam('new_password') != Params::getParam('new_password2') ) {
                                                osc_add_flash_message( _m('Passwords don\'t match'));
                                                $this->redirectTo( osc_change_user_password_url() ) ;
                                            }

                                            User::newInstance()->update(
                                                        array( 's_password' => sha1( Params::getParam ('new_password') ) )
                                                        ,array( 'pk_i_id' => Session::newInstance()->_get('userId') )
                                                ) ;
                                            
                                            osc_add_flash_message( _m('Password has been changed')) ;
                                            $this->redirectTo( osc_user_profile_url() ) ;
            break;
            case 'items':                   // view items user
                                            $itemsPerPage = (Params::getParam('itemsPerPage')!='')?Params::getParam('itemsPerPage'):5;
                                            $page = (Params::getParam('iPage')!='')?Params::getParam('iPage'):0;
                                            $total_items = Item::newInstance()->countByUserID($_SESSION['userId']);
                                            $total_pages = ceil($total_items/$itemsPerPage);
                                            $items = Item::newInstance()->findByUserID($_SESSION['userId'], $page*$itemsPerPage, $itemsPerPage);

                                            $this->_exportVariableToView('items', $items);
                                            $this->_exportVariableToView('list_total_pages', $total_pages);
                                            $this->_exportVariableToView('list_total_items', $total_items);
                                            $this->_exportVariableToView('items_per_page', $itemsPerPage);
                                            $this->_exportVariableToView('list_page', $page);

                                            $this->doView('user-items.php');

            break;            
            case 'unsub_alert':
                $email = Params::getParam('email');
                $alert = Params::getParam('alert');
                if($email!='' && $alert!='') {
                    Alerts::newInstance()->delete(array('s_email' => $email, 's_search' => $alert));
                    osc_add_flash_message(__('Unsubscribed correctly.'));
                } else {
                    osc_add_flash_message(__('Ops! There was a problem trying to unsubscribe you. Please contact the administrator.'));
                }
                $this->redirectTo(osc_user_alerts_url());
            break;            
        }
    }

    //hopefully generic...
    function doView($file) {
        osc_current_web_theme_path($file) ;
    }
}



?>
