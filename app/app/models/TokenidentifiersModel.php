<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Tokenidentifiers.php';
use App\Models\DataClasses\Tokenidentifiers;

use Lib\Systems\Models\Model;

class TokenidentifiersModel extends Model {
    /**
     * Tokenidentifiers constructor.
     *
     * Initializes the model with the table name 'Tokenidentifiers'.
     */
    public function __construct() {
        parent::__construct('tokenidentifiers');
    }

    /**
     * Retrieve all Tokenidentifiers entities from the database.
     *
     * @return array|false Array of Tokenidentifiers objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Tokenidentifiers => new Tokenidentifiers($row), $result);
        return $result;
    }

    /**
     * Find an Tokenidentifiers entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Tokenidentifiers entity to find.
     * @return object|array|false|null Tokenidentifiers object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Tokenidentifiers($result);
        return $result;
    }

    /**
     * Select Tokenidentifiers entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Tokenidentifiers objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Tokenidentifiers => new Tokenidentifiers($row), $result);
        return $result;
    }

    /**
     * Select the first Tokenidentifiers entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Tokenidentifiers object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Tokenidentifiers($result);
        return $result;
    }
}
