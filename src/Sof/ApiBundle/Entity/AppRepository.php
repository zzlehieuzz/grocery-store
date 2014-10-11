<?php

namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Sof\ApiBundle\Lib\Config;
use Sof\ApiBundle\Lib\StringUtil;
use Sof\ApiBundle\Exception\EntityException;
use Sof\ApiBundle\Exception\WrongArgumentException;
use Sof\ApiBundle\Lib\ValueList;
use Sof\ApiBundle\Lib\TryUtil;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;

abstract class AppRepository extends EntityRepository implements ContainerAwareInterface
{
  protected $container;
  private $alias;

  /**
   * @var QueryBuilder
   */
  private $query;

  public function setContainer(ContainerInterface $container = null)
  {
    $this->container = $container;
  }

  /**
   * Build query with exclude deleted support
   * @param string $alias
   * @param Array $options
   *
   * @author Anhnt 2012/11/18
   */
  public function buildQuery($alias, $options = array()) {

    if (!is_string($alias)) {
      throw new WrongArgumentException('AppRepository:buildQuery');
    }

    $query = $this->createQueryBuilder($alias);
    $this->alias = $alias;

    return $query;
  }

  /**
   * Build search query base on form search
   * @param string $alias
   * @param Form $form
   * @param Array $basic_fields
   *
   * @author Anhnt
   * @author modified Khiemnd 2012/12/04
   */
  protected function buildSearchQuery($alias, $form, $basic_fields = array()) {
    $this->query = $this->buildQuery($alias);

    if (isset($basic_fields['string'])) {
      foreach ($basic_fields['string'] as $field) {
        $this->addSearchString($form, $field);
      }
    }

    if (isset($basic_fields['number'])) {
      foreach ($basic_fields['number'] as $field) {
        $this->addSearchNumber($form, $field);
      }
    }

    if (isset($basic_fields['tel'])) {
      foreach ($basic_fields['tel'] as $field => $extraFields) {
        $this->addSearchTel($form, $field, $extraFields);
      }
    }

    if (isset($basic_fields['fromto'])) {
      foreach ($basic_fields['fromto'] as $field) {
        $this->addSearchFromTo($form, $field);
      }
    }

    if (isset($basic_fields['foreign'])) {
      foreach ($basic_fields['foreign'] as $field) {
        $this->addSearchForeignKey($form, $field);
      }
    }

    if (isset($basic_fields['date'])) {
      foreach ($basic_fields['date'] as $field) {
        $this->addSearchDate($form, $field);
      }
    }

    if (isset($basic_fields['Ym'])) {
      foreach ($basic_fields['Ym'] as $field) {
        $this->addSearchYm($form, $field);
      }
    }

    if (isset($basic_fields['singlecheck'])) {
      foreach ($basic_fields['singlecheck'] as $field) {
        $this->addSearchSingleCheck($form, $field);
      }
    }

    if (isset($basic_fields['multicheck'])) {
      foreach ($basic_fields['multicheck'] as $field) {
        $this->addSearchMulticheck($form, $field);
      }
    }

    if (isset($basic_fields['equal'])) {
      foreach ($basic_fields['equal'] as $field) {
        $this->addSearchEqual($form, $field);
      }
    }

    if (isset($basic_fields['searchSection'])) {
      $sectionArr = $this->getDefaultSearchSection();

      foreach ($basic_fields['searchSection'] as $field) {
        $this->addSearchSection($sectionArr, $form, $field);
      }
    }

    return $this->query;
  }

  /**
   * Get All Default Search section of login user
   * @return array
   */
  public function getDefaultSearchSection() {
    $user = $this->container->get('security.context')->getToken()->getUser();
    $userStaff = TryUtil::callMethod($user, 'getStaff:getId');

    $query = $this->getEntityManager()
      ->createQuery('SELECT s.id FROM PS2IjnetBundle:SearchSection ss
                                               LEFT JOIN ss.section s WHERE ss.staff = :staff')
      ->setParameter('staff', $userStaff);

    $sectionArr = array();
    foreach($query->getArrayResult() as $section) {
      $sectionArr[] = $section['id'];
    }

    return $sectionArr;
  }

  /**
   * Delete single or multi row
   * @param mixed $idsOrEntities
   *
   * @author Khiemnd 2012/12/04
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
      throw new EntityException(Config::getMessage('delete_fail'));
    }

  }

  private function addSearchNumber($form, $field)
  {
    if ($form->has($field) && $form[$field]->getData() !== NULL && $form[$field]->getData() !== '') {
      $formData = $form[$field]->getData();

      if (is_numeric($formData)) {
        $this->query->andWhere(sprintf('%s = %s', $this->dbFieldName($field), $formData));
      } else {
        $this->query->andWhere(sprintf('%s = :%s', $this->dbFieldName($field), $field))
                    ->setParameter($field, $formData);
      }
    }
  }

  private function addSearchEqual($form, $field)
  {
    if ($form->has($field) && $form[$field]->getData()) {
      $this->query->andWhere(sprintf('%s = :%s', $this->dbFieldName($field), $field))
                  ->setParameter($field, $form[$field]->getData());
    }
  }

  /**
   * Add search query with tel
   * @param Form $form
   * @param string $field
   * @param string|array $extraFields
   *
   * @author Khiemnd 2013/01/31
   */
  private function addSearchTel($form, $field, $extraFields)
  {
    if (is_numeric($field) && is_string($extraFields)) {
      $field = $extraFields;
      $extraFields = array();
    }

    if ($form->has($field) && $form[$field]->getData()) {
      $fieldData = trim($form[$field]->getData());
      $fieldData = str_replace('-', '', $fieldData) . '%';

      if (StringUtil::startsWith($fieldData, '*')) { // partial match
        $fieldData = preg_replace('/^\*/', '%', $fieldData); // replace leading * with %
      }

      $searchStr = array();

      if (is_array($extraFields)) {
        array_push($extraFields, $field);

        foreach ($extraFields as $extraField) {
          $dbFieldName = sprintf("REPLACE(%s, '-', '')", $this->dbFieldName($extraField)); // ex: REPLACE(tel1, '-', '')
          array_push($searchStr, sprintf('%s LIKE :%s', $dbFieldName, $extraField));
          $this->query->setParameter($extraField, $fieldData);
        }

        if (count($searchStr) > 0) {
          $this->query->andWhere(implode(' OR ', $searchStr));
        }
      }
    }

  }

  /**
   * Build search query with value as string
   * @param Form $form
   * @param string $field
   *
   * @author Anhnt 2013/01/18
   */
  private function addSearchString($form, $field)
  {
    if ($form->has($field) && $form[$field]->getData()) {
      $fieldData = trim($form[$field]->getData());
      $fieldData = $fieldData . '%'; // left-hand match
      $dbFieldName = $this->dbFieldName($field);

      if (StringUtil::startsWith($fieldData, '*')) { // partial match
        $fieldData = preg_replace('/^\*/', '%', $fieldData); // replace leading * with %
      }

      $this->query->andWhere(sprintf('%s LIKE :%s', $dbFieldName, $field))->setParameter($field, $fieldData);
    }
  }

  /**
   * Add search query with from, to date
   * @param Form $form
   * @param string $field
   *
   * @author Khiemnd 2012/12/04
   */
  private function addSearchFromTo($form, $field)
  {
    if ($form->has($field) && isset($form[$field]['from']) && isset($form[$field]['to'])) {

      $formData['from'] = $form[$field]['from']->getData();
      $formData['to'] = $form[$field]['to']->getData();

      if ($formData['from']) {
        $dataParam = $formData['from'];

        if (method_exists($dataParam, 'format')) {
          $dataParam = $dataParam->format(Config::get('date_format'));
        }

        if (is_numeric($dataParam)) {
          $this->query->andWhere(sprintf('%s >= %d', $this->dbFieldName($field), $dataParam));
        } elseif (preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $dataParam)) {
          // is date
          $this->query->andWhere(sprintf('DATE(%s) >= :%s', $this->dbFieldName($field), $field.'From'))
                      ->setParameter($field.'From', $dataParam);
        } else {
          // is string
          $this->query->andWhere(sprintf('%s >= :%s', $this->dbFieldName($field), $field.'From'))
                      ->setParameter($field.'From', $dataParam);
        }
      }

      if ($formData['to']) {
        $dataParam = $formData['to'];

        if (method_exists($dataParam, 'format')) {
          $dataParam = $dataParam->format(Config::get('date_format'));
        }

        if (is_numeric($dataParam)) {
          $this->query->andWhere(sprintf('%s <= %d', $this->dbFieldName($field), $dataParam));
        } elseif (preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $dataParam)) {
          // is date
          $this->query->andWhere(sprintf('DATE(%s) <= :%s', $this->dbFieldName($field), $field.'To'))
                      ->setParameter($field.'To', $dataParam);
        } else {
          // is string
          $this->query->andWhere(sprintf('%s <= :%s', $this->dbFieldName($field), $field.'To'))
                      ->setParameter($field.'To', $dataParam);
        }
      }
    }
  }

  /**
   * Add search query with foreign key value
   * @param Form $form
   * @param string $field
   *
   * @author Khiemnd 2012/12/04
   */
  private function addSearchForeignKey($form, $field)
  {
    if ($form->has($field) && $form[$field]->getData()) {
      $formData = $form[$field]->getData();

      if (is_object($formData)) {
        $formData = TryUtil::callMethod($formData, 'getId');
      } else {
        $field = preg_replace('/(Id)$/', '', $field); // ex: staffId => staff
      }

      if (is_numeric($formData)) {
        $this->query->andWhere(sprintf('%s = %s', $this->dbFieldName($field), $formData));
      } else {
        $this->query->andWhere(sprintf('%s = :%s', $this->dbFieldName($field), $field))
                    ->setParameter($field, $formData);
      }
    }
  }

  /**
   * Convert form field name to db field name
   * @param string $field
   * @return string $dbFieldName
   *
   * @author Anhnt 2013/01/11
   */
  private function dbFieldName($field)
  {
    $field = preg_replace('/_00\d$/', '', $field); // ex: deviceModel_001 => deviceModel

    if (StringUtil::contains($field, '_')) {
      $fieldName = str_replace('_', '.', $field); // ex: supplierStaff_kana => supplierStaff.kana
    } else {
      $fieldName = $this->alias . '.' . $field;
    }

    return $fieldName;
  }

  /**
   * Add search query with date string
   * @param Form $form
   * @param string $field
   *
   * @author Khiemnd 2013/01/22
   */
  private function addSearchDate($form, $field)
  {
    if ($form->has($field) && $form[$field]->getViewData()) {
      $this->query->andWhere(sprintf('DATE(%s) = :%s', $this->dbFieldName($field), $field))
                  ->setParameter($field, $form[$field]->getViewData());
    }
  }

  /**
   * Add search query with Year Month string
   * @param Form $form
   * @param string $field
   *
   * @author Khiemnd 2013/01/22
   */
  private function addSearchYm($form, $field)
  {
    if ($form->has($field) && $form[$field]->getViewData()) {
      $this->query->andWhere(sprintf('DATE_FORMAT(%s, \'%s\') = :%s', $this->dbFieldName($field), '%Y/%m', $field))
                  ->setParameter($field, $form[$field]->getViewData());
    }
  }

  /**
   * Add search query with single checkbox
   * @param Form $form
   * @param string $field
   *
   * @author Khiemnd 2013/02/25
   */
  private function addSearchSingleCheck($form, $field)
  {
    if ($form->has($field) && $form[$field]->getData()) {
      $this->query->andWhere(sprintf('%s = %d', $this->dbFieldName($field), ValueList::constToValue('common.is_checked.YES')));
    }
  }

  /**
   * Add search query with multicheck
   * @param Form $form
   * @param string $field
   *
   * @author Khiemnd 2013/02/05
   */
  private function addSearchMulticheck($form, $field)
  {
    if ($form->has($field) && $form[$field]->getData() != NULL && $form[$field]->getData() != '') {
      $searchData = explode(',', $form[$field]->getData());
      $dbFieldName = $this->dbFieldName($field);
      $query = array();

      foreach ($searchData as $data) {
        $data = trim($data);
        array_push($query, sprintf('FIND_IN_SET(:%s, %s) <> 0', $field . $data, $dbFieldName));
        $this->query->setParameter($field . $data, $data);
      }

      $this->query->andWhere('(' . implode(' OR ', $query) . ')');
    }
  }

  /**
   * Add search query with SearchSection
   * @param $searchSectionIds
   * @param Form $form
   * @param string $field
   *
   * @author Datdvq 2013/03/20
   */
  private function addSearchSection($searchSectionIds, $form, $field)
  {
    if ($form->has($field)) {
      if ($form->get($field)->getData()) {
        $this->addSearchForeignKey($form, $field);

      } elseif ($searchSectionIds) {
        $field = preg_replace('/(Id)$/', '', $field); // ex: staffId => staff
        $this->query->andWhere(sprintf('%s IS NULL OR %s IN (:%s)',
                              $this->dbFieldName($field), $this->dbFieldName($field), $field))
                    ->setParameter($field, $searchSectionIds);
      }
    }
  }

  /**
   * generate Branch Code
   * @throws \Sof\ApiBundle\Exception\WrongArgumentException
   * @param string $entityName
   * @param string $parentId
   * @param string $parentCode
   * @return string $branchCode
   *
   * @author Datdvq 2012/12/05
   */
  function createBranchCode($entityName, $parentId = null, $parentCode = null)
  {
    if (!is_string($entityName)) {
      throw new WrongArgumentException('createBranchCode');
    }
    $char  = FALSE;
    $start = FALSE;
    switch($entityName) {
      case 'Contact':
        $parentEntityName   = 'customer';
        $branchName         = 'branchCode';
        $codeFormat         = '%s%04s';
        $splitLength        = 0;
        $limitCode          = 9999;
        break;
      case 'Billing':
        $parentEntityName   = 'customer';
        $branchName         = 'branchCode';
        $codeFormat         = '%s%04s';
        $splitLength        = 0;
        $limitCode          = 9999;
        break;
      case 'LocationContact':
        $parentEntityName   = 'locationArea';
        $branchName         = 'branchCode';
        $codeFormat         = '%s%03s';
        $splitLength        = 0;
        $limitCode          = 999;
        break;
      case 'LocationBilling':
        $parentEntityName   = 'locationArea';
        $branchName         = 'branchCode';
        $codeFormat         = '%s%03s';
        $splitLength        = 0;
        $limitCode          = 999;
        break;
      case 'SupplierStaff':
        $parentEntityName   = 'supplier';
        $branchName         = 'supplierStaffCode';
        $codeFormat         = '%06s%03s';
        $splitLength        = 6;
        $limitCode          = 999;
        break;
      case 'Maintenance':
        $parentEntityName   = 'locationArea';
        $branchName         = 'maintenanceCode';
        $codeFormat         = '%06s%02s';
        $splitLength        = 6;
        $limitCode          = 99;
        break;
      case 'MaintenanceSupplier':
        $parentEntityName   = 'locationArea';
        $branchName         = 'maintenanceSupplierCode';
        $codeFormat         = '%06s%02s';
        $splitLength        = 6;
        $limitCode          = 99;
        $query              = sprintf('SELECT MAX(d.%s) FROM PS2IjnetBundle:%s d JOIN d.maintenance m WHERE m.%s = :parentId',
                                      $branchName, $entityName, $parentEntityName);
        break;
      case 'Device':
        $parentEntityName   = 'locationArea';
        $branchName         = 'deviceCode';
        $codeFormat         = '%06s%04s';
        $splitLength        = 6;
        $limitCode          = 9999;
        break;
      case 'Goods':
        $parentEntityName   = 'request';
        $branchName         = 'goodsCode';
        $codeFormat         = '%010s%02s';
        $splitLength        = 10;
        $limitCode          = 99;
        break;
      case 'GoodsDetail':
        $parentEntityName   = 'goods';
        $branchName         = 'branchCode';
        $codeFormat         = '%s%02s';
        $splitLength        = 0;
        $limitCode          = 99;
        break;
      case 'Report':
        $parentEntityName   = 'ttsOrder';
        $branchName         = 'reportCode';
        $codeFormat         = '%07s%03s';
        $splitLength        = 7;
        $limitCode          = 999;
        break;
      case 'Request':
        $parentEntityName   = 'ttsOrder';
        $branchName         = 'requestCode';
        $codeFormat         = '%07s%03s';
        $splitLength        = 7;
        $limitCode          = 999;
        break;
      case 'Cost':
        $parentEntityName   = 'ttsOrder';
        $branchName         = 'costCode';
        $codeFormat         = '%07s%03s';
        $splitLength        = 7;
        $limitCode          = 999;
        break;
      case 'CostDetail':
        $parentEntityName   = 'cost';
        $branchName         = 'costDetailCode';
        $codeFormat         = '%s%03s';
        $splitLength        = 0;
        $limitCode          = 999;
        break;
      case 'Quotation':
        $parentEntityName   = 'ttsOrder';
        $branchName         = 'quotationCode';
        $codeFormat         = '%07s%02s';
        $splitLength        = 7;
        $limitCode          = 99;
        break;
      case 'Quotation.supplierQuotationCode':
        $char               = 'P';
        $quotation          = explode('.', $entityName);
        $entityName         = $quotation[0];
        $parentEntityName   = 'request';
        $branchName         = $quotation[1];
        $codeFormat         = '%010s%01s';
        $splitLength        = 11;
        $limitCode          = 9;
        break;
      case 'QuotationDetail':
        $parentEntityName   = 'quotation';
        $branchName         = 'quotationDetailCode';
        $codeFormat         = '%s%03s';
        $splitLength        = 0;
        $limitCode          = 999;
        break;
      case 'QuotationNote':
        $parentEntityName   = 'quotation';
        $branchName         = 'quotationNoteCode';
        $codeFormat         = '%s%03s';
        $splitLength        = 0;
        $limitCode          = 999;
        break;
      case 'Sales':
        $parentEntityName   = 'ttsOrder';
        $branchName         = 'salesCode';
        $codeFormat         = '%07s%02s';
        $splitLength        = 7;
        $limitCode          = 99;
        break;
      case 'SalesDetail':
        $parentEntityName   = 'sales';
        $branchName         = 'salesDetailCode';
        $codeFormat         = '%s%03s';
        $splitLength        = 0;
        $limitCode          = 999;
        break;
      case 'CmClaim':
        $parentEntityName   = 'ttsOrder';
        $branchName         = 'cmClaimCode';
        $codeFormat         = '%07s%02s';
        $splitLength        = 7;
        $limitCode          = 99;
        break;
      case 'Stock.purchaseCode':
        $start              = 1;
        $stock              = explode('.', $entityName);
        $entityName         = $stock[0];
        $parentEntityName   = 'ttsOrder';
        $branchName         = $stock[1];
        $codeFormat         = '%s%07s';
        $splitLength        = 0;
        $limitCode          = 9999999;
        break;
      case 'Stock.deliveryCode':
        $stock              = explode('.', $entityName);
        $entityName         = $stock[0];
        $parentEntityName   = 'ttsOrder';
        $branchName         = $stock[1];
        $codeFormat         = '%07s%03s';
        $splitLength        = 7;
        $limitCode          = 999;
        break;
      case 'Stock.purchaseDetailCode':
        $stock              = explode('.', $entityName);
        $entityName         = $stock[0];
        $parentEntityName   = 'ttsOrder';
        $branchName         = $stock[1];
        $codeFormat         = '%s%03s';
        $splitLength        = 0;
        $limitCode          = 999;
        break;
      case 'CmDailyReport':
        $parentEntityName   = 'ttsOrder';
        $branchName         = 'branchCode';
        $codeFormat         = '%s%03s';
        $splitLength        = 0;
        $limitCode          = 999;
        break;
      case 'CmPickup':
        $parentEntityName   = 'ttsOrder';
        $branchName         = 'cmQuotationProgressDetailCode';
        $codeFormat         = '%s%02s';
        $splitLength        = 0;
        $limitCode          = 99;
        break;
      case 'CmPurchaseOrder':
        $parentEntityName   = 'ttsOrder';
        $branchName         = 'cmPurchaseOrderCode';
        $codeFormat         = '%07s%02s';
        $splitLength        = 7;
        $limitCode          = 99;
        break;
      case 'MultiInvoiceDetail':
        $parentEntityName   = 'multiInvoice';
        $branchName         = 'multiInvoiceDetailCode';
        $codeFormat         = '%s%03s';
        $splitLength        = 0;
        $limitCode          = 999;
        break;
      case 'CmQuotationProgress':
        $parentEntityName   = 'cmQuotationRequest';
        $branchName         = 'detailCode';
        $codeFormat         = '%s%02s';
        $splitLength        = 0;
        $limitCode          = 99;
        break;
      default:
        throw new WrongArgumentException(sprintf('Entity %s have\'nt branch code.', $entityName));
    }

    $em = $this->getEntityManager();
    $em->getFilters()->disable('softdeleteable');


    if (isset($query)) {
      $result = $em->createQuery($query);
      if ($parentId) {
        $result = $result->setParameter('parentId', $parentId);
      }
    } else if ($parentId) {
      $query = sprintf('SELECT MAX(d.%s) FROM PS2IjnetBundle:%s d WHERE d.%s = :parentId',
                       $branchName, $entityName, $parentEntityName);
      $result = $em->createQuery($query)->setParameter('parentId', $parentId);
    } else {
      $query = sprintf('SELECT MAX(d.%s) FROM PS2IjnetBundle:%s d', $branchName, $entityName);
      $result = $em->createQuery($query);
    }

    $maxCode = $result->getSingleScalarResult();

    $em->getFilters()->enable('softdeleteable');

    if ($maxCode) {
      if($splitLength) {
        $maxCode = str_split($maxCode, $splitLength);
      } else {
        $maxCode = array('', $maxCode);
      }

      if (count($maxCode) >= 2) {
        if ($maxCode[1] >= $limitCode) {
          throw new WrongArgumentException(sprintf('Entity %s with %s = %s exceed branch code.', $entityName, $parentEntityName, $parentId));
        }

        $maxCode[1] = (int)$maxCode[1] + 1;
        $branchCode = sprintf($codeFormat, $maxCode[0], $maxCode[1]);
      } else {
        throw new WrongArgumentException(sprintf('Entity %s exist wrongs branch code.', $entityName, $parentEntityName, $parentId));
      }
    } else {
      if ($splitLength == 0) {
        $parentId = '';
      } elseif ($parentCode) {
        $parentId = $parentCode;
      }

      if ($char) {
        $parentId = sprintf('%s%s', $char, $parentId);
      }
      $branchCode = sprintf($codeFormat, $parentId, 1);

      if ($start) {
        $branchCode = sprintf('%s%s', str_pad($start, (strlen($branchCode) - 1), 0, STR_PAD_RIGHT), 1);
      }
    }

    return $branchCode;
  }

  /**
   * @author DatDvq 2013/01/31
   * TODO modify to use findFirst
   */
  public function getOneEntityData($conditions)
  {
    $query = $this->buildQuery('en')->select('en');

    foreach ($conditions as $condition => $value) {
      if ($condition === 'id') {
        $query->andWhere('en.id = :id')->setParameter($condition, $value);
      }

      if (substr($condition, 0, 7) == 'extra__') {
        $field = str_replace('extra__', '' , $condition);
        $query->andWhere('en.'.$field.' = :'.$field.'')->setParameter($field, $value);
      }
    }

    return $query->addOrderBy('en.id', 'ASC')->getQuery()
                 ->getOneOrNullResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
  }

  /**
   * Check flag on or off
   * @param $fieldFlag, $baseFieldArray
   * @return mixed
   *
   * @author Khiemnd 2013/02/14
   */
  public function hasFlagOn($fieldFlag, $baseFieldArray = array(), $excludeId = NULL)
  {
    $query = $this->buildQuery('entity')
                  ->andWhere(sprintf('entity.%s = :flag_yes', $fieldFlag))
                  ->setParameter('flag_yes', ValueList::constToValue('common.is_checked.YES'));

    if ($excludeId) {
      $query->andWhere('entity.id != :id')->setParameter('id', $excludeId);
    }

    foreach ($baseFieldArray as $baseField => $fieldValue) {
      $query->andWhere(sprintf('entity.%s = :%s', $baseField, $baseField))
            ->setParameter($baseField, $fieldValue);
    }

    return $query->getQuery()->getOneOrNullResult();
  }

  /**
   * Get first result
   * @author AnhNT 2013/02/14
   */
  public function findFirst($conditions = array())
  {
    $query = $this->buildQuery('en');
    $query->setMaxResults(1);
    return $query->getQuery()->getOneOrNullResult();
  }

  /**
   * addSelectDeviceDetail
   * @param $query
   *
   * @author Khiemnd 2013/02/21
   */
  public function addSelectDeviceDetail($query, $alias = 'device')
  {
    for ($detail = 1;$detail <= 11;$detail++) {
      $query->addSelect('deviceDetail' . $detail)
            ->leftJoin($alias . '.deviceDetail' . $detail, 'deviceDetail' . $detail);
    }
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
      $query = sprintf('SELECT MAX(d.%s) FROM PS2IjnetBundle:%s d WHERE d.%s = :parentId', $fieldMax, $entityName, $parentField);
      $result = $em->createQuery($query)->setParameter('parentId', $parentId);
    } else {
      $query = sprintf('SELECT MAX(d.%s) FROM PS2IjnetBundle:%s d', $fieldMax, $entityName);
      $result = $em->createQuery($query);
    }
    $maxCode = $result->getSingleScalarResult();

    if (!$maxCode) {
      $maxCode = 0;
    }

    return $maxCode;
  }

  /**
   * addCommonFieldExport
   * @param $query
   *
   * @author Khiemnd 2013/04/11
   */
  public function addCommonFieldExport($query, $alias)
  {
    return $query->addSelect('createdSection, createdStaff, createdBy, updatedSection, updatedStaff, updatedBy')
                 ->leftJoin($alias . '.createdSection', 'createdSection')
                 ->leftJoin($alias . '.createdStaff', 'createdStaff')
                 ->leftJoin($alias . '.createdBy', 'createdBy')
                 ->leftJoin('createdStaff.user', 'createdStaffUser')
                 ->leftJoin($alias . '.updatedSection', 'updatedSection')
                 ->leftJoin($alias . '.updatedStaff', 'updatedStaff')
                 ->leftJoin($alias . '.updatedBy', 'updatedBy')
                 ->leftJoin('updatedStaff.user', 'updatedStaffUser');
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
   * @param $schedule $alias
   * @return Query
   *
   * @author DatDvq 2013/06/17
   */
  public function queryGroupSchedulePattern2($schedule) {
    $em = $this->getEntityManager();

    $qBuilder = $em->createQueryBuilder()->from('PS2IjnetBundle:Schedule', $schedule)->select("$schedule");

    $request     = $schedule. '_request';
    $repSchedule = $schedule. '_repSchedule';
    $repRequest  = $schedule. '_repRequest';

    $qBuilder->innerJoin('PS2IjnetBundle:Schedule', $repSchedule, 'WITH', "$repSchedule.scheduleFlag = $schedule.scheduleFlag")
      ->innerJoin("$repSchedule.request", $repRequest)
      ->innerJoin("$schedule.request",    $request)
      ->where("$repRequest.repFlag       = ".ValueList::constToValue('common.is_checked.YES'))
      ->andWhere("$request.repFlag IS NULL OR $request.repFlag <> ".ValueList::constToValue('common.is_checked.YES'))
      ->andWhere("$request.orderFlag     = ".ValueList::constToValue('common.order_flag.MAINTENANCE_SALES'))
      ->andWhere("$schedule.scheduleFlag = ".ValueList::constToValue('S266.schedule_flag.PARTERN_2'))
      ->andWhere("$repRequest.orderFlag   = $request.orderFlag")
      ->andWhere("$repRequest.ttsOrder    = $request.ttsOrder")
      ->andWhere("$repRequest.requestDate = $request.requestDate")
      ->andWhere("$repRequest.dateStatus  = $request.dateStatus")
      ->andWhere("$repSchedule.supplier           = $schedule.supplier")
      ->andWhere("$repSchedule.supplierStaff      = $schedule.supplierStaff")
      ->andWhere("$repSchedule.workingStartTime   = $schedule.workingStartTime")
      ->andWhere("$repSchedule.workingEndTime     = $schedule.workingEndTime")
      ->andWhere("$repSchedule.workingStartDate   = $schedule.workingStartDate");

    return $qBuilder;
  }

  public function checkHasRelationWithAnotherEntity($ids, $entityCheck = null) {
    if (!$ids) {
      return 0;
    }

    if (!is_array($ids)) {
      $ids = array($ids);
    }

    $relationEntities = array();

    $entityName = explode('\\', $this->_entityName);
    $entityName = $entityName[count($entityName) - 1];

    if (!$entityCheck) {
      $entityCheck = $entityName;
    }

    $relations = array();

    switch($entityCheck) {
      case 'Customer':
        $relationEntities = array (
          array('entity' => 'Customer',         'field' => 'salesMerge'),
          array('entity' => 'Customer',         'field' => 'receivable'),
          array('entity' => 'Billing' ,         'field' => 'customer'),
          array('entity' => 'Contact' ,         'field' => 'customer'),
          array('entity' => 'CustomerLocation', 'field' => 'customer'),
          array('entity' => 'Supplier',         'field' => 'nettingId'),
          array('entity' => 'VirtualAccount',   'field' => 'customer'),
          array('entity' => 'Activity',         'field' => 'customer')
        );
        break;
      case 'Billing':
        $relationEntities = array (
          array('entity' => 'LocationBilling',  'field' => 'billing'),
          array('entity' => 'TtsOrder',         'field' => 'billing')
        );
        break;
      case 'Contact':
        $relationEntities = array (
          array('entity' => 'LocationContact',  'field' => 'contact'),
          array('entity' => 'TtsOrder',         'field' => 'contact1'),
          array('entity' => 'TtsOrder',         'field' => 'contact2'),
          array('entity' => 'Activity',         'field' => 'contact')
        );
        break;
      case 'Supplier':
        $relationEntities = array (
          array('entity' => 'Customer',               'field' => 'nonFree'),
          array('entity' => 'MaintenanceSupplier',    'field' => 'supplier'),
          array('entity' => 'SupplierStaff',          'field' => 'supplier'),
          array('entity' => 'Notification',           'field' => 'fromSupplier'),
          array('entity' => 'Request',                'field' => 'supplier'),
          array('entity' => 'Goods',                  'field' => 'supplier'),
          array('entity' => 'Cost',                   'field' => 'supplier'),
          array('entity' => 'Stock',                  'field' => 'supplier'),
          array('entity' => 'Stock',                  'field' => 'vendor'),
          array('entity' => 'CmBudgetDetail',         'field' => 'supplier'),
          array('entity' => 'CmQuotationRequest',     'field' => 'supplier'),
          array('entity' => 'CmConstructionRequest',  'field' => 'constructionSupplier'),
          array('entity' => 'CmConstructionRequest',  'field' => 'locationMeetingSupplier'),
          array('entity' => 'CmPurchaseOrder',        'field' => 'supplier'),
          array('entity' => 'CmPurchaseOrder',        'field' => 'section'),
          array('entity' => 'CmPurchaseOrder',        'field' => 'publishSection'),
          array('entity' => 'PurchaseConversion',     'field' => 'supplier')
        );

        $relations[] = '(SELECT COUNT(notification_send.id) FROM PS2IjnetBundle:Notification notification_send WHERE notification_send.notificationCat = '. ValueList::constToValue('S252.notification_cat2.PARTNER').
                        ' AND FIND_IN_SET(:ids, notification_send.sendTo) <> 0 AS count_sendTo';

        break;
      case 'SupplierStaff':
        $relationEntities = array (
          array('entity' => 'Schedule',               'field' => 'supplierStaff'),
          array('entity' => 'Notification',           'field' => 'fromSupplierStaff'),
          array('entity' => 'Request',                'field' => 'supplierStaff'),
          array('entity' => 'Request',                'field' => 'preSupplierStaff'),
          array('entity' => 'Quotation',              'field' => 'supplierStaff'),
          array('entity' => 'CmQuotationRequest',     'field' => 'supplierStaff'),
          array('entity' => 'CmConstructionRequest',  'field' => 'constructionSupplierStaff'),
          array('entity' => 'CmConstructionRequest',  'field' => 'locationMeetingStaff'),
          array('entity' => 'CmConstructionRequest',  'field' => 'companionStaff'),
          array('entity' => 'CmConstructionRequest',  'field' => 'locationMeetingTrader'),
          array('entity' => 'CmPlan',                 'field' => 'responsiblePerson'),
          array('entity' => 'CmBudgetDetail',         'field' => 'supplierStaff'),
          array('entity' => 'CmPurchaseOrder',        'field' => 'constructionSupplierStaff')
        );
        break;
      case 'LocationBilling':
        $relationEntities = array (
          array('entity' => 'TtsOrder',               'field' => 'locationBilling'),
          array('entity' => 'Sales',                  'field' => 'locationBilling')
        );
        break;
      case 'LocationContact':
        $relationEntities = array (
          array('entity' => 'TtsOrder',               'field' => 'locationContact1'),
          array('entity' => 'TtsOrder',               'field' => 'locationContact2')
        );
        break;
      case 'Item':
        $relationEntities = array (
          array('entity' => 'Device',                 'field' => 'item'),
          array('entity' => 'Device',                 'field' => 'item2'),
          array('entity' => 'Device',                 'field' => 'item3'),
          array('entity' => 'Device',                 'field' => 'item4'),
          array('entity' => 'Device',                 'field' => 'item5'),
          array('entity' => 'GoodsDetail',            'field' => 'item'),
          array('entity' => 'UnitChange',             'field' => 'item'),
          array('entity' => 'ProposalParts',          'field' => 'item'),
          array('entity' => 'Quotation',              'field' => 'item'),
          array('entity' => 'QuotationDetail',        'field' => 'item'),
          array('entity' => 'ItemRelation',           'field' => 'productItem'),
          array('entity' => 'ItemRelation',           'field' => 'partsItem'),
          array('entity' => 'ReportItemRelation',     'field' => 'item'),
          array('entity' => 'Report',                 'field' => 'item'),
          array('entity' => 'Report',                 'field' => 'item2'),
          array('entity' => 'Report',                 'field' => 'item3'),
          array('entity' => 'Report',                 'field' => 'item4'),
          array('entity' => 'Report',                 'field' => 'item5'),
          array('entity' => 'Request',                'field' => 'item')
        );
        break;
      case 'Material':
        $relationEntities = array (
          array('entity' => 'GoodsDetail',            'field' => 'material'),
          array('entity' => 'QuotationDetail',        'field' => 'material'),
          array('entity' => 'UnitChangeDetail',       'field' => 'spec'),
          array('entity' => 'CmPipeExtraction',       'field' => 'material'),
          array('entity' => 'CmPipingSite',           'field' => 'material'),
          array('entity' => 'CmValve',                'field' => 'material'),
          array('entity' => 'CmArticleSpecified',     'field' => 'material'),
          array('entity' => 'CmElectricWork',         'field' => 'material'),
          array('entity' => 'CmRestorationWork',      'field' => 'material'),
          array('entity' => 'CmTemporaryWithdrawal',  'field' => 'material'),
          array('entity' => 'CmReportRemoval',        'field' => 'material')
        );
        break;
      case 'Repair':
        $relationEntities = array (
          array('entity' => 'Maintenance',            'field' => 'contractCategory'),
          array('entity' => 'Request',                'field' => 'repair'),
          array('entity' => 'CmQuotationProgress',    'field' => 'repair'),
          array('entity' => 'TargetCoreItem',         'field' => 'repair'),
          array('entity' => 'ForecastCoreItem',       'field' => 'repair'),
          array('entity' => 'Sales',                  'field' => 'repair'),
          array('entity' => 'Quotation',              'field' => 'repairNo'),
          array('entity' => 'TtsOrder',               'field' => 'repair')
        );
        break;
      case 'Maker':
        $relationEntities = array (
          array('entity' => 'Device',             'field' => 'deviceMaker'),
          array('entity' => 'Device',             'field' => 'device2Maker'),
          array('entity' => 'Device',             'field' => 'device3Maker'),
          array('entity' => 'Device',             'field' => 'device4Maker'),
          array('entity' => 'Device',             'field' => 'device5Maker'),
          array('entity' => 'Item',               'field' => 'maker'),
          array('entity' => 'ReportDetail1',      'field' => 'maker'),
          array('entity' => 'ReportDetail1',      'field' => 'waterTankMaker'),
          array('entity' => 'ReportDetail1',      'field' => 'elevatedTankMaker'),
          array('entity' => 'ReportDetail2',      'field' => 'maker1'),
          array('entity' => 'ReportDetail2',      'field' => 'maker2'),
          array('entity' => 'ReportDetail2',      'field' => 'maker3'),
          array('entity' => 'ReportDetail2',      'field' => 'maker4'),
          array('entity' => 'ReportDetail2',      'field' => 'maker5'),
          array('entity' => 'ReportDetail2',      'field' => 'waterTankMaker'),
          array('entity' => 'ReportDetail2',      'field' => 'elevatedTankMaker'),
          array('entity' => 'ReportDetail3',      'field' => 'maker'),
          array('entity' => 'ReportDetail4',      'field' => 'maker'),
          array('entity' => 'ReportDetail6',      'field' => 'maker'),
          array('entity' => 'ReportDetail7',      'field' => 'maker'),
          array('entity' => 'ReportDetail7',      'field' => 'waterTankMaker'),
          array('entity' => 'ReportDetail8',      'field' => 'waterTankMaker'),
          array('entity' => 'ReportDetail9',      'field' => 'maker'),
          array('entity' => 'ReportDetail10',     'field' => 'maker'),
          array('entity' => 'Item',               'field' => 'maker'),
          array('entity' => 'Request',            'field' => 'maker')
        );
        break;
    }

    foreach($relationEntities as $key => $relation) {
      $entityAlias = lcfirst($relation['entity']) .'_' . $key;
      $relations[] = sprintf('(SELECT COUNT(%s.id) FROM PS2IjnetBundle:%s %s WHERE %s.%s IN (:ids)) AS count_%s', $entityAlias, $relation['entity'], $entityAlias, $entityAlias, $relation['field'], $entityAlias);
    }

    $em = $this->getEntityManager();
    $querySql = sprintf('SELECT %s FROM PS2IjnetBundle:%s owner', implode(', ', $relations) , $entityName);
    $result = $em->createQuery($querySql)->setMaxResults(1)->setParameter('ids', $ids)->getSingleResult();

    $count = 0;
    foreach($result as $key => $value) {
      $count += $value;
    }

    return $count;
  }

  /**
   * Delete by
   * @author hieunld 2013/10/31
   */
  public function deleteBy($tableName, $conditions = array())
  {
    $where = array();

    foreach ($conditions as $key => $conditionItem) {
      $where[] = 'tbl.' . $key . ' = ' . $conditionItem;
    }

    if ($where) {
      $em = $this->getEntityManager();
      $querySql = sprintf('DELETE FROM PS2IjnetBundle:%s tbl WHERE %s', $tableName, implode('AND ', $where));

      $q  = $em->createQuery($querySql);
      $numDeleted = $q->execute();
    }
  }
}
