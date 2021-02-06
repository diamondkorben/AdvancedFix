<?php

use UrHuckle\AdvancedFix\commands\FixCommand;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase {
    public function onEnable(){
        if(!is_dir($this->getDataFolder())){
            mkdir($this->getDataFolder());
        }
        $this->saveDefaultConfig();
        $this->getLogger()->info("Enabled");
    }
}
