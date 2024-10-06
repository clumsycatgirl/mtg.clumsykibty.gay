<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Sets.php';
use App\Models\DataClasses\Sets;

use Lib\Systems\Models\Model;

class SetsModel extends Model {
    /**
     * Sets constructor.
     *
     * Initializes the model with the table name 'Sets'.
     */
    public function __construct() {
        parent::__construct('sets');
    }

    /**
     * Retrieve all Sets entities from the database.
     *
     * @return array|false Array of Sets objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Sets => new Sets($row), $result);
        return $result;
    }

    /**
     * Find an Sets entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Sets entity to find.
     * @return object|array|false|null Sets object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Sets($result);
        return $result;
    }

    /**
     * Select Sets entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Sets objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Sets => new Sets($row), $result);
        return $result;
    }

    /**
     * Select the first Sets entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Sets object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Sets($result);
        return $result;
    }
}
