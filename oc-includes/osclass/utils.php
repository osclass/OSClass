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

/**
 * Remove resources from disk
 * @param <type> $id
 * @return boolean
 */
function osc_deleteResource( $id ) {
    if( is_array( $id ) ){
        $id = $id[0];
    }
    $resource = ItemResource::newInstance()->findByPrimaryKey($id) ;
    if( !is_null($resource) ){
        $resource_original  = osc_base_path() . $resource['s_path'] .$resource['pk_i_id'].".".$resource['s_extension'];
        $resource_thum      = osc_base_path() . $resource['s_path'] .$resource['pk_i_id']."_*".".".$resource['s_extension'];
        array_map( "unlink" , glob($resource_thum));
        array_map( "unlink" , glob($resource_original));
    }
}
/**
 * Tries to delete the directory recursivaly.
 * @return true on success.
 */
function osc_deleteDir($path) {
    if (!is_dir($path))
        return false;

    $fd = @opendir($path);
    if (!$fd)
        return false;

    while ($file = @readdir($fd)) {
        if ($file != '.' && $file != '..') {
            if (!is_dir($path . '/' . $file)) {
                if (!@unlink($path . '/' . $file)) {
                    closedir($fd);
                    return false;
                } else {
                    osc_deleteDir($path . '/' . $file);
                }
            } else {
                osc_deleteDir($path . '/' . $file);
            }
        }
    }
    closedir($fd);

    return @rmdir($path);
}

/**
 * Unpack a ZIP file into the specific path in the second parameter.
 * @return true on success.
 */
function osc_packageExtract($zipPath, $path) {
    if(!file_exists($path)) {
        if (!@mkdir($path, 0666)) {
            return false;
        }
    }

    @chmod($path, 0777);

    $zip = new ZipArchive;
    if ($zip->open($zipPath) === true) {
        $zip->extractTo($path);
        $zip->close();
        return true;
    } else {
        return false;
    }
}

/**
 * Serialize the data (usefull at plugins activation)
 * @return the data serialized
 */
function osc_serialize($data) {

    if (!is_serialized($data)) {
        if (is_array($data) || is_object($data)) {
            return serialize($data);
        }
    }

    return $data;
}

/**
 * Unserialize the data (usefull at plugins activation)
 * @return the data unserialized
 */
function osc_unserialize($data) {
    if (is_serialized($data)) { // don't attempt to unserialize data that wasn't serialized going in
        return @unserialize($data);
    }

    return $data;
}

/**
 * Checks is $data is serialized or not
 * @return bool False if not serialized and true if it was.
 */
function is_serialized($data) {
    // if it isn't a string, it isn't serialized
    if (!is_string($data))
        return false;
    $data = trim($data);
    if ('N;' == $data)
        return true;
    if (!preg_match('/^([adObis]):/', $data, $badions))
        return false;
    switch ($badions[1]) {
        case 'a' :
        case 'O' :
        case 's' :
            if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data))
                return true;
            break;
        case 'b' :
        case 'i' :
        case 'd' :
            if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data))
                return true;
            break;
    }
    return false;
}

/**
 * Check whether serialized data is of string type.
 * @return bool False if not a serialized string, true if it is.
 */
/*function is_serialized_string($data) {
    // if it isn't a string, it isn't a serialized string
    if (!is_string($data))
        return false;
    $data = trim($data);
    if (preg_match('/^s:[0-9]+:.*;$/s', $data)) // this should fetch all serialized strings
        return true;
    return false;
}*/

/**
 * VERY BASIC
 * Perform a POST request, so we could launch fake-cron calls and other core-system calls without annoying the user
 */
function osc_doRequest($url, $_data) {

    if (function_exists('fputs')) {
        // convert variables array to string:
        $data = array();
        while (list($n, $v) = each($_data)) {
            $data[] = "$n=$v";
        }
        $data = implode('&', $data);
        // format --> test1=a&test2=b etc.
        // parse the given URL
        $url = parse_url($url);
        if ($url['scheme'] != 'http') {
            //die('Only HTTP request are supported !');
        }

        // extract host and path:
        $host = $url['host'];
        $path = $url['path'];

        // open a socket connection on port 80
        $fp = @fsockopen($host, 80);
        
        if($fp!==false) {
            // send the request headers:
            fputs($fp, "POST $path HTTP/1.1\r\n");
            fputs($fp, "Host: $host\r\n");
            fputs($fp, "Referer: OSClass\r\n");
            fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fputs($fp, "Content-length: " . strlen($data) . "\r\n");
            fputs($fp, "Connection: close\r\n\r\n");
            fputs($fp, $data);

            // close the socket connection:
            fclose($fp);
        } else {
            osc_add_flash_message( _m('Error, auto-cron is not working propertly'), 'admin');
        }
    }
}

function osc_sendMail($params) {
    require_once osc_lib_path() . 'phpmailer/class.phpmailer.php';

    $mail = new PHPMailer(true);
    try {
        $mail->CharSet = "utf-8";

        if (osc_mailserver_auth()) {
            $mail->IsSMTP() ;
            $mail->SMTPAuth = true ;
        }

        $mail->SMTPSecure = ( isset($params['ssl']) ) ? $params['ssl'] : osc_mailserver_ssl() ;
        $mail->Username = ( isset($params['username']) ) ? $params['username'] : osc_mailserver_username() ;
        $mail->Password = ( isset($params['password']) ) ? $params['password'] : osc_mailserver_password() ;
        $mail->Host = ( isset($params['host']) ) ? $params['host'] : osc_mailserver_host() ;
        $mail->Port = ( isset($params['port']) ) ? $params['port'] : osc_mailserver_port() ;
        $mail->From = ( isset($params['from']) ) ? $params['from'] : osc_contact_email() ;
        $mail->FromName = ( isset($params['from_name']) ) ? $params['from_name'] : osc_page_title() ;
        $mail->Subject = ( isset($params['subject']) ) ? $params['subject'] : '' ;
        $mail->Body = ( isset($params['body']) ) ? $params['body'] : '' ;
        $mail->AltBody = ( isset($params['alt_body']) ) ? $params['alt_body'] : '' ;
        $to = ( isset($params['to']) ) ? $params['to'] : '' ;
        $to_name = ( isset($params['to_name']) ) ? $params['to_name'] : '' ;
        if ( isset($params['add_bbc']) ) $mail->AddBCC($params['add_bbc']);
        if ( isset($params['reply_to']) ) $mail->AddReplyTo($params['reply_to']);

        if( isset($params['attachment']) ) {
            $mail->AddAttachment($params['attachment']) ;
        }

        $mail->IsHTML(true) ;
        $mail->AddAddress($to, $to_name) ;
        $mail->Send() ;
        return true ;

    } catch (phpmailerException $e) {
        return false;
    } catch (Exception $e) {
        return false;
    }
    return false;
}


function osc_mailBeauty($text, $params) {

	$text = str_ireplace($params[0], $params[1], $text) ;
	$kwords = array('{WEB_URL}', '{WEB_TITLE}', '{CURRENT_DATE}', '{HOUR}') ;
	$rwords = array(osc_base_url(), osc_page_title(), date('Y-m-d H:i:s'), date('H:i')) ;
	$text = str_ireplace($kwords, $rwords, $text) ;
    
	return $text ;
}


function osc_copy($source, $dest, $options=array('folderPermission'=>0755,'filePermission'=>0755)) {
	$result =true;
	if (is_file($source)) {
		if ($dest[strlen($dest)-1]=='/') {
			if (!file_exists($dest)) {
				cmfcDirectory::makeAll($dest,$options['folderPermission'],true);
			}
			$__dest=$dest."/".basename($source);
		} else {
			$__dest=$dest;
		}
		if(function_exists('copy')) {
            $result = @copy($source, $__dest);
		} else {
			$result=osc_copyemz($source, $__dest);
		}
		@chmod($__dest,$options['filePermission']);

	} elseif(is_dir($source)) {
		if ($dest[strlen($dest)-1]=='/') {
			if ($source[strlen($source)-1]=='/') {
				//Copy only contents
			} else {
				//Change parent itself and its contents
				$dest=$dest.basename($source);
				@mkdir($dest);
				@chmod($dest,$options['filePermission']);
			}
		} else {
			if ($source[strlen($source)-1]=='/') {
				//Copy parent directory with new name and all its content
				@mkdir($dest,$options['folderPermission']);
				@chmod($dest,$options['filePermission']);
			} else {
				//Copy parent directory with new name and all its content
				@mkdir($dest,$options['folderPermission']);
				@chmod($dest,$options['filePermission']);
			}
		}

		$dirHandle=opendir($source);
		$result = true;
		while($file=readdir($dirHandle)) {
			if($file!="." && $file!="..") {
				if(!is_dir($source."/".$file)) {
					$__dest=$dest."/".$file;
				} else {
					$__dest=$dest."/".$file;
				}
				//echo "$source/$file ||| $__dest<br />";
				$data = osc_copy($source."/".$file, $__dest, $options);
				if($data==false) {
				    $result = false;
				}
			}
		}
		closedir($dirHandle);

	} else {
		$result=true;
	}
	return $result;
}



function osc_copyemz($file1,$file2){
	$contentx =@file_get_contents($file1);
	$openedfile = fopen($file2, "w");
	fwrite($openedfile, $contentx);
	fclose($openedfile);
	if ($contentx === FALSE) {
		$status=false;
	} else {
		$status=true;
	}
                   
	return $status;
} 




function osc_dbdump($path, $file)
{
    if ( !is_writable($path) ) return -5 ;
	if($path == '') return -1 ;

    //checking connection
    $link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) ;
    if (!$link) return -2 ;
    
    //selecting database
    mysql_query("SET NAMES 'utf8'", $link) ;
    $db = mysql_select_db(DB_NAME, $link) ;
    if (!$db) return -3 ;

    $path .= $file ;
    
    $sql = 'show tables;' ;
    $result = mysql_query($sql) ;
    
    if(!$result) {
        $_str = '' ;
        $_str .= '/* no tables in ' . DB_NAME . ' */' ;
        $_str .= "\n" ;

        $f = fopen($path, "a") ;
        fwrite($f, $_str) ;
        fclose() ;

        return -4 ;
    }


    $_str = '' ;
    $_str .= '/* OSCLASS MYSQL Autobackup (' . date('Y-m-d H:i:s') . ') */' ;
    $_str .= "\n" ;

    $f = fopen($path, "a") ;
    fwrite($f, $_str) ;
    fclose($f) ;

    $tables = array() ;
    while($row = mysql_fetch_row($result)) {
        $tables[$row[0]] = $row[0];
    }

    $tables_order = array('t_locale', 't_country', 't_currency', 't_region', 't_city', 't_city_area', 't_widget', 't_admin', 't_user', 't_user_description', 't_category', 't_category_description', 't_category_stats', 't_item', 't_item_description', 't_item_location', 't_item_stats', 't_item_resource', 't_item_comment', 't_preference', 't_user_preferences', 't_pages', 't_pages_description', 't_plugin_category', 't_cron', 't_alerts', 't_keywords');
    // Backup default OSClass tables in order, so no problem when importing them back
    foreach($tables_order as $table) {
        if(array_key_exists(DB_TABLE_PREFIX . $table, $tables)) {
            osc_dump_table_structure($path, DB_TABLE_PREFIX . $table) ;
            osc_dump_table_data($path, DB_TABLE_PREFIX . $table) ;
            unset($tables[DB_TABLE_PREFIX . $table]) ;
        }
    }
    
    // Backup the rest of tables
    foreach($tables as $table) {
        osc_dump_table_structure($path, $table) ;
        osc_dump_table_data($path, $table) ;
    }

    mysql_free_result($result) ;    
    mysql_close() ;
    
    return 1 ;
}

function osc_dump_table_structure($path, $table) {

    if ( !is_writable($path) ) return false ;
    
    $_str = '' ;
	$_str .= '/* Table structure for table `' . $table . '` */' ;
    $_str .= "\n" ;

	// DANGEROUS LINE
	//fwrite($f, "DROP TABLE IF EXISTS `$table`;\n\n";

	$sql = 'show create table `' . $table . '`;' ;
	$result = mysql_query($sql) ;
	if($result) {
		if($row = mysql_fetch_assoc($result)) {
			$_str .= $row['Create Table'] . ';' ;
            $_str .= "\n\n" ;
		}
        mysql_free_result($result) ;
	}

    $f = fopen($path, "a") ;
    fwrite($f, $_str) ;
	fclose($f) ;

    return true ;
}

function osc_dump_table_data($path, $table)
{
	if ( !is_writable($path) ) return false ;

    $sql = "select * from `$table`;" ;
	$result = mysql_query($sql) ;
    $_str = '' ;
	if($result) {
		$num_rows = mysql_num_rows($result) ;
		$num_fields = mysql_num_fields($result) ;

		if( $num_rows > 0 ) {
			$_str .= '/* dumping data for table `' . $table . '` */' ;
            $_str .= "\n" ;

			$field_type = array() ;
			$i = 0 ;
			while( $i < $num_fields) {
				$meta = mysql_fetch_field($result, $i);
				array_push($field_type, $meta->type);
				$i++;
			}

			$_str .= 'insert into `' . $table . '` values' ;
            $_str .= "\n" ;

            $index = 0 ;
			while( $row = mysql_fetch_row($result) ) {
				$_str .= "(" ;

                for( $i = 0 ; $i < $num_fields ; $i++ ) {
					if(is_null( $row[$i])) {
                        $_str .= 'null' ;
					} else {
						switch( $field_type[$i]) {
							case 'int':
                                $_str .= $row[$i] ;
                                break;
							case 'string':
							case 'blob' :
							default:
								$_str .= '\'' . mysql_real_escape_string($row[$i]) . '\'' ;
						}
					}
					if($i < $num_fields-1) {
                        $_str .= ',' ;
					}
				}
                $_str .= ')' ;

				if($index < $num_rows-1) {
                    $_str .= ',' ;
				} else {
                    $_str .= ';' ;
				}
                $_str .= "\n" ;

				$index++ ;
			}
		}
        mysql_free_result($result) ;
	}
	
	$_str .= "\n" ;

    $f = fopen($path, "a") ;
    fwrite($f, $_str) ;
    fclose($f) ;

    return true ;
}


function osc_downloadFile($sourceFile, $downloadedFile) {

	set_time_limit(0);
	ini_set('display_errors',true);
			
	$fp = fopen (osc_content_path() . 'downloads/' . $downloadedFile, 'w+');
	$ch = curl_init($sourceFile);
	curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);

}


function osc_file_get_contents($url){
    /*
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $data = curl_exec($ch);
    curl_close($ch);
    return $data;*/

    return file_get_contents($url) ;
}



// If JSON ext is not present
if ( !function_exists('json_encode') ) {
    function json_encode( $string ) {
        global $osc_json;

        if ( !is_a($osc_json, 'Services_JSON') ) {
            require_once LIB_PATH . 'json/JSON.php';
            $osc_json = new Services_JSON();
        }

        return $osc_json->encode( $string );
    }
}

if ( !function_exists('json_decode') ) {
    function json_decode( $string, $assoc_array = false ) {
        global $osc_json;

        if ( !is_a($osc_json, 'Services_JSON') ) {
            require_once LIB_PATH . '/json/JSON.php';
            $osc_json = new Services_JSON();
        }

        $res = $osc_json->decode( $string );
        if ( $assoc_array ) $res = _json_decode_object_helper( $res );

        return $res;
    }

    function _json_decode_object_helper($data) {
        if ( is_object($data) )
            $data = get_object_vars($data);

        return is_array($data) ? array_map(__FUNCTION__, $data) : $data;
    }
}


/**
 * Check if we loaded some specific module of apache
 *
 * @param string $mod
 * 
 * @return bool
 */
function apache_mod_loaded($mod) {

    if(function_exists('apache_get_modules')) {
        $modules = apache_get_modules();
        if(in_array($mod, $modules)) { return true; }
    } else if(function_exists('phpinfo')) {
        ob_start();
        phpinfo(INFO_MODULES);
        $content = ob_get_contents();
        if(stripos($content, $mod)!==FALSE) { return true; }
        ob_end_clean();
    }
    return false;
}

/**
 * Change version to param number
 *
 * @param mixed version
 */
function osc_changeVersionTo($version = null) {

    if($version != null) {
        Preference::newInstance()->update(array('s_value' => $version), array( 's_section' => 'osclass', 's_name' => 'version'));
        //XXX: I don't know if it's really needed. Only for reload the values of the preferences
        Preference::newInstance()->toArray() ;
    }    
}

function strip_slashes_extended($array) {
    if(is_array($array)) {
        foreach($array as $k => &$v) {
            $v = strip_slashes_extended($v);
        }
    } else {
        $array = stripslashes($array);
    }
    return $array;
}

/**
 * Unzip's a specified ZIP file to a location
 *
 * @param string $file Full path of the zip file
 * @param string $to Full path where it is going to be unzipped
 * @return int
 */
function osc_unzip_file($file, $to) {
    if (!file_exists($to)) {
        if (!@mkdir($to, 0766)) {
            return 0;
        }
    }

    @chmod($to, 0777);

    if (!is_writable($to)) {
        return 0;
    }

    if (class_exists('ZipArchive')) {
        return _unzip_file_ziparchive($file, $to);
    }

    // if ZipArchive class doesn't exist, we use PclZip
    return _unzip_file_pclzip($file, $to);
}

/**
 * We assume that the $to path is correct and can be written. It unzips an archive using the PclZip library.
 *
 * @param string $file Full path of the zip file
 * @param string $to Full path where it is going to be unzipped
 * @return int
 */
function _unzip_file_ziparchive($file, $to) {
    $zip = new ZipArchive();
    $zipopen = $zip->open($file, 4);

    if ($zipopen !== true) {
        return 2;
    }
    // The zip is empty
    if($zip->numFiles==0) {
        return 2;
    }
    

    for ($i = 0; $i < $zip->numFiles; $i++) {
        $file = $zip->statIndex($i);

        if (!$file) {
            return -1;
        }

        if (substr($file['name'], 0, 9) === '__MACOSX/') {
            continue;
        }

        if (substr($file['name'], -1) == '/') {
            @mkdir($to . $file['name'], 0777);
            continue;
        }

        $content = $zip->getFromIndex($i);
        if ($content === false) {
            return -1;
        }

        $fp = @fopen($to . $file['name'], 'w');
        if (!$fp) {
            return -1;
        }

        @fwrite($fp, $content);
        @fclose($fp);
    }

    $zip->close();

    return 1;
}

/**
 * We assume that the $to path is correct and can be written. It unzips an archive using the PclZip library.
 *
 * @param string $zip_file Full path of the zip file
 * @param string $to Full path where it is going to be unzipped
 * @return int 
 */
function _unzip_file_pclzip($zip_file, $to) {
    // first, we load the library
    require_once LIB_PATH . 'pclzip/pclzip.lib.php';

    $archive = new PclZip($zip_file);
    if (($files = $archive->extract(PCLZIP_OPT_EXTRACT_AS_STRING)) == false) {
        return 2;
    }

    // check if the zip is not empty
    if (count($files) == 0) {
        return 2;
    }

    // Extract the files from the zip
    foreach ($files as $file) {
        if (substr($file['filename'], 0, 9) === '__MACOSX/') {
            continue;
        }
        
        if ($file['folder']) {
            @mkdir($to . $file['filename'], 0777);
            continue;
        }


        $fp = @fopen($to . $file['filename'], 'w');
        if (!$fp) {
            return -1;
        }

        @fwrite($fp, $file['content']);
        @fclose($fp);
    }

    return 1;
}


/**
 * Common interface to zip a specified folder to a file using ziparchive or pclzip
 *
 * @param string $archive_folder full path of the folder
 * @param string $archive_name full path of the destination zip file
 * @return int
 */
function osc_zip_folder($archive_folder, $archive_name) {
    if (class_exists('ZipArchive')) {
        return _zip_folder_ziparchive($archive_folder, $archive_name);
    }
    // if ZipArchive class doesn't exist, we use PclZip
    return _zip_folder_pclzip($archive_folder, $archive_name);
}

/**
 * Zips a specified folder to a file
 *
 * @param string $archive_folder full path of the folder
 * @param string $archive_name full path of the destination zip file
 * @return int
 */
function _zip_folder_ziparchive($archive_folder, $archive_name) {

	$zip = new ZipArchive;
	if ($zip -> open($archive_name, ZipArchive::CREATE) === TRUE) {
		$dir = preg_replace('/[\/]{2,}/', '/', $archive_folder."/");
   
		$dirs = array($dir);
		while (count($dirs)) {
			$dir = current($dirs);
			$zip -> addEmptyDir(str_replace(ABS_PATH, '', $dir));
      
			$dh = opendir($dir);
			while (false !== ($_file = readdir($dh))) {
				
				if ($_file != '.' && $_file != '..') {
					if (is_file($dir.$_file)) {
						$zip -> addFile($dir.$_file, str_replace(ABS_PATH, '', $dir.$_file));
					} elseif (is_dir($dir.$_file)) {
						$dirs[] = $dir.$_file."/";
					}
				}
			}
			closedir($dh);
			array_shift($dirs);
		}   
		$zip -> close();
		return true;
	} else {
		return false;
	}

}

/**
 * Zips a specified folder to a file
 *
 * @param string $archive_folder full path of the folder
 * @param string $archive_name full path of the destination zip file
 * @return int
 */
function _zip_folder_pclzip($archive_folder, $archive_name) {

    // first, we load the library
    require_once LIB_PATH . 'pclzip/pclzip.lib.php';

    $zip = new PclZip($archive_name);
    if($zip) {
		$dir = preg_replace('/[\/]{2,}/', '/', $archive_folder."/");
   
        $v_dir = osc_base_path();
        $v_remove = $v_dir;

        // To support windows and the C: root you need to add the 
        // following 3 lines, should be ignored on linux
        if (substr($v_dir, 1,1) == ':') {
            $v_remove = substr($v_dir, 2);
        }
        $v_list = $zip->create($v_dir, PCLZIP_OPT_REMOVE_PATH, $v_remove);
        if ($v_list == 0) {
            return false;
        }
        return true;
    } else {
        return false;
    }
    
}

function osc_check_recaptcha() {

    require_once osc_lib_path() . 'recaptchalib.php';
    if ( Params::getParam("recaptcha_challenge_field") != '') {
        $resp = recaptcha_check_answer (osc_recaptcha_private_key()
                                        ,$_SERVER["REMOTE_ADDR"]
                                        ,Params::getParam("recaptcha_challenge_field")
                                        ,Params::getParam("recaptcha_response_field"));
                                        
        return $resp->is_valid;
    }

    return false;
}

?>
