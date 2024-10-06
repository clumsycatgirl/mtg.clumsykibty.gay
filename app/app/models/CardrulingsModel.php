<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Cardrulings.php';
use App\Models\DataClasses\Cardrulings;

use Lib\Systems\Models\Model;

class CardrulingsModel extends Model {
    /**
     * Cardrulings constructor.
     *
     * Initializes the model with the table name 'Cardrulings'.
     */
    public function __construct() {
        parent::__construct('cardrulings');
    }

    /**
     * Retrieve all Cardrulings entities from the database.
     *
     * @return array|false Array of Cardrulings objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Cardrulings => new Cardrulings($row), $result);
        return $result;
    }

    /**
     * Find an Cardrulings entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Cardrulings entity to find.
     * @return object|array|false|null Cardrulings object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Cardrulings($result);
        return $result;
    }

    /**
     * Select Cardrulings entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Cardrulings objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Cardrulings => new Cardrulings($row), $result);
        return $result;
    }

    /**
     * Select the first Cardrulings entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Cardrulings object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Cardrulings($result);
        return $result;
    }
}
