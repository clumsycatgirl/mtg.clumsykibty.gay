<?php

namespace App\Models\DataClasses;

/**
 * Class Setboostercontentweights
 *
 * Represents a Setboostercontentweights in the system.
 */
class Setboostercontentweights {
    private int|null $id;
    private int|null $booster_index;
    private string|null $booster_name;
    private int|null $booster_weight;
    private string|null $set_code;

    /**
     * Constructor.
     *
     * @param array $array The row from the database.
     */
    public function __construct(array $array) {
        $this->id = $array['id'] ?? null;
        $this->booster_index = $array['boosterIndex'] ?? null;
        $this->booster_name = $array['boosterName'] ?? null;
        $this->booster_weight = $array['boosterWeight'] ?? null;
        $this->set_code = $array['setCode'] ?? null;
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
     * Get the booster_index.
     *
     * @return int|null
     */
    public function booster_index(): int|null {
        return $this->booster_index;
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
     * Get the booster_weight.
     *
     * @return int|null
     */
    public function booster_weight(): int|null {
        return $this->booster_weight;
    }

    /**
     * Get the set_code.
     *
     * @return string|null
     */
    public function set_code(): string|null {
        return $this->set_code;
    }

}
