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

    class CWebSearch extends BaseModel
    {
        var $mSearch ;
        
        function __construct() {
            parent::__construct() ;

            $this->mSearch = new Search() ;
        }

        //Business Layer...
        function doModel() {
            $mCategories = new Category() ;
            $aCategories = $mCategories->findRootCategories() ;
            $mCategoryStats = new CategoryStats() ;

            ////////////////////////////////
            //GETTING AND FIXING SENT DATA//
            ////////////////////////////////
            $p_sCategory  = Params::getParam('sCategory');
            if(!is_array($p_sCategory)) {
                if($p_sCategory == '') {
                    $p_sCategory = array() ;
                } else {
                    $p_sCategory = explode(",",$p_sCategory);
                }
            }

            $p_sCity      = Params::getParam('sCity');
            if(!is_array($p_sCity)) {
                if($p_sCity == '') {
                    $p_sCity = array() ;
                } else {
                    $p_sCity = explode(",", $p_sCity);
                }
            }

            $p_sRegion    = Params::getParam('sRegion');
            if(!is_array($p_sRegion)) {
                if($p_sRegion == '') {
                    $p_sRegion = array() ;
                } else {
                    $p_sRegion = explode(",", $p_sRegion);
                }
            }

            $p_sCountry   = Params::getParam('sCountry');
            if(!is_array($p_sCountry)) {
                if($p_sCountry == '') {
                    $p_sCountry = array() ;
                } else {
                    $p_sCountry = explode(",", $p_sCountry);
                }
            }

            $p_sPattern   = strip_tags(Params::getParam('sPattern'));

            $p_bPic       = Params::getParam('bPic');
            ($p_bPic == 1) ? $p_bPic = 1 : $p_bPic = 0 ;

            $p_sPriceMin  = Params::getParam('sPriceMin');
            $p_sPriceMax  = Params::getParam('sPriceMax');

            //WE CAN ONLY USE THE FIELDS RETURNED BY Search::getAllowedColumnsForSorting()
            $p_sOrder     = Params::getParam('sOrder');
            if(!in_array($p_sOrder, Search::getAllowedColumnsForSorting())) {
                $p_sOrder = osc_default_order_field_at_search() ;
            }

            //ONLY 0 ( => 'asc' ), 1 ( => 'desc' ) AS ALLOWED VALUES
            $p_iOrderType = Params::getParam('iOrderType');
            $allowedTypesForSorting = Search::getAllowedTypesForSorting() ;
            $orderType = osc_default_order_type_at_search();
            foreach($allowedTypesForSorting as $k => $v) {
                if($p_iOrderType==$v) {
                    $orderType = $k;
                    break;
                }
            }
            $p_iOrderType = $orderType;

            $p_sFeed      = Params::getParam('sFeed');
            $p_iPage      = intval(Params::getParam('iPage'));

            if($p_sFeed != '') {
                $p_sPageSize = 1000;
            }

            $p_sShowAs    = Params::getParam('sShowAs');
            $aValidShowAsValues = array('list', 'gallery');
            if (!in_array($p_sShowAs, $aValidShowAsValues)) {
                $p_sShowAs = osc_default_show_as_at_search() ;
            }

            // search results: it's blocked with the maxResultsPerPage@search defined in t_preferences
            $p_iPageSize  = intval(Params::getParam('iPagesize')) ;
            if($p_iPageSize > 0) {
                if($p_iPageSize > osc_max_results_per_page_at_search()) $p_iPageSize = osc_max_results_per_page_at_search() ;
            } else {
                $p_iPageSize = osc_default_results_per_page_at_search() ;
            }

            //FILTERING CATEGORY
            $bAllCategoriesChecked = false ;
            if(count($p_sCategory) > 0) {
                foreach($p_sCategory as $category) {
                    $this->mSearch->addCategory($category);
                }
            } else {
                $bAllCategoriesChecked = true ;
            }

            //FILTERING CITY
            foreach($p_sCity as $city) {
                $this->mSearch->addCity($city);
            }
            $p_sCity = implode(", ", $p_sCity);

            //FILTERING REGION
            foreach($p_sRegion as $region) {
                $this->mSearch->addRegion($region);
            }
            $p_sRegion = implode(", ", $p_sRegion);

            //FILTERING COUNTRY
            foreach($p_sCountry as $country) {
                $this->mSearch->addCountry($country);
            }
            $p_sCountry = implode(", ", $p_sCountry);

            // FILTERING PATTERN
            if($p_sPattern != '') {
                $this->mSearch->addConditions(sprintf("(d.s_title LIKE '%%%s%%' OR d.s_description LIKE '%%%s%%')", $p_sPattern, $p_sPattern));
                $osc_request['sPattern'] = $p_sPattern;
            }

            // FILTERING IF WE ONLY WANT ITEMS WITH PICS
            if($p_bPic) {
                $this->mSearch->withPicture(true) ;
            }

            //FILTERING BY RANGE PRICE
            $this->mSearch->priceRange($p_sPriceMin, $p_sPriceMax);

            //ORDERING THE SEARCH RESULTS
            $this->mSearch->order($p_sOrder, $allowedTypesForSorting[$p_iOrderType]) ;

            //SET PAGE
            $this->mSearch->page($p_iPage, $p_iPageSize);

            osc_run_hook('search_conditions', Params::getParamsAsArray());

            $this->mSearch->addConditions(sprintf("%st_item.e_status = 'ACTIVE' ", DB_TABLE_PREFIX));

            // RETRIEVE ITEMS AND TOTAL
            $iTotalItems = $this->mSearch->count();
            $aItems = $this->mSearch->search();

            if(!Params::existParam('sFeed')) {
                $iStart    = $p_iPage * $p_iPageSize ;
                $iEnd      = min(($p_iPage+1) * $p_iPageSize, $iTotalItems) ;
                //Static data, which is the point?
                /*$aOrders   = array(
                                 __('Newly listed')       => array('sOrder' => 'dt_pub_date', 'iOrderType' => 'desc')
                                ,__('Lower price first')  => array('sOrder' => 'f_price', 'iOrderType' => 'asc')
                                ,__('Higher price first') => array('sOrder' => 'f_price', 'iOrderType' => 'desc')
                             );*/
                $iNumPages = ceil($iTotalItems / $p_iPageSize) ;

                //Categories for select at view "search.php"
                $mCategories = new Category();
                $aCategories = $mCategories->findRootCategories();
                $mCategoryStats = new CategoryStats();
                $aCategories = $mCategories->toTree();
                foreach($aCategories as $k => $v) {
                    $iCategoryNumItems = CategoryStats::newInstance()->getNumItems($v);
                    if($iCategoryNumItems > 0) {
                        $aCategories[$k]['total'] = $iCategoryNumItems;
                    } else {
                        unset($aCategories[$k]);
                    }
                }

                osc_run_hook('search', $this->mSearch) ;

                //preparing variables...
                $this->_exportVariableToView('categories', $aCategories) ;
                $this->_exportVariableToView('search_start', $iStart) ;
                $this->_exportVariableToView('search_end', $iEnd) ;
                $this->_exportVariableToView('search_category', $p_sCategory) ;
                $this->_exportVariableToView('search_order_type', $p_iOrderType) ;
                $this->_exportVariableToView('search_order', $p_sOrder) ;
                $this->_exportVariableToView('search_pattern', $p_sPattern) ;
                $this->_exportVariableToView('search_total_pages', $iNumPages) ;
                $this->_exportVariableToView('search_page', $p_iPage) ;
                $this->_exportVariableToView('search_has_pic', $p_bPic) ;
                $this->_exportVariableToView('search_city', $p_sCity) ;
                $this->_exportVariableToView('search_price_min', $p_sPriceMin) ;
                $this->_exportVariableToView('search_price_max', $p_sPriceMax) ;
                $this->_exportVariableToView('search_total_items', $iTotalItems) ;
                $this->_exportVariableToView('items', $aItems) ;
                $this->_exportVariableToView('search_show_as', $p_sShowAs);
                $this->_exportVariableToView('search', $this->mSearch);
                
                //calling the view...
                $this->doView('search.php') ;

            } else {
                $this->_exportVariableToView('items', $aItems) ;
                if($p_sFeed=='' || $p_sFeed=='rss') {
                    // FEED REQUESTED!
                    header('Content-type: text/xml; charset=utf-8');
                    
                    $feed = new RSSFeed;
                    $feed->setTitle(__('Latest items added') . ' - ' . osc_page_title());
                    $feed->setLink(osc_base_url());
                    $feed->setDescription(__('Latest items added in') . ' ' . osc_page_title());

                    if(osc_count_items()>0) {
                        while(osc_has_items()) {
                            $feed->addItem(array(
                                'title' => osc_item_title(),
                                'link' => osc_item_url(),
                                'description' => osc_item_description()
                            ));
                        }
                    }

                    osc_run_hook('feed', $feed);
                    $feed->dumpXML();
                } else {
                    osc_run_hook('feed_' . $p_sFeed, $aItems) ;
                }
            }
        }

        //hopefully generic...
        function doView($file) {
            osc_current_web_theme_path($file) ;
        }

    }

?>
