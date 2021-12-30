<?php

namespace fernanACM\RGBArmor;

use fernanACM\RGBArmor\PluginUtils;
use fernanACM\RGBArmor\utils\FormImageFix;
use fernanACM\RGBArmor\FormsUI\CustomForm;
use fernanACM\RGBArmor\FormsUI\SimpleForm;
use fernanACM\RGBArmor\FormsUI\Form;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use pocketmine\event\Listener;

use pocketmine\item\Item;
use pocketmine\item\Armor;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

use pocketmine\utils\Config;
use pocketmine\color\Color;

class Rgb extends PluginBase implements Listener {

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        switch ($command->getName()) {
            case "rgb":
                if($sender instanceof Player)       {
                           $this->getRGB($sender);
                           PluginUtils::PlaySound($sender, "random.pop", 1, 1.5);
                     } else {
                             $sender->sendMessage("Use this command in-game");
                              return true;
                     }
            break;
        }
        return true;
    }


    public function getRGB($player){
        $form = new SimpleForm(function (Player $player, $data) {
            if ($data !== null) {
                switch ($data) {
                    case 0:
                        $this->getSimpleArmor($player);
                        PluginUtils::PlaySound($player, "hit.vines", 1, 1.5);
                    break;

                    case 1:
                        $this->getArmorGUI($player);
                        PluginUtils::PlaySound($player, "land.vines", 1, 1.5);
                    break;

                    case 2:
                        PluginUtils::PlaySound($player, "mob.fish.flop", 1, 1.5);
                    break;
                }
            }
        });
        $form->setTitle("§l§cRGB§b MENU");
        $form->setContent($this->getConfig()->get("MenuContent"));
        $form->addButton($this->getConfig()->get("ButtonSingle"),1,"https://i.imgur.com/FCgLqBq.png");
        $form->addButton($this->getConfig()->get("ButtonMultiple"),1,"https://i.imgur.com/vskph2R.png");
        $form->addButton($this->getConfig()->get("ButtonExit"),1,"https://i.imgur.com/yMeST4l.png");
        $player->sendForm($form);
    }

    public function getArmorGUI($player)
    {
        $form = new CustomForm(function (Player $player, $data) {
            if ($data !== null) {
                if (isset($data[0]) && isset($data[1]) && isset($data[2]) && isset($data[3])) {
                    $helm_rgb = explode(",", $data[0]);
                    $chest_rgb = explode(",", $data[1]);
                    $pants_rgb = explode(",", $data[2]);
                    $boots_rgb = explode(",", $data[3]);
                    if (count($helm_rgb) == 3 && count($chest_rgb) == 3 && count($pants_rgb) == 3 && count($boots_rgb) == 3) {
                        $this->makeHelmet($helm_rgb, $player);
                        $this->makeChest($chest_rgb, $player);
                        $this->makePants($pants_rgb, $player);
                        $this->makeBoots($boots_rgb, $player);
                    } else {
                        $player->sendMessage($this->getConfig()->get("Prefix") . $this->getConfig()->get("RGB-CODE"));
                    }
                } else {
                    $player->sendMessage($this->getConfig()->get("Prefix") . $this->getConfig()->get("Error"));
                }
            }
        });
        $form->setTitle("§l§2Armor Menu");
        $form->addInput("§eHelmet", "r, g, b");
        $form->addInput("§eChestplate", "r, g, b");
        $form->addInput("§ePants", "r, g, b");
        $form->addInput("§eBoots", "r, g, b");
        $player->sendForm($form);
    }

    public function getSimpleArmor($player)
    {
        $form = new CustomForm(function (Player $player, $data) {
            if ($data !== null) {
                if (isset($data[0])) {
                    $armor_rgb = explode(",", $data[0]);
                    if (count($armor_rgb) == 3) {
                        $this->makeHelmet($armor_rgb, $player);
                        $this->makeChest($armor_rgb, $player);
                        $this->makePants($armor_rgb, $player);
                        $this->makeBoots($armor_rgb, $player);
                    } else {
                        $player->sendMessage($this->getConfig()->get("Prefix") . $this->getConfig()->get("RGB-CODE"));
                    }
                } else {
                    $player->sendMessage($this->getConfig()->get("Prefix") . $this->getConfig()->get("Error"));
                }
            }
        });
        $form->setTitle("§l§2Armor Menu");
        $form->addInput("§eArmor color", "r, g, b");
        $player->sendForm($form);
    }

    public function makeHelmet(array $array, $player)
    {
        list($r, $g, $b) = $array;

        /** @var Armor $item */
        $item = ItemFactory::getInstance()->get(ItemIds::LEATHER_HELMET);
        $item->setCustomColor(new Color($r, $g, $b));
        $player->getInventory()->addItem($item);
    }

    public function makeChest(array $array, $player)
    {
        list($r, $g, $b) = $array;

        /** @var Armor $item */
        $item = ItemFactory::getInstance()->get(ItemIds::LEATHER_CHESTPLATE);
        $item->setCustomColor(new Color($r, $g, $b));
        $player->getInventory()->addItem($item);
    }

    public function makeBoots(array $array, $player)
    {
        list($r, $g, $b) = $array;

        /** @var Armor $item */
        $item = ItemFactory::getInstance()->get(ItemIds::LEATHER_BOOTS);
        $item->setCustomColor(new Color($r, $g, $b));
        $player->getInventory()->addItem($item);
    }

    public function makePants(array $array, $player)
    {
        list($r, $g, $b) = $array;

        /** @var Armor $item */
        $item = ItemFactory::getInstance()->get(ItemIds::LEATHER_PANTS);
        $item->setCustomColor(new Color($r, $g, $b));
        $player->getInventory()->addItem($item);
    }

    /**
     * @param $hex
     * @param bool $alpha
     * @return mixed
     *
     * Not used at this moment
     *
     */
    public function hexToRgb($hex, $alpha = false)
    {
        $hex = str_replace('#', '', $hex);
        $length = strlen($hex);
        $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
        $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
        $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
        if ($alpha) {
            $rgb['a'] = $alpha;
        }
        return $rgb;
    }
}
