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

    class CWebPage extends BaseModel
    {
        var $pageManager ;

        function __construct() {
            parent::__construct() ;

            //specific things for this class
            $this->pageManager = Page::newInstance() ;
        }

        //Business Layer...
        function doModel() {
            $id = Params::getParam('id') ;
            $page = $this->pageManager->findByPrimaryKey($id) ;
            
            if(empty($page) || $page['b_indelible'] == 1 ) {
                $this->do404() ;
            } else {
                if(file_exists(osc_themes_path() . osc_theme() . '/' . $page['s_internal_name'].".php")) {
                    $this->doView($page['s_internal_name'].".php");
                } else if(file_exists(osc_themes_path() . osc_theme() . '/pages/' . $page['s_internal_name'].".php")) {
                    $this->doView("pages/".$page['s_internal_name'].".php");
                } else {
                    //calling the view...
                    if( Params::getParam('lang') != '' ) {
                        Session::newInstance()->_set('userLocale', Params::getParam('lang'));
                    };

                    $this->_exportVariableToView('page', $page) ;
                    $this->doView('page.php') ;
                }
            }
        }

        //hopefully generic...
        function doView($file) {
            osc_current_web_theme_path($file) ;
        }
    }

?>
