<?php

namespace FRashkar\TopKillLeaderBoard\commands;

use pocketmine\player\Player;
use pocketmine\command\command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use FRashkar\TopKillLeaderBoard\TopKillLeaderBoard;

class TopKillLeaderBoardCommands extends Command implements PluginOwned {

	private TopKillLeaderBoard $plugin;

	public function __construct(TopKillLeaderBoard $plugin){
		$this->plugin = $plugin;
		parent::__construct("settopkill", "Set TOP Kill Leaderboard Commands", null, ["stk"]);
		$this->setPermission("settopkill.command");
	}

	public function execute(CommandSender $sender, string $label, array $args){
		if(!$sender->hasPermission("settopkill.command")){
			$sender->sendMessage("§cYou no permission to use this command !");
			return true;
		}
		if(!$sender instanceof Player){
			$sender->sendMessage("§cPlease use command in-game !");
			return true;
		}
		if(isset($args[0])){
			if(!is_numeric($args[0])){
				$sender->sendMessage("§cUsage:§7 /settopkill <int: slot top>");
				return true;
			}
			$this->getOwningPlugin()->spawnLeaderboard($sender, (int)$args[0]);
			$sender->sendMessage("§aSuccessfully spawn leaderboard top kill !");
		}else{
			$sender->sendMessage("§cUsage:§7 /settopmoney <int: slot top>");
		}
	}

	public function getOwningPlugin() : TopKillLeaderBoard {
		return $this->plugin;
	}
}