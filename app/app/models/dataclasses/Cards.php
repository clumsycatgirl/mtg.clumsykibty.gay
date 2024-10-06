<?php

namespace App\Models\DataClasses;

/**
 * Class Cards
 *
 * Represents a Cards in the system.
 */
class Cards {
    private int|null $id;
    private string|null $artist;
    private string|null $artist_ids;
    private string|null $ascii_name;
    private string|null $attraction_lights;
    private string|null $availability;
    private string|null $booster_types;
    private string|null $border_color;
    private string|null $card_parts;
    private string|null $color_identity;
    private string|null $color_indicator;
    private string|null $colors;
    private string|null $defense;
    private string|null $duel_deck;
    private int|null $edhrec_rank;
    private mixed $edhrec_saltiness;
    private mixed $face_converted_mana_cost;
    private string|null $face_flavor_name;
    private mixed $face_mana_value;
    private string|null $face_name;
    private string|null $finishes;
    private string|null $flavor_name;
    private string|null $flavor_text;
    private string|null $frame_effects;
    private string|null $frame_version;
    private string|null $hand;
    private mixed $has_alternative_deck_limit;
    private mixed $has_content_warning;
    private mixed $has_foil;
    private mixed $has_non_foil;
    private mixed $is_alternative;
    private mixed $is_full_art;
    private mixed $is_funny;
    private mixed $is_online_only;
    private mixed $is_oversized;
    private mixed $is_promo;
    private mixed $is_rebalanced;
    private mixed $is_reprint;
    private mixed $is_reserved;
    private mixed $is_starter;
    private mixed $is_story_spotlight;
    private mixed $is_textless;
    private mixed $is_timeshifted;
    private string|null $keywords;
    private string|null $language;
    private string|null $layout;
    private string|null $leadership_skills;
    private string|null $life;
    private string|null $loyalty;
    private string|null $mana_cost;
    private mixed $mana_value;
    private string|null $name;
    private string|null $number;
    private string|null $original_printings;
    private string|null $original_release_date;
    private string|null $original_text;
    private string|null $original_type;
    private string|null $other_face_ids;
    private string|null $power;
    private string|null $printings;
    private string|null $promo_types;
    private string|null $rarity;
    private string|null $rebalanced_printings;
    private string|null $related_cards;
    private string|null $security_stamp;
    private string|null $set_code;
    private string|null $side;
    private string|null $signature;
    private string|null $source_products;
    private string|null $subsets;
    private string|null $subtypes;
    private string|null $supertypes;
    private string|null $text;
    private string|null $toughness;
    private string|null $type;
    private string|null $types;
    private string|null $uuid;
    private string|null $variations;
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
        $this->attraction_lights = $array['attractionLights'] ?? null;
        $this->availability = $array['availability'] ?? null;
        $this->booster_types = $array['boosterTypes'] ?? null;
        $this->border_color = $array['borderColor'] ?? null;
        $this->card_parts = $array['cardParts'] ?? null;
        $this->color_identity = $array['colorIdentity'] ?? null;
        $this->color_indicator = $array['colorIndicator'] ?? null;
        $this->colors = $array['colors'] ?? null;
        $this->defense = $array['defense'] ?? null;
        $this->duel_deck = $array['duelDeck'] ?? null;
        $this->edhrec_rank = $array['edhrecRank'] ?? null;
        $this->edhrec_saltiness = $array['edhrecSaltiness'] ?? null;
        $this->face_converted_mana_cost = $array['faceConvertedManaCost'] ?? null;
        $this->face_flavor_name = $array['faceFlavorName'] ?? null;
        $this->face_mana_value = $array['faceManaValue'] ?? null;
        $this->face_name = $array['faceName'] ?? null;
        $this->finishes = $array['finishes'] ?? null;
        $this->flavor_name = $array['flavorName'] ?? null;
        $this->flavor_text = $array['flavorText'] ?? null;
        $this->frame_effects = $array['frameEffects'] ?? null;
        $this->frame_version = $array['frameVersion'] ?? null;
        $this->hand = $array['hand'] ?? null;
        $this->has_alternative_deck_limit = $array['hasAlternativeDeckLimit'] ?? null;
        $this->has_content_warning = $array['hasContentWarning'] ?? null;
        $this->has_foil = $array['hasFoil'] ?? null;
        $this->has_non_foil = $array['hasNonFoil'] ?? null;
        $this->is_alternative = $array['isAlternative'] ?? null;
        $this->is_full_art = $array['isFullArt'] ?? null;
        $this->is_funny = $array['isFunny'] ?? null;
        $this->is_online_only = $array['isOnlineOnly'] ?? null;
        $this->is_oversized = $array['isOversized'] ?? null;
        $this->is_promo = $array['isPromo'] ?? null;
        $this->is_rebalanced = $array['isRebalanced'] ?? null;
        $this->is_reprint = $array['isReprint'] ?? null;
        $this->is_reserved = $array['isReserved'] ?? null;
        $this->is_starter = $array['isStarter'] ?? null;
        $this->is_story_spotlight = $array['isStorySpotlight'] ?? null;
        $this->is_textless = $array['isTextless'] ?? null;
        $this->is_timeshifted = $array['isTimeshifted'] ?? null;
        $this->keywords = $array['keywords'] ?? null;
        $this->language = $array['language'] ?? null;
        $this->layout = $array['layout'] ?? null;
        $this->leadership_skills = $array['leadershipSkills'] ?? null;
        $this->life = $array['life'] ?? null;
        $this->loyalty = $array['loyalty'] ?? null;
        $this->mana_cost = $array['manaCost'] ?? null;
        $this->mana_value = $array['manaValue'] ?? null;
        $this->name = $array['name'] ?? null;
        $this->number = $array['number'] ?? null;
        $this->original_printings = $array['originalPrintings'] ?? null;
        $this->original_release_date = $array['originalReleaseDate'] ?? null;
        $this->original_text = $array['originalText'] ?? null;
        $this->original_type = $array['originalType'] ?? null;
        $this->other_face_ids = $array['otherFaceIds'] ?? null;
        $this->power = $array['power'] ?? null;
        $this->printings = $array['printings'] ?? null;
        $this->promo_types = $array['promoTypes'] ?? null;
        $this->rarity = $array['rarity'] ?? null;
        $this->rebalanced_printings = $array['rebalancedPrintings'] ?? null;
        $this->related_cards = $array['relatedCards'] ?? null;
        $this->security_stamp = $array['securityStamp'] ?? null;
        $this->set_code = $array['setCode'] ?? null;
        $this->side = $array['side'] ?? null;
        $this->signature = $array['signature'] ?? null;
        $this->source_products = $array['sourceProducts'] ?? null;
        $this->subsets = $array['subsets'] ?? null;
        $this->subtypes = $array['subtypes'] ?? null;
        $this->supertypes = $array['supertypes'] ?? null;
        $this->text = $array['text'] ?? null;
        $this->toughness = $array['toughness'] ?? null;
        $this->type = $array['type'] ?? null;
        $this->types = $array['types'] ?? null;
        $this->uuid = $array['uuid'] ?? null;
        $this->variations = $array['variations'] ?? null;
        $this->watermark = $array['watermark'] ?? null;
    }

    public static function from_imcomplete_class(\__PHP_Incomplete_Class $array): Cards {
        $obj = new Cards([]);
        foreach ($array as $key => $value) {
            if (property_exists($obj, $key)) {
                $obj->$key = $value;
            } else {
                $setter = "set_$key";
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
     * Get the attraction_lights.
     *
     * @return string|null
     */
    public function attraction_lights(): string|null {
        return $this->attraction_lights;
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
     * Get the card_parts.
     *
     * @return string|null
     */
    public function card_parts(): string|null {
        return $this->card_parts;
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
     * Get the color_indicator.
     *
     * @return string|null
     */
    public function color_indicator(): string|null {
        return $this->color_indicator;
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
     * Get the defense.
     *
     * @return string|null
     */
    public function defense(): string|null {
        return $this->defense;
    }

    /**
     * Get the duel_deck.
     *
     * @return string|null
     */
    public function duel_deck(): string|null {
        return $this->duel_deck;
    }

    /**
     * Get the edhrec_rank.
     *
     * @return int|null
     */
    public function edhrec_rank(): int|null {
        return $this->edhrec_rank;
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
     * Get the face_converted_mana_cost.
     *
     * @return mixed
     */
    public function face_converted_mana_cost(): mixed {
        return $this->face_converted_mana_cost;
    }

    /**
     * Get the face_flavor_name.
     *
     * @return string|null
     */
    public function face_flavor_name(): string|null {
        return $this->face_flavor_name;
    }

    /**
     * Get the face_mana_value.
     *
     * @return mixed
     */
    public function face_mana_value(): mixed {
        return $this->face_mana_value;
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
     * Get the flavor_name.
     *
     * @return string|null
     */
    public function flavor_name(): string|null {
        return $this->flavor_name;
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
     * Get the hand.
     *
     * @return string|null
     */
    public function hand(): string|null {
        return $this->hand;
    }

    /**
     * Get the has_alternative_deck_limit.
     *
     * @return mixed
     */
    public function has_alternative_deck_limit(): mixed {
        return $this->has_alternative_deck_limit;
    }

    /**
     * Get the has_content_warning.
     *
     * @return mixed
     */
    public function has_content_warning(): mixed {
        return $this->has_content_warning;
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
     * Get the is_alternative.
     *
     * @return mixed
     */
    public function is_alternative(): mixed {
        return $this->is_alternative;
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
     * Get the is_online_only.
     *
     * @return mixed
     */
    public function is_online_only(): mixed {
        return $this->is_online_only;
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
     * Get the is_rebalanced.
     *
     * @return mixed
     */
    public function is_rebalanced(): mixed {
        return $this->is_rebalanced;
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
     * Get the is_reserved.
     *
     * @return mixed
     */
    public function is_reserved(): mixed {
        return $this->is_reserved;
    }

    /**
     * Get the is_starter.
     *
     * @return mixed
     */
    public function is_starter(): mixed {
        return $this->is_starter;
    }

    /**
     * Get the is_story_spotlight.
     *
     * @return mixed
     */
    public function is_story_spotlight(): mixed {
        return $this->is_story_spotlight;
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
     * Get the is_timeshifted.
     *
     * @return mixed
     */
    public function is_timeshifted(): mixed {
        return $this->is_timeshifted;
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
     * Get the leadership_skills.
     *
     * @return string|null
     */
    public function leadership_skills(): string|null {
        return $this->leadership_skills;
    }

    /**
     * Get the life.
     *
     * @return string|null
     */
    public function life(): string|null {
        return $this->life;
    }

    /**
     * Get the loyalty.
     *
     * @return string|null
     */
    public function loyalty(): string|null {
        return $this->loyalty;
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
     * Get the mana_value.
     *
     * @return mixed
     */
    public function mana_value(): mixed {
        return $this->mana_value;
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
     * Get the original_printings.
     *
     * @return string|null
     */
    public function original_printings(): string|null {
        return $this->original_printings;
    }

    /**
     * Get the original_release_date.
     *
     * @return string|null
     */
    public function original_release_date(): string|null {
        return $this->original_release_date;
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
     * Get the printings.
     *
     * @return string|null
     */
    public function printings(): string|null {
        return $this->printings;
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
     * Get the rarity.
     *
     * @return string|null
     */
    public function rarity(): string|null {
        return $this->rarity;
    }

    /**
     * Get the rebalanced_printings.
     *
     * @return string|null
     */
    public function rebalanced_printings(): string|null {
        return $this->rebalanced_printings;
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
     * Get the source_products.
     *
     * @return string|null
     */
    public function source_products(): string|null {
        return $this->source_products;
    }

    /**
     * Get the subsets.
     *
     * @return string|null
     */
    public function subsets(): string|null {
        return $this->subsets;
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
     * Get the variations.
     *
     * @return string|null
     */
    public function variations(): string|null {
        return $this->variations;
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
