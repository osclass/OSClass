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

    class CAdminMedia extends AdminSecBaseModel
    {
        private $resourcesManager ;

        function __construct() {
            parent::__construct() ;

            //specific things for this class
            $this->resourcesManager = ItemResource::newInstance() ;
        }

        //Business Layer...
        function doModel() {
            parent::doModel() ;

            //specific things for this class
            switch ($this->action)
            {
                case 'delete':          $ids = Params::getParam("id");
                                        if($ids!='') {
                                            foreach($ids as $id) {
                                                osc_deleteResource( $id );
                                            }
                                            $this->resourcesManager->delete(array(
                                                DB_CUSTOM_COND => 'pk_i_id IN (' . implode(', ', $ids). ')'
                                            ));
                                        }
                                        osc_add_flash_message( _m('Resource deleted'), 'admin') ;
                                        $this->redirectTo( osc_admin_base_url(true) . "?page=media" ) ;
                break;
                default:                $resourceId = Params::getParam("id");


                                        if( $resourceId != '' ) {
                                            $resources = $this->resourcesManager->getAllResources($resourceId);
                                        } else {
                                            $resources = $this->resourcesManager->getAllResources(NULL);
                                        }

                                        //calling the view...
                                        $this->_exportVariableToView("resources", $resources) ;
                                        $this->_exportVariableToView("resourceId", $resourceId) ;
                                        $this->doView('media/index.php');

            }
        }

        //hopefully generic...
        function doView($file) {
            osc_current_admin_theme_path($file) ;
        }
    }

?>
