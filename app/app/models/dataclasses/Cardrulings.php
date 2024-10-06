<?php

namespace App\Models\DataClasses;

/**
 * Class Cardrulings
 *
 * Represents a Cardrulings in the system.
 */
class Cardrulings {
    private int|null $id;
    private mixed $date;
    private string|null $text;
    private string|null $uuid;

    /**
     * Constructor.
     *
     * @param array $array The row from the database.
     */
    public function __construct(array $array) {
        $this->id = $array['id'] ?? null;
        $this->date = $array['date'] ?? null;
        $this->text = $array['text'] ?? null;
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
     * Get the date.
     *
     * @return mixed
     */
    public function date(): mixed {
        return $this->date;
    }

    /**
     * Get the text.
     *
     * @return string|null
     */
    public function text(): string|null {
        return $this->text;
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
