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

    $timezone = array('Africa/Abidjan',
                      'Africa/Accra',
                      'Africa/Addis_Ababa',
                      'Africa/Algiers',
                      'Africa/Asmara',
                      'Africa/Bamako',
                      'Africa/Bangui',
                      'Africa/Banjul',
                      'Africa/Bissau',
                      'Africa/Blantyre',
                      'Africa/Brazzaville',
                      'Africa/Bujumbura',
                      'Africa/Cairo',
                      'Africa/Casablanca',
                      'Africa/Ceuta',
                      'Africa/Conakry',
                      'Africa/Dakar',
                      'Africa/Dar_es_Salaam',
                      'Africa/Djibouti',
                      'Africa/Douala',
                      'Africa/El_Aaiun',
                      'Africa/Freetown',
                      'Africa/Gaborone',
                      'Africa/Harare',
                      'Africa/Johannesburg',
                      'Africa/Kampala',
                      'Africa/Khartoum',
                      'Africa/Kigali',
                      'Africa/Kinshasa',
                      'Africa/Lagos',
                      'Africa/Libreville',
                      'Africa/Lome',
                      'Africa/Luanda',
                      'Africa/Lubumbashi',
                      'Africa/Lusaka',
                      'Africa/Malabo',
                      'Africa/Maputo',
                      'Africa/Maseru',
                      'Africa/Mbabane',
                      'Africa/Mogadishu',
                      'Africa/Monrovia',
                      'Africa/Nairobi',
                      'Africa/Ndjamena',
                      'Africa/Niamey',
                      'Africa/Nouakchott',
                      'Africa/Ouagadougou',
                      'Africa/Porto-Novo',
                      'Africa/Sao_Tome',
                      'Africa/Tripoli',
                      'Africa/Tunis',
                      'Africa/Windhoek',
                      'America/Adak',
                      'America/Anchorage',
                      'America/Anguilla',
                      'America/Antigua',
                      'America/Araguaina',
                      'America/Argentina/Buenos_Aires',
                      'America/Argentina/Catamarca',
                      'America/Argentina/Cordoba',
                      'America/Argentina/Jujuy',
                      'America/Argentina/La_Rioja',
                      'America/Argentina/Mendoza',
                      'America/Argentina/Rio_Gallegos',
                      'America/Argentina/Salta',
                      'America/Argentina/San_Juan',
                      'America/Argentina/San_Luis',
                      'America/Argentina/Tucuman',
                      'America/Argentina/Ushuaia',
                      'America/Aruba',
                      'America/Asuncion',
                      'America/Atikokan',
                      'America/Bahia',
                      'America/Barbados',
                      'America/Belem',
                      'America/Belize',
                      'America/Blanc-Sablon',
                      'America/Boa_Vista',
                      'America/Bogota',
                      'America/Boise',
                      'America/Cambridge_Bay',
                      'America/Campo_Grande',
                      'America/Cancun',
                      'America/Caracas',
                      'America/Cayenne',
                      'America/Cayman',
                      'America/Chicago',
                      'America/Chihuahua',
                      'America/Costa_Rica',
                      'America/Cuiaba',
                      'America/Curacao',
                      'America/Danmarkshavn',
                      'America/Dawson',
                      'America/Dawson_Creek',
                      'America/Denver',
                      'America/Detroit',
                      'America/Dominica',
                      'America/Edmonton',
                      'America/Eirunepe',
                      'America/El_Salvador',
                      'America/Fortaleza',
                      'America/Glace_Bay',
                      'America/Godthab',
                      'America/Goose_Bay',
                      'America/Grand_Turk',
                      'America/Grenada',
                      'America/Guadeloupe',
                      'America/Guatemala',
                      'America/Guayaquil',
                      'America/Guyana',
                      'America/Halifax',
                      'America/Havana',
                      'America/Hermosillo',
                      'America/Indiana/Indianapolis',
                      'America/Indiana/Knox',
                      'America/Indiana/Marengo',
                      'America/Indiana/Petersburg',
                      'America/Indiana/Tell_City',
                      'America/Indiana/Vevay',
                      'America/Indiana/Vincennes',
                      'America/Indiana/Winamac',
                      'America/Inuvik',
                      'America/Iqaluit',
                      'America/Jamaica',
                      'America/Juneau',
                      'America/Kentucky/Louisville',
                      'America/Kentucky/Monticello',
                      'America/La_Paz',
                      'America/Lima',
                      'America/Los_Angeles',
                      'America/Maceio',
                      'America/Managua',
                      'America/Manaus',
                      'America/Marigot',
                      'America/Martinique',
                      'America/Mazatlan',
                      'America/Menominee',
                      'America/Merida',
                      'America/Mexico_City',
                      'America/Miquelon',
                      'America/Moncton',
                      'America/Monterrey',
                      'America/Montevideo',
                      'America/Montreal',
                      'America/Montserrat',
                      'America/Nassau',
                      'America/New_York',
                      'America/Nipigon',
                      'America/Nome',
                      'America/Noronha',
                      'America/North_Dakota/Center',
                      'America/North_Dakota/New_Salem',
                      'America/Panama',
                      'America/Pangnirtung',
                      'America/Paramaribo',
                      'America/Phoenix',
                      'America/Port-au-Prince',
                      'America/Port_of_Spain',
                      'America/Porto_Velho',
                      'America/Puerto_Rico',
                      'America/Rainy_River',
                      'America/Rankin_Inlet',
                      'America/Recife',
                      'America/Regina',
                      'America/Resolute',
                      'America/Rio_Branco',
                      'America/Santarem',
                      'America/Santiago',
                      'America/Santo_Domingo',
                      'America/Sao_Paulo',
                      'America/Scoresbysund',
                      'America/Shiprock',
                      'America/St_Barthelemy',
                      'America/St_Johns',
                      'America/St_Kitts',
                      'America/St_Lucia',
                      'America/St_Thomas',
                      'America/St_Vincent',
                      'America/Swift_Current',
                      'America/Tegucigalpa',
                      'America/Thule',
                      'America/Thunder_Bay',
                      'America/Tijuana',
                      'America/Toronto',
                      'America/Tortola',
                      'America/Vancouver',
                      'America/Whitehorse',
                      'America/Winnipeg',
                      'America/Yakutat',
                      'America/Yellowknife',
                      'Asia/Aden',
                      'Asia/Almaty',
                      'Asia/Amman',
                      'Asia/Anadyr',
                      'Asia/Aqtau',
                      'Asia/Aqtobe',
                      'Asia/Ashgabat',
                      'Asia/Baghdad',
                      'Asia/Bahrain',
                      'Asia/Baku',
                      'Asia/Bangkok',
                      'Asia/Beirut',
                      'Asia/Bishkek',
                      'Asia/Brunei',
                      'Asia/Choibalsan',
                      'Asia/Chongqing',
                      'Asia/Colombo',
                      'Asia/Damascus',
                      'Asia/Dhaka',
                      'Asia/Dili',
                      'Asia/Dubai',
                      'Asia/Dushanbe',
                      'Asia/Gaza',
                      'Asia/Harbin',
                      'Asia/Ho_Chi_Minh',
                      'Asia/Hong_Kong',
                      'Asia/Hovd',
                      'Asia/Irkutsk',
                      'Asia/Jakarta',
                      'Asia/Jayapura',
                      'Asia/Jerusalem',
                      'Asia/Kabul',
                      'Asia/Kamchatka',
                      'Asia/Karachi',
                      'Asia/Kashgar',
                      'Asia/Kathmandu',
                      'Asia/Kolkata',
                      'Asia/Krasnoyarsk',
                      'Asia/Kuala_Lumpur',
                      'Asia/Kuching',
                      'Asia/Kuwait',
                      'Asia/Macau',
                      'Asia/Magadan',
                      'Asia/Makassar',
                      'Asia/Manila',
                      'Asia/Muscat',
                      'Asia/Nicosia',
                      'Asia/Novokuznetsk',
                      'Asia/Novosibirsk',
                      'Asia/Omsk',
                      'Asia/Oral',
                      'Asia/Phnom_Penh',
                      'Asia/Pontianak',
                      'Asia/Pyongyang',
                      'Asia/Qatar',
                      'Asia/Qyzylorda',
                      'Asia/Rangoon',
                      'Asia/Riyadh',
                      'Asia/Sakhalin',
                      'Asia/Samarkand',
                      'Asia/Seoul',
                      'Asia/Shanghai',
                      'Asia/Singapore',
                      'Asia/Taipei',
                      'Asia/Tashkent',
                      'Asia/Tbilisi',
                      'Asia/Tehran',
                      'Asia/Thimphu',
                      'Asia/Tokyo',
                      'Asia/Ulaanbaatar',
                      'Asia/Urumqi',
                      'Asia/Vientiane',
                      'Asia/Vladivostok',
                      'Asia/Yakutsk',
                      'Asia/Yekaterinburg',
                      'Asia/Yerevan',
                      'Atlantic/Azores',
                      'Atlantic/Bermuda',
                      'Atlantic/Canary',
                      'Atlantic/Cape_Verde',
                      'Atlantic/Faroe',
                      'Atlantic/Madeira',
                      'Atlantic/Reykjavik',
                      'Atlantic/South_Georgia',
                      'Atlantic/St_Helena',
                      'Atlantic/Stanley',
                      'Australia/ACT',
                      'Australia/Adelaide',
                      'Australia/Brisbane',
                      'Australia/Broken_Hill',
                      'Australia/Canberra',
                      'Australia/Currie',
                      'Australia/Darwin',
                      'Australia/Eucla',
                      'Australia/Hobart',
                      'Australia/LHI',
                      'Australia/Lindeman',
                      'Australia/Lord_Howe',
                      'Australia/Melbourne',
                      'Australia/North',
                      'Australia/NSW',
                      'Australia/Perth',
                      'Australia/Queensland',
                      'Australia/South',
                      'Australia/Sydney',
                      'Australia/Tasmania',
                      'Australia/Victoria',
                      'Australia/West',
                      'Australia/Yancowinna',
                      'Europe/Amsterdam',
                      'Europe/Andorra',
                      'Europe/Athens',
                      'Europe/Belgrade',
                      'Europe/Berlin',
                      'Europe/Bratislava',
                      'Europe/Brussels',
                      'Europe/Bucharest',
                      'Europe/Budapest',
                      'Europe/Chisinau',
                      'Europe/Copenhagen',
                      'Europe/Dublin',
                      'Europe/Gibraltar',
                      'Europe/Guernsey',
                      'Europe/Helsinki',
                      'Europe/Isle_of_Man',
                      'Europe/Istanbul',
                      'Europe/Jersey',
                      'Europe/Kaliningrad',
                      'Europe/Kiev',
                      'Europe/Lisbon',
                      'Europe/Ljubljana',
                      'Europe/London',
                      'Europe/Luxembourg',
                      'Europe/Madrid',
                      'Europe/Malta',
                      'Europe/Mariehamn',
                      'Europe/Minsk',
                      'Europe/Monaco',
                      'Europe/Moscow',
                      'Europe/Oslo',
                      'Europe/Paris',
                      'Europe/Podgorica',
                      'Europe/Prague',
                      'Europe/Riga',
                      'Europe/Rome',
                      'Europe/Samara',
                      'Europe/San_Marino',
                      'Europe/Sarajevo',
                      'Europe/Simferopol',
                      'Europe/Skopje',
                      'Europe/Sofia',
                      'Europe/Stockholm',
                      'Europe/Tallinn',
                      'Europe/Tirane',
                      'Europe/Uzhgorod',
                      'Europe/Vaduz',
                      'Europe/Vatican',
                      'Europe/Vienna',
                      'Europe/Vilnius',
                      'Europe/Volgograd',
                      'Europe/Warsaw',
                      'Europe/Zagreb',
                      'Europe/Zaporozhye',
                      'Europe/Zurich',
                      'Indian/Antananarivo',
                      'Indian/Chagos',
                      'Indian/Christmas',
                      'Indian/Cocos',
                      'Indian/Comoro',
                      'Indian/Kerguelen',
                      'Indian/Mahe',
                      'Indian/Maldives',
                      'Indian/Mauritius',
                      'Indian/Mayotte',
                      'Indian/Reunion',
                      'Pacific/Apia',
                      'Pacific/Auckland',
                      'Pacific/Chatham',
                      'Pacific/Easter',
                      'Pacific/Efate',
                      'Pacific/Enderbury',
                      'Pacific/Fakaofo',
                      'Pacific/Fiji',
                      'Pacific/Funafuti',
                      'Pacific/Galapagos',
                      'Pacific/Gambier',
                      'Pacific/Guadalcanal',
                      'Pacific/Guam',
                      'Pacific/Honolulu',
                      'Pacific/Johnston',
                      'Pacific/Kiritimati',
                      'Pacific/Kosrae',
                      'Pacific/Kwajalein',
                      'Pacific/Majuro',
                      'Pacific/Marquesas',
                      'Pacific/Midway',
                      'Pacific/Nauru',
                      'Pacific/Niue',
                      'Pacific/Norfolk',
                      'Pacific/Noumea',
                      'Pacific/Pago_Pago',
                      'Pacific/Palau',
                      'Pacific/Pitcairn',
                      'Pacific/Ponape',
                      'Pacific/Port_Moresby',
                      'Pacific/Rarotonga',
                      'Pacific/Saipan',
                      'Pacific/Tahiti',
                      'Pacific/Tarawa',
                      'Pacific/Tongatapu',
                      'Pacific/Truk',
                      'Pacific/Wake',
                      'Pacific/Wallis');

?>