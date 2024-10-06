<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Cards.php';
use App\Models\DataClasses\Cards;

use Lib\Systems\Models\Model;

class CardsModel extends Model {
    /**
     * Cards constructor.
     *
     * Initializes the model with the table name 'Cards'.
     */
    public function __construct() {
        parent::__construct('cards');
    }

    /**
     * Retrieve all Cards entities from the database.
     *
     * @return array|false Array of Cards objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Cards => new Cards($row), $result);
        return $result;
    }

    /**
     * Find an Cards entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Cards entity to find.
     * @return object|array|false|null Cards object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Cards($result);
        return $result;
    }

    /**
     * Select Cards entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Cards objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Cards => new Cards($row), $result);
        return $result;
    }

    /**
     * Select the first Cards entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Cards object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Cards($result);
        return $result;
    }
}
