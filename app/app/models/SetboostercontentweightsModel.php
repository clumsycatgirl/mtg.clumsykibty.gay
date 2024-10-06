<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Setboostercontentweights.php';
use App\Models\DataClasses\Setboostercontentweights;

use Lib\Systems\Models\Model;

class SetboostercontentweightsModel extends Model {
    /**
     * Setboostercontentweights constructor.
     *
     * Initializes the model with the table name 'Setboostercontentweights'.
     */
    public function __construct() {
        parent::__construct('setboostercontentweights');
    }

    /**
     * Retrieve all Setboostercontentweights entities from the database.
     *
     * @return array|false Array of Setboostercontentweights objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Setboostercontentweights => new Setboostercontentweights($row), $result);
        return $result;
    }

    /**
     * Find an Setboostercontentweights entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Setboostercontentweights entity to find.
     * @return object|array|false|null Setboostercontentweights object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Setboostercontentweights($result);
        return $result;
    }

    /**
     * Select Setboostercontentweights entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Setboostercontentweights objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Setboostercontentweights => new Setboostercontentweights($row), $result);
        return $result;
    }

    /**
     * Select the first Setboostercontentweights entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Setboostercontentweights object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Setboostercontentweights($result);
        return $result;
    }
}
