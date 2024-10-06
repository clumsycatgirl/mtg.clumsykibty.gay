<?php

namespace App\Models\DataClasses;

/**
 * Class Setboostersheets
 *
 * Represents a Setboostersheets in the system.
 */
class Setboostersheets {
    private int|null $id;
    private string|null $booster_name;
    private string|null $set_code;
    private mixed $sheet_has_balance_colors;
    private mixed $sheet_is_foil;
    private string|null $sheet_name;

    /**
     * Constructor.
     *
     * @param array $array The row from the database.
     */
    public function __construct(array $array) {
        $this->id = $array['id'] ?? null;
        $this->booster_name = $array['boosterName'] ?? null;
        $this->set_code = $array['setCode'] ?? null;
        $this->sheet_has_balance_colors = $array['sheetHasBalanceColors'] ?? null;
        $this->sheet_is_foil = $array['sheetIsFoil'] ?? null;
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
     * Get the set_code.
     *
     * @return string|null
     */
    public function set_code(): string|null {
        return $this->set_code;
    }

    /**
     * Get the sheet_has_balance_colors.
     *
     * @return mixed
     */
    public function sheet_has_balance_colors(): mixed {
        return $this->sheet_has_balance_colors;
    }

    /**
     * Get the sheet_is_foil.
     *
     * @return mixed
     */
    public function sheet_is_foil(): mixed {
        return $this->sheet_is_foil;
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
