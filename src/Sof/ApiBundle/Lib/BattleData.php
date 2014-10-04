<?php
/**
 * Created by PhpStorm.
 * User: vansy
 * Date: 2014/04/21
 * Time: 17:48
 */

namespace Sof\ApiBundle\Lib;

use Sof\ApiBundle\Entity\BaseEntity;
use Sof\ApiBundle\Exception\SofApiException;

class BattleData {

    //Const
    const STATUS_WIN      = 'WIN';
    const STATUS_CONTINUE = 'CONTINUE';
    const STATUS_LOSE     = 'LOSE';
    const SKILL_TYPE_ACTIVE      = 1;//能動型
    const SKILL_TYPE_PASSIVE     = 2;// 受動型
    const SKILL_TYPE_OBSERVATION = 3;//監視型

    //Single var
    /**
     * $ARG[バトルID]
     * @var BtTempDataCollection
     */
    private $arg_battleId;

    /**
     * $ARG[日時]
     * @var BtTempDataCollection
     */
    private $arg_date;

    /**
     * $atk_str
     * @var BtTempDataCollection
     */
    private $atkStr;

    /**
     * $REQUEST[エンカウントストックID]
     * @var BtTempDataCollection
     */
    private $request_encounterStockId;

    /**
     * $REQUEST[デッキID]
     * @var BtTempDataCollection
     */
    private $request_deckId;

    /**
     * $REQUEST[ユーザID]
     * @var BtTempDataCollection
     */
    private $request_userId;

    /**
     * $REQUEST[応援フレンドその１のカードID]
     * @var BtTempDataCollection
     */
    private $request_cardOneCheeringFriends;

    /**
     * $REQUEST[応援フレンドその２のカードID]
     * @var BtTempDataCollection
     */
    private $request_cardTwoCheeringFriends;

    /**
     * $spd_str
     * @var BtTempDataCollection
     */
    private $spdStr;

    /**
     * $エンカウントストック情報
     * @var BtTempDataCollection
     */
    private $encounterStockInformation;

    /**
     * $エンカウント情報
     * @var BtTempDataCollection
     */
    private $encounterInformation;

    /**
     * バトルID
     * @var BtTempDataCollection
     */
    private $battleId;

    /**
     * ターン番号
     * @var BtTempDataCollection
     */
    private $turnNumber;

    /**
     * 勝敗状態
     * @var BtTempDataCollection
     */
    private $statusBattle;

    /**
     * 発動可能状態
     * @var BtTempDataCollection
     */
    private $activatedState;

    /**
     * フェイズ
     * @var BtTempDataCollection
     */
    private $phase;

    /**
     * 補正済ATK
     * @var BtTempDataCollection
     */
    private $correctedAtk;

    /**
     * 補正率
     * @var BtTempDataCollection
     */
    private $correctionFactor;

    /**
     * 計算式文字列の計算結果
     * @var BtTempDataCollection
     */
    private $resultOfFormulaString;

    //Array
    /**
     * 発動スキル
     * @var BtTempDataCollection
     */
    private $activateSkills;

    /**
     * ターゲット
     * @var BtTempDataCollection
     */
    private $target;

    /**
     * スキル計算式
     * @var BtTempDataCollection
     */
    private $skillsFormula;

    //List
    /**
     * 能動型スキルリスト
     * @var BtTempDataCollection
     */
    private $activeTypeSkillList;

    /**
     * 監視型スキルリスト
     * @var BtTempDataCollection
     */
    private $inspectionTypeSkillList;

    /**
     * 受動型スキルリスト
     * @var BtTempDataCollection
     */
    private $passiveTypeSkillList;

    /**
     * ターゲットリスト
     * @var BtTempDataCollection
     */
    private $targetList;

    /**
     * 巻き添えリスト
     * @var BtTempDataCollection
     */
    private $collateralList;

    public function __construct()
    {
        //Single var
        $this->turnNumber       = 0;
        $this->statusBattle     = self::STATUS_CONTINUE;
        $this->correctedAtk     = 0;
        $this->correctionFactor = 0;

        //Collection
        $this->activateSkills = new ActivateSkills();
        $this->target         = new Target();
        $this->skillsFormula  = new SkillsFormula();

        //Collection list
        $this->activeTypeSkillList     = new BtTempDataCollection();
        $this->inspectionTypeSkillList = new BtTempDataCollection();
        $this->passiveTypeSkillList    = new BtTempDataCollection();
        $this->targetList              = new BtTempDataCollection();
        $this->collateralList          = new BtTempDataCollection();
    }

    /**
     * @param BtTempDataCollection $arg_battleId
     * @return BattleData
     */
    public function setArgBattleId($arg_battleId)
    {
        $this->arg_battleId = $arg_battleId;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getArgBattleId()
    {
        return $this->arg_battleId;
    }

    /**
     * @param BtTempDataCollection $arg_date
     * @return BattleData
     */
    public function setArgDate($arg_date)
    {
        $this->arg_date = $arg_date;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getArgDate()
    {
        return $this->arg_date;
    }

    /**
     * @param BtTempDataCollection $atkStr
     * @return BattleData
     */
    public function setAtkStr($atkStr)
    {
        $this->atkStr = $atkStr;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getAtkStr()
    {
        return $this->atkStr;
    }

    /**
     * @param BtTempDataCollection $request_cardOneCheeringFriends
     * @return BattleData
     */
    public function setRequestCardOneCheeringFriends($request_cardOneCheeringFriends)
    {
        $this->request_cardOneCheeringFriends = $request_cardOneCheeringFriends;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getRequestCardOneCheeringFriends()
    {
        return $this->request_cardOneCheeringFriends;
    }

    /**
     * @param BtTempDataCollection $request_cardTwoCheeringFriends
     * @return BattleData
     */
    public function setRequestCardTwoCheeringFriends($request_cardTwoCheeringFriends)
    {
        $this->request_cardTwoCheeringFriends = $request_cardTwoCheeringFriends;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getRequestCardTwoCheeringFriends()
    {
        return $this->request_cardTwoCheeringFriends;
    }

    /**
     * @param BtTempDataCollection $request_deckId
     * @return BattleData
     */
    public function setRequestDeckId($request_deckId)
    {
        $this->request_deckId = $request_deckId;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getRequestDeckId()
    {
        return $this->request_deckId;
    }

    /**
     * @param BtTempDataCollection $request_encounterStockId
     * @return BattleData
     */
    public function setRequestEncounterStockId($request_encounterStockId)
    {
        $this->request_encounterStockId = $request_encounterStockId;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getRequestEncounterStockId()
    {
        return $this->request_encounterStockId;
    }

    /**
     * @param BtTempDataCollection $request_userId
     * @return BattleData
     */
    public function setRequestUserId($request_userId)
    {
        $this->request_userId = $request_userId;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getRequestUserId()
    {
        return $this->request_userId;
    }

    /**
     * @param BtTempDataCollection $spdStr
     * @return BattleData
     */
    public function setSpdStr($spdStr)
    {
        $this->spdStr = $spdStr;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getSpdStr()
    {
        return $this->spdStr;
    }

    /**
     * @param BtTempDataCollection $encounterStockInformation
     * @return BattleData
     */
    public function setEncounterStockInformation($encounterStockInformation)
    {
        $this->encounterStockInformation = $encounterStockInformation;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getEncounterStockInformation()
    {
        return $this->encounterStockInformation;
    }

    /**
     * @param BtTempDataCollection $encounterInformation
     * @return BattleData
     */
    public function setEncounterInformation($encounterInformation)
    {
        $this->encounterInformation = $encounterInformation;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getEncounterInformation()
    {
        return $this->encounterInformation;
    }

    /**
     * @param BtTempDataCollection $battleId
     * @return BattleData
     */
    public function setBattleId($battleId)
    {
        $this->battleId = $battleId;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getBattleId()
    {
        return $this->battleId;
    }

    /**
     * @param BtTempDataCollection $turnNumber
     * @return BattleData
     */
    public function setTurnNumber($turnNumber)
    {
        $this->turnNumber = $turnNumber;
        return $this;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getTurnNumber()
    {
        return $this->turnNumber;
    }

    /**
     * @param BtTempDataCollection $statusBattle
     * @return BattleData
     */
    public function setStatusBattle($statusBattle)
    {
        $this->statusBattle = $statusBattle;
        return $this;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getStatusBattle()
    {
        return $this->statusBattle;
    }

    /**
     * @param BtTempDataCollection $activatedState
     * @return BattleData
     */
    public function setActivatedState($activatedState)
    {
        $this->activatedState = $activatedState;

        return $this;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getActivatedState()
    {
        return $this->activatedState;
    }

    /**
     * @param BtTempDataCollection $phase
     * @return BattleData
     */
    public function setPhase($phase)
    {
        $this->phase = $phase;

        return $this;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getPhase()
    {
        return $this->phase;
    }

    /**
     * @param BtTempDataCollection $correctedAtk
     * @return BattleData
     */
    public function setCorrectedAtk($correctedAtk)
    {
        $this->correctedAtk = $correctedAtk;

        return $this;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getCorrectedAtk()
    {
        return $this->correctedAtk;
    }

    /**
     * @param BtTempDataCollection $correctionFactor
     * @return BattleData
     */
    public function setCorrectionFactor($correctionFactor)
    {
        $this->correctionFactor = $correctionFactor;

        return $this;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getCorrectionFactor()
    {
        return $this->correctionFactor;
    }

    /**
     * @param BtTempDataCollection $resultOfFormulaString
     * @return BattleData
     */
    public function setResultOfFormulaString($resultOfFormulaString)
    {
        $this->resultOfFormulaString = $resultOfFormulaString;

        return $this;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getResultOfFormulaString()
    {
        return $this->resultOfFormulaString;
    }

    /**
     * @param ActivateSkills $activateSkills
     * @return BattleData
     */
    public function setActiveSkills($activateSkills)
    {
        $this->activateSkills = $activateSkills;

        return $this;
    }

    /**
     * @return ActivateSkills
     */
    public function getActiveSkills()
    {
        return $this->activateSkills;
    }

    /**
     * add activateSkills to $activateSkills
     * @param ActivateSkills ActivateSkills
     * @return BattleData;
     */
    public function addActiveSkillsElement($activateSkills){
        $this->activateSkills->add($activateSkills);

        return $this;
    }

    /**
     * @param Target $target
     * @return BattleData
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return Target
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * add target to $target
     * @param Target Target
     * @return BattleData;
     */
    public function addTargetElement($target){
        $this->target->add($target);

        return $this;
    }

    /**
     * @param SkillsFormula $skillsFormula
     * @return BattleData
     */
    public function setSkillsFormula($skillsFormula)
    {
        $this->skillsFormula = $skillsFormula;

        return $this;
    }

    /**
     * @return SkillsFormula
     */
    public function getSkillsFormula()
    {
        return $this->skillsFormula;
    }

    /**
     * add target to $skillsFormula
     * @param SkillsFormula SkillsFormula
     * @return BattleData;
     */
    public function addSkillsFormulaElement($skillsFormula){
        $this->skillsFormula->add($skillsFormula);

        return $this;
    }

    /**
     * @param BtTempDataCollection $activeTypeSkillList
     * @return BattleData
     */
    public function setActiveTypeSkillList($activeTypeSkillList)
    {
        $this->activeTypeSkillList = $activeTypeSkillList;

        return $this;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getActiveTypeSkillList()
    {
        return $this->activeTypeSkillList;
    }

    /**
     * add activeTypeSkill to $activeTypeSkillList
     * @param ActiveTypeSkill BtTempActiveTypeSkill
     * @return BattleData;
     */
    public  function addActiveTypeSkill($activeTypeSkill){
        $this->activeTypeSkillList->add($activeTypeSkill);

        return $this;
    }

    /**
     * @param BtTempDataCollection $collateralList
     * @return BattleData
     */
    public function setCollateralList($collateralList)
    {
        $this->collateralList = $collateralList;

        return $this;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getCollateralList()
    {
        return $this->collateralList;
    }

    /**
     * add  $collateral to $collateralList
     * @param BtTempCollateral $collateral
     * @return BattleData
     */
    public function addCollateral($collateral){
        $this->collateralList->add($collateral);

        return $this;
    }

    /**
     * @param BtTempDataCollection $inspectionTypeSkillList
     * @return BattleData
     */
    public function setInspectionTypeSkillList($inspectionTypeSkillList)
    {
        $this->inspectionTypeSkillList = $inspectionTypeSkillList;

        return $this;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getInspectionTypeSkillList()
    {
        return $this->inspectionTypeSkillList;
    }

    /**
     * add $inspectionTypeSkill to $inspectionTypeSkillList
     * @param BtTempInspectionTypeSkill $inspectionTypeSkill
     * @return BattleData
     */
    public function addInspectionTypeSkill($inspectionTypeSkill){
        $this->inspectionTypeSkillList->add($inspectionTypeSkill);

        return $this;
    }

    /**
     * @param BtTempDataCollection $passiveTypeSkillList
     * @return BattleData
     */
    public function setPassiveTypeSkillList($passiveTypeSkillList)
    {
        $this->passiveTypeSkillList = $passiveTypeSkillList;

        return $this;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getPassiveTypeSkillList()
    {
        return $this->passiveTypeSkillList;
    }

    /**
     * add $passiveTypeSkill to $passiveTypeSkillList
     * @param BtTempPassiveTypeSkill $passiveTypeSkill
     * @return BattleData
     */
    public function addPassiveTypeSkill($passiveTypeSkill){
        $this->passiveTypeSkillList->add($passiveTypeSkill);

        return $this;
    }

    /**
     * @param BtTempDataCollection $targetList
     * @return BattleData
     */
    public function setTargetList($targetList)
    {
        $this->targetList = $targetList;

        return $this;
    }

    /**
     * @return BtTempDataCollection
     */
    public function getTargetList()
    {
        return $this->targetList;
    }

    /**
     * add $target to $targetList
     * @param BtTempTarget $target
     * @return BattleData
     */
    public function addTarget($target){
        $this->targetList->add($target);

        return $this;
    }
}

/**
 * 発動スキル
 */
class ActivateSkills {
    /**
     * $発動スキル.カードID
     */
    private $cardId;

    /**
     * $発動スキル.スキルID
     */
    private $skillId;

    /**
     * $発動スキル.ターゲットカードID
     */
    private $targetCardId;

    /**
     * @param mixed $targetCardId
     */
    public function setTargetCardId($targetCardId)
    {
        $this->targetCardId = $targetCardId;
    }

    /**
     * @return mixed
     */
    public function getTargetCardId()
    {
        return $this->targetCardId;
    }

    /**
     * $発動スキル.自分がターゲット
     */
    private $isMyTarget;

    public function __construct()
    {
        $this->cardId       = 0;
        $this->skillId      = 0;
        $this->targetCardId = 0;
        $this->isMyTarget   = BaseEntity::FLAG_ON;
    }

    /**
     * @param int $cardId
     * @return ActivateSkills
     */
    public function setCardId($cardId)
    {
        $this->cardId = $cardId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * @param int $skillId
     * @return ActivateSkills
     */
    public function setSkillId($skillId)
    {
        $this->skillId = $skillId;

        return $this;
    }

    /**
     * @return int
     */
    public function getSkillId()
    {
        return $this->skillId;
    }

    /**
     * @return int
     */
    public function getIsMyTarget()
    {
        return $this->isMyTarget;
    }

    /**
     * @param int $isMyTarget
     * @return ActivateSkills
     */
    public function setIsMyTarget($isMyTarget)
    {
        $this->isMyTarget = $isMyTarget;

        return $this;
    }
}

/**
 * ターゲット
 */
class Target  {

    /**
     * $ターゲット.スキルID
     */
    private $skillsId;

    /**
     * $ターゲット.スキル系列ID
     */
    private $skillsSeriesId;

    /**
     * $ターゲット.ターゲットカードID
     */
    private $targetIdCard;

    /**
     * $ターゲット[ATK]
     */
    private $atk;

    /**
     * $ターゲット.発動カードID
     */
    private $activatedCardId;

    /**
     * $ターゲット[起点X]
     */
    private $pointX;

    /**
     * $ターゲット[起点Y]
     */
    private $pointY;

    /**
     * @param mixed $skillsId
     */
    public function setSkillsId($skillsId)
    {
        $this->skillsId = $skillsId;
    }

    /**
     * @return mixed
     */
    public function getSkillsId()
    {
        return $this->skillsId;
    }

    /**
     * @param mixed $activatedCardId
     */
    public function setActivatedCardId($activatedCardId)
    {
        $this->activatedCardId = $activatedCardId;
    }

    /**
     * @return mixed
     */
    public function getActivatedCardId()
    {
        return $this->activatedCardId;
    }

    /**
     * @param mixed $atk
     */
    public function setAtk($atk)
    {
        $this->atk = $atk;
    }

    /**
     * @return mixed
     */
    public function getAtk()
    {
        return $this->atk;
    }

    /**
     * @param mixed $pointX
     */
    public function setPointX($pointX)
    {
        $this->pointX = $pointX;
    }

    /**
     * @return mixed
     */
    public function getPointX()
    {
        return $this->pointX;
    }

    /**
     * @param mixed $pointY
     */
    public function setPointY($pointY)
    {
        $this->pointY = $pointY;
    }

    /**
     * @return mixed
     */
    public function getPointY()
    {
        return $this->pointY;
    }

    /**
     * @param mixed $skillsSeriesId
     */
    public function setSkillsSeriesId($skillsSeriesId)
    {
        $this->skillsSeriesId = $skillsSeriesId;
    }

    /**
     * @return mixed
     */
    public function getSkillsSeriesId()
    {
        return $this->skillsSeriesId;
    }

    /**
     * @param mixed $targetIdCard
     */
    public function setTargetIdCard($targetIdCard)
    {
        $this->targetIdCard = $targetIdCard;
    }

    /**
     * @return mixed
     */
    public function getTargetIdCard()
    {
        return $this->targetIdCard;
    }
}

/**
 * スキル計算式
 */
class SkillsFormula  {

    /**
     * スキル計算式[ダメージ倍率]
     */
    private $damageMagnification;

    /**
     * スキル計算式[対象項目名]
     */
    private $targetItemName;

    /**
     * スキル計算式[繰り返し回数]
     */
    private $numberOfIterations;

    /**
     * スキル計算式[計算式]
     */
    private $formula;

    /**
     * @param mixed $damageMagnification
     */
    public function setDamageMagnification($damageMagnification)
    {
        $this->damageMagnification = $damageMagnification;
    }

    /**
     * @return mixed
     */
    public function getDamageMagnification()
    {
        return $this->damageMagnification;
    }

    /**
     * @param mixed $formula
     */
    public function setFormula($formula)
    {
        $this->formula = $formula;
    }

    /**
     * @return mixed
     */
    public function getFormula()
    {
        return $this->formula;
    }

    /**
     * @param mixed $numberOfIterations
     */
    public function setNumberOfIterations($numberOfIterations)
    {
        $this->numberOfIterations = $numberOfIterations;
    }

    /**
     * @return mixed
     */
    public function getNumberOfIterations()
    {
        return $this->numberOfIterations;
    }

    /**
     * @param mixed $targetItemName
     */
    public function setTargetItemName($targetItemName)
    {
        $this->targetItemName = $targetItemName;
    }

    /**
     * @return mixed
     */
    public function getTargetItemName()
    {
        return $this->targetItemName;
    }
}

/**
 * 能動型スキルリスト
 */
class BtTempActiveTypeSkill{
    use BtTempData;

    // カードID
    private $cardId;

    //スキルID
    private $skillId;

    //発動順
    private $activationOrder;

    //atk
    private $atk;

    //spd
    private $spd;

    //発動状態
    private $activatedState;

    public function __construct()
    {
        $this->setId();

        $this->cardId = 0;
        $this->skillId = 0;
        $this->activationOrder = 0;
        $this->atk = 0;
        $this->spd = 0;
        $this->activatedState = 0;
    }

    /**
     * @param int $activatedState
     * @return BtTempActiveTypeSkill
     */
    public function setActivatedState($activatedState)
    {
        $this->activatedState = $activatedState;
        return $this;
    }

    /**
     * @return int
     */
    public function getActivatedState()
    {
        return $this->activatedState;
    }

    /**
     * @param int $activationOrder
     * @return BtTempActiveTypeSkill
     */
    public function setActivationOrder($activationOrder)
    {
        $this->activationOrder = $activationOrder;
        return $this;
    }

    /**
     * @return int
     */
    public function getActivationOrder()
    {
        return $this->activationOrder;
    }

    /**
     * @param int $atk
     * @return BtTempActiveTypeSkill
     */
    public function setAtk($atk)
    {
        $this->atk = $atk;
        return $this;
    }

    /**
     * @return int
     */
    public function getAtk()
    {
        return $this->atk;
    }

    /**
     * @param int $cardId
     * @return BtTempActiveTypeSkill
     */
    public function setCardId($cardId)
    {
        $this->cardId = $cardId;
        return $this;
    }

    /**
     * @return int
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * @param int $skillId
     * @return BtTempActiveTypeSkill
     */
    public function setSkillId($skillId)
    {
        $this->skillId = $skillId;

        return $this;
    }

    /**
     * @return int
     */
    public function getSkillId()
    {
        return $this->skillId;
    }

    /**
     * @param int $spd
     * @return BtTempActiveTypeSkill
     */
    public function setSpd($spd)
    {
        $this->spd = $spd;
        return $this;
    }

    /**
     * @return int
     */
    public function getSpd()
    {
        return $this->spd;
    }
}

/**
 * 監視型スキルリスト
 *
 * Class BtTempInspectionTypeSkill
 * @package Sof\ApiBundle\Lib
 */
class BtTempInspectionTypeSkill {
    use BtTempData;

    // カードID
    private $cardId;

    //スキルID
    private $skillId;

    //対象項目名
    private $targetItemName;

    //監視条件式
    private $inspectionCondition;

    //atk
    private $atk;

    //spd
    private $spd;

    //発動状態
    private $activatedState;

    public function __construct()
    {
        $this->setId();

        $this->cardId = 0;
        $this->skillId = 0;
        $this->targetItemName = "";
        $this->inspectionCondition = "";
        $this->atk = 0;
        $this->spd = 0;
        $this->activatedState = 0;
    }

    /**
     * @param int $activatedState
     * @return BtTempInspectionTypeSkill
     */
    public function setActivatedState($activatedState)
    {
        $this->activatedState = $activatedState;
        return $this;
    }

    /**
     * @return int
     */
    public function getActivatedState()
    {
        return $this->activatedState;
    }

    /**
     * @param int $atk
     * @return BtTempInspectionTypeSkill
     */
    public function setAtk($atk)
    {
        $this->atk = $atk;
        return $this;
    }

    /**
     * @return int
     */
    public function getAtk()
    {
        return $this->atk;
    }

    /**
     * @param int $cardId
     * @return BtTempInspectionTypeSkill
     */
    public function setCardId($cardId)
    {
        $this->cardId = $cardId;
        return $this;
    }

    /**
     * @return int
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * @param string $inspectionCondition
     * @return BtTempInspectionTypeSkill
     */
    public function setInspectionCondition($inspectionCondition)
    {
        $this->inspectionCondition = $inspectionCondition;
        return $this;
    }

    /**
     * @return string
     */
    public function getInspectionCondition()
    {
        return $this->inspectionCondition;
    }

    /**
     * @param int $skillId
     * @return BtTempInspectionTypeSkill
     */
    public function setSkillId($skillId)
    {
        $this->skillId = $skillId;
        return $this;
    }

    /**
     * @return int
     */
    public function getSkillId()
    {
        return $this->skillId;
    }

    /**
     * @param int $spd
     * @return BtTempInspectionTypeSkill
     */
    public function setSpd($spd)
    {
        $this->spd = $spd;
        return $this;
    }

    /**
     * @return int
     */
    public function getSpd()
    {
        return $this->spd;
    }

    /**
     * @param string $targetItemName
     * @return BtTempInspectionTypeSkill
     */
    public function setTargetItemName($targetItemName)
    {
        $this->targetItemName = $targetItemName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTargetItemName()
    {
        return $this->targetItemName;
    }
}

/**
 * 受動型スキルリスト
 *
 * Class BtTempPassiveTypeSkill
 * @package Sof\ApiBundle\Lib
 */
class BtTempPassiveTypeSkill{
    use BtTempData;

    //カードID
    private $cardId;

    //ターゲットカード ID
    private $targetCardId;

    //スキルId
    private $skillId;

    //stk
    private $atk;

    //spd
    private $spd;


    //発動状態
    private $activatedState;


    public function __construct()
    {
        $this->setId();

        $this->cardId = 0;
        $this->targetCardId = 0;
        $this->skillId = 0;
        $this->atk = 0;
        $this->spd = 0;
        $this->activatedState = 0;
    }

    /**
     * @param int $activatedState
     * @return BtTempPassiveTypeSkill
     */
    public function setActivatedState($activatedState)
    {
        $this->activatedState = $activatedState;
        return $this;
    }

    /**
     * @return int
     */
    public function getActivatedState()
    {
        return $this->activatedState;
    }

    /**
     * @param int $atk
     * @return BtTempPassiveTypeSkill
     */
    public function setAtk($atk)
    {
        $this->atk = $atk;
        return $this;
    }

    /**
     * @return int
     */
    public function getAtk()
    {
        return $this->atk;
    }

    /**
     * @param int $cardId
     * @return BtTempPassiveTypeSkill
     */
    public function setCardId($cardId)
    {
        $this->cardId = $cardId;
        return $this;
    }

    /**
     * @return int
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * @param int $skillId
     * @return BtTempPassiveTypeSkill
     */
    public function setSkillId($skillId)
    {
        $this->skillId = $skillId;
        return $this;
    }

    /**
     * @return int
     */
    public function getSkillId()
    {
        return $this->skillId;
    }

    /**
     * @param int $spd
     * @return BtTempPassiveTypeSkill
     */
    public function setSpd($spd)
    {
        $this->spd = $spd;
        return $this;
    }

    /**
     * @return int
     */
    public function getSpd()
    {
        return $this->spd;
    }

    /**
     * @param int $targetCardId
     * @return BtTempPassiveTypeSkill
     */
    public function setTargetCardId($targetCardId)
    {
        $this->targetCardId = $targetCardId;
        return $this;
    }

    /**
     * @return int
     */
    public function getTargetCardId()
    {
        return $this->targetCardId;
    }
}

/**
 * ターゲットリスト
 *
 * Class BtTempTarget
 * @package Sof\ApiBundle\Lib
 */
class BtTempTarget{
    use BtTempData;

    //発動カードID
    private $activationCardId;

    //ターゲットカードId
    private $targetCardId;

    //スキルID
    private $skillId;

    //スキル系列ID
    private $skillSeriesId;

    //命中
    private $hit;

    //標的順
    private $targetOrder;

    //補正効果値（100％のみ）
    private $correctionEffectValue;

    public function __construct()
    {
        $this->setId();

        $this->activationCardId = 0;
        $this->targetCardId = 0;
        $this->skillId = 0;
        $this->skillSeriesId = 0;
        $this->hit = 0;
        $this->targetOrder = 0;
        $this->correctionEffectValue = 0;
    }

    /**
     * @param int $activationCardId
     * @return BtTempTarget
     */
    public function setActivationCardId($activationCardId)
    {
        $this->activationCardId = $activationCardId;
        return $this;
    }

    /**
     * @return int
     */
    public function getActivationCardId()
    {
        return $this->activationCardId;
    }

    /**
     * @param int $correctionEffectValue
     * @return BtTempTarget
     */
    public function setCorrectionEffectValue($correctionEffectValue)
    {
        $this->correctionEffectValue = $correctionEffectValue;
        return $this;
    }

    /**
     * @return int
     */
    public function getCorrectionEffectValue()
    {
        return $this->correctionEffectValue;
    }

    /**
     * @param int $hit
     * @return BtTempTarget
     */
    public function setHit($hit)
    {
        $this->hit = $hit;
        return $this;
    }

    /**
     * @return int
     */
    public function getHit()
    {
        return $this->hit;
    }

    /**
     * @param int $skillId
     * @return BtTempTarget
     */
    public function setSkillId($skillId)
    {
        $this->skillId = $skillId;
        return $this;
    }

    /**
     * @return int
     */
    public function getSkillId()
    {
        return $this->skillId;
    }

    /**
     * @param int $skillSeriesId
     * @return BtTempTarget
     */
    public function setSkillSeriesId($skillSeriesId)
    {
        $this->skillSeriesId = $skillSeriesId;
        return $this;
    }

    /**
     * @return int
     */
    public function getSkillSeriesId()
    {
        return $this->skillSeriesId;
    }

    /**
     * @param int $targetCardId
     * @return BtTempTarget
     */
    public function setTargetCardId($targetCardId)
    {
        $this->targetCardId = $targetCardId;
        return $this;
    }

    /**
     * @return int
     */
    public function getTargetCardId()
    {
        return $this->targetCardId;
    }

    /**
     * @param int $targetOrder
     * @return BtTempTarget
     */
    public function setTargetOrder($targetOrder)
    {
        $this->targetOrder = $targetOrder;
        return $this;
    }

    /**
     * @return int
     */
    public function getTargetOrder()
    {
        return $this->targetOrder;
    }
}

/**
 * 巻き添えリスト
 *
 * Class BtTempCollateral
 * @package Sof\ApiBundle\Lib
 */
class BtTempCollateral{
    use BtTempData;

    //発動カードID
    private $activationCardId;

    //ターゲットカードId
    private $targetCardId;

    //スキルID
    private $skillId;

    //スキル系列ID
    private $skillSeriesId;

    //命中
    private $hit;

    //標的順
    private $targetOrder;

    //補正効果値（100％のみ）
    private $correctionEffectValue;

    public function __construct()
    {
        $this->setId();

        $this->activationCardId = 0;
        $this->targetCardId = 0;
        $this->skillId = 0;
        $this->skillSeriesId = 0;
        $this->hit = 0;
        $this->targetOrder = 0;
        $this->correctionEffectValue = 100;
    }

    /**
     * @param mixed $activationCardId
     * @return BtTempCollateral
     */
    public function setActivationCardId($activationCardId)
    {
        $this->activationCardId = $activationCardId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActivationCardId()
    {
        return $this->activationCardId;
    }

    /**
     * @param mixed $correctionEffectValue
     * @return BtTempCollateral
     */
    public function setCorrectionEffectValue($correctionEffectValue)
    {
        $this->correctionEffectValue = $correctionEffectValue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCorrectionEffectValue()
    {
        return $this->correctionEffectValue;
    }

    /**
     * @param mixed $hit
     * @return BtTempCollateral
     */
    public function setHit($hit)
    {
        $this->hit = $hit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHit()
    {
        return $this->hit;
    }

    /**
     * @param mixed $skillId
     * @return BtTempCollateral
     */
    public function setSkillId($skillId)
    {
        $this->skillId = $skillId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSkillId()
    {
        return $this->skillId;
    }

    /**
     * @param mixed $skillSeriesId
     * @return BtTempCollateral
     */
    public function setSkillSeriesId($skillSeriesId)
    {
        $this->skillSeriesId = $skillSeriesId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSkillSeriesId()
    {
        return $this->skillSeriesId;
    }

    /**
     * @param mixed $targetCardId
     * @return BtTempCollateral
     */
    public function setTargetCardId($targetCardId)
    {
        $this->targetCardId = $targetCardId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTargetCardId()
    {
        return $this->targetCardId;
    }

    /**
     * @param mixed $targetOrder
     * @return BtTempCollateral
     */
    public function setTargetOrder($targetOrder)
    {
        $this->targetOrder = $targetOrder;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTargetOrder()
    {
        return $this->targetOrder;
    }
}

/**
 * Class BtTempData
 * @package Sof\ApiBundle\Lib
 */
trait BtTempData {
    private static $autoIncrementId = 0;

    /**
     * @return int
     */
    public static function getAutoIncrementId()
    {
        return self::$autoIncrementId;
    }

    private $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    private function setId()
    {
        $this->id = ++self::$autoIncrementId;
    }
}

/**
 * Class BtTempDataCollection
 * @package Sof\ApiBundle\Lib
 */
class BtTempDataCollection
{

    protected $objects; // array
    protected $deletedObjects; // array
    protected $resetFlag;
    protected $numObjects;
    protected $iterateNum;

    public function __construct()
    {
        $this->resetIterator();
        $this->numObjects        = 0;
        $this->objects           = array();
        $this->deletedObjects    = array();
    }


    public function add($obj)
    {
        $this->objects[] = $obj;
        $this->numObjects++;
    }


    public function next()
    {
        $num = ($this->currentObjIsLast()) ? 0 : $this->iterateNum + 1;
        $this->iterateNum = $num;
    }


    public function isOdd()
    {
        return $this->iterateNum%2==1;
    }


    public function isEven()
    {
        return $this->iterateNum%2==0;
    }

    /*
        get an obj based on one of it's properties.
        i.e. a User obj with the property 'username' and a value of 'someUser'
            can be retrieved by Collection::getByProperty('username', 'someUser')
        --    assumes that the obj has a getter method
            with the same spelling as the property, i.e. getUsername()
    */
    public function getByProperty($propertyName, $property)
    {
        $methodName = "get".ucwords($propertyName);

        foreach ($this->objects as $key => $obj) {
            if ($obj->{$methodName}() == $property) {
                return $this->objects[$key];
            }
        }
        return false;
    }

    /*
        alias for getByProperty()
    */
    public function findByProperty($propertyName, $property)
    {
        return $this->getByProperty($propertyName, $property);
    }


    /*
        get an objects number based on one of it's properties.
        i.e. a User obj with the property 'username' and a value of 'someUser'
            can be retrieved by Collection::getByProperty('username', 'someUser')
        --    assumes that the obj has a getter method
            with the same spelling as the property, i.e. getUsername()
    */
    public function getObjNumByProperty($propertyName, $property)
    {
        $methodName = "get".ucwords($propertyName);

        foreach ($this->objects as $key => $obj) {
            if ($obj->{$methodName}() == $property) {
                return $key;
            }
        }
        return false;
    }

    /*
        get the number of objects that have a property
            with a value matches the given value
        i.e. if there are objs with a property of 'verified' set to 1
            the number of these objects can be retrieved by:
                Collection::getNumObjectsWithProperty('verified', 1)
        --    assumes that the obj has a getter method
            with the same spelling as the property, i.e. getUsername()
    */
    public function getNumObjectsWithProperty($propertyName, $value)
    {
        $methodName = "get".ucwords($propertyName);
        $count = 0;
        foreach ($this->objects as $key => $obj) {
            if ($obj->{$methodName}() == $value) {
                $count++;
            }
        }
        return $count;
    }

    /*
        remove an obj based on one of it's properties.
        i.e. a User obj with the property 'username' and a value of 'someUser'
            can be removed by Collection::removeByProperty('username', 'someUser')
        --    assumes that the obj has a getter method
            with the same spelling as the property, i.e. getUsername()
    */
    public function removeByProperty($propertyName, $property)
    {
        $methodName = "get".ucwords($propertyName);

        foreach ($this->objects as $key => $obj) {
            if ($obj->{$methodName}() == $property) {
                $this->deletedObjects[] = $this->objects[$key];
                unset($this->objects[$key]);
                // reindex array & subtract 1 from numObjects
                $this->objects = array_values($this->objects);
                $this->numObjects--;
                $this->iterateNum = ($this->iterateNum >= 0) ? $this->iterateNum - 1 : 0;
                return true;
            }
        }
        return false;
    }


    public function currentObjIsFirst()
    {
        return ($this->iterateNum == 0);
    }


    public function currentObjIsLast()
    {
        return (($this->numObjects-1) == $this->iterateNum);
    }


    public function getObjNum($num)
    {
        return (isset($this->objects[$num])) ? $this->objects[$num] : false;
    }


    public function getLast()
    {
        return $this->objects[$this->numObjects-1];
    }


    public function removeCurrent()
    {
        $this->deletedObjects[] = $this->objects[$this->iterateNum];
        unset($this->objects[$this->iterateNum]);
        // reindex array & subtract 1 from iterator
        $this->objects = array_values($this->objects);

        if ($this->iterateNum == 0) { // if deleting 1st object
            $this->resetFlag = true;
        } elseif ($this->iterateNum > 0) {
            $this->iterateNum--;
        } else {
            $this->iterateNum = 0;
        }
        $this->numObjects--;
    }


    public function removeLast()
    {
        $this->deletedObjects[] = $this->objects[$this->numObjects-1];
        unset($this->objects[$this->numObjects-1]);
        $this->objects = array_values($this->objects);
        // if iterate num is set to last object
        if ($this->iterateNum == $this->numObjects-1) {
            $this->resetIterator();
        }
        $this->numObjects--;
    }


    public function removeAll()
    {
        $this->deletedObjects = array_merge($this->deletedObjects, $this->objects);
        $this->objects = array();
        $this->numObjects = 0;
    }

    /*
        sort the objects by the value of each objects property

        $type:
            r    regular, ascending
            rr    regular, descending'
            n    numeric, ascending
            nr    numeric, descending
            s    string, ascending
            sr    string, descending
    */
    public function sortByProperty($propName, $type='r')
    {
        $tempArray = array();
        $newObjects = array();

        while ($obj = $this->iterate()) {
            $tempArray[] = call_user_func(array($obj, 'get'.ucwords($propName)));
        }

        switch($type)
        {
            case 'r':
                asort($tempArray);
                break;
            case 'rr':
                arsort($tempArray);
                break;
            case 'n':
                asort($tempArray, SORT_NUMERIC);
                break;
            case 'nr':
                arsort($tempArray, SORT_NUMERIC);
                break;
            case 's':
                asort($tempArray, SORT_STRING);
                break;
            case 'sr':
                arsort($tempArray, SORT_STRING);
                break;
            default:
                throw new SofApiException(
                    'Collection->sortByProperty():
                    illegal sort type "'.$type.'"'
                );
        }

        foreach ($tempArray as $key => $val) {
            $newObjects[] = $this->objects[$key];
        }
        $this->objects = $newObjects;
    }


    public function isEmpty()
    {
        return ($this->numObjects == 0);
    }


    public function getCurrent()
    {
        return $this->objects[$this->iterateNum];
    }


    public function setCurrent($obj)
    {
        $this->objects[$this->iterateNum] = $obj;
    }


    public function getObjectByIterateNum($iterateNum)
    {
        return (
        isset($this->objects[$iterateNum])
            ? $this->objects[$iterateNum]
            : false
        );
    }


    public function iterate()
    {
        if ($this->iterateNum < 0) {
            $this->iterateNum = 0;
        }
        if ($this->resetFlag) {
            $this->resetFlag = false;
        } else {
            $this->iterateNum++;
        }
        if (    $this->iterateNum == $this->numObjects
            || !isset($this->objects[$this->iterateNum])
        ) {
            $this->resetIterator();
            return false;
        }

        return $this->getCurrent();
    }


    public function resetIterator()
    {
        $this->iterateNum = 0;
        $this->resetFlag = true;
    }


    public function __toString()
    {
        $str = '';
        foreach ($this->objects as $obj) {
            $str .= '--------------------------<br />'.$obj.'<br />';
        }
        return $str;
    }


    ####################   GETTERS

    public function getDeletedObjects()
    {
        return $this->deletedObjects;
    }

    public function getIterateNum()
    {
        return $this->iterateNum;
    }

    public function getNumObjects()
    {
        return $this->numObjects;
    }

    ####################   SETTERS

    public function setDeletedObjects($key, $val)
    {
        $this->deletedObjects[$key] = $val;
    }

    public function resetDeletedObjects()
    {
        $this->deletedObjects = array();
    }
}