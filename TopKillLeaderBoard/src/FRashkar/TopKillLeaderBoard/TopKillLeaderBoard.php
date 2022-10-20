<?php

namespace FRashkar\TopKillLeaderBoard;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\world\World;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\NameTag;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class TopKillLeaderBoard extends PluginBase implements Listener {

    public static $instance;

    public static function getInstance() : self {
        return self::$instance;
    }
    
    public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->setDefaultConfig();
        $this->getServer()->getCommandMap()->register("/settopkill", new TopKillLeaderBoardCommands($this));
        EntityFactory::getInstance()->register(TopKillEntity::class, function (World $world, CompoundTag $nbt): TopKillEntity {
            return new TopKillEntity(EnitityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ['TopKillLeaderBoard', 'TopKill']);
        self::$instance = $this;
    }

    public function spawnLeaderboard(Player $player, int $slot){
		$nbt = CompoundTag::create();
		$nbt->setTag("Name", new StringTag($player->getSkin()->getSkinId()));
		$nbt->setTag("Data", new ByteArrayTag($player->getSkin()->getSkinData()));
		$nbt->setTag("CapeData", new ByteArrayTag($player->getSkin()->getCapeData()));
		$nbt->setTag("GeometryName", new StringTag($player->getSkin()->getGeometryName()));
		$nbt->setTag("GeometryData", new ByteArrayTag($player->getSkin()->getGeometryData()));
		$entity = new TopKillEntity(Location::fromObject($player->getPosition(), $player->getPosition()->getWorld(), $player->getLocation()->getYaw() ?? 0, $player->getLocation()->getPitch() ?? 0), $player->getSkin(), $nbt);
		$txt = "";
        $array = [];
        foreach($array as $name => $kill){
            $txt .= str_replace(["{line}", "{name}", "{player}", "{display_name}", "{top}", "{kill}"], ["\n", $name, $name, $name, $top, $money], strval($this->getConfig()->getAll()["leaderboard"]["format"]));
           $top++;
        }
        $entity->setNameTag("" . $this->getConfig()->getAll()["leaderboard"]["name"] . "\n" . $txt);
        $entity->setNameTagAlwaysVisible(true);
        $entity->spawnToAll();
    }
}