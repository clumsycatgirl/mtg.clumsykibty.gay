<?php

namespace App\Models\DataClasses;

/**
 * Class Sets
 *
 * Represents a Sets in the system.
 */
class Sets {
    private int|null $id;
    private int|null $base_set_size;
    private string|null $block;
    private int|null $cardsphere_set_id;
    private string|null $code;
    private mixed $is_foil_only;
    private mixed $is_foreign_only;
    private mixed $is_non_foil_only;
    private mixed $is_online_only;
    private mixed $is_partial_preview;
    private string|null $keyrune_code;
    private string|null $languages;
    private int|null $mcm_id;
    private int|null $mcm_id_extras;
    private string|null $mcm_name;
    private string|null $mtgo_code;
    private string|null $name;
    private string|null $parent_code;
    private string|null $release_date;
    private int|null $tcgplayer_group_id;
    private string|null $token_set_code;
    private int|null $total_set_size;
    private string|null $type;

    /**
     * Constructor.
     *
     * @param array $array The row from the database.
     */
    public function __construct(array $array) {
        $this->id = $array['id'] ?? null;
        $this->base_set_size = $array['baseSetSize'] ?? null;
        $this->block = $array['block'] ?? null;
        $this->cardsphere_set_id = $array['cardsphereSetId'] ?? null;
        $this->code = $array['code'] ?? null;
        $this->is_foil_only = $array['isFoilOnly'] ?? null;
        $this->is_foreign_only = $array['isForeignOnly'] ?? null;
        $this->is_non_foil_only = $array['isNonFoilOnly'] ?? null;
        $this->is_online_only = $array['isOnlineOnly'] ?? null;
        $this->is_partial_preview = $array['isPartialPreview'] ?? null;
        $this->keyrune_code = $array['keyruneCode'] ?? null;
        $this->languages = $array['languages'] ?? null;
        $this->mcm_id = $array['mcmId'] ?? null;
        $this->mcm_id_extras = $array['mcmIdExtras'] ?? null;
        $this->mcm_name = $array['mcmName'] ?? null;
        $this->mtgo_code = $array['mtgoCode'] ?? null;
        $this->name = $array['name'] ?? null;
        $this->parent_code = $array['parentCode'] ?? null;
        $this->release_date = $array['releaseDate'] ?? null;
        $this->tcgplayer_group_id = $array['tcgplayerGroupId'] ?? null;
        $this->token_set_code = $array['tokenSetCode'] ?? null;
        $this->total_set_size = $array['totalSetSize'] ?? null;
        $this->type = $array['type'] ?? null;
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
     * Get the base_set_size.
     *
     * @return int|null
     */
    public function base_set_size(): int|null {
        return $this->base_set_size;
    }

    /**
     * Get the block.
     *
     * @return string|null
     */
    public function block(): string|null {
        return $this->block;
    }

    /**
     * Get the cardsphere_set_id.
     *
     * @return int|null
     */
    public function cardsphere_set_id(): int|null {
        return $this->cardsphere_set_id;
    }

    /**
     * Get the code.
     *
     * @return string|null
     */
    public function code(): string|null {
        return $this->code;
    }

    /**
     * Get the is_foil_only.
     *
     * @return mixed
     */
    public function is_foil_only(): mixed {
        return $this->is_foil_only;
    }

    /**
     * Get the is_foreign_only.
     *
     * @return mixed
     */
    public function is_foreign_only(): mixed {
        return $this->is_foreign_only;
    }

    /**
     * Get the is_non_foil_only.
     *
     * @return mixed
     */
    public function is_non_foil_only(): mixed {
        return $this->is_non_foil_only;
    }

    /**
     * Get the is_online_only.
     *
     * @return mixed
     */
    public function is_online_only(): mixed {
        return $this->is_online_only;
    }

    /**
     * Get the is_partial_preview.
     *
     * @return mixed
     */
    public function is_partial_preview(): mixed {
        return $this->is_partial_preview;
    }

    /**
     * Get the keyrune_code.
     *
     * @return string|null
     */
    public function keyrune_code(): string|null {
        return $this->keyrune_code;
    }

    /**
     * Get the languages.
     *
     * @return string|null
     */
    public function languages(): string|null {
        return $this->languages;
    }

    /**
     * Get the mcm_id.
     *
     * @return int|null
     */
    public function mcm_id(): int|null {
        return $this->mcm_id;
    }

    /**
     * Get the mcm_id_extras.
     *
     * @return int|null
     */
    public function mcm_id_extras(): int|null {
        return $this->mcm_id_extras;
    }

    /**
     * Get the mcm_name.
     *
     * @return string|null
     */
    public function mcm_name(): string|null {
        return $this->mcm_name;
    }

    /**
     * Get the mtgo_code.
     *
     * @return string|null
     */
    public function mtgo_code(): string|null {
        return $this->mtgo_code;
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
     * Get the parent_code.
     *
     * @return string|null
     */
    public function parent_code(): string|null {
        return $this->parent_code;
    }

    /**
     * Get the release_date.
     *
     * @return string|null
     */
    public function release_date(): string|null {
        return $this->release_date;
    }

    /**
     * Get the tcgplayer_group_id.
     *
     * @return int|null
     */
    public function tcgplayer_group_id(): int|null {
        return $this->tcgplayer_group_id;
    }

    /**
     * Get the token_set_code.
     *
     * @return string|null
     */
    public function token_set_code(): string|null {
        return $this->token_set_code;
    }

    /**
     * Get the total_set_size.
     *
     * @return int|null
     */
    public function total_set_size(): int|null {
        return $this->total_set_size;
    }

    /**
     * Get the type.
     *
     * @return string|null
     */
    public function type(): string|null {
        return $this->type;
    }

}
