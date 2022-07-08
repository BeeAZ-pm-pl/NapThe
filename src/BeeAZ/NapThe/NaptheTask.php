<?php
namespace BeeAZ\NapThe;

use pocketmine\scheduler\Task;

class NaptheTask extends Task{
  
  private $plugin;
  private $post = [];
  private $player;
  private $telco;
  private $code;
  private $serial;
  private $amount;
  private $request_id;
  
  public function __construct($plugin, $player, string $nha_mang, string $code, string $serial, string $amount, string $request_id){
         $this->plugin = $plugin;
         $this->player = $player;
         $this->telco = strtoupper($nha_mang);
         $this->code = $code;
         $this->serial = $serial;
         $this->amount = $amount;
         $this->request_id = $request_id;
         $this->post["telco"] = strtoupper($nha_mang);
         $this->post["code"] = $code;
         $this->post["serial"] = $serial;
         $this->post["amount"] = $amount;
         $this->post["partner_id"] = $this->plugin->cfg->get("ID");
         $this->post["sign"] = md5($this->plugin->cfg->get("KEY").$code.$serial);
         $this->post["command"] = "charging";
         $this->post["request_id"] = intval($request_id);
        }
  
  public function onRun() : void{
         $curl = curl_init("https://".$this->plugin->cfg->get("WEBSITE")."/chargingws/v2");
         curl_setopt($curl, CURLOPT_POST, true);
         curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->post));
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($curl, CURLOPT_HEADER, false);
         curl_setopt($curl, CURLINFO_HEADER_OUT, true);
         curl_setopt($curl, CURLOPT_TIMEOUT, 120);
         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
         $result = json_decode(curl_exec($curl), true);
         var_dump($result);
         if(isset($result)){
         switch($result["status"]){
               case 99:
               if($this->player->isOnline()){
               $this->player->sendPopup($this->plugin->cfg->get("STATUS"));
               $this->plugin->getScheduler()->scheduleDelayedTask(new NaptheTask($this->plugin, $this->player, $this->telco, $this->code, $this->serial, $this->amount, $this->request_id), 25);
               }
               break;
               case 1:
               if($this->player->isOnline()){
               $this->theDung($this->player);
               }
               break;
               case 2:
               if($this->player->isOnline()){
               $this->theSaiMenhGia($this->player);
               }
               break;
               case 3:
               if($this->player->isOnline()){
               $this->theLoi($this->player);
               }
               break;
               case 4:
               if($this->player->isOnline()){
               $this->baoTri($this->player);
               }
               break;   
               default:
               $this->theLoi($this->player);
               break;
               }
         }else{
         $this->plugin->getLogger()->notice("Lỗi Nạp Thẻ Do ID Và Key Nhập Sai Hoặc Server Chưa Mở Port Không Thể Kết Nối");
         $this->player->sendMessage("Nạp Thẻ Lỗi Hãy Báo Admin");
         }
     }
  
  private function theDung($player){
          if($this->player->isOnline()){
          $sql = $this->plugin->db->prepare("UPDATE dulieu SET data = data + :amount WHERE donater = :name");
          $sql->bindValue(":amount", $this->amount);
          $sql->bindValue(":name", strtolower($player->getName()));
          $sql->execute();
          $this->plugin->cfg->set("DOANHTHU", $this->plugin->cfg->get("DOANHTHU") + $this->amount);
          $this->plugin->getServer()->broadcastMessage(str_replace(["{NAME}", "{AMOUNT}"], [strtolower($player->getName()), $this->amount], $this->plugin->cfg->get("BROADCAST")));
          $api = $this->plugin->getServer()->getPluginManager()->getPlugin($this->plugin->cfg->get("PLUGIN"));
          $function = $this->plugin->cfg->get("FUNCTION");
          $rate = $this->plugin->cfg->get("RATE");
          $bonus = $this->plugin->cfg->get("BONUS");
          $api->$function($player, ($this->amount / $rate) * $bonus);
          }
  }
  
  private function theSaiMenhGia($player){
      if($this->player->isOnline()){
          $sql = $this->plugin->db->prepare("UPDATE dulieu SET data = data + :amount WHERE donater = :name");
          $sql->bindValue(":amount", $this->amount/2);
          $sql->bindValue(":name", strtolower($player->getName()));
          $sql->execute();
          $this->plugin->cfg->set("DOANHTHU", $this->plugin->cfg->get("DOANHTHU") + $this->amount);
          $this->plugin->getServer()->broadcastMessage(str_replace(["{NAME}", "{AMOUNT}"], [strtolower($player->getName()), $this->amount/2], $this->plugin->cfg->get("BROADCAST-SAIMENHGIA")));
          $api = $this->plugin->getServer()->getPluginManager()->getPlugin($this->plugin->cfg->get("PLUGIN"));
          $function = $this->plugin->cfg->get("FUNCTION");
          $rate = $this->plugin->cfg->get("RATE");
          $bonus = $this->plugin->cfg->get("BONUS");
          $api->$function($player, (($this->amount / $rate) / 2) * $bonus);
       }
  }
  
  private function theLoi($player){
         if($player->isOnline()){
         $player->sendMessage($this->plugin->cfg->get("THELOI"));
         }
  }
  
  private function baoTri($player){
         if($player->isOnline()){
         $player->sendMessage($this->plugin->cfg->get("BAOTRI"));
         }
     }
}
