<?php

namespace Sof\ApiBundle\Lib;

use Sof\ApiBundle\Lib\SofUtil;

class SqlExport
{
  var $cnx;
  var $db;
  var $server;
  var $port;
  var $user;
  var $password;
  var $table;
  var $tables;
  var $exported;

  /**
   * @param $server
   * @param $user
   * @param $password
   * @param $db
   * @param $tables
   */
  public function sqlExportConnect($server, $user, $password, $db, $tables)
  {
    $this->db = $db;
    $this->user = $user;
    $this->password = $password;

    $sa = explode(":", $server);
    $this->server = $sa[0];
    $this->port = $sa[1];
    unset($sa);

    $this->tables = $tables;

    $this->cnx = mysql_connect($this->server, $this->user, $this->password) or $this->error(mysql_error());
    mysql_select_db($this->db, $this->cnx) or $this->error(mysql_error());
  }

  public function export()
  {
    $exportString = '';
    foreach ($this->tables as $t) {
      $this->table = $t;
      $header = $this->createHeader();
      $data   = $this->getData();
      $exportString .= "-- ----------------------------\n-- Table structure for $t\n-- ----------------------------";
      $exportString .= "\nDROP TABLE IF EXISTS `$t`;\n";
      $exportString .= $header . $data . "\n";
    }
    $exportString = "SET FOREIGN_KEY_CHECKS=0;\n\n" .$exportString;

    return $this->saveFile($exportString);
  }

  public function createHeader()
  {
    $arrType = array(
        'int',
        'string',
        'datetime',
        'not_null',
        'auto_increment',
        'binary',
        'primary_key'
    );

    $arrTypeReplace = array(
        'int',
        'varchar',
        'datetime',
        'NOT NULL',
        'AUTO_INCREMENT',
        '', ''
    );

    $fields = mysql_list_fields($this->db, $this->table, $this->cnx);
    $create = "CREATE TABLE `" . $this->table . "` (";
    $h = array();
    $pkey = '';
    for ($i = 0; $i < mysql_num_fields($fields); $i++) {
      $name  = mysql_field_name($fields, $i);
      $flags = str_replace($arrType, $arrTypeReplace, mysql_field_flags($fields, $i));
      $len   = mysql_field_len($fields, $i);
      $type  = str_replace($arrType, $arrTypeReplace, mysql_field_type($fields, $i));

      if ($type != 'datetime') {
        if($type == 'varchar') {
          $type = "$type($len) COLLATE utf8_unicode_ci";
        } else $type = "$type($len)";
      }


      $h[] = "\n  `$name` $type $flags";

      if (strpos($flags, "AUTO_INCREMENT")) {
        $pkey = ",\n  PRIMARY KEY (`$name`)\n";
      }
    }
    $last= "$pkey) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='" . $this->table . "';\n";
    $str = $create . implode(',', $h) . $last;

    return $str;
  }

  public function PMA_noCacheHeader()
  {
    if (defined('TESTSUITE')) {
      return;
    }
    // rfc2616 - Section 14.21
    header('Expires: ' . date(DATE_RFC1123));
    // HTTP/1.1
    header(
      'Cache-Control: no-store, no-cache, must-revalidate,'
      . '  pre-check=0, post-check=0, max-age=0'
    );

    header('Pragma: no-cache'); // HTTP/1.0
    header('Last-Modified: ' . date(DATE_RFC1123));
  }

  public function getData()
  {
    $d = null;
    $data = mysql_query("SELECT * FROM `" . $this->table . "` WHERE 1", $this->cnx) or $this->error(mysql_error());

    while ($cr = mysql_fetch_array($data, MYSQL_NUM)) {
      $d .= "INSERT INTO `" . $this->table . "` VALUES (";

      for ($i = 0; $i < sizeof($cr); $i++) {

        if ($cr[$i] == '') {
          $d .= 'NULL,';
        } else {
//          $v1 = mb_convert_encoding($cr[$i], 'Shift_JIS');
//          $v1 = addslashes($cr[$i]);
//          $v1 = preg_replace("#\n#i","\\n",$v1);
//          $cr[$i] = addslashes($cr[$i]);
//          $cr[$i] = ereg_replace("\n","\\n",$cr[$i]);
//          $cr[$i] = mb_convert_encoding($cr[$i], "HTML-ENTITIES", "UTF-8");
//          $cr[$i] = html_entity_decode($cr[$i]);
//          $v1 =$this->w1250_to_utf8($cr[$i]);

//          ini_set('mbstring.substitute_character', "none");
//          $strH= mb_convert_encoding($cr[$i], 'iso-8859-3', 'iso-8859-3');
//          $strH = iconv('UCS-2LE', 'UTF-8', $cr[$i]);
//          $strH = iconv("CP1252", "ISO-8859-1", $cr[$i]);
//            $strH = iconv('windows-1256', 'utf-8', $cr[$i]);
//          $strH = iconv('iso-8859-1', 'utf-8', $cr[$i]);
          $strH = iconv('iso-8859-1', 'ASCII//TRANSLIT', $cr[$i]);
//          iso-8859-1 -t utf-8
          //VISCII, TCVN, CP1258
//          $strH = iconv('UTF-8', 'ASCII//TRANSLIT', $cr[$i]);
//          $strH = mb_encode_mimeheader($cr[$i], 'UTF-8', 'UTF-8', "\n");
          //$strH = iconv('Windows', 'utf-8', $cr[$i]);
//          $strH = iconv("UTF-8", "ASCII//TRANSLIT", $cr[$i]);
//          echo $strH;

          $d .= "'$strH',";
        }
      }//die;
      $d = substr($d, 0, strlen($d) - 1);
      $d .= ");\n";
    }

    return ($d);
  }

    /**
     * Save SQL to file
     * @param string $sql
     * @param string $outputDir
     * @return bool
     */
    protected function saveFile(&$sql, $outputDir = 'database')
    {
        if (!$sql) return false;

        try {
            $directory   = "app\\backup\\db\\";
            $filenameSQL = SofUtil::createFileName() . '.sql';

            if (!is_dir($directory)) {
                mkdir($directory, 0777);
                umask(umask(0));
            }

//          $this->PMA_noCacheHeader();
//          header('Content-Description: File Transfer');
//          header('Content-Disposition: attachment; filename="' . $filenameSQL . '"');
//          header('Content-Type: text/x-sql');
//          header('Content-Transfer-Encoding: binary');
//          header('Content-Length: ' . 0);
//          return iconv(
//            'utf-8', 'utf-8//TRANSLIT', ''
//          );
          ini_set('mssql.charset', 'utf-8');
          $handle = fopen($directory . 'db-' . $filenameSQL, 'w+');
//          $strH = utf8_encode($sql);
//          $strH = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $sql);
//
          fwrite($handle, $sql);
          fclose($handle);
        } catch (Exception $e) {
            var_dump($e->getMessage());
            return false;
        }

        return true;
    }

  public function error($err)
  {
    die($err);
  }
}

?>