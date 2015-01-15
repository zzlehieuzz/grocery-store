<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sof\ApiBundle\Entity\ValueConst\BaseConst;

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
        $entityService = $this->getEntityService();

        $params = $this->getJsonParams();

        $path = $this->getRequest()->server->get('DOCUMENT_ROOT') . '/' . $this->getRequest()->getBasePath() . '/database/';

        $command = '';
        $error   = '';
        try {
            $db     = "chincchi_store";
            $dbpass = "admin123";
            $dbname = "chincchi_store";

            $filename = $path . date("YmdHis");
//            $command = "mysqldump -u $dbname -p $dbpass $db | gzip > $filename.sql.gz";
            $command = "mysqldump -u $dbname -p $dbpass --routines --complete-insert --opt $db > db_$filename.sql";
            system($command);
        } catch (\Exception $e) {
            $error = 'mysqldump-php error: ' . $e->getMessage();
        }

        return $this->jsonResponse(array('data' => array($command, $error)));
    }

    /**
     * @Route("/Common_Restore", name="Common_Restore")
     * @Method("GET")
     */
    public function Common_RestoreAction()
    {

    }
}