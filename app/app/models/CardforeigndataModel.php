<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Cardforeigndata.php';
use App\Models\DataClasses\Cardforeigndata;

use Lib\Systems\Models\Model;

class CardforeigndataModel extends Model {
    /**
     * Cardforeigndata constructor.
     *
     * Initializes the model with the table name 'Cardforeigndata'.
     */
    public function __construct() {
        parent::__construct('cardforeigndata');
    }

    /**
     * Retrieve all Cardforeigndata entities from the database.
     *
     * @return array|false Array of Cardforeigndata objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Cardforeigndata => new Cardforeigndata($row), $result);
        return $result;
    }

    /**
     * Find an Cardforeigndata entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Cardforeigndata entity to find.
     * @return object|array|false|null Cardforeigndata object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Cardforeigndata($result);
        return $result;
    }

    /**
     * Select Cardforeigndata entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Cardforeigndata objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Cardforeigndata => new Cardforeigndata($row), $result);
        return $result;
    }

    /**
     * Select the first Cardforeigndata entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Cardforeigndata object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Cardforeigndata($result);
        return $result;
    }
}
