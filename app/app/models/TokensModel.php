<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Tokens.php';
use App\Models\DataClasses\Tokens;

use Lib\Systems\Models\Model;

class TokensModel extends Model {
    /**
     * Tokens constructor.
     *
     * Initializes the model with the table name 'Tokens'.
     */
    public function __construct() {
        parent::__construct('tokens');
    }

    /**
     * Retrieve all Tokens entities from the database.
     *
     * @return array|false Array of Tokens objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Tokens => new Tokens($row), $result);
        return $result;
    }

    /**
     * Find an Tokens entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Tokens entity to find.
     * @return object|array|false|null Tokens object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Tokens($result);
        return $result;
    }

    /**
     * Select Tokens entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Tokens objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Tokens => new Tokens($row), $result);
        return $result;
    }

    /**
     * Select the first Tokens entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Tokens object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Tokens($result);
        return $result;
    }
}
