<?php
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
            
    require_once 'oc-load.php' ;
    
    switch( Params::getParam('page') )
    {
        case ('user'):      // user pages (with security)

                            if(Params::getParam('action')=='change_email_confirm'
                            || (Params::getParam('action')=='unsub_alert' && !osc_is_web_user_logged_in())) {
                                require_once(osc_base_path() . 'user-non-secure.php') ;
                                $do = new CWebUserNonSecure() ;
                                $do->doModel() ;
                            } else {
                                require_once(osc_base_path() . 'user.php') ;
                                $do = new CWebUser() ;
                                $do->doModel() ;
                            }
        break;
        case ('item'):      // item pages
                            require_once(osc_base_path() . 'item.php');
                            $do = new CWebItem() ;
                            $do->doModel() ;
        break;
        case ('search'):    // search pages
                            require_once(osc_base_path() . 'search.php') ;
                            $do = new CWebSearch() ;
                            $do->doModel() ;
        break;
        case ('page'):      // static pages
                            require_once(osc_base_path() . 'page.php') ;
                            $do = new CWebPage() ;
                            $do->doModel() ;
        break;
        case ('register'):  // register page
                            require_once(osc_base_path() . 'register.php') ;
                            $do = new CWebRegister() ;
                            $do->doModel() ;
        break;
        case ('ajax'):      // ajax
                            require_once(osc_base_path() . 'ajax.php') ;
                            $do = new CWebAjax() ;
                            $do->doModel() ;
        break;
        case ('login'):     // login page
                            require_once(osc_base_path() . 'login.php') ;
                            $do = new CWebLogin() ;
                            $do->doModel() ;
        break;
        case ('language'):  // set language
                            require_once(osc_base_path() . 'language.php');
                            $do = new CWebLanguage();
                            $do->doModel();
        break;
        case ('contact'):   //contact
                            require_once(osc_base_path() . 'contact.php') ;
                            $do = new CWebContact() ;
                            $do->doModel() ;
        break;
        default:            // home and static pages that are mandatory...
                            require_once(osc_base_path() . 'main.php') ;
                            $do = new CWebMain() ;
                            $do->doModel() ;
        break;
    }

?>
