<?php

namespace UrHuckle\AdvancedFix;

use UrHuckle\AdvancedFix\commands\FixCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\Tool;

class Loader extends PluginBase {
    public function onEnable(){
        if(!is_dir($this->getDataFolder())){
            mkdir($this->getDataFolder());
        }
        $this->saveDefaultConfig();
        $this->getLogger()->info("Enabled");
        if($this->getConfig()->get("economy.repair") == true && $this->getConfig()->get("xp.repair") == true){
            $this->getServer()->getPluginManager()->disablePlugin($this);
            $this->getServer()->error("You can only have 1 economy set too true");
        }
        if($this->getConfig()->get("economy.repair") == true){
            if(!$this->getConfig()->get("economy.cost") > 0){
                $this->getServer()->getPluginManager()->disablePlugin($this);
                $this->getServer()->error("Economy cost cannot be 0");
            }
        }
        if($this->getConfig()->get("xp.repair") == true){
            if(!$this->getConfig()->get("xp.cost") > 0){
                $this->getServer()->getPluginManager()->disablePlugin($this);
                $this->getServer()->error("XP cost cannot be 0");
            }
        }
    }
    public function isRepairable(Item $item): bool{
        return $item instanceof Tool || $item instanceof Armor;
    }
}
