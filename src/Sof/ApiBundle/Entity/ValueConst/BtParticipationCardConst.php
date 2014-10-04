<?php
namespace Sof\ApiBundle\Entity\ValueConst;
use Sof\ApiBundle\Entity\BaseEntity;

class BtParticipationCardConst extends BaseEntity
{
    //affiliation_camp_id 所属陣営ID
    const CAMP_PLAYER = 0;//Player陣営に所属しています
    const CAMP_ENEMY  = 1;//Enemy陣営に所属しています

    //attribute 属性
    const ATTRIBUTE_FIRE  = 1;
    const ATTRIBUTE_EARTH = 2;
    const ATTRIBUTE_WIND  = 3;
    const ATTRIBUTE_WATER = 4;
}
