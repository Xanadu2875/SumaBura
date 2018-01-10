<?php

namespace xanadu2875\sumabura;

use pocketmine\{Player as P, plugin\PluginBase as PB, utils\Config as C, utils\Utils as U};
use pocketmine\event\{Listener as L, player\PlayerJoinEvent as PJE, player\PlayerDeathEvent as PDE, player\PlayerQuitEvent as PQE, entity\EntityDamageEvent as EDE, entity\EntityDamageByEntityEvent as EDBEE};  //一度やってみたかった...糞コードだけど許してね💛

class SumaBura extends PB implements L
{
  private $players = [];
  private $knockBackPower = 1.0;

  public function onLoad()
  {
    @mkdir($this->getDataFolder());

    $this->knockBackPower = (new C($this->getDataFolder() . "KnockBackPower.yml", C::YAML, ["KnockBackPower" => 1.0]))->get("KnockBackPower");

    if($this->checkUpdata())
    {
      $this->getServer()->getLogger()->notice("新しいバージョンがリリースされています！ ⇒ " . $this->getDescription()->getWebsite());
    }
  }

  public function onEnable()
  {
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }

  private function checkUpdata() : bool { return str_replace("\n", "",U::getURL("https://raw.githubusercontent.com/Xanadu2875/VersionManager/master/SumaBura.txt" . '?' . time() . mt_rand())) === $this->getDescription()->getVersion(); }

  /**
   * @priority HIGH
   */
  public function onED(EDE $event)
  {
    if(!$event->isCancelled())
      if(($player = $event->getEntity()) instanceof P)
      {
        if($event instanceof EDBEE) //この名前を使う時が来るとは露程にも思わなかった...kaiさん戻ってきて下しあ
        {
          $this->players[$player->getName()] += $event->getDamage();
          $event->setDamage(0);
          $event->setKnockBack($this->players[$player->getName()] * $this->knockBackPower);
          $player->sendMessage((string)$this->players[$player->getName()]);
        }
      }
  }

  /**
   * @priority HIGH
   */
  public function onPD(PDE $event)
  {
    $this->players[$event->getPlayer()->getName()] = 0;
  }

  public function onPJ(PJE $event) { $this->players[$event->getPlayer()->getName()] = 0; }

  public function onPQ(PQE $event) { unset($this->players[$event->getPlayer()->getName()]); }
}
