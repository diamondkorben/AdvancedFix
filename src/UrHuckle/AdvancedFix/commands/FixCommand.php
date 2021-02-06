<?php

use UrHuckle\AdvancedFix\Loader;
use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;
use onebone\economyapi\EconomyAPI;

class FixCommand extends PluginCommand {

    private $plugin;

    /**
     * constructor
     * @ param string $name
     * @ param Loader $plugin
     */
    public function __construct(string $name, Loader $plugin){
        parent::__construct($name, $plugin);
        $this->setDescription("Access to fix command.");
        $this->setUsage("/fix [all:hand]");
        $this->setPermission("af.command.use");
        $this->plugin = $plugin;
    }
    public function execute(CommandSender $sender, string $alias, array $args): bool {
        if(!$sender->hasPermission("af.command.use")){
            return true;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(C::RED . "You can only run this command ingame");
        }
        if($this->plugin->getConfig()->get("economy.repair") == true){
            if($args[0] == "hand"){
                if(!$sender->hasPermission("af.command.hand")){
                    $sender->sendMessage(C::RED . "You do not have permission to run this command.");
                    return true;
                }
                $index = $sender->getInventory()->getHeldItemIndex();
                $item = $sender->getInventory()->getItem($index);
                if(!$this->plugin->isRepairable($item)){
                    $sender->sendMessage(TextFormat::RED . "[Error] This item can't be repaired!");
                    return true;
                }
                $cost = $this->plugin->getConfig()->get("economy.cost");
                if(EconomyAPI::getInstance()->myMoney($sender->getName()) >= $cost) {
                    if($item->getDamage() > 0){
                        EconomyAPI::getInstance()->reduceMoney($sender->getName(), $cost);
                        $sender->getInventory()->setItem($index, $item->setDamage(0));
                        $sender->sendMessage(C::GREEN . "Item Successfully repaired.");
                    }else {
                        $sender->sendMessage(TextFormat::RED . "Item does not have any damage");
                    }
                }else{
                    $sender->sendMessage("you do not have " . $cost . " to fix this item");
                }
            }elseif ($args[0] == "all"){
                if(!$sender->hasPermission("af.command.hand")){
                    $sender->sendMessage(C::RED . "You do not have permission to run this command");
                    return true;
                }
                $cost = $this->plugin->getConfig()->get("economy.cost");
                foreach($sender->getInventory()->getContents() as $index => $item){
                    if($this->plugin->isRepairable($item)){
                        if($item->getDamage() > 0){
                            if(EconomyAPI::getInstance()->myMoney($sender->getName()) >= $cost) {
                                $sender->getInventory()->setItem($index, $item->setDamage(0));
                                EconomyAPI::getInstance()->reduceMoney($sender->getName(), $cost);
                            }else{
                                $sender->sendMessage(C::RED . "You ran out of money during the fixing process");
                            }
                        }
                    }
                }
                foreach($sender->getArmorInventory()->getContents() as $index => $item){
                    if($this->plugin->isRepairable($item)){
                        if($item->getDamage() > 0){
                            if(EconomyAPI::getInstance()->myMoney($sender->getName()) >= $cost) {
                                $sender->getArmorInventory()->setItem($index, $item->setDamage(0));
                                EconomyAPI::getInstance()->reduceMoney($sender->getName(), $cost);
                            }else{
                                $sender->sendMessage(C::RED . "You ran out of money during the fixing process");
                            }
                        }
                    }
                }
                $sender->sendMessage(C::GREEN . "All items in your inventory have been fixed.");
            }
        }elseif ($this->plugin->getConfig()->get("xp.repair") == true){
            //$calculate = $this->plugin->getConfig()->get("xp.cost");
        }
    }
}
