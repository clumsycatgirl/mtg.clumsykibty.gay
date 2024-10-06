<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Cardidentifiers.php';
use App\Models\DataClasses\Cardidentifiers;

use Lib\Systems\Models\Model;

class CardidentifiersModel extends Model {
    /**
     * Cardidentifiers constructor.
     *
     * Initializes the model with the table name 'Cardidentifiers'.
     */
    public function __construct() {
        parent::__construct('cardidentifiers');
    }

    /**
     * Retrieve all Cardidentifiers entities from the database.
     *
     * @return array|false Array of Cardidentifiers objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Cardidentifiers => new Cardidentifiers($row), $result);
        return $result;
    }

    /**
     * Find an Cardidentifiers entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Cardidentifiers entity to find.
     * @return object|array|false|null Cardidentifiers object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Cardidentifiers($result);
        return $result;
    }

    /**
     * Select Cardidentifiers entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Cardidentifiers objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Cardidentifiers => new Cardidentifiers($row), $result);
        return $result;
    }

    /**
     * Select the first Cardidentifiers entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Cardidentifiers object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Cardidentifiers($result);
        return $result;
    }
}
