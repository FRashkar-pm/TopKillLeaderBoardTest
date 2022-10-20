<?php

namespace FRashkar\TopKillLeaderBoard\entity;

use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\EntitySizeInfo;

class TopKillEntity extends Human {
	
	protected function getInitialSizeInfo() : EntitySizeInfo {
		return new EntitySizeInfo(0, 0);
	}

	public function getName() : string {
		return "TopKillLeaderBoard";
	}

	public function initEntity(CompoundTag $nbt) : void {
		parent::initEntity($nbt);
		$this->setScale(0.01);
		$this->setNameTagAlwaysVisible();
	}

	public function attack(EntityDamageEvent $source) : void {
		$source->cancel();
	}
}
