<?php

declare(strict_types=1);

namespace Terpz710\EasyWarps\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use Terpz710\EasyWarps\Main;

class DeleteWarpCommand extends Command {

    private $plugin;

    public function __construct(Plugin $plugin) {
        parent::__construct("delwarp", "Delete a set warp", "/delwarp <warpName>");
        $this->plugin = $plugin;
        $this->setPermission("easywarp.deletewarp");
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if (count($args) === 1) {
            $warpName = strtolower($args[0]);

            if ($this->warpExists($warpName)) {
                if ($sender->hasPermission("easywarp.deletewarp)){
                    $this->deleteWarp($warpName);
                    $sender->sendMessage(TextFormat::GREEN . "Warp '$warpName' deleted!");
                    return true;
                } else {
                    $sender->sendMessage(TextFormat::RED . "You do not have permission to delete warp '$warpName'.");
                }
            } else {
                $sender->sendMessage(TextFormat::RED . "Warp '$warpName' does not exist.");
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "Usage: /delwarp <warpName>");
        }

        return false;
    }

    private function warpExists(string $warpName): bool {
        $config = new Config($this->plugin->getDataFolder() . "EasyWarps.yml", Config::YAML);
        $warps = $config->get("warps", []);

        return isset($warps[$warpName]);
    }

    private function deleteWarp(string $warpName): void {
        $config = new Config($this->plugin->getDataFolder() . "EasyWarps.yml", Config::YAML);
        $warps = $config->get("warps", []);

        if (isset($warps[$warpName])) {
            unset($warps[$warpName]);
            $config->set("warps", $warps);
            $config->save();
        }
    }
}
