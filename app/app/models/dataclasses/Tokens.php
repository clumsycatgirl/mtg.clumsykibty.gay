<?php

namespace App\Models\DataClasses;

/**
 * Class Tokens
 *
 * Represents a Tokens in the system.
 */
class Tokens {
    private int|null $id;
    private string|null $artist;
    private string|null $artist_ids;
    private string|null $ascii_name;
    private string|null $availability;
    private string|null $booster_types;
    private string|null $border_color;
    private string|null $color_identity;
    private string|null $colors;
    private mixed $edhrec_saltiness;
    private string|null $face_name;
    private string|null $finishes;
    private string|null $flavor_text;
    private string|null $frame_effects;
    private string|null $frame_version;
    private mixed $has_foil;
    private mixed $has_non_foil;
    private mixed $is_full_art;
    private mixed $is_funny;
    private mixed $is_oversized;
    private mixed $is_promo;
    private mixed $is_reprint;
    private mixed $is_textless;
    private string|null $keywords;
    private string|null $language;
    private string|null $layout;
    private string|null $mana_cost;
    private string|null $name;
    private string|null $number;
    private string|null $orientation;
    private string|null $original_text;
    private string|null $original_type;
    private string|null $other_face_ids;
    private string|null $power;
    private string|null $promo_types;
    private string|null $related_cards;
    private string|null $reverse_related;
    private string|null $security_stamp;
    private string|null $set_code;
    private string|null $side;
    private string|null $signature;
    private string|null $subtypes;
    private string|null $supertypes;
    private string|null $text;
    private string|null $toughness;
    private string|null $type;
    private string|null $types;
    private string|null $uuid;
    private string|null $watermark;

    /**
     * Constructor.
     *
     * @param array $array The row from the database.
     */
    public function __construct(array $array) {
        $this->id = $array['id'] ?? null;
        $this->artist = $array['artist'] ?? null;
        $this->artist_ids = $array['artistIds'] ?? null;
        $this->ascii_name = $array['asciiName'] ?? null;
        $this->availability = $array['availability'] ?? null;
        $this->booster_types = $array['boosterTypes'] ?? null;
        $this->border_color = $array['borderColor'] ?? null;
        $this->color_identity = $array['colorIdentity'] ?? null;
        $this->colors = $array['colors'] ?? null;
        $this->edhrec_saltiness = $array['edhrecSaltiness'] ?? null;
        $this->face_name = $array['faceName'] ?? null;
        $this->finishes = $array['finishes'] ?? null;
        $this->flavor_text = $array['flavorText'] ?? null;
        $this->frame_effects = $array['frameEffects'] ?? null;
        $this->frame_version = $array['frameVersion'] ?? null;
        $this->has_foil = $array['hasFoil'] ?? null;
        $this->has_non_foil = $array['hasNonFoil'] ?? null;
        $this->is_full_art = $array['isFullArt'] ?? null;
        $this->is_funny = $array['isFunny'] ?? null;
        $this->is_oversized = $array['isOversized'] ?? null;
        $this->is_promo = $array['isPromo'] ?? null;
        $this->is_reprint = $array['isReprint'] ?? null;
        $this->is_textless = $array['isTextless'] ?? null;
        $this->keywords = $array['keywords'] ?? null;
        $this->language = $array['language'] ?? null;
        $this->layout = $array['layout'] ?? null;
        $this->mana_cost = $array['manaCost'] ?? null;
        $this->name = $array['name'] ?? null;
        $this->number = $array['number'] ?? null;
        $this->orientation = $array['orientation'] ?? null;
        $this->original_text = $array['originalText'] ?? null;
        $this->original_type = $array['originalType'] ?? null;
        $this->other_face_ids = $array['otherFaceIds'] ?? null;
        $this->power = $array['power'] ?? null;
        $this->promo_types = $array['promoTypes'] ?? null;
        $this->related_cards = $array['relatedCards'] ?? null;
        $this->reverse_related = $array['reverseRelated'] ?? null;
        $this->security_stamp = $array['securityStamp'] ?? null;
        $this->set_code = $array['setCode'] ?? null;
        $this->side = $array['side'] ?? null;
        $this->signature = $array['signature'] ?? null;
        $this->subtypes = $array['subtypes'] ?? null;
        $this->supertypes = $array['supertypes'] ?? null;
        $this->text = $array['text'] ?? null;
        $this->toughness = $array['toughness'] ?? null;
        $this->type = $array['type'] ?? null;
        $this->types = $array['types'] ?? null;
        $this->uuid = $array['uuid'] ?? null;
        $this->watermark = $array['watermark'] ?? null;
    }

    public static function from_imcomplete_class(\__PHP_Incomplete_Class $array): Tokens {
        $obj = new Tokens([]);
        foreach ($array as $key => $value) {
            if (property_exists($obj, $key)) {
                $obj->$key = $value;
            } else {
                if (method_exists($obj, $setter)) {
                    $obj->$setter($value);
                }
            }
        }
        return $obj;
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
     * Get the artist.
     *
     * @return string|null
     */
    public function artist(): string|null {
        return $this->artist;
    }

    /**
     * Get the artist_ids.
     *
     * @return string|null
     */
    public function artist_ids(): string|null {
        return $this->artist_ids;
    }

    /**
     * Get the ascii_name.
     *
     * @return string|null
     */
    public function ascii_name(): string|null {
        return $this->ascii_name;
    }

    /**
     * Get the availability.
     *
     * @return string|null
     */
    public function availability(): string|null {
        return $this->availability;
    }

    /**
     * Get the booster_types.
     *
     * @return string|null
     */
    public function booster_types(): string|null {
        return $this->booster_types;
    }

    /**
     * Get the border_color.
     *
     * @return string|null
     */
    public function border_color(): string|null {
        return $this->border_color;
    }

    /**
     * Get the color_identity.
     *
     * @return string|null
     */
    public function color_identity(): string|null {
        return $this->color_identity;
    }

    /**
     * Get the colors.
     *
     * @return string|null
     */
    public function colors(): string|null {
        return $this->colors;
    }

    /**
     * Get the edhrec_saltiness.
     *
     * @return mixed
     */
    public function edhrec_saltiness(): mixed {
        return $this->edhrec_saltiness;
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
     * Get the finishes.
     *
     * @return string|null
     */
    public function finishes(): string|null {
        return $this->finishes;
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
     * Get the frame_effects.
     *
     * @return string|null
     */
    public function frame_effects(): string|null {
        return $this->frame_effects;
    }

    /**
     * Get the frame_version.
     *
     * @return string|null
     */
    public function frame_version(): string|null {
        return $this->frame_version;
    }

    /**
     * Get the has_foil.
     *
     * @return mixed
     */
    public function has_foil(): mixed {
        return $this->has_foil;
    }

    /**
     * Get the has_non_foil.
     *
     * @return mixed
     */
    public function has_non_foil(): mixed {
        return $this->has_non_foil;
    }

    /**
     * Get the is_full_art.
     *
     * @return mixed
     */
    public function is_full_art(): mixed {
        return $this->is_full_art;
    }

    /**
     * Get the is_funny.
     *
     * @return mixed
     */
    public function is_funny(): mixed {
        return $this->is_funny;
    }

    /**
     * Get the is_oversized.
     *
     * @return mixed
     */
    public function is_oversized(): mixed {
        return $this->is_oversized;
    }

    /**
     * Get the is_promo.
     *
     * @return mixed
     */
    public function is_promo(): mixed {
        return $this->is_promo;
    }

    /**
     * Get the is_reprint.
     *
     * @return mixed
     */
    public function is_reprint(): mixed {
        return $this->is_reprint;
    }

    /**
     * Get the is_textless.
     *
     * @return mixed
     */
    public function is_textless(): mixed {
        return $this->is_textless;
    }

    /**
     * Get the keywords.
     *
     * @return string|null
     */
    public function keywords(): string|null {
        return $this->keywords;
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
     * Get the layout.
     *
     * @return string|null
     */
    public function layout(): string|null {
        return $this->layout;
    }

    /**
     * Get the mana_cost.
     *
     * @return string|null
     */
    public function mana_cost(): string|null {
        return $this->mana_cost;
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
     * Get the number.
     *
     * @return string|null
     */
    public function number(): string|null {
        return $this->number;
    }

    /**
     * Get the orientation.
     *
     * @return string|null
     */
    public function orientation(): string|null {
        return $this->orientation;
    }

    /**
     * Get the original_text.
     *
     * @return string|null
     */
    public function original_text(): string|null {
        return $this->original_text;
    }

    /**
     * Get the original_type.
     *
     * @return string|null
     */
    public function original_type(): string|null {
        return $this->original_type;
    }

    /**
     * Get the other_face_ids.
     *
     * @return string|null
     */
    public function other_face_ids(): string|null {
        return $this->other_face_ids;
    }

    /**
     * Get the power.
     *
     * @return string|null
     */
    public function power(): string|null {
        return $this->power;
    }

    /**
     * Get the promo_types.
     *
     * @return string|null
     */
    public function promo_types(): string|null {
        return $this->promo_types;
    }

    /**
     * Get the related_cards.
     *
     * @return string|null
     */
    public function related_cards(): string|null {
        return $this->related_cards;
    }

    /**
     * Get the reverse_related.
     *
     * @return string|null
     */
    public function reverse_related(): string|null {
        return $this->reverse_related;
    }

    /**
     * Get the security_stamp.
     *
     * @return string|null
     */
    public function security_stamp(): string|null {
        return $this->security_stamp;
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
     * Get the side.
     *
     * @return string|null
     */
    public function side(): string|null {
        return $this->side;
    }

    /**
     * Get the signature.
     *
     * @return string|null
     */
    public function signature(): string|null {
        return $this->signature;
    }

    /**
     * Get the subtypes.
     *
     * @return string|null
     */
    public function subtypes(): string|null {
        return $this->subtypes;
    }

    /**
     * Get the supertypes.
     *
     * @return string|null
     */
    public function supertypes(): string|null {
        return $this->supertypes;
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
     * Get the toughness.
     *
     * @return string|null
     */
    public function toughness(): string|null {
        return $this->toughness;
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
     * Get the types.
     *
     * @return string|null
     */
    public function types(): string|null {
        return $this->types;
    }

    /**
     * Get the uuid.
     *
     * @return string|null
     */
    public function uuid(): string|null {
        return $this->uuid;
    }

    /**
     * Get the watermark.
     *
     * @return string|null
     */
    public function watermark(): string|null {
        return $this->watermark;
    }

}
