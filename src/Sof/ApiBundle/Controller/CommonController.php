<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\ValueConst\BaseConst;
use Sof\ApiBundle\Lib\SofUtil;
use Sof\ApiBundle\Lib\SqlExport;

class CommonController extends BaseController
{
    /**
     * @Route("/Common_Index", name="Common_Index")
     * @Method("GET")
     * @Template("SofApiBundle:Common:index.html.twig")
     */
    public function Common_IndexAction()
    {
        $module = $this->getEntityService()->getAllData('Module',
            array('selects' => array('name', 'iconCls', 'module'),
                  'conditions' => array('isActive' => BaseConst::FLAG_ON),
                  'orderBy' => array('sort')));

        $user = $this->get('security.context')->getToken()->getUser();

        $userData = $this->getEntityService()->getFirstData('User',
            array('selects' => array('id', 'roleId', 'userName', 'password', 'name'),
                  'conditions' => array('userName' => $user->getUserName())));

        return array('moduleJson' => json_encode($module),
                     'userJson'   => json_encode($userData));
    }

    /**
     * @Route("/Common_Backup", name="Common_Backup")
     * @Method("GET")
     */
    public function Common_BackupAction()
    {
        $error   = '';
//        $path = $this->getRequest()->server->get('DOCUMENT_ROOT') . '/' . $this->getRequest()->getBasePath() . '/database/';

//        $command = '';
//        $error   = '';
        try {
//            $command = "php app/console -em=dev db:dump";
//            system($command);
            $server   = $this->container->getParameter('database_host');
            $username = $this->container->getParameter('database_user');
            $password = $this->container->getParameter('database_password');
            $db       = $this->container->getParameter('database_name');
            $port     = $this->container->getParameter('database_port');
            $server   = $server . ':' . $port;

            $dbHost   = $this->container->getParameter('database_host');
            $dbUser = $this->container->getParameter('database_user');
            $dbPass = $this->container->getParameter('database_password');
            $dbName       = $this->container->getParameter('database_name');
            $port     = $this->container->getParameter('database_port');

            // your config
            $filename = 'yourGigaByteDump.sql';
//            $dbHost = 'localhost';
//            $dbUser = 'user';
//            $dbPass = '__pass__';
//            $dbName = 'dbname';
            $maxRuntime = 8; // less then your max script execution limit


            $deadline = time()+$maxRuntime;
            $progressFilename = $filename.'_filepointer'; // tmp file for progress
            $errorFilename = $filename.'_error'; // tmp file for erro

            mysql_connect($dbHost, $dbUser, $dbPass) OR die('connecting to host: '.$dbHost.' failed: '.mysql_error());
            mysql_select_db($dbName) OR die('select db: '.$dbName.' failed: '.mysql_error());

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


            ($fp = fopen($directory . 'db-' . $filenameSQL, 'r')) OR die('failed to open file:'.$filenameSQL);

// check for previous error
            if( file_exists($errorFilename) ){
                die('<pre> previous error: '.file_get_contents($errorFilename));
            }

// activate automatic reload in browser
            echo '<html><head> <meta http-equiv="refresh" content="'.($maxRuntime+2).'"><pre>';

// go to previous file position
            $filePosition = 0;
            if( file_exists($progressFilename) ){
                $filePosition = file_get_contents($progressFilename);
                fseek($fp, $filePosition);
            }

            $queryCount = 0;
            $query = '';
            while( $deadline>time() AND ($line=fgets($fp, 1024000)) ){
                if(substr($line,0,2)=='--' OR trim($line)=='' ){
                    continue;
                }

                $query .= $line;
                if( substr(trim($query),-1)==';' ){
                    if( !mysql_query($query) ){
                        $error = 'Error performing query \'<strong>' . $query . '\': ' . mysql_error();
                        file_put_contents($errorFilename, $error."\n");
                        exit;
                    }
                    $query = '';
                    file_put_contents($progressFilename, ftell($fp)); // save the current file position for
                    $queryCount++;
                }
            }

            if( feof($fp) ){
                echo 'dump successfully restored!';
            }else{
                echo ftell($fp).'/'.filesize($filenameSQL).' '.(round(ftell($fp)/filesize($filenameSQL), 2)*100).'%'."\n";
                echo $queryCount.' queries processed! please reload or wait for automatic browser refresh!';
            }





//            //Connect to DB the old fashioned way and get the names of the tables on the server
//            $cnx = mysql_connect($server, $username, $password) or die(mysql_error());
//            mysql_select_db($db, $cnx) or die(mysql_error());
//            mysql_set_charset('utf8', $cnx);
//            $tables = mysql_list_tables($db) or die(mysql_error());
//
//            //Create a list of tables to be exported
//            $table_list = array();
//            while($t = mysql_fetch_array($tables))
//            {
//                array_push($table_list, $t[0]);
//            }
//
//            //Instantiate the SQL_Export class
//            $se = new SqlExport();
//            $se->sqlExportConnect($server, $username, $password, $db, $table_list);
//            //Run the export
////            echo $se->export();
//            $error = $se->export();
//            //Clean up the joint
//            mysql_close($se->cnx);
//            mysql_close($cnx);
        } catch (\Exception $e) {
            $error = 'error: ' . $e->getMessage();
        }

        return $this->jsonResponse(array('data' => array($error)));
    }

    /**
     * @Route("/Common_Restore", name="Common_Restore")
     * @Method("GET")
     */
    public function Common_RestoreAction()
    {
        $t = array("table1", "table2", "table3");
        $exporter = new SQL_Export("localhost:3306", "username", "password", "sample", $t);
        $sql = $exporter->export();
    }
}