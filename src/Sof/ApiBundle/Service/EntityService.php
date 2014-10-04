<?php

namespace Sof\ApiBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Sof\ApiBundle\Entity\BaseRepository;
use Sof\ApiBundle\Exception\SofApiException;
use Sof\ApiBundle\Lib\DateUtil;
use Sof\ApiBundle\Lib\StringUtil;
use Symfony\Component\Config\Definition\Exception\Exception;

class EntityService
{
    const SLAVE     = 'select_only';
    const MASTER    = 'default';

    /**
     * @var EntityManager
     */
    private $em;

    private $doctrine;

    /**
     * @var boolean
     */
    private $autoExecute = true;

    /**
     * @var boolean
     */
    private $dqlProcessing = false;

    /**
     * @var boolean
     */
    private $queryList = array();

    /**
     * @var boolean
     */
    private $needSaveExecute = false;

    /**
     * @param $doctrine
     * @param EntityManager $em
     */
    public function __construct($doctrine, $em)
    {
        $this->doctrine = $doctrine;
        $this->em = $em;
    }

    /**
     * @return mixed
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param boolean $isFlush
     */
    public function setAutoExecute($isFlush)
    {
        $this->autoExecute = $isFlush;
    }

    /**
     * @return boolean
     */
    public function getAutoExecute()
    {
        return $this->autoExecute;
    }

    /**
     * @return boolean
     */
    public function getDqlProcessing()
    {
        return $this->dqlProcessing;
    }

    /**
     * @param boolean $dqlProcessing
     */
    public function setDqlProcessing($dqlProcessing)
    {
        return $this->dqlProcessing = $dqlProcessing;
    }

    /**
     * @return array
     */
    public function getQueryList()
    {
        return $this->queryList;
    }

    /**
     * @param array $entities
     * @param bool|string $isFlush
     */
    public function save($entities, $isFlush = 'DEFAULT')
    {
        if (!$entities) {
            return;
        }

        if (!is_array($entities)) {
            $entities = array($entities);
        }

        foreach ($entities as $entity) {
            $this->em->persist($entity);
        }

        if ($isFlush === 'DEFAULT') {
            $isFlush = $this->getAutoExecute();
        }

        $this->needSaveExecute = true;
        if ($isFlush) {
            $this->saveExecute();
        }
    }

    /**
     * @param String $table
     * @param Mixed $singleOrArray
     * @param bool|string $isExecute
     * @return int|mixed
     */
    public function delete($table, $singleOrArray, $isExecute = 'DEFAULT')
    {
        if ($isExecute === 'DEFAULT') {
            $isExecute = $this->getAutoExecute();
        }

        return $this->process($table . ':delete', $singleOrArray, $isExecute);
    }

    /**
     * Select data in slave database
     * @param $callback
     * @return int|mixed
     */
    public function selectOnly($callback)
    {
        $params = func_get_args();
        array_shift($params);
        return $this->process($callback, $params, self::SLAVE);
    }

    /**
     * Select data in slave|master database depend on query
     * @param $callback
     * @return int|mixed
     */
    public function selectOnMaster($callback)
    {
        $params = func_get_args();
        array_shift($params);
        return $this->process($callback, $params, self::MASTER);
    }

    /**
     * @param $callback
     * @param $params
     * @param string $connection
     * @return int|mixed
     */
    private function process($callback, $params, $connection = self::MASTER)
    {
        list($entity, $method) = explode(':', $callback);
        $repositoryName = BaseRepository::ENTITY_BUNDLE . ':' . $entity;
        $repository = $this->doctrine->getRepository($repositoryName, $connection);
        $handler = array($repository, $method);

        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        return -1;
    }

    public function saveCopyData($entities, $params, $isFlush = 'DEFAULT')
    {
        if (!$entities) {
            return;
        }

        if (!is_array($entities)) {
            $entities = array($entities);
        }

        foreach ($entities as $entity) {
            foreach ($params as $key => $value) {
                if (method_exists($entity, 'set' . ucfirst($key))) {
                    call_user_func(array($entity, 'set' . ucfirst($key)), $value);
                }
            }
        }

        $this->save($entities, $isFlush);
    }

    public function getFirstData($entity, $args = array(), $connection = self::SLAVE)
    {
        $qBuilder = $this->process($entity . ':querySimpleEntities', array($args), $connection);
        $result = $qBuilder->getQuery()->setMaxResults(1)->getOneOrNullResult(
            \Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY
        );

        if ($result && isset($args['selects']) && count($args['selects']) == 1) {
            if (StringUtil::startsWith($args['selects'][0], 'SUM(')
                || StringUtil::startsWith($args['selects'][0], 'COUNT(')
                || StringUtil::startsWith($args['selects'][0], 'MAX(')
                || StringUtil::startsWith($args['selects'][0], 'MIN(')) {
                $result = current($result);
            } else {
                $result = $result[$args['selects'][0]];
            }

        }

        return $result;
    }

    public function getOneForUpdate($entity, $args = array())
    {
        $qBuilder = $this->process($entity . ':querySimpleEntities', array($args), self::MASTER);
        $result   = $qBuilder->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD, TRUE)->setMaxResults(1)->getOneOrNullResult();

        return $result;
    }

    public function getAllData($entity, $args = array(), $connection = self::SLAVE)
    {
        $qBuilder = $this->process($entity . ':querySimpleEntities', array($args), $connection);

        return $qBuilder->getQuery()->getArrayResult();
    }

    public function getForUpdate($entity, $args = array())
    {
        $qBuilder = $this->process($entity . ':querySimpleEntities', array($args), self::MASTER);
        $result   = $qBuilder->getQuery()->setHint(Query::HINT_FORCE_PARTIAL_LOAD, TRUE)->getResult();

        return $result;
    }

    public function dqlUpdate($entity, $args, $isExecute = true) {
        if (!isset($args['update']) || isset($args['selects'])) {
            throw new \Exception;
        }

        return $this->dqlRun('UPDATE', $entity, $args, $isExecute);
    }

    public function dqlDelete($entity, $args, $isExecute = true) {
        if (isset($args['update']) || isset($args['selects'])) {
            throw new \Exception;
        }

        return $this->dqlRun('DELETE', $entity, $args, $isExecute);
    }

    private function dqlRun($dqlType, $entity, $args, $isExecute = true) {
        $qBuilder = $this->process($entity . ':querySimpleEntities', array($args), self::MASTER);

        if ($dqlType == 'UPDATE') {
            $qBuilder->update();
        } else if ($dqlType == 'DELETE'){
            $qBuilder->delete();
        } else {
            throw new \Exception;
        }

        $this->queryList[] = $qBuilder->getQuery();

        $dqlResult = 0;

        if ($isExecute) {
            $dqlResult = $this->queryExecute();
        }

        return $dqlResult;
    }

    public function rawSqlUpInsert($entity, $args) {
        if (!isset($args['insert'])) {
            throw new \Exception;
        }

        $metadata = $this->getEntityManager()->getClassMetadata(BaseRepository::ENTITY_BUNDLE. ':'.$entity);

        $insertData = array();
        $updateData = array();
        foreach($args as $area => $fieldArea) {
            $dataMap = array();
            if ($area == 'insert' || $area == 'update') {
                foreach($fieldArea as $field => $value) {
                    $fieldMapping = $metadata->getFieldMapping($field);

                    if ($fieldMapping) {
                        if ($value instanceof \DateTime) {
                            $value = $value->format(DateUtil::FORMAT_DATE_TIME);
                        }
                        $dataMap[$fieldMapping['columnName']] = "'".$value."'";
                    }
                }

                if ($area == 'insert') {
                    $insertData['field'] = implode(',', array_keys($dataMap));
                    $insertData['value'] = implode(',', array_values($dataMap));
                } else {
                    $updateData = $dataMap;
                }
            }
        }

        $sqlUpdate = array();
        if (isset($args['selfUpdate'])) {
            foreach($args['selfUpdate'] as $field => $value) {
                $fieldMapping = $metadata->getFieldMapping($field);
                $field = $fieldMapping['columnName'];
                $sqlUpdate[] = "$field=$field" . $value;
            }
        }

        foreach($updateData as $field => $value) {
            $sqlUpdate[] = "$field=$value";
        }

        if ($insertData) {
            $this->queryExecute();

            $rawSql = "INSERT INTO {$metadata->getTableName()} ({$insertData['field']}) VALUES ({$insertData['value']})"
                ." ON DUPLICATE KEY UPDATE ".implode(',', $sqlUpdate).";";

            return $this->getEntityManager()->getConnection()->exec($rawSql);
        }

        return false;
    }

    public function dqlRawSqlUpdate($entity, $args) {
//        if ((!isset($args['selfUpdate']) && !isset($args['update'])) || !isset($args['conditions'])) {
//            throw new \Exception;
//        }

        $metadata = $this->getEntityManager()->getClassMetadata(BaseRepository::ENTITY_BUNDLE. ':'.$entity);

        $updateData = array();
        $whereData  = array();
        foreach($args as $area => $fieldArea) {
            $dataMap = array();
            if ($area == 'conditions' || $area == 'update') {
                foreach($fieldArea as $field => $value) {
                    $fieldMapping = $metadata->getFieldMapping($field);

                    if ($fieldMapping) {
                        if ($value instanceof \DateTime) {
                            $value = $value->format(DateUtil::FORMAT_DATE_TIME);
                        }
                        $dataMap[$fieldMapping['columnName']] = "'".$value."'";
                    }
                }

                if ($area == 'conditions') {
                    $whereData = $dataMap;
                } else {
                    $updateData = $dataMap;
                }
            }
        }

        $sqlUpdate = array();
        if (isset($args['selfUpdate'])) {
            foreach ($args['selfUpdate'] as $fieldOrg => $record) {
                $fieldMappingOrg = $metadata->getFieldMapping($fieldOrg);
                $fieldOrg = $fieldMappingOrg['columnName'];
                foreach ($record as $field => $value) {
                    $fieldMapping = $metadata->getFieldMapping($field);
                    $field = $fieldMapping['columnName'];
                    $sqlUpdate[] = "$fieldOrg=$field" . $value;
                }
            }
        }

        foreach($updateData as $field => $value) {
            $sqlUpdate[] = "$field=$value";
        }

        $where = array();
        foreach($whereData as $field => $value) {
            $where[] = "$field=$value";
        }

        $this->queryExecute();
        $rawSql = "UPDATE {$metadata->getTableName()} SET " . implode(', ', $sqlUpdate) . " WHERE " . implode(' AND ', $where) . ";";

        return $this->getEntityManager()->getConnection()->exec($rawSql);
    }

    /**
     * Find max by field
     *
     * @param $entityName
     * @param $fieldMax
     * @param null $parentField
     * @param null $parentId
     * @return int|mixed
     *
     * @author HieuNLD 2014/01/22
     */
    public function findMaxByField($entityName, $fieldMax, $parentField = null, $parentId = null) {

        return $this->selectOnMaster($entityName . ':findMaxByField', $entityName, $fieldMax, $parentField, $parentId);
    }

    public function completeTransaction($rollBack = FALSE) {
        $connection =  $this->em->getConnection();
        $result = TRUE;

        try {
            if ($rollBack) {
                throw new \Exception;
            }

            $this->queryExecute();
            $this->saveExecute();

            if ($this->dqlProcessing) {
                $connection->commit();
            }
        } catch (\Exception $e) {
            $result = FALSE;
            if ($this->dqlProcessing) {
                $connection->rollback();
                $this->em->close();
                $this->doctrine->resetEntityManager(self::MASTER);
                $this->em = $this->doctrine->getManager(self::MASTER);
            }
        }

        $this->dqlProcessing = FALSE;

        if (!($rollBack || $result)) {
            throw new \Exception;
        }
    }

    private function queryExecute() {
        if ($this->getQueryList() && !$this->dqlProcessing) {
            $connection =  $this->em->getConnection();
            $connection->beginTransaction();
            $this->dqlProcessing = TRUE;
        }

        $dqlResult = 0;

        foreach ($this->getQueryList() as $query) {
            $dqlResult = $query->execute();
        }
        $this->queryList = array();

        return $dqlResult;
    }

    public function saveExecute() {
        if ($this->needSaveExecute) {
            if (!$this->dqlProcessing) {
                $connection =  $this->em->getConnection();
                $connection->beginTransaction();
                $this->dqlProcessing = true;
            }

            $this->em->flush();
            $this->needSaveExecute = false;
        }
    }


    /**
     * 最終処理日付 = 処理日付マスタ（処理日付）
     * 条件、判定日時 < $dateTime 上記の最大の処理日付を取得する。
     *
     * @param $dateTime
     * @return \DateTime
     */
    public function getProcessingDateFor($dateTime) {
        return DateUtil::convertDateTimeToInsert($this->getFirstData(
            'ProcessingDateMaster',
            array(
                'conditions' => array(
                    'decisionDate' => array("<" => $dateTime)
                ),
                'selects'  => array('processingDate'),
                'orderBy' => array('processingDate' => 'DESC')
            )
        ));
    }

    /**
     * 処理日付翌日 = 処理日付マスタ（処理日付）
     * 条件、判定日時 > $dateTime の最小の処理日付を取得する。
     *
     * @param $dateTime
     * @return \DateTime
     */
    public function getNextProcessingDateFor($dateTime) {
        return $this->getFirstData(
            'ProcessingDateMaster',
            array(
                'conditions' => array(
                    'decisionDate' => array(">" => $dateTime)
                ),
                'selects'  => array('processingDate'),
                'orderBy' => array('processingDate')
            )
        );
    }


    /**
     * Select random in db with condition
     * @param $entityName
     * @param array $condition
     * @param $weightFieldName
     * @param array $select
     * @param string $connection
     * @throws \Sof\ApiBundle\Exception\SofApiException
     * @return mixed
     *
     * @author DatDvq 2014/04/15
     */
    public function selectRandomIn($params)
    {
        $initParams = array(
            'conditions'  => array(),
            'weightField' => '',
            'selects'     => array(),
            'connection'  => self::SLAVE
        );

        if (!isset($params['entityName'])) {
            throw new SofApiException;
        }

        $params = array_merge($initParams, $params);

        if ($params['weightField']) {
            $weightField = array("SUM(entity.{$params['weightField']})");
        } else {
            $metadata = $this->getEntityManager()->getClassMetadata(BaseRepository::ENTITY_BUNDLE. ':' . $params['entityName']);
            $weightField = array("COUNT(entity.{$metadata->getFieldNames()[0]}) AS countRecord");
        }

        $weightTotal = $this->getFirstData($params['entityName'], array('conditions' => $params['conditions'], 'selects' => $weightField), $params['connection']);
        if (!$weightTotal) {
            throw new SofApiException;
        }
        $weight = rand(1, $weightTotal);

        $result = null;
        if ($params['weightField']) {
            if ($params['selects']) {
                $params['selects'][] = $params['weightField'];
            }

            $list = $this->getAllData($params['entityName'], $params, $params['connection']);

            $weightTmp = 0;
            foreach($list as $record) {
                $weightTmp += $record[$params['weightField']];
                if ($weightTmp >= $weight) {
                    $result = $record;
                    break;
                }
            }

            if (count($params['selects']) == 2) {
                return $result[$params['selects'][0]];
            }
        } else {
            $args['firstResult'] = $weight - 1;
            $result = $this->getFirstData($params['entityName'], $params, $params['connection']);
        }

        return $result;
    }

    public function truncate($entities)
    {
        if (!is_array($entities)) {
            $entities = array($entities);
        }

        $numTruncated = 0;
        $em = $this->getEntityManager();
        $connection = $em->getConnection();
        $platform   = $connection->getDatabasePlatform();
        foreach ($entities as $entity) {
            $metadata = $em->getClassMetadata(BaseRepository::ENTITY_BUNDLE. ':'.$entity);
            $numTruncated += $connection->executeUpdate($platform->getTruncateTableSQL($metadata->getTableName(), true));
        }
        return $numTruncated;
    }
}