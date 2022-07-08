<?php

namespace BeeAZ\NapThe;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use BeeAZ\NapThe\command\NapTheCommand;
use BeeAZ\NapThe\command\TopNapThe;
use BeeAZ\NapThe\EventListener;
use SQLite3;

class Main extends PluginBase implements Listener{
  
  public $cfg;
  
  public $db;
  
  public function onEnable() : void{
         $this->saveDefaultConfig();
         $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
         $this->cfg = $this->getConfig();
         $this->getServer()->getCommandMap()->register("napthe", new NapTheCommand($this));
         $this->getServer()->getCommandMap()->register("topnapthe", new TopNapThe($this));
         $this->db = new SQLite3($this->getDataFolder()."dulieu.db");
         $this->db->exec("CREATE TABLE IF NOT EXISTS dulieu (donater TEXT PRIMARY KEY NOT NULL, data INTEGER default 0 NOT NULL);");
         }
         
  public function onDisable(): void{
         $this->db->close();
         }
 
     
     
  public function top(){
              $count = 10;
              $top = 0;
              $sql = $this->db->prepare("SELECT donater,data FROM dulieu ORDER BY data DESC LIMIT 10");
              $result = $sql->execute();
              $list = "";
              while($e = $result->fetchArray(SQLITE3_ASSOC)) {
              $top++;
              $list .= "§c§l↣TOP {$top}. §d{$e["donater"]} : §a{$e["data"]}". "\n";
        }
        return $list;
  }
}