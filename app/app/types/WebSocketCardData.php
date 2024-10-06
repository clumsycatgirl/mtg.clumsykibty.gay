<?php

namespace App\Types;
use Lib\Systems\Traits\GettersTrait;

class WebSocketCardData {
    private string $uuid;
    private string $multiverse_id;
    private string $name;
    private string $mana_cost;
    private string $power;
    private string $toughness;
    private string $text;

    public function __construct(mixed $data) {
        $this->uuid = $data->cardUuid;
        $this->multiverse_id = $data->multiverseId;
        $this->name = $data->name;
        $this->mana_cost = $data->manaCost;
        $this->power = $data->power;
        $this->toughness = $data->toughness;
        $this->text = $data->text;
    }

    public function uuid(): string {
        return $this->uuid;
    }
    public function multiverse_id(): string {
        return $this->multiverse_id;
    }
    public function name(): string {
        return $this->name;
    }
    public function mana_cost(): string {
        return $this->mana_cost;
    }
    public function power(): string {
        return $this->power;
    }
    public function toughness(): string {
        return $this->toughness;
    }
    public function text(): string {
        return $this->text;
    }

    public function src(): string {
        return "https://gatherer.wizards.com/Handlers/Image.ashx?multiverseid={$this->multiverse_id}&type=card";
    }

    public function to_array(): array {
        $result = [];
        foreach ($this as $key => $value) {
            $result[str_replace('_', '-', $key)] = $value;
        }
        $result['src'] = $this->src();
        $result['back-src'] = '/assets/images/back.webp';
        return $result;
    }
}
