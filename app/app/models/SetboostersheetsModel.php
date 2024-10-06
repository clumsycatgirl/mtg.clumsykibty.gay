<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Setboostersheets.php';
use App\Models\DataClasses\Setboostersheets;

use Lib\Systems\Models\Model;

class SetboostersheetsModel extends Model {
    /**
     * Setboostersheets constructor.
     *
     * Initializes the model with the table name 'Setboostersheets'.
     */
    public function __construct() {
        parent::__construct('setboostersheets');
    }

    /**
     * Retrieve all Setboostersheets entities from the database.
     *
     * @return array|false Array of Setboostersheets objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Setboostersheets => new Setboostersheets($row), $result);
        return $result;
    }

    /**
     * Find an Setboostersheets entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Setboostersheets entity to find.
     * @return object|array|false|null Setboostersheets object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Setboostersheets($result);
        return $result;
    }

    /**
     * Select Setboostersheets entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Setboostersheets objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Setboostersheets => new Setboostersheets($row), $result);
        return $result;
    }

    /**
     * Select the first Setboostersheets entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Setboostersheets object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Setboostersheets($result);
        return $result;
    }
}
