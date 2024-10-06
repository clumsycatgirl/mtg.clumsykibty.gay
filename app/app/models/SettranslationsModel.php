<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Settranslations.php';
use App\Models\DataClasses\Settranslations;

use Lib\Systems\Models\Model;

class SettranslationsModel extends Model {
    /**
     * Settranslations constructor.
     *
     * Initializes the model with the table name 'Settranslations'.
     */
    public function __construct() {
        parent::__construct('settranslations');
    }

    /**
     * Retrieve all Settranslations entities from the database.
     *
     * @return array|false Array of Settranslations objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Settranslations => new Settranslations($row), $result);
        return $result;
    }

    /**
     * Find an Settranslations entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Settranslations entity to find.
     * @return object|array|false|null Settranslations object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Settranslations($result);
        return $result;
    }

    /**
     * Select Settranslations entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Settranslations objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Settranslations => new Settranslations($row), $result);
        return $result;
    }

    /**
     * Select the first Settranslations entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Settranslations object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Settranslations($result);
        return $result;
    }
}
