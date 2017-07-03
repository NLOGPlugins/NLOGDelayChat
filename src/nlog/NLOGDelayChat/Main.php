<?php

namespace nlog\NLOGDelayChat;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerQuitEvent;

class Main extends PluginBase implements Listener{

	public $time, $chat, $count;
	
 	 public function onEnable(){
    	$this->getServer()->getPluginManager()->registerEvents($this, $this);
    	$this->getLogger()->notice("채팅 딜레이 (도배 방지) 플러그인입니다.");
    	$this->getLogger()->notice("Made by NLOG (nlog.kro.kr)");
    	
    	$this->count = [ ];
    	$this->time = [ ];
    	$this->chat = [ ];
 	 }
 	 
 	 public function onPlayerChat (PlayerChatEvent $ev) {
 	 	
 	 	$name = $ev->getPlayer()->getName();
 	 	$msg = md5(strtolower(TextFormat::clean($ev->getMessage())));
 	 	
 	 	if (!isset($this->time[$name])) {
 	 		$this->time[$name] = $this->nowTime();
 	 		return true;
 	 	}
 	 	
 	 	if (isset($this->time[$name])  or isset($this->chat[$name][$msg])) {
 	 		if ($this->nowTime() - $this->time[$name] <= 3) {
 	 			$ev->setCancelled(true);
 	 			
 	 			$this->time[$name] = $this->nowTime();
 	 			
 	 			if (!isset($this->count[$name])) {
 	 				$this->count[$name] = 1;
 	 			}else{
 	 				$this->count[$name] += 1;
 	 			}
 	 			
 	 			if ($this->count[$name] > 3) {
 	 				$ev->getPlayer()->kick("도배 방지를 위해 킥되었습니다.");
 	 				return true;
 	 			}
 	 			
 	 			$ev->getPlayer()->sendMessage("§o§b[ 알림 ]§7 3초 뒤에 다시 입력해주세요.");
 	 			return true;
 	 		}
 	 		if (isset($this->chat[$name][$msg])) {
 	 			$ev->setCancelled(true);
 	 			$ev->getPlayer()->sendMessage("§o§b[ 알림 ]§7 이미 입력하신 채팅 내용입니다.");
 	 			return true;
 	 		}else{
 	 			unset($this->count[$name]);
 	 			$this->time[$name] == $this->nowTime();
 	 			$this->chat[$name][$msg] = "";
 	 			return true;
 	 		}
 	 	}
 	 	$this->chat[$name][$msg] = "";
 	 }
 	 
 	 public function nowTime() {
 	 	$date = date("Y-m-d H:i:s");
 	 	$y = substr($date, 0, 4); //xxxx년
 	 	$m = substr($date, 5, 2); //xx월
 	 	$d = substr($date, 8, 2); //xx일
 	 	$h = substr($date, 11, 2); //xx시
 	 	$i = substr($date, 14, 2); //xx분
 	 	$s = substr($date, 17, 2); //xx초
 	 	return mktime($h, $i, $s, $m, $d, $y);
 	 }
 	 
 	 public function onPlayerQuitEvent (PlayerQuitEvent $ev) {
 	 	
 	 	$name = $ev->getPlayer()->getName();
 	 	
 	 	if (isset($this->chat[$name])) {
 	 		unset($this->chat[$name]);
 	 	}
 	 	if (isset($this->time[$name])) {
 	 		unset($this->time[$name]);
 	 	}
 	 	if (isset($this->count[$name])) {
 	 		unset($this->time[$name]);
 	 	}
 	 }
 	 
 	 
  }
?>