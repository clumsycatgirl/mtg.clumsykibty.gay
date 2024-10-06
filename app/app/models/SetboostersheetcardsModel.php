<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Setboostersheetcards.php';
use App\Models\DataClasses\Setboostersheetcards;

use Lib\Systems\Models\Model;

class SetboostersheetcardsModel extends Model {
    /**
     * Setboostersheetcards constructor.
     *
     * Initializes the model with the table name 'Setboostersheetcards'.
     */
    public function __construct() {
        parent::__construct('setboostersheetcards');
    }

    /**
     * Retrieve all Setboostersheetcards entities from the database.
     *
     * @return array|false Array of Setboostersheetcards objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Setboostersheetcards => new Setboostersheetcards($row), $result);
        return $result;
    }

    /**
     * Find an Setboostersheetcards entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Setboostersheetcards entity to find.
     * @return object|array|false|null Setboostersheetcards object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Setboostersheetcards($result);
        return $result;
    }

    /**
     * Select Setboostersheetcards entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Setboostersheetcards objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Setboostersheetcards => new Setboostersheetcards($row), $result);
        return $result;
    }

    /**
     * Select the first Setboostersheetcards entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Setboostersheetcards object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Setboostersheetcards($result);
        return $result;
    }
}
