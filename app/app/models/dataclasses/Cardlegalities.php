<?php

namespace App\Models\DataClasses;

/**
 * Class Cardlegalities
 *
 * Represents a Cardlegalities in the system.
 */
class Cardlegalities {
    private int|null $id;
    private string|null $alchemy;
    private string|null $brawl;
    private string|null $commander;
    private string|null $duel;
    private string|null $explorer;
    private string|null $future;
    private string|null $gladiator;
    private string|null $historic;
    private string|null $legacy;
    private string|null $modern;
    private string|null $oathbreaker;
    private string|null $oldschool;
    private string|null $pauper;
    private string|null $paupercommander;
    private string|null $penny;
    private string|null $pioneer;
    private string|null $predh;
    private string|null $premodern;
    private string|null $standard;
    private string|null $standardbrawl;
    private string|null $timeless;
    private string|null $uuid;
    private string|null $vintage;

    /**
     * Constructor.
     *
     * @param array $array The row from the database.
     */
    public function __construct(array $array) {
        $this->id = $array['id'] ?? null;
        $this->alchemy = $array['alchemy'] ?? null;
        $this->brawl = $array['brawl'] ?? null;
        $this->commander = $array['commander'] ?? null;
        $this->duel = $array['duel'] ?? null;
        $this->explorer = $array['explorer'] ?? null;
        $this->future = $array['future'] ?? null;
        $this->gladiator = $array['gladiator'] ?? null;
        $this->historic = $array['historic'] ?? null;
        $this->legacy = $array['legacy'] ?? null;
        $this->modern = $array['modern'] ?? null;
        $this->oathbreaker = $array['oathbreaker'] ?? null;
        $this->oldschool = $array['oldschool'] ?? null;
        $this->pauper = $array['pauper'] ?? null;
        $this->paupercommander = $array['paupercommander'] ?? null;
        $this->penny = $array['penny'] ?? null;
        $this->pioneer = $array['pioneer'] ?? null;
        $this->predh = $array['predh'] ?? null;
        $this->premodern = $array['premodern'] ?? null;
        $this->standard = $array['standard'] ?? null;
        $this->standardbrawl = $array['standardbrawl'] ?? null;
        $this->timeless = $array['timeless'] ?? null;
        $this->uuid = $array['uuid'] ?? null;
        $this->vintage = $array['vintage'] ?? null;
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
     * Get the alchemy.
     *
     * @return string|null
     */
    public function alchemy(): string|null {
        return $this->alchemy;
    }

    /**
     * Get the brawl.
     *
     * @return string|null
     */
    public function brawl(): string|null {
        return $this->brawl;
    }

    /**
     * Get the commander.
     *
     * @return string|null
     */
    public function commander(): string|null {
        return $this->commander;
    }

    /**
     * Get the duel.
     *
     * @return string|null
     */
    public function duel(): string|null {
        return $this->duel;
    }

    /**
     * Get the explorer.
     *
     * @return string|null
     */
    public function explorer(): string|null {
        return $this->explorer;
    }

    /**
     * Get the future.
     *
     * @return string|null
     */
    public function future(): string|null {
        return $this->future;
    }

    /**
     * Get the gladiator.
     *
     * @return string|null
     */
    public function gladiator(): string|null {
        return $this->gladiator;
    }

    /**
     * Get the historic.
     *
     * @return string|null
     */
    public function historic(): string|null {
        return $this->historic;
    }

    /**
     * Get the legacy.
     *
     * @return string|null
     */
    public function legacy(): string|null {
        return $this->legacy;
    }

    /**
     * Get the modern.
     *
     * @return string|null
     */
    public function modern(): string|null {
        return $this->modern;
    }

    /**
     * Get the oathbreaker.
     *
     * @return string|null
     */
    public function oathbreaker(): string|null {
        return $this->oathbreaker;
    }

    /**
     * Get the oldschool.
     *
     * @return string|null
     */
    public function oldschool(): string|null {
        return $this->oldschool;
    }

    /**
     * Get the pauper.
     *
     * @return string|null
     */
    public function pauper(): string|null {
        return $this->pauper;
    }

    /**
     * Get the paupercommander.
     *
     * @return string|null
     */
    public function paupercommander(): string|null {
        return $this->paupercommander;
    }

    /**
     * Get the penny.
     *
     * @return string|null
     */
    public function penny(): string|null {
        return $this->penny;
    }

    /**
     * Get the pioneer.
     *
     * @return string|null
     */
    public function pioneer(): string|null {
        return $this->pioneer;
    }

    /**
     * Get the predh.
     *
     * @return string|null
     */
    public function predh(): string|null {
        return $this->predh;
    }

    /**
     * Get the premodern.
     *
     * @return string|null
     */
    public function premodern(): string|null {
        return $this->premodern;
    }

    /**
     * Get the standard.
     *
     * @return string|null
     */
    public function standard(): string|null {
        return $this->standard;
    }

    /**
     * Get the standardbrawl.
     *
     * @return string|null
     */
    public function standardbrawl(): string|null {
        return $this->standardbrawl;
    }

    /**
     * Get the timeless.
     *
     * @return string|null
     */
    public function timeless(): string|null {
        return $this->timeless;
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
     * Get the vintage.
     *
     * @return string|null
     */
    public function vintage(): string|null {
        return $this->vintage;
    }

}
