<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Cardlegalities.php';
use App\Models\DataClasses\Cardlegalities;

use Lib\Systems\Models\Model;

class CardlegalitiesModel extends Model {
    /**
     * Cardlegalities constructor.
     *
     * Initializes the model with the table name 'Cardlegalities'.
     */
    public function __construct() {
        parent::__construct('cardlegalities');
    }

    /**
     * Retrieve all Cardlegalities entities from the database.
     *
     * @return array|false Array of Cardlegalities objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Cardlegalities => new Cardlegalities($row), $result);
        return $result;
    }

    /**
     * Find an Cardlegalities entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Cardlegalities entity to find.
     * @return object|array|false|null Cardlegalities object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Cardlegalities($result);
        return $result;
    }

    /**
     * Select Cardlegalities entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Cardlegalities objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Cardlegalities => new Cardlegalities($row), $result);
        return $result;
    }

    /**
     * Select the first Cardlegalities entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Cardlegalities object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Cardlegalities($result);
        return $result;
    }
}
