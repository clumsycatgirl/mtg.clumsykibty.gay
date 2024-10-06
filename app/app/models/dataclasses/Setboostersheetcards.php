<?php

namespace App\Models\DataClasses;

/**
 * Class Setboostersheetcards
 *
 * Represents a Setboostersheetcards in the system.
 */
class Setboostersheetcards {
    private int|null $id;
    private string|null $booster_name;
    private string|null $card_uuid;
    private mixed $card_weight;
    private string|null $set_code;
    private string|null $sheet_name;

    /**
     * Constructor.
     *
     * @param array $array The row from the database.
     */
    public function __construct(array $array) {
        $this->id = $array['id'] ?? null;
        $this->booster_name = $array['boosterName'] ?? null;
        $this->card_uuid = $array['cardUuid'] ?? null;
        $this->card_weight = $array['cardWeight'] ?? null;
        $this->set_code = $array['setCode'] ?? null;
        $this->sheet_name = $array['sheetName'] ?? null;
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
     * Get the booster_name.
     *
     * @return string|null
     */
    public function booster_name(): string|null {
        return $this->booster_name;
    }

    /**
     * Get the card_uuid.
     *
     * @return string|null
     */
    public function card_uuid(): string|null {
        return $this->card_uuid;
    }

    /**
     * Get the card_weight.
     *
     * @return mixed
     */
    public function card_weight(): mixed {
        return $this->card_weight;
    }

    /**
     * Get the set_code.
     *
     * @return string|null
     */
    public function set_code(): string|null {
        return $this->set_code;
    }

    /**
     * Get the sheet_name.
     *
     * @return string|null
     */
    public function sheet_name(): string|null {
        return $this->sheet_name;
    }

}
