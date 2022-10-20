<?php

namespace FRashkar\TopKillLeaderBoard\entity;

use pocketmine\player\Player;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\Ageable;
use pocketmine\entity\EntitySizeInfo;
use NoobMCBG\TopMoneyLeaderBoard\TopMoneyLeaderBoard;

class TopMoneyEntity extends Human implements Ageable {
    
	private $baby = false;
	
	protected function getInitialSizeInfo() : EntitySizeInfo {
		return new EntitySizeInfo(0, 0);
	}

	public function getName() : string {
		return "TopKillLeaderBoard";
	}
    
    public function isBaby() : bool {
    	return $this->baby;
    }

	public function initEntity(CompoundTag $nbt) : void {
		parent::initEntity($nbt);
		$this->setNameTagAlwaysVisible(true);
		$this->setScale(0.0000000000000000000000000000000001);
	}

	public function attack(EntityDamageEvent $source) : void {
		$source->cancel();
	}
}