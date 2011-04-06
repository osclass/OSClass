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

     if( !function_exists('meta_title') ) {
         function meta_title( ) {
            $location = Rewrite::newInstance()->get_location();
            $section  = Rewrite::newInstance()->get_section();

            switch ($location) {
                case ('item'):
                    switch ($section) {
                        case 'item_add':    return __('Publish an item','modern') . ' - ' . osc_page_title(); break;
                        case 'item_edit':   return __('Edit your item','modern') . ' - ' . osc_page_title(); break;
                        case 'send_friend': return __('Send to a friend','modern') . ' - ' . osc_item_title() . ' - ' . osc_page_title(); break;
                        case 'contact':     return __('Contact seller','modern') . ' - ' . osc_item_title() . ' - ' . osc_page_title(); break;
                        default:            return osc_item_title() . ' - ' . osc_page_title(); break;
                    }
                break;
                case('page'):
                    return osc_static_page_title() . ' - ' . osc_page_title();
                break;
                case('search'):
                    $region   = Params::getParam('sRegion');
                    $city     = Params::getParam('sCity');
                    $pattern  = Params::getParam('sPattern');
                    $category = osc_search_category_id();
                    $category = ((count($category) == 1) ? $category[0] : '');
                    $s_page = '';
                    $i_page = Params::getParam('iPage');

                    if($i_page != '' && $i_page > 0) {
                        $s_page = __('page', 'modern') . ' ' . ($i_page + 1) . ' - ';
                    }

                    $b_show_all = ($region == '' && $city == '' & $pattern == '' && $category == '');
                    $b_category = ($category != '');
                    $b_pattern  = ($pattern != '');
                    $b_city     = ($city != '');
                    $b_region   = ($region != '');

                    if($b_show_all) {
                        return __('Show all items', 'modern') . ' - ' . $s_page . osc_page_title();
                    }

                    $result = '';
                    if($b_pattern) {
                        $result .= $pattern . ' &raquo; ';
                    }

                    if($b_category) {
                        $list        = array();
                        $aCategories = Category::newInstance()->toRootTree($category);
                        if(count($aCategories) > 0) {
                            foreach ($aCategories as $single) {
                                $list[] = $single['s_name'];
                            }
                            $result .= implode(' &raquo; ', $list) . ' &raquo; ';
                        }
                    }

                    if($b_city) {
                        $result .= $city . ' &raquo; ';
                    }

                    if($b_region) {
                        $result .= $region . ' &raquo; ';
                    }

                    $result = preg_replace('|\s?&raquo;\s$|', '', $result);

                    if($result == '') {
                        $result = __('Search', 'modern');
                    }

                    return $result . ' - ' . $s_page . osc_page_title();
                break;
                case('login'):
                    switch ($section) {
                        case('recover'): return __('Recover your password','modern') . ' - ' . osc_page_title();
                        default:         return __('Login','modern') . ' - ' . osc_page_title();
                    }
                break;
                case('register'):
                    return __('Create a new account','modern') . ' - ' . osc_page_title();
                break;
                case('user'):
                    switch ($section) {
                        case('dashboard'):       return __('Dashboard','modern') . ' - ' . osc_page_title(); break;
                        case('items'):           return __('Manage my items','modern') . ' - ' . osc_page_title(); break;
                        case('alerts'):          return __('Manage my alerts','modern') . ' - ' . osc_page_title(); break;
                        case('profile'):         return __('Update my profile','modern') . ' - ' . osc_page_title(); break;
                        case('change_email'):    return __('Change my email','modern') . ' - ' . osc_page_title(); break;
                        case('change_password'): return __('Change my password','modern') . ' - ' . osc_page_title(); break;
                        case('forgot'):          return __('Recover my password','modern') . ' - ' . osc_page_title(); break;
                        default:                 return osc_page_title(); break;
                    }
                break;
                case('contact'):
                    return __('Contact','modern') . ' - ' . osc_page_title();
                break;
                default:
                    return osc_page_title();
                break;
            }
         }
     }

     if( !function_exists('meta_description') ) {
         function meta_description( ) {
            $location = Rewrite::newInstance()->get_location();
            $section  = Rewrite::newInstance()->get_section();

            switch ($location) {
                case ('item'):
                    switch ($section) {
                        case 'item_add':    return ''; break;
                        case 'item_edit':   return ''; break;
                        case 'send_friend': return ''; break;
                        case 'contact':     return ''; break;
                        default:
                            return osc_item_category() . ', ' . osc_highlight(osc_item_description(), 140) . '..., ' . osc_item_category();
                            break;
                    }
                break;
                case('page'):
                    return osc_highlight(strip_tags(osc_static_page_text()), 140);
                break;
                case('search'):
                    $result = '';

                    if(osc_count_items() == 0) {
                        return '';
                    }

                    if(osc_has_items ()) {
                        $result = osc_item_category() . ', ' . osc_highlight(strip_tags(osc_item_description()), 140) . '..., ' . osc_item_category();
                    }

                    osc_reset_items();
                    return $result;
                case(''): // home
                    $result = '';

                    if(osc_count_latest_items() == 0) {
                        return '';
                    }

                    if(osc_has_latest_items()) {
                        $result = osc_item_category() . ', ' . osc_highlight(strip_tags(osc_item_description()), 140) . '..., ' . osc_item_category();
                    }

                    osc_reset_items();
                    return $result;
                break;
            }
         }
     }
?>