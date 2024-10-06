<?php

namespace App\Models\DataClasses;

/**
 * Class Settranslations
 *
 * Represents a Settranslations in the system.
 */
class Settranslations {
    private int|null $id;
    private string|null $language;
    private string|null $set_code;
    private string|null $translation;

    /**
     * Constructor.
     *
     * @param array $array The row from the database.
     */
    public function __construct(array $array) {
        $this->id = $array['id'] ?? null;
        $this->language = $array['language'] ?? null;
        $this->set_code = $array['setCode'] ?? null;
        $this->translation = $array['translation'] ?? null;
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
     * Get the language.
     *
     * @return string|null
     */
    public function language(): string|null {
        return $this->language;
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
     * Get the translation.
     *
     * @return string|null
     */
    public function translation(): string|null {
        return $this->translation;
    }

}
