<?php

namespace App\Models\DataClasses;

/**
 * Class Tokenidentifiers
 *
 * Represents a Tokenidentifiers in the system.
 */
class Tokenidentifiers {
    private int|null $id;
    private string|null $card_kingdom_etched_id;
    private string|null $card_kingdom_foil_id;
    private string|null $card_kingdom_id;
    private string|null $cardsphere_foil_id;
    private string|null $cardsphere_id;
    private string|null $mcm_id;
    private string|null $mcm_meta_id;
    private string|null $mtg_arena_id;
    private string|null $mtgjson_foil_version_id;
    private string|null $mtgjson_non_foil_version_id;
    private string|null $mtgjson_v4_id;
    private string|null $mtgo_foil_id;
    private string|null $mtgo_id;
    private string|null $multiverse_id;
    private string|null $scryfall_card_back_id;
    private string|null $scryfall_id;
    private string|null $scryfall_illustration_id;
    private string|null $scryfall_oracle_id;
    private string|null $tcgplayer_etched_product_id;
    private string|null $tcgplayer_product_id;
    private string|null $uuid;

    /**
     * Constructor.
     *
     * @param array $array The row from the database.
     */
    public function __construct(array $array) {
        $this->id = $array['id'] ?? null;
        $this->card_kingdom_etched_id = $array['cardKingdomEtchedId'] ?? null;
        $this->card_kingdom_foil_id = $array['cardKingdomFoilId'] ?? null;
        $this->card_kingdom_id = $array['cardKingdomId'] ?? null;
        $this->cardsphere_foil_id = $array['cardsphereFoilId'] ?? null;
        $this->cardsphere_id = $array['cardsphereId'] ?? null;
        $this->mcm_id = $array['mcmId'] ?? null;
        $this->mcm_meta_id = $array['mcmMetaId'] ?? null;
        $this->mtg_arena_id = $array['mtgArenaId'] ?? null;
        $this->mtgjson_foil_version_id = $array['mtgjsonFoilVersionId'] ?? null;
        $this->mtgjson_non_foil_version_id = $array['mtgjsonNonFoilVersionId'] ?? null;
        $this->mtgjson_v4_id = $array['mtgjsonV4Id'] ?? null;
        $this->mtgo_foil_id = $array['mtgoFoilId'] ?? null;
        $this->mtgo_id = $array['mtgoId'] ?? null;
        $this->multiverse_id = $array['multiverseId'] ?? null;
        $this->scryfall_card_back_id = $array['scryfallCardBackId'] ?? null;
        $this->scryfall_id = $array['scryfallId'] ?? null;
        $this->scryfall_illustration_id = $array['scryfallIllustrationId'] ?? null;
        $this->scryfall_oracle_id = $array['scryfallOracleId'] ?? null;
        $this->tcgplayer_etched_product_id = $array['tcgplayerEtchedProductId'] ?? null;
        $this->tcgplayer_product_id = $array['tcgplayerProductId'] ?? null;
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
     * Get the card_kingdom_etched_id.
     *
     * @return string|null
     */
    public function card_kingdom_etched_id(): string|null {
        return $this->card_kingdom_etched_id;
    }

    /**
     * Get the card_kingdom_foil_id.
     *
     * @return string|null
     */
    public function card_kingdom_foil_id(): string|null {
        return $this->card_kingdom_foil_id;
    }

    /**
     * Get the card_kingdom_id.
     *
     * @return string|null
     */
    public function card_kingdom_id(): string|null {
        return $this->card_kingdom_id;
    }

    /**
     * Get the cardsphere_foil_id.
     *
     * @return string|null
     */
    public function cardsphere_foil_id(): string|null {
        return $this->cardsphere_foil_id;
    }

    /**
     * Get the cardsphere_id.
     *
     * @return string|null
     */
    public function cardsphere_id(): string|null {
        return $this->cardsphere_id;
    }

    /**
     * Get the mcm_id.
     *
     * @return string|null
     */
    public function mcm_id(): string|null {
        return $this->mcm_id;
    }

    /**
     * Get the mcm_meta_id.
     *
     * @return string|null
     */
    public function mcm_meta_id(): string|null {
        return $this->mcm_meta_id;
    }

    /**
     * Get the mtg_arena_id.
     *
     * @return string|null
     */
    public function mtg_arena_id(): string|null {
        return $this->mtg_arena_id;
    }

    /**
     * Get the mtgjson_foil_version_id.
     *
     * @return string|null
     */
    public function mtgjson_foil_version_id(): string|null {
        return $this->mtgjson_foil_version_id;
    }

    /**
     * Get the mtgjson_non_foil_version_id.
     *
     * @return string|null
     */
    public function mtgjson_non_foil_version_id(): string|null {
        return $this->mtgjson_non_foil_version_id;
    }

    /**
     * Get the mtgjson_v4_id.
     *
     * @return string|null
     */
    public function mtgjson_v4_id(): string|null {
        return $this->mtgjson_v4_id;
    }

    /**
     * Get the mtgo_foil_id.
     *
     * @return string|null
     */
    public function mtgo_foil_id(): string|null {
        return $this->mtgo_foil_id;
    }

    /**
     * Get the mtgo_id.
     *
     * @return string|null
     */
    public function mtgo_id(): string|null {
        return $this->mtgo_id;
    }

    /**
     * Get the multiverse_id.
     *
     * @return string|null
     */
    public function multiverse_id(): string|null {
        return $this->multiverse_id;
    }

    /**
     * Get the scryfall_card_back_id.
     *
     * @return string|null
     */
    public function scryfall_card_back_id(): string|null {
        return $this->scryfall_card_back_id;
    }

    /**
     * Get the scryfall_id.
     *
     * @return string|null
     */
    public function scryfall_id(): string|null {
        return $this->scryfall_id;
    }

    /**
     * Get the scryfall_illustration_id.
     *
     * @return string|null
     */
    public function scryfall_illustration_id(): string|null {
        return $this->scryfall_illustration_id;
    }

    /**
     * Get the scryfall_oracle_id.
     *
     * @return string|null
     */
    public function scryfall_oracle_id(): string|null {
        return $this->scryfall_oracle_id;
    }

    /**
     * Get the tcgplayer_etched_product_id.
     *
     * @return string|null
     */
    public function tcgplayer_etched_product_id(): string|null {
        return $this->tcgplayer_etched_product_id;
    }

    /**
     * Get the tcgplayer_product_id.
     *
     * @return string|null
     */
    public function tcgplayer_product_id(): string|null {
        return $this->tcgplayer_product_id;
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
