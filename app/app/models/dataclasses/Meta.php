<?php

namespace App\Models\DataClasses;

/**
 * Class Meta
 *
 * Represents a Meta in the system.
 */
class Meta {
    private int|null $id;
    private mixed $date;
    private string|null $version;

    /**
     * Constructor.
     *
     * @param array $array The row from the database.
     */
    public function __construct(array $array) {
        $this->id = $array['id'] ?? null;
        $this->date = $array['date'] ?? null;
        $this->version = $array['version'] ?? null;
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
     * Get the date.
     *
     * @return mixed
     */
    public function date(): mixed {
        return $this->date;
    }

    /**
     * Get the version.
     *
     * @return string|null
     */
    public function version(): string|null {
        return $this->version;
    }

}
