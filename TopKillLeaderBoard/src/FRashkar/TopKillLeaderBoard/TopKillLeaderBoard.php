<?php

namespace FRashkar\TopKillLeaderBoard;

use pocketmine\entity\Skin;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\world\World;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\PlayerDeathEvent;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityDeathEvent;
use pocketmine\entity\EntityDamageByEntityEvent;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use FRashkar\TopKillLeaderBoard\entity\TopKillEntity;
use FRashkar\TopKillLeaderBoard\commands\TopKillLeaderBoardCommands;

class TopKillLeaderBoard extends PluginBase implements Listener {

	public static $instance;

    public static function getInstance() : self {
        return self::$instance;
    }
    
    public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->getServer()->getCommandMap()->register("/settopkill", new TopKillLeaderBoardCommands($this));
        EntityFactory::getInstance()->register(TopKillEntity::class, function (World $world, CompoundTag $nbt): TopKillEntity {
            return new TopKillEntity(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ['TopKillLeaderBoard', 'TopKill']);
        self::$instance = $this;
    }
	
    public function registerPlayer(Player $player): void;
	
    public function addKillPoints(Player $player, int $points = 1): void;
	
    public function getPlayerKillPoints(Player $player): int;
	
    public function onPlayerKill(PlayerDeathEvent $event){
	    $player = $event->getPlayer();
	    $cause = $player->getLastDamageCause();
	    if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$this->plugin->getTopKillLeaderBoard()->addKillPoints($damager, int);
			}
	    }
    }
	
    public function spawnLeaderboard(Player $player, int $slot): void {
		$entity = new TopKillEntity(Location::fromObject($player->getPosition(), $player->getPosition()->getWorld(), $player->getLocation()->getYaw(), $player->getLocation()->getPitch()), new Skin("Standard_Custom", str_repeat("\x00", 8192), "", "geometry.humanoid.custom"));
		$txt = "";
        $array = $cause;
	$top = 1;
        foreach($array as $name => $cause) {
            $txt .= str_replace(["{line}", "{name}", "{player}", "{display_name}", "{top}", "{cause}"], ["\n", $name, $name, $name, $top, $cause], strval($this->getConfig()->getAll()["leaderboard"]["format"]));
			$top++;
        }
        $entity->setNameTag("" . $this->getConfig()->getAll()["leaderboard"]["name"] . "\n" . $txt);
        $entity->setNameTagAlwaysVisible();
        $entity->spawnToAll();
    }
}
