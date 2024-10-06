<?php

namespace App\Models\DataClasses;

/**
 * Class Cardpurchaseurls
 *
 * Represents a Cardpurchaseurls in the system.
 */
class Cardpurchaseurls {
    private int|null $id;
    private string|null $card_kingdom;
    private string|null $card_kingdom_etched;
    private string|null $card_kingdom_foil;
    private string|null $cardmarket;
    private string|null $tcgplayer;
    private string|null $tcgplayer_etched;
    private string|null $uuid;

    /**
     * Constructor.
     *
     * @param array $array The row from the database.
     */
    public function __construct(array $array) {
        $this->id = $array['id'] ?? null;
        $this->card_kingdom = $array['cardKingdom'] ?? null;
        $this->card_kingdom_etched = $array['cardKingdomEtched'] ?? null;
        $this->card_kingdom_foil = $array['cardKingdomFoil'] ?? null;
        $this->cardmarket = $array['cardmarket'] ?? null;
        $this->tcgplayer = $array['tcgplayer'] ?? null;
        $this->tcgplayer_etched = $array['tcgplayerEtched'] ?? null;
        $this->uuid = $array['uuid'] ?? null;
    }

    /**
     * Get the id.
     *
     * @return int|null
     */
    public function id(): int|null {
        return $this->id;
    }

    /**
     * Get the card_kingdom.
     *
     * @return string|null
     */
    public function card_kingdom(): string|null {
        return $this->card_kingdom;
    }

    /**
     * Get the card_kingdom_etched.
     *
     * @return string|null
     */
    public function card_kingdom_etched(): string|null {
        return $this->card_kingdom_etched;
    }

    /**
     * Get the card_kingdom_foil.
     *
     * @return string|null
     */
    public function card_kingdom_foil(): string|null {
        return $this->card_kingdom_foil;
    }

    /**
     * Get the cardmarket.
     *
     * @return string|null
     */
    public function cardmarket(): string|null {
        return $this->cardmarket;
    }

    /**
     * Get the tcgplayer.
     *
     * @return string|null
     */
    public function tcgplayer(): string|null {
        return $this->tcgplayer;
    }

    /**
     * Get the tcgplayer_etched.
     *
     * @return string|null
     */
    public function tcgplayer_etched(): string|null {
        return $this->tcgplayer_etched;
    }

    /**
     * Get the uuid.
     *
     * @return string|null
     */
    public function uuid(): string|null {
        return $this->uuid;
    }

}
