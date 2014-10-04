<?php

namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Sof\ApiBundle\Lib\StringUtil;

abstract class BaseRepository extends EntityRepository
{
    const ENTITY_BUNDLE = 'SofApiBundle';

    private $alias;

    /**
     * @var QueryBuilder
     */
    private $query;

    /**
     * @param $alias
     * @param array $options
     * @return QueryBuilder
     * @throws \Exception
     */
    public function buildQuery($alias, $options = array()) {

        if (!is_string($alias)) {
            throw new \Exception();
        }

        $query = $this->createQueryBuilder($alias);
        $this->alias = $alias;
        return $query;
    }

    /**
     * @param $alias
     * @param $filter
     * @param array $basic_fields
     * @return QueryBuilder
     */
    protected function buildSearchQuery($alias, $filter, $basic_fields = array()) {
        $this->query = $this->buildQuery($alias);

        if (isset($basic_fields['number'])) {
            foreach ($basic_fields['number'] as $field) {
                $this->addSearchNumber($filter, $field);
            }
        }

        if (isset($basic_fields['string'])) {
            foreach ($basic_fields['string'] as $field) {
                $this->addSearchString($filter, $field);
            }
        }

        if (isset($basic_fields['like'])) {
            foreach ($basic_fields['like'] as $field) {
                $this->addSearchLike($filter, $field);
            }
        }

        if (isset($basic_fields['single_check'])) {
            foreach ($basic_fields['single_check'] as $field) {
                $this->addSearchSingleCheck($filter, $field);
            }
        }

        if (isset($basic_fields['multi_check'])) {
            foreach ($basic_fields['multi_check'] as $field) {
                $this->addSearchMulticheck($filter, $field);
            }
        }

        return $this->query;
    }


    /**
     * @param $filter
     * @param $field
     */
    private function addSearchNumber($filter, $field)
    {
        if (isset($filter[$field]) && $filter[$field] !== NULL && $filter[$field] !== '') {
            $filterData = $filter[$field];

            if (is_numeric($filterData)) {
                $this->query->andWhere(sprintf('%s = %s', $this->dbFieldName($field), $filterData));
            } else {
                $this->query->andWhere(sprintf('%s = :%s', $this->dbFieldName($field), $field))
                    ->setParameter($field, $filterData);
            }
        }
    }

    /**
     * @param $filter
     * @param $field
     */
    private function addSearchString($filter, $field)
    {
        if (isset($filter[$field])) {
            $this->query->andWhere(sprintf('%s = :%s', $this->dbFieldName($field), $field))
                ->setParameter($field, $filter[$field]);
        }
    }

    /**
     * @param $filter
     * @param $field
     */
    private function addSearchLike($filter, $field)
    {
//        if (isset($filter[$field]) && $filter[$field]) {
//            $fieldData = trim($filter[$field]);
//            $fieldData = $fieldData . '%'; // left-hand match
//            $dbFieldName = $this->dbFieldName($field);
//
//            if (StringUtil::startsWith($fieldData, '*')) { // partial match
//                $fieldData = preg_replace('/^\*/', '%', $fieldData); // replace leading * with %
//            }
//
//            $this->query->andWhere(sprintf('%s LIKE :%s', $dbFieldName, $field))->setParameter($field, $fieldData);
//        }
    }

    /**
     * Convert form field name to db field name
     * @param string $field
     * @return string $dbFieldName
     */
    private function dbFieldName($field)
    {
        if (StringUtil::startsWith($field, 'SUM(') || StringUtil::startsWith($field, 'COUNT(')
            || StringUtil::startsWith($field, 'MAX(') || StringUtil::startsWith($field, 'MIN(')
            || StringUtil::startsWith($field, 'DISTINCT(')) {
            return $field;
        }

        if (StringUtil::contains($field,".")) {
//            $fieldName = str_replace('_', '.', $field); // ex: supplierStaff_kana => supplierStaff.kana
            $fieldName = $field;
        } else {
            $fieldName = $this->alias . '.' . $field;
        }

        return $fieldName;
    }

    /**
     * @param $filter
     * @param $field
     */
    private function addSearchSingleCheck($filter, $field)
    {
//        if (isset($filter[$field]) && $filter[$field]) {
//            $this->query->andWhere(sprintf('%s = %d', $this->dbFieldName($field), ValueList::constToValue('common.is_checked.YES')));
//        }
    }

    /**
     * @param $filter
     * @param $field
     */
    private function addSearchMultiCheck($filter, $field)
    {
//        if (isset($filter[$field]) && $filter[$field]) {
//            $searchData = explode(',', $filter[$field]);
//            $dbFieldName = $this->dbFieldName($field);
//            $query = array();
//
//            foreach ($searchData as $data) {
//                $data = trim($data);
//                array_push($query, sprintf('FIND_IN_SET(:%s, %s) <> 0', $field . $data, $dbFieldName));
//                $this->query->setParameter($field . $data, $data);
//            }
//
//            $this->query->andWhere('(' . implode(' OR ', $query) . ')');
//        }
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
     * @author hieunld 2013/03/29
     */
    public function findMaxByField($entityName, $fieldMax, $parentField = null, $parentId = null)
    {
        $em = $this->getEntityManager();
        if ($parentField && $parentId) {
            $query = sprintf('SELECT MAX(d.%s) FROM '. self::ENTITY_BUNDLE .':%s d WHERE d.%s = :parentId', $fieldMax, $entityName, $parentField);
            $result = $em->createQuery($query)->setParameter('parentId', $parentId);
        } else {
            $query = sprintf('SELECT MAX(d.%s) FROM '. self::ENTITY_BUNDLE .':%s d', $fieldMax, $entityName);
            $result = $em->createQuery($query);
        }
        $maxCode = $result->getSingleScalarResult();

        if (!$maxCode) {
            $maxCode = 0;
        }

        return $maxCode;
    }

    /**
     * forcePartialLoad. Query only in selected table
     * @param Query $query
     * @return Query
     *
     * @author Khiemnd 2013/06/17
     */
    public function setForcePartialLoad($query)
    {
        return $query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, TRUE);
    }

    /**
     * @param $idsOrEntities
     * @param bool $autoFlush
     * @throws \Exception
     */
    public function delete($idsOrEntities, $autoFlush = TRUE)
    {
        if (!is_array($idsOrEntities)) {
            $idsOrEntities = array($idsOrEntities);
        }

        $entities = array();
        $ids = array();
        $idToEntities = array();
        $notDeleteCount = count($idsOrEntities);

        foreach ($idsOrEntities as $idOrEntity) {
            if (is_numeric($idOrEntity)) {
                array_push($ids, $idOrEntity);
            }

            if (is_object($idOrEntity)) {
                array_push($entities, $idOrEntity);
            }
        }

        if (count($ids) > 0) {
            $idToEntities = $this->buildQuery('entity')
                ->andWhere('entity.id IN (:ids)')
                ->setParameter('ids', $ids)
                ->getQuery()
                ->getResult();
        }

        $em = $this->getEntityManager();

        foreach (array_merge($entities, $idToEntities) as $entity) {
            $em->remove($entity);
            $notDeleteCount--;
        }

        if ($notDeleteCount === 0) {
            if ($autoFlush) {
                $em->flush();
            }
        } else {
            throw new \Exception();
        }
    }

    /**
     * @param $tableName
     * @param array $conditions
     * @return int|mixed
     */
    public function deleteBy($tableName, $conditions = array())
    {
        $where = array();
        $numDeleted = 0;

        foreach ($conditions as $key => $conditionItem) {
            $where[] = 'tbl.' . $key . ' = ' . $conditionItem;
        }

        if ($where) {
            $em = $this->getEntityManager();
            $querySql = sprintf('DELETE FROM '. self::ENTITY_BUNDLE .':%s tbl WHERE %s', $tableName, implode('AND ', $where));

            $q  = $em->createQuery($querySql);
            $numDeleted = $q->execute();
        }

        return $numDeleted;
    }


    /**
     * @param $args
     * @return QueryBuilder
     */
    public function querySimpleEntities($args)
    {
        if (isset($args['alias'])) {
            $alias = $args['alias'];
        } else {
            $alias = 'entity';
        }
        $this->query = $this->buildQuery($alias);

        if (isset($args['selects'])) {
            $selects = $args['selects'];

            foreach ($selects as $index => $select) {
                if ($index == 0) {
                    $this->query->select($this->dbFieldName($select));
                } else {
                    $this->query->addSelect($this->dbFieldName($select));
                }
            }
        } else if (isset($args['update'])) {
            foreach($args['update'] as $field => $value) {
                $entityName = $this->getEntityName();
                $updateObj = new $entityName;

                if (method_exists($updateObj, 'set' . ucfirst($field))) {
                    $fieldName =  'val_' . $field;
                    $this->query->set($this->dbFieldName($field), ':' .$fieldName)
                        ->setParameter($fieldName, $value);
                }
            }
        }

        if (isset($args['conditions'])) {
            $conditions = $args['conditions'];

            foreach ($conditions as $field => $values) {
                if (!is_array($values)) {
                    $this->addSearchString($conditions, $field);
                } else {
                    if (ctype_digit(implode(array_keys($values)))) {
                        $this->query->andWhere(sprintf('%s IN (:%s)', $this->dbFieldName($field), $field))
                            ->setParameter($field, $values);
                    } else {
                        $operatorIndex = 0;
                        foreach ($values as $operator => $value) {
                            if (in_array($operator, array('>=', '<=', '>', '<', '<>', 'IN', 'NOT IN', '!='))) {
                                $operatorIndex++;
                                $fieldName = $field . '_' . $operatorIndex;
                                $this->query->andWhere(sprintf( $operator == 'IN' || $operator == 'NOT IN' ? '%s %s (:%s)' : '%s %s :%s',
                                        $this->dbFieldName($field), $operator, $fieldName))
                                    ->setParameter($fieldName, $value);
                            }
                        }
                    }
                }
            }
        }

        if (isset($args['orderBy'])) {
            $orderBy = $args['orderBy'];

            foreach ($orderBy as $field => $order) {
                if (is_int($field)) {
                    $this->query->addOrderBy($this->dbFieldName($order), 'ASC');
                } else {
                    $this->query->addOrderBy($this->dbFieldName($field), $order);
                }
            }
        }

        if (isset($args['groupBy'])) {
            $groupBy = $args['groupBy'];

            foreach ($groupBy as $field) {
                $this->query->addGroupBy($this->dbFieldName($field));
            }
        }

        if (isset($args['maxResults'])) {
            $this->query->setMaxResults($args['maxResults']);
        }

        if (isset($args['firstResult'])) {
            $this->query->setFirstResult($args['firstResult']);
        }

        return $this->query;
    }
}
