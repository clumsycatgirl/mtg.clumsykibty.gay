<?php

namespace App\Models\DataClasses;

/**
 * Class Cardforeigndata
 *
 * Represents a Cardforeigndata in the system.
 */
class Cardforeigndata {
    private int|null $id;
    private string|null $face_name;
    private string|null $flavor_text;
    private string|null $identifiers;
    private string|null $language;
    private int|null $multiverse_id;
    private string|null $name;
    private string|null $text;
    private string|null $type;
    private string|null $uuid;

    /**
     * Constructor.
     *
     * @param array $array The row from the database.
     */
    public function __construct(array $array) {
        $this->id = $array['id'] ?? null;
        $this->face_name = $array['faceName'] ?? null;
        $this->flavor_text = $array['flavorText'] ?? null;
        $this->identifiers = $array['identifiers'] ?? null;
        $this->language = $array['language'] ?? null;
        $this->multiverse_id = $array['multiverseId'] ?? null;
        $this->name = $array['name'] ?? null;
        $this->text = $array['text'] ?? null;
        $this->type = $array['type'] ?? null;
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
     * Get the face_name.
     *
     * @return string|null
     */
    public function face_name(): string|null {
        return $this->face_name;
    }

    /**
     * Get the flavor_text.
     *
     * @return string|null
     */
    public function flavor_text(): string|null {
        return $this->flavor_text;
    }

    /**
     * Get the identifiers.
     *
     * @return string|null
     */
    public function identifiers(): string|null {
        return $this->identifiers;
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
     * Get the multiverse_id.
     *
     * @return int|null
     */
    public function multiverse_id(): int|null {
        return $this->multiverse_id;
    }

    /**
     * Get the name.
     *
     * @return string|null
     */
    public function name(): string|null {
        return $this->name;
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
     * Get the type.
     *
     * @return string|null
     */
    public function type(): string|null {
        return $this->type;
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
