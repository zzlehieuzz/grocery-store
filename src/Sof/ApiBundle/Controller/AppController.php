<?php
/*
@author: anhnt
@lastModified: 2012/11/20 10:15
*/
namespace Sof\ApiBundle\Controller;

use Sof\ApiBundle\Exception\InvalidRequestException;
use Sof\ApiBundle\Lib\AccountingUtil;
use Sof\ApiBundle\Lib\ExcelUtil;
use Sof\ApiBundle\Lib\FileUtil;
use Sof\ApiBundle\Entity\MailHistoryDetail;
use Sof\ApiBundle\Entity\MailHistory;
use Sof\ApiBundle\Lib\StringUtil;
use Sof\ApiBundle\Service\EntityService;
use Symfony\Component\HttpFoundation\Response;
use Sof\ApiBundle\Lib\ValueList;
use Sof\ApiBundle\Lib\TryUtil;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sof\ApiBundle\Lib\Config;
use Sof\ApiBundle\Lib\DateUtil;
use Sof\ApiBundle\Exception\ImportException;
use Sof\ApiBundle\Exception\ExportException;
use Symfony\Component\Validator\Constraints\DateTime;
use Sof\ApiBundle\Entity\Announcement;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_Drawing;
use Sof\ApiBundle\Entity\BillingHistory;
use Sof\ApiBundle\Entity\AppEntity;
use Symfony\Component\HttpFoundation\RedirectResponse;


abstract class AppController extends Controller implements FilterControllerInterface
{

  public function preAction()
  {
    if (is_file(Config::uploadPath() . Config::get('batch.prevent_access'))) {
      throw new InvalidRequestException(Config::getMessage('common.ERR.not_access_for_running_batch'));
    }
  }

  public function postAction()
  {
  }

  private $balanceDate;

  /**
   * Get Entity
   * @param String $table
   * @param Mixed $args
   * @return Entity
   *
   * @author Anhnt
   */
  protected function getEntity($table, $args)
  {
    if (strpos($table, ':') === false) { // not found :
      $finder = sprintf('%s:find', $table); // $callback = 'Contact:find'
    } else {
      $finder = $table;
    }

    $entity = $this->get('entity_service')->process($finder, $args);

    if (!$entity) {
      throw $this->createNotFoundException(Config::getMessage('not_found'));
    }

    if (is_array($entity)) {

      throw $this->createNotFoundException(Config::getMessage('not_a_single_entity'));

    }

    return $entity;
  }

  /**
   * Get entity with footer info
   * @param String $table
   * @param Integet $id
   *
   * @author Khiemnd
   */
  protected function getEntityAndInfo($table, $id)
  {
    return $this->getEntity($table, $id);
  }

  /**
   * Deleted multi or single item
   * @param String $table
   * @param Mixed $singleOrArray
   *
   * @author Khiemnd
   */
  protected function delete($table, $singleOrArray)
  {
    $this->get('entity_service')->delete($table, $singleOrArray);
  }

  /**
   * Save and auto redirect
   * @param Entity $entity
   * @param String $routeName
   * @param Array $urlParams
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *
   * @author Anhnt
   * @author modified Khiemnd 2012/12/04
   */
  protected function saveAndRedirect($entity, $routeName, $urlParams = array())
  {
    $this->get('entity_service')->save($entity);
    $this->addFlashMessage(Config::getMessage('save_ok'));

    if (array_key_exists('id', $urlParams) && $urlParams['id'] === null) {
      $urlParams['id'] = $entity->getId();
    }

    return $this->redirect($this->generateUrl($routeName, $urlParams));
  }

  /**
   * Deleted and auto redirect
   * @param String $table
   * @param Mixed $singleIdOrArray
   * @param String $routeName
   * @param Array $urlParams
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *
   * @author Anhnt
   * @author modified Khiemnd 2012/12/04
   */
  protected function deleteAndRedirect($table, $singleIdOrArray, $routeName, $urlParams = array())
  {
    if ($singleIdOrArray) {
      $this->delete($table, $singleIdOrArray);
      $this->addFlashMessage(Config::getMessage('delete_ok'));
    }

    return $this->redirect($this->generateUrl($routeName, $urlParams));
  }

  /**
   * Not be used
   * Will be remove
   */
  private function autoGenerateUrl($routeName, $entity = null)
  {
    if (!$entity) {
      return $this->generateUrl($routeName);
    }

    $params = array();
    $route = $this->get('router')->getRouteCollection()->get($routeName);

    if (preg_match_all('/.*\{([a-z]+)\}.*/', $route->getPattern(), $match)) {
      foreach ($match[1] as $paramName) {
        $method = sprintf('get%s', ucfirst($paramName));
        if (method_exists($entity, $method)) {
          $params[$paramName] = call_user_func(array($entity, $method));
        }
      }
    }

    return $this->generateUrl($routeName, $params);
  }

  /**
   * Get postIds
   * @return array
   *
   * @author Khiemnd
   */
  protected function getPostIds()
  {
    return $this->getRequest()->get('postIds', array());
  }

  /**
   * Get Transfer parameter between main screen with popup
   * @return array
   *
   * @author Datdvq
   */
  protected function getTransferParams()
  {
    return array('params' => $this->getRequest()->get('params', array())
    , 'targets' => $this->getRequest()->get('targets', array())
    , 'func' => $this->getRequest()->get('func', null)
    );
  }


  /**
   * @param array $data
   * @param bool $isError
   * @return JsonResponse
   */
  protected function getJsonResponse(array $data, $isError = FALSE)
  {
    $result = array();
    $result['responseCode'] = $isError ? 204 : 200;
    $result['data'] = array();

    $targets = $this->getRequest()->get('targets', array());

    foreach ($targets as $key) {
      if (array_key_exists($key, $data)) {
        if (isset($data[$key]['options']) && is_array($data[$key]['options'])) {
          $options = array();

          foreach ($data[$key]['options'] as $index => $text) {
            $options['#index#' . $index] = $text;
          }

          $result['data'][$key]['options'] = $options;
        } else {
          $result['data'][$key] = $data[$key];
        }
      }
    }

    if (isset($data['error']) && $data['error']) {
      $result['data']['error'] = $data['error'];
    }

    return new JsonResponse($result);
  }

  public function closePopup(array $transferParams, $isReload = FALSE)
  {
    if ($isReload) {
      $transferParams = array('func' => 'reloadPage()');
    }
    return $this->render('PS2IjnetBundle:Common:popup_close.html.twig', array('transferParams' => $transferParams));
  }

  /**
   * generate Branch Code
   * @param string $entityName
   * @param string $parentId
   * @param string $parentCode
   * @return string $branchCode
   *
   * @author Datdvq 2012/12/05
   */
  protected function createBranchCode($entityName, $parentId = null, $parentCode = null)
  {
    return $this->get('entity_service')->process('Activity:createBranchCode', $entityName, $parentId, $parentCode);
  }

  /**
   * add flash message
   * @param string message
   *
   * @author Khiemnd 2013/03/26
   */
  public function addFlashMessage($message)
  {
    $this->get('session')->getFlashBag()->add('message', $message);
  }

  /**
   * add flash error
   * @param string error
   *
   * @author Khiemnd 2013/03/26
   */
  public function addFlashError($error)
  {
    $this->get('session')->getFlashBag()->add('error', $error);
  }

  /**
   * Find max by field
   *
   * @param string $entityName
   * @param $fieldMax
   * @param null $parentField
   * @param null $parentId
   *
   * @author hieunld 2013/03/29
   */
  protected function findMaxByField($entityName, $fieldMax, $parentField = null, $parentId = null)
  {
    return $this->get('entity_service')->process('Activity:findMaxByField', $entityName, $fieldMax, $parentField, $parentId);
  }

  /**
   * export CSV
   * @param Array $entities
   * @param string $formatYmlFile
   *
   * @author Khiemnd 2013/04/15
   */
  public function exportCSV($entities, $formatYmlFile)
  {
    return FileUtil::csvExportEntities($entities, $formatYmlFile);
  }

  public function formImportCSV()
  {
//    return $this->createForm(new CommonImportCSVType());
  }

  protected $flagUpdate = FALSE;

  /**
   * importCSV
   * @param Object $entityInstanceor
   * @param string $formatYmlFile
   *
   * @author Khiemnd 2013/04/15
   */
  public function importCSV($entityInstance, $formatYmlFile, $callbackBefore = '', $callbackAfter = '')
  {
    $form = $this->formImportCSV()->bind($this->getRequest());
    $file = $form->get('importCSVFile')->getData();

    if ($form->isValid() && $file) {
      $CSVformat = Config::getCSVformat($formatYmlFile);

      $data = $file->openFile('r');
      $keys = array();
      $count = 0;
      $error = FALSE;
      $errorMsg = '';
      $em = $this->getDoctrine()->getManager();
      $em->getConnection()->beginTransaction();

      while (!$data->eof()) {
        $rowArray = $data->fgetcsv();

        if (count($rowArray) >= count($CSVformat)) {
          if ($count == 0) {
            $count++;
            continue; // skip label row
          }

          if ($count == 1) {
            $keys = $rowArray;
            $count++;
            continue; // skip field name row
          }

          $entity = clone $entityInstance;

          // callback before to validation
          if ($callbackBefore) {
            try {
              $callbackBefore($entity, $keys, $rowArray, $file);
            } catch (ImportException $e) {
              $error = TRUE;
              $this->addFlashError($e->getMessage());
            }
          }

          foreach ($rowArray as $col => $cellData) {
            $key = $keys[$col];

            if (isset($CSVformat[$key]) && $key != 'id') {
              $format = $CSVformat[$key];

              if ($cellData) {
                $set = 'set' . ucfirst($format['field']);

                if (!isset($format['type'])) {
                  $setData = $cellData;
                } else {
                  switch ($format['type']) {
                    case 'list':
                      $setData = $cellData;
                      break;

                    case 'code':
                      $setData = trim($cellData, '="');
                      break;

                    case 'entity':
                      $setData = $this->get('entity_service')->process($format['class'] . ':find', $cellData);

                      if (!$setData) {
                        $error = TRUE;
                        $errorMsg = Config::getMessage('common.ERR.import_foreign_key_not_found');
                      }

                      break;

                    case 'date':
                      $setData = date_create($cellData);

                      if (!$setData) {
                        $error = TRUE;
                        $errorMsg = Config::getMessage('common.ERR.import_date_invalid');
                      }

                      break;

                    case 'datetime':
                      $setData = date_create($cellData);

                      if (!$setData) {
                        $error = TRUE;
                        $errorMsg = Config::getMessage('common.ERR.import_date_invalid');
                      }

                      break;

                    default:
                      throw new ImportException(
                        Config::getMessage('common.ERR.import_type_not_support',
                          array(
                            'label' => $format['label'],
                            'type' => $format['type'],
                            'filename' => $formatYmlFile . '.csv.yml'))
                      );
                  }
                }

                if (!$error && method_exists($entity, $set)) {
                  if (is_string($setData)) {
                    $setData = FileUtil::csvDecode($setData);
                  }

                  call_user_func(array($entity, $set), $setData);

                }

              } else if (isset($format['required']) && $format['required']) {
                $error = TRUE;
                $errorMsg = Config::getMessage('common.ERR.import_required_field');
              }
            }

            if ($error) {
              break;
            }
          }

          // callback after to set default
          if ($callbackAfter) {
            try {
              $callbackAfter($entity, $keys, $rowArray);
            } catch (ImportException $e) {
              $error = TRUE;
              $this->addFlashError($e->getMessage());
            }
          }

          if (!$error) {
            try {
              if (!$this->flagUpdate) {
                $this->get('entity_service')->save($entity);
              } else {
                $this->flagUpdate = FALSE;
              }
            } catch (\Exception $e) {
              $error = TRUE;
              $errorMsg = Config::getMessage('common.ERR.import_can_not_insert_db');
            }
          }

          if ($error) {
            break;
          }

          $count++;
        }
      }

      if (!$error && $count == 0) {
        $this->addFlashError(Config::getMessage('common.ERR.import_no_record'));
      } else {
        if (!$error) {
          $em->getConnection()->commit();
          $this->addFlashMessage(Config::getMessage('common.MSG.import_success'));
        } else {
          $em->getConnection()->rollback();
          $em->close();
          $fieldLabel = '';
          if (isset($format['field_label'])) {
            $fieldLabel = $format['field_label'];
          }
          $this->addFlashError(Config::getMessage('common.ERR.import_not_success', array('label' => $fieldLabel, 'line' => $count + 1)) . $errorMsg);
        }
      }

      $em->close();
    }
  }

  public function exportPDF($fileData, $formName, $extraOptions = array())
  {
    $sysret = '';

    if (is_array($fileData)) {
      // convert array to csv file and return absolute filepath
      $fileData = FileUtil::generatePdfExportData($fileData);
    }

    if (file_exists($fileData)) {
      $workDir = Config::uploadPath() . 'export_template_pdf';
      $styleFile = $formName . '.sty';

      $fileCsvData = array($fileData);

      if (isset($extraOptions['addCsvData']) && is_array($extraOptions['addCsvData'])) {
        $fileCsvData = $extraOptions['addCsvData'];
        array_unshift($fileCsvData, $fileData);
      }

      if (isset($extraOptions['isPrint']) && $extraOptions['isPrint']) {
        $outputRelativePath = 'tmp/' . $_POST["OutputFileName"];
        $outputFile = Config::uploadPath() . $outputRelativePath;
        $ccdoption  = $_POST["CcdFileOption"];

        $cmd = 'cprintst -D' . $workDir . ' -s' . $styleFile . ' -o' . $outputFile . ' -c' . $ccdoption . ' ' . implode(' ', $fileCsvData);
      } else {
        $pr1 = '';

        if (isset($extraOptions['pr1']) && $extraOptions['pr1']) {
          $pr1 = ' -pr1';
        }

        $outputRelativePath = 'tmp/form_' . $formName . uniqid() . '.pdf';
        $outputFile = Config::uploadPath() . $outputRelativePath;

        $cmd = 'ccast -D' . $workDir . ' -s' . $styleFile . $pr1 . ' -o' . $outputFile . ' ' . implode(' ', $fileCsvData);
      }

      $error = 0;
      $sysret = system($cmd, $error);

      if (!isset($extraOptions['not_del_data']) || !$extraOptions['not_del_data']) {
        foreach ($fileCsvData as $fileData) {
          if (file_exists($fileData)) {
            unlink($fileData);
          }
        }
      }

      if (file_exists($outputFile)) {
        if (isset($extraOptions['isGetDownloadPath']) && $extraOptions['isGetDownloadPath']) {
          return Config::get('domain') . $this->generateUrl('Common_downloadFile', array('uploadType' => 'pdf', 'path' => $outputRelativePath));
        }

        if (isset($extraOptions['isGetPath']) && $extraOptions['isGetPath']) {
          return $outputRelativePath;
        }

        return FileUtil::responseDownload($outputFile, array('del_file' => TRUE, 'is_download' => TRUE, 'filename' => 'form' . date('YmdHis') . '.pdf'));
      }
    }

    if (isset($outputFile) && file_exists($outputFile)) {
      unlink($outputFile);
    }

    throw new ExportException(Config::getMessage('common.ERR.can_not_export_pdf_form') . 'Error:' . $sysret);
  }

  public function exportExcel($dataSheets, $formName, $extraOptions = array())
  {
    $filename = $formName . '.xlsx';
    $fileTemplate =  Config::uploadPath() . 'export_template_excel/' . $filename;

    if (is_file($fileTemplate)) {
      $objReader = PHPExcel_IOFactory::createReader('Excel2007');
      $objReader->setIncludeCharts(TRUE);
      $objPHPExcel = $objReader->load($fileTemplate);

      if (isset($extraOptions['isSheetCopy']) && $extraOptions['isSheetCopy']) {
        $arraySheetCopy = $extraOptions['isSheetCopy'];
        foreach ($arraySheetCopy as $sheetCopy) {
          $sheetSource = $objPHPExcel->getSheetByName($sheetCopy['sheetNameSource'])->copy();
          $sheetDestination = clone $sheetSource;
          $sheetDestination->setTitle($sheetCopy['sheetNameCopy']);
          $objPHPExcel->addSheet($sheetDestination, $sheetCopy['sheetPosition']);
        }
      }

      foreach ($dataSheets as $key => $dataOptions) {
        $objPHPExcel->setActiveSheetIndex($key);
        $data = $dataOptions['data'];
        $dataFormat = $dataOptions['dataFormat'];

        $activeSheet = $objPHPExcel->getActiveSheet();
        
        foreach ($dataFormat as $formatExcel) {
          if (isset($formatExcel['rowFrom']) && isset($formatExcel['rowTo'])) {
            $activeSheet->getPageSetup()->setRowsToRepeatAtTop(array($formatExcel['rowFrom'],$formatExcel['rowTo']));
          }

          if (isset($formatExcel['page']) && $formatExcel['page']) {
            $activeSheet->getHeaderFooter()->setOddHeader('&RPage &P');
          }

          if (isset($formatExcel['numRowInsert']) && isset($formatExcel['rowToInsert'])) {
            $activeSheet->insertNewRowBefore($formatExcel['rowToInsert'], $formatExcel['numRowInsert']);
          }

          if (isset($formatExcel['removeRow'])) {
            $activeSheet->removeRow($formatExcel['removeRow'], 2000);
          }

          if (isset($formatExcel['breakRow']) && isset($formatExcel['breakColumn'])) {
            $activeSheet->setBreak($formatExcel['breakRow'] , \PHPExcel_Worksheet::BREAK_ROW );
            $activeSheet->setBreak($formatExcel['breakColumn'] , \PHPExcel_Worksheet::BREAK_COLUMN );
          }

          if (isset($formatExcel['printArea'])) {
            $activeSheet->getPageSetup()->setPrintArea($formatExcel['printArea']);
          }

          if (isset($formatExcel['dataRange']) && isset($formatExcel['startCell'])) {
            $dataRange = $activeSheet->rangeToArray($formatExcel['dataRange']);
            $activeSheet->fromArray($dataRange, null, $formatExcel['startCell']);
          }

          if (isset($formatExcel['styleSource']) && $formatExcel['styleDestination']) {
            $activeSheet->duplicateStyle($activeSheet->getStyle($formatExcel['styleSource']), $formatExcel['styleDestination'] . ':' . $formatExcel['styleDestination']);
          }

          if (isset($formatExcel['getRowHeight'])) {
            $activeSheet->getRowDimension($formatExcel['getRowHeight'])->getRowHeight();
          }

          if (isset($formatExcel['setRowHeight']) && isset($formatExcel['rowHeight'])) {
            $rowHeight = $formatExcel['rowHeight'];
            if (isset($formatExcel['getRowHeight'])) {
              $rowHeight = $activeSheet->getRowDimension($formatExcel['getRowHeight'])->getRowHeight();
            }
            $activeSheet->getRowDimension($formatExcel['setRowHeight'])->setRowHeight($rowHeight);
          }

          if (isset($formatExcel['mergeCells']) && isset($formatExcel['mergeCells']['start']) && isset($formatExcel['mergeCells']['end'])) {
            $activeSheet->mergeCells($formatExcel['mergeCells']['start'] . ':' . $formatExcel['mergeCells']['end']);
          }

          if (isset($formatExcel['unMergeCells']) && isset($formatExcel['unMergeCells']['start']) && isset($formatExcel['unMergeCells']['end'])) {
            $activeSheet->unmergeCells($formatExcel['unMergeCells']['start'] . ':' . $formatExcel['unMergeCells']['end']);
          }

          if (isset($formatExcel['setHorizontal']) && isset($formatExcel['style'])) {
            if ($formatExcel['style'] == ExcelUtil::Horizontal_Center) {
              $activeSheet->getStyle($formatExcel['setHorizontal'])->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            } elseif ($formatExcel['style'] == ExcelUtil::Horizontal_Right) {
              $activeSheet->getStyle($formatExcel['setHorizontal'])->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            } elseif ($formatExcel['style'] == ExcelUtil::Horizontal_Left) {
              $activeSheet->getStyle($formatExcel['setHorizontal'])->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            } else {
              $activeSheet->getStyle($formatExcel['setHorizontal'])->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_GENERAL);
            }
          }

          if (isset($formatExcel['insertImage']) && isset($formatExcel['insertImage']['pathImage']) && isset($formatExcel['insertImage']['position'])
                  && is_file($formatExcel['insertImage']['pathImage'])) {
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setResizeProportional(false);
            $objDrawing->setName('Image');
            $objDrawing->setDescription('Image');
            $objDrawing->setPath($formatExcel['insertImage']['pathImage']);
            $objDrawing->setHeight($formatExcel['insertImage']['height']);
            $objDrawing->setWidth($formatExcel['insertImage']['width']);
            $objDrawing->setWorksheet ($activeSheet);
            $objDrawing->setCoordinates($formatExcel['insertImage']['position']);
            $objDrawing->setOffsetY(0.96);
          }

          if (isset($formatExcel['removeSheet'])) {
            $objPHPExcel->removeSheetByIndex($formatExcel['removeSheet']);
          }

          if (isset($formatExcel['sheetName'])) {
            $activeSheet->setTitle($formatExcel['sheetName']);
          }

          if (isset($formatExcel['styleArray'])) {
            $activeSheet->getStyle($formatExcel['fromCell'].':'.$formatExcel['toCell'])->applyFromArray($formatExcel['styleArray']);
          }
        }

        foreach ($data as $row => $dataRow) {
          foreach ($dataRow as $column => $dataCell) {
            $activeSheet->setCellValue($column . $row, $dataCell);
          }
        }
      }

      $outputRelativePath = 'tmp/' . $formName . '_' . uniqid() . '.xlsx';
      $tempPath = Config::uploadPath() . $outputRelativePath;
      $outputFile = $tempPath;
      $objWriter = PHPExcel_IOFactory :: createWriter($objPHPExcel, 'Excel2007');
      $objWriter->setIncludeCharts(TRUE);
      $objWriter->save($tempPath);

      if (file_exists($outputFile)) {
        if (isset($extraOptions['isGetPath']) && $extraOptions['isGetPath']) {
          return $outputRelativePath;
        }

        return FileUtil::responseDownload($outputFile, array('del_file' => TRUE, 'is_download' => TRUE, 'filename' => 'form_excel' . date('YmdHis') . '.xlsx'));
      }
    }

    throw new ExportException(Config::getMessage('common.ERR.can_not_export_pdf_form'));
  }

  /**
   * @param $customer
   * @param $sales
   * @return \DateTime|string
   *
   * @author hocNt 2013/05/13
   */
  public function getPaymentDueDate($customer, $sales)
  {
    return DateUtil::getPaymentDueDate($customer, $sales);
  }

  /**
   * @param $customer
   * @param $sales
   * @return \DateTime|string
   *
   * @author AnhNT 2013/06/13
   */
  public function getBillingDueDate($customer, $sales)
  {
    return DateUtil::getBillingDueDate($customer, $sales);
  }

  /**
   * Compare with balance Date and return True if less than
   * @param $compareDate DateTime or String with year_month format
   * @return bool
   *
   * @author DatDvq 2013/05/30
   */
  public function isLessThanBalanceDate($compareDate)
  {
    if ($compareDate  instanceof \DateTime) {
      $compareDate = $compareDate->format(Config::get('yearmonth_format'));
    }
    $compareDate = substr($compareDate, 0, 7);

    if (!$this->balanceDate) {
      $balanceDate = $this->get('entity_service')->process('MonthlyBalance:getBalanceDate');
      $balanceDate = $balanceDate->format(Config::get('yearmonth_format'));
      $this->balanceDate = $balanceDate;
    }

    return $compareDate < $this->balanceDate;
  }

  /**
   * add alert to announcement
   * @param $alertFlag
   * @param $entity
   * @author Khiemnd 2013/06/07
   */
  public function addAlertAnnouncement($alertFlag, $entity = NULL)
  {
    $announcements = array();

    switch ($alertFlag) {
      case ValueList::constToValue('S268.alert_flag.CUSTOMER_INTERIM'):
        $officeSupervisors = $this->get('entity_service')->process('Staff:getOfficeDepartSupervisor');

        foreach ($officeSupervisors as $officeSupervisor) {
          $announcement = new Announcement();
          $announcement->setAlertFlag(ValueList::constToValue('S268.alert_flag.CUSTOMER_INTERIM'));
          $announcement->setUser($officeSupervisor->getUser());
          array_push($announcements, $announcement);
        }

        break;

      case ValueList::constToValue('S268.alert_flag.CUSTOMER_APPROVE_REQUEST'):
        $headAndSuperStaffs = $this->get('entity_service')->process('Staff:getManageStaff',
                                 $entity->getSection(), ValueList::constToValue('S119.approve_authority.BOTH'));

        foreach ($headAndSuperStaffs as $headStaff) {
          $announcement = new Announcement();
          $announcement->setAlertFlag(ValueList::constToValue('S268.alert_flag.CUSTOMER_APPROVE_REQUEST'));
          $announcement->setUser($headStaff->getUser());
          array_push($announcements, $announcement);
        }

        break;

      case ValueList::constToValue('S268.alert_flag.SUPPLIER_NOT_APPROVED'):
        $announcement = new Announcement();
        $announcement->setAlertFlag(ValueList::constToValue('S268.alert_flag.SUPPLIER_NOT_APPROVED'));
        $announcement->setUser($entity->getApproveStaff()->getUser());
        array_push($announcements, $announcement);

        break;

      case ValueList::constToValue('S268.alert_flag.QUOTATION_BIZ_NOT_CONFIRM'):
        $announcement = new Announcement();
        $announcement->setAlertFlag(ValueList::constToValue('S268.alert_flag.QUOTATION_BIZ_NOT_CONFIRM'));
        $announcement->setUser(TryUtil::callMethod($entity, 'getStaff:getUser'));
        array_push($announcements, $announcement);

        break;

      case ValueList::constToValue('S268.alert_flag.QUOTATION_NOT_APPROVED'):
        $announcement = new Announcement();
        $announcement->setAlertFlag(ValueList::constToValue('S268.alert_flag.QUOTATION_NOT_APPROVED'));
        $announcement->setUser(TryUtil::callMethod($entity, 'getApproveStaff:getUser'));
        array_push($announcements, $announcement);

        break;

      case ValueList::constToValue('S268.alert_flag.REPORT_NOT_APPROVED'):
        $announcement = new Announcement();
        $announcement->setAlertFlag(ValueList::constToValue('S268.alert_flag.REPORT_NOT_APPROVED'));
        $announcement->setUser(TryUtil::callMethod($entity, 'getApproveStaff:getUser'));
        array_push($announcements, $announcement);

        break;

      case ValueList::constToValue('S268.alert_flag.MAINTENANCE_REQUEST_NOT_APPROVED'):
        $announcement = new Announcement();
        $announcement->setAlertFlag(ValueList::constToValue('S268.alert_flag.MAINTENANCE_REQUEST_NOT_APPROVED'));
        $announcement->setUser($entity->getApproveStaff()->getUser());
        array_push($announcements, $announcement);

        break;

      case ValueList::constToValue('S268.alert_flag.SALES_ZERO_HANSOKU_NOT_CONFIRM'):
        $superStaffs = $this->get('entity_service')->process('Staff:getManageStaff', $entity->getSection());

        foreach ($superStaffs as $superStaff) {
          $announcement = new Announcement();
          $announcement->setAlertFlag(ValueList::constToValue('S268.alert_flag.SALES_ZERO_HANSOKU_NOT_CONFIRM'));
          $announcement->setUser($superStaff->getUser());
          array_push($announcements, $announcement);
        }

        break;

      case ValueList::constToValue('S268.alert_flag.SALES_ZERO_HANSOKU_NOT_APPROVE'):
        $headStaffs = $this->get('entity_service')->process('Staff:getManageStaff',
                                 $entity->getSection(), ValueList::constToValue('S119.approve_authority.DEPARTMENT_HEAD'));

        foreach ($headStaffs as $headStaff) {
          $announcement = new Announcement();
          $announcement->setAlertFlag(ValueList::constToValue('S268.alert_flag.SALES_ZERO_HANSOKU_NOT_APPROVE'));
          $announcement->setUser($headStaff->getUser());
          array_push($announcements, $announcement);
        }

        break;

      case ValueList::constToValue('S268.alert_flag.SALES_ZERO_KEJO_NOT_APPROVE'):
        $superStaffs = $this->get('entity_service')->process('Staff:getManageStaff', $entity->getSection());

        foreach ($superStaffs as $superStaff) {
          $announcement = new Announcement();
          $announcement->setAlertFlag(ValueList::constToValue('S268.alert_flag.SALES_ZERO_KEJO_NOT_APPROVE'));
          $announcement->setUser($superStaff->getUser());
          array_push($announcements, $announcement);
        }

        break;

      case ValueList::constToValue('S268.alert_flag.SALES_AKADEN_NOT_CONFIRM'):
        $superStaffs = $this->get('entity_service')->process('Staff:getManageStaff', $entity->getSection());

        foreach ($superStaffs as $superStaff) {
          $announcement = new Announcement();
          $announcement->setAlertFlag(ValueList::constToValue('S268.alert_flag.SALES_AKADEN_NOT_CONFIRM'));
          $announcement->setUser($superStaff->getUser());
          array_push($announcements, $announcement);
        }

        break;

      case ValueList::constToValue('S268.alert_flag.SALES_AKADEN_NOT_APPROVE'):
        $headStaffs = $this->get('entity_service')->process('Staff:getManageStaff',
                                 $entity->getSection(), ValueList::constToValue('S119.approve_authority.DEPARTMENT_HEAD'));

        foreach ($headStaffs as $headStaff) {
          $announcement = new Announcement();
          $announcement->setAlertFlag(ValueList::constToValue('S268.alert_flag.SALES_AKADEN_NOT_APPROVE'));
          $announcement->setUser($headStaff->getUser());
          array_push($announcements, $announcement);
        }

        break;
    }

    $this->get('entity_service')->save($announcements);
  }

  /**
   * checkTtsOrderToCreateNewRelation S260_7, S258_4, 257_4, 259_4
   * @param TtsOrder $ttsOrder
   * @author Khiemnd 2013/07/17
   */
  public function checkTtsOrderToCreateNewRelation($ttsOrder)
  {
    $orderFlag = $ttsOrder->getOrderFlag();
    $requestSection = $ttsOrder->getRequestSection();

    $REPAIR_SALES = ValueList::constToValue('common.order_flag.REPAIR_SALES');
    $REPLACE_SALES = ValueList::constToValue('common.order_flag.REPLACE_SALES');
    $CONSTRUCT_SALES = ValueList::constToValue('common.order_flag.CONSTRUCT_SALES');
    $CONSTRUCT_INVESTIGATION = ValueList::constToValue('common.order_flag.CONSTRUCT_INVESTIGATION');
    $MAINTENANCE_SALES = ValueList::constToValue('common.order_flag.MAINTENANCE_SALES');
    $requestSectionTech     = ValueList::constToValue('S251.request_to.TECH');
    $requestSectionPartner  = ValueList::constToValue('S251.request_to.PARTNER');

    if((!$ttsOrder->getRequests()->count()
       && (in_array($orderFlag, array($REPAIR_SALES, $CONSTRUCT_INVESTIGATION, $MAINTENANCE_SALES))
          || ($requestSection == $requestSectionPartner && in_array($orderFlag, array($REPLACE_SALES, $CONSTRUCT_SALES)))))) {

      $this->addFlashError(Config::getMessage('common.ERR.no_request_data_you_can_not_create'));
    } elseif (!$ttsOrder->getCmConstructionRequest()
        && $requestSection == $requestSectionTech && in_array($orderFlag, array($REPLACE_SALES, $CONSTRUCT_SALES))) {

      $this->addFlashError(Config::getMessage('common.ERR.no_work_request_data_you_can_not_create'));
    } else {

      return TRUE;
    }

    return FALSE;
  }

  /**
   * sort list entity from search and edit screen
   * @param $ids
   * @param $entities
   * @param $params
   * @return array
   * @author DatDvq 2013/07/25
   */
  function sortListEntityForEdit($entities, $ids, $params = array()) {
    $entityIndex = array();
    foreach($entities as $entity) {
      $entityIndex[$entity->getId()] = $entity;
    }

    $sortEntityList = array();
    $sortParamList = array();

    if ($ids) {
      foreach($ids as $key => $id) {
        $entity = TryUtil::fetchArray($entityIndex, $id);
        $param  = TryUtil::fetchArray($params, $key);

        if ($entity && $param) {
          $sortEntityList[$key] = $entity;
          $sortParamList[$key]  = $param;
        }
      }
    }

    return array('entities' => $sortEntityList, 'params' => $sortParamList);
  }

  /**
   * getMailDownloadLink
   * @param MailHistory $mailHistory
   * @param $detailData
   * @return array
   * @author Khiemnd 2013/07/26
   */
  public function getMailDownloadLink(MailHistory $mailHistory, $detailData)
  {
    $password = StringUtil::strRand($length = 6, $output = 'alphanum');
    $accessCode = StringUtil::strRand($length = 6, $output = 'alphanum');
    $mailHistory->setPassword($password)
                ->setAccessCode($accessCode)
                ->setSendTime(new \DateTime())
                ->setStaff($this->getUser()->getStaff());

    $saveArr = array($mailHistory);

    if (!is_array($detailData)) {
      $detailData = array($detailData);
    }

    foreach ($detailData as $mailHistoryDetail) {
      if ($mailHistoryDetail && $mailHistoryDetail->getExportFormData() && $mailHistoryDetail->getExportFormName()) {
        $filePath = $this->exportPDF($mailHistoryDetail->getExportFormData(),
                                      $mailHistoryDetail->getExportFormName(),
                                      array('addCsvData' => isset($mailHistoryDetail->addCsvData)?$mailHistoryDetail->addCsvData:array(), 'isGetPath' => TRUE));
      } else {
        $extraOption = $mailHistoryDetail->dataExcel;
        $filePath = $this->exportExcel($extraOption['dataSheets'], $extraOption['formName'],
                                      array('isSheetCopy' =>  $extraOption['isSheetCopy'], 'isGetPath' => TRUE));
      }

      $mailHistoryDetail->setMailHistory($mailHistory)
        ->setFilePath($filePath)
        ->setSendTime(new \DateTime());
      array_push($saveArr, $mailHistoryDetail);
    }

    $this->get('entity_service')->save($saveArr);

    $url = Config::get('domain').$this->generateUrl('S275_1', array('accessCode' => $accessCode));

    return array('url' => $url, 'password' => $password);
  }

  /**
   * get list email technical
   * @return string
   * @author ThongTq 2013/07/29
   */
  public function getListMailTechnical(){
    $listStaff = $this->get('entity_service')->process('User:getStaffByUserFlag', ValueList::constToValue('S119.user_flag.INSIDE_TECHNICAL'));
    $emailTechArr = array();
    $emailTechnical = "";

    if ($listStaff) {
      foreach($listStaff as $staff){
        if ($staff->getStaff()->getEmail()) {
          $emailTechArr[] = $staff->getStaff()->getEmail();
        }

        $emailTechnical = implode(';', $emailTechArr);
      }
    }

    return $emailTechnical;
  }

  /**
   * get list email SupperVisor by section
   * @param $section
   * @param $approveAuthority
   * @return string
   * @author ThongTq 2013/08/19
   */
  public function getListMailSupperVisorBySection($section, $approveAuthority)
  {
    $emailSupperVisor = '';
    $listStaff = $this->get('entity_service')->process('Staff:getManageStaff', $section, $approveAuthority);

    if ($listStaff) {
      $listEmail = array();
      foreach($listStaff as $staff){
        $listEmail[] = $staff->getEmail();
      }

      $emailSupperVisor = implode(';', $listEmail);
    }

    return $emailSupperVisor;
  }

  /**
   * get list email Supplier and Supplier staff
   * @param $id
   * @param $type
   * @return string
   * @author ThongTq 2013/07/29
   */
  public function getListMail($id, $type)
  {
    $emailSuppliers = '';
    if ($type == 'request') {
      $entity = $this->getEntity('Request', $id);
    }  else {
      $entity = $this->getEntity('Schedule', $id);
    }

    if ($entity->getSupplier()) {
      $emailSuppliers = $entity->getSupplier()->getAllEmail();
    }

    $listEmailTo = $emailSuppliers;

    $emailSupplierStaff = array();
    if ($entity->getSupplierStaff()) {
      $emailSupplierStaff[] = $entity->getSupplierStaff()->getEmail1();
      $emailSupplierStaff[] = $entity->getSupplierStaff()->getEmail2();
      $supplierStaff = implode(';', $emailSupplierStaff);
      $listEmailTo = $listEmailTo.';'.$supplierStaff;
    }

    return $listEmailTo;
  }

  /**
   * get update sales and create new billing history for S401, S259_4
   * @param $arrIds
   * @param $screen
   * @return string
   * @author ThongTq 2013/08/29
   */
  public function updateSalesWhenPressPrintButton($arrIds, $screen){
    $arrData = array();
    $toDate  = new \DateTime;
    $salesStatus_FIX = ValueList::constToValue('common.sales_status.FIX');
    $invoicePublishSection_OFFICE = ValueList::constToValue('S259.invoice_publish_section.OFFICE');
    $sendFlag_FINISHED = ValueList::constToValue('S259.send_flag.FINISHED');

    if ($screen == "S401") {
      $arrRepublishFlags = $this->getRequest()->get('postRepublishFlags', array());
    }

    $section = $this->getUser()->getSection();
    $staff = $this->getUser()->getStaff();
    $sales = $this->get('entity_service')->process('Sales:findById', $arrIds);

    foreach ($sales as $key => $salesItem) {
      if ($screen == "S401") {
        $salesItem->setRepublishFlag($arrRepublishFlags[$key]);
      }

      if ($salesItem->getSalesStatus() == $salesStatus_FIX) {
        $publishCount = $salesItem->getPublishCount() ? $salesItem->getPublishCount() : 0;
        $salesItem->setFinalPublishStaff($staff)
          ->setFinalPublishDate($toDate)
          ->setPublishCount($publishCount + 1);

        if ($salesItem->getInvoicePublishSection() == $invoicePublishSection_OFFICE) {
          $salesItem->setSendFlag($sendFlag_FINISHED);
        }

        $billingHistory = new BillingHistory();
        $billingHistory->setSales($salesItem)
                      ->setInvoicePublishDate($salesItem->getInvoicePublishDate())
                      ->setIssueStaff($staff)
                      ->setIssueSection($section)
                      ->setPublishDate($toDate);

        $republishDisplay = ValueList::constToValue('common.is_checked.NO');
        if ($screen == "S401") {
          $republishDisplay = $arrRepublishFlags[$key];
        }
        $billingHistory->setRepublishDisplay($republishDisplay);

        $arrData[] = $billingHistory;
      }

      $arrData[] = $salesItem;
    }

    if ($arrData) {
      $this->get('entity_service')->save($arrData);
    }

    return new RedirectResponse($this->getRequest()->headers->get('referer'));
  }

// Set updated common fields in case update by ajax
  public function setUpdatedCommonFieldsForSaveAjax($entity, $user){
    $entity->setUpdatedAt(new \DateTime());
    $entity->setUpdatedBy($user);
    $entity->setUpdatedSection($user->getStaff()->getSection());
    $entity->setUpdatedStaff($user->getStaff());
    $entity->setUpdatedDisplayName($user->getStaff()->getStaffName());
  }


  /**
   * @author Datdqv
   */
  public function checkRolePartner($entity, $method = 'getSupplier') {
    if ($this->get('security.context')->isGranted('ROLE_PARTNER')) {
      $loginSupplier = TryUtil::callMethod($this, 'getUser:getSupplierStaff:getSupplier');

      if ($loginSupplier) {
        $checkObj = TryUtil::callMethod($entity, $method);

        if ($checkObj != $loginSupplier) {
          throw $this->createNotFoundException(Config::getMessage('not_found'));
        }
      }
    }
  }

  /**
   * tax rate set default #13604
   * @author Khiemnd
   */
  public function setDefaultTaxRate($entity, $checkDate = null)
  {
      $entity->setTaxRate(AccountingUtil::getDefaultTaxRate($checkDate));
  }
  /**
   * CSVデータ出力(Optionsを追加)
   * @author toanlm
   * @since 2014/09/19
   */
  public function  exportCSVExtra($entities, $formatYmlFile, $extraOptions=array())
{
    return FileUtil::csvExportEntitiesExtra($entities, $formatYmlFile, $extraOptions);
}

  /**
   * @return EntityService
   */
  public function getEntityService()
  {
    return $this->get('entity_service');
  }
}
