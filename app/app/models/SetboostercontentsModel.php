<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Setboostercontents.php';
use App\Models\DataClasses\Setboostercontents;

use Lib\Systems\Models\Model;

class SetboostercontentsModel extends Model {
    /**
     * Setboostercontents constructor.
     *
     * Initializes the model with the table name 'Setboostercontents'.
     */
    public function __construct() {
        parent::__construct('setboostercontents');
    }

    /**
     * Retrieve all Setboostercontents entities from the database.
     *
     * @return array|false Array of Setboostercontents objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Setboostercontents => new Setboostercontents($row), $result);
        return $result;
    }

    /**
     * Find an Setboostercontents entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Setboostercontents entity to find.
     * @return object|array|false|null Setboostercontents object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Setboostercontents($result);
        return $result;
    }

    /**
     * Select Setboostercontents entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Setboostercontents objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Setboostercontents => new Setboostercontents($row), $result);
        return $result;
    }

    /**
     * Select the first Setboostercontents entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Setboostercontents object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Setboostercontents($result);
        return $result;
    }
}
