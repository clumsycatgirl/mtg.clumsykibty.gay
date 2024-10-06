<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Meta.php';
use App\Models\DataClasses\Meta;

use Lib\Systems\Models\Model;

class MetaModel extends Model {
    /**
     * Meta constructor.
     *
     * Initializes the model with the table name 'Meta'.
     */
    public function __construct() {
        parent::__construct('meta');
    }

    /**
     * Retrieve all Meta entities from the database.
     *
     * @return array|false Array of Meta objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Meta => new Meta($row), $result);
        return $result;
    }

    /**
     * Find an Meta entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Meta entity to find.
     * @return object|array|false|null Meta object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Meta($result);
        return $result;
    }

    /**
     * Select Meta entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Meta objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Meta => new Meta($row), $result);
        return $result;
    }

    /**
     * Select the first Meta entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Meta object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Meta($result);
        return $result;
    }
}
