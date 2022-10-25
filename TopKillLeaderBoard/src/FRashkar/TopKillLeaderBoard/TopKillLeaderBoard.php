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
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityTeleportEvent;
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
	
    public function onEntityDamage(EntityDamageEvent $event): void
	{
		$victim = $event->getEntity();
		if (!$victim instanceof Player) {
			return;
		}
		if ($event instanceof EntityDamageByEntityEvent) {
			$damager = $event->getDamager();
			if (!$damager instanceof Player) {
				return;
			}
			if ($event->getFinalDamage() > $victim->getHealth()) {
				$damagerSession = $this->getSessionFor($damager);
				$victimSession = $this->getSessionFor($victim);
				$damagerSession->addKill();
				$victimSession->addDeath($damager);
				$kill = $damagerSession->getKill();
			}
			return;
		}
		if ($event->getFinalDamage() > $victim->getHealth()) {
			$session = $this->getSessionFor($victim);
			$session->addDeath();
		}
	}
	
    public function spawnLeaderboard(Player $player, int $slot): void {
		$entity = new TopKillEntity(Location::fromObject($player->getPosition(), $player->getPosition()->getWorld(), $player->getLocation()->getYaw(), $player->getLocation()->getPitch()), new Skin("Standard_Custom", str_repeat("\x00", 8192), "", "geometry.humanoid.custom"));
		$txt = "";
        $array = [];
	foreach(EntityDamageByEntityEvent::getInstance()->getFinalDamage() as $damagerSession => $kill){
		$array[mb_strtolower($damagerSession)] = $kill;
	}
	arsort($array);
	$array = array_slice($array, 0, $slot);
	$top = 1;
        foreach($array as $name => $kill) {
            $txt .= str_replace(["{line}", "{name}", "{player}", "{display_name}", "{top}", "{kills}"], ["\n", $name, $name, $name, $top, $kill], strval($this->getConfig()->getAll()["leaderboard"]["format"]));
			$top++;
        }
        $entity->setNameTag("" . $this->getConfig()->getAll()["leaderboard"]["name"] . "\n" . $txt);
        $entity->setNameTagAlwaysVisible();
        $entity->spawnToAll();
    }
}
