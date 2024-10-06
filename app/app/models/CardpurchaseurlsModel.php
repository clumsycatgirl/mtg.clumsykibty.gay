<?php

namespace App\Models;

// include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . 'Cardpurchaseurls.php';
use App\Models\DataClasses\Cardpurchaseurls;

use Lib\Systems\Models\Model;

class CardpurchaseurlsModel extends Model {
    /**
     * Cardpurchaseurls constructor.
     *
     * Initializes the model with the table name 'Cardpurchaseurls'.
     */
    public function __construct() {
        parent::__construct('cardpurchaseurls');
    }

    /**
     * Retrieve all Cardpurchaseurls entities from the database.
     *
     * @return array|false Array of Cardpurchaseurls objects, or false on failure.
     */
    public function all(): array|false|string {
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): Cardpurchaseurls => new Cardpurchaseurls($row), $result);
        return $result;
    }

    /**
     * Find an Cardpurchaseurls entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the Cardpurchaseurls entity to find.
     * @return object|array|false|null Cardpurchaseurls object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {
        $result = parent::find($id);
        if (is_array($result))
            $result = new Cardpurchaseurls($result);
        return $result;
    }

    /**
     * Select Cardpurchaseurls entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of Cardpurchaseurls objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): Cardpurchaseurls => new Cardpurchaseurls($row), $result);
        return $result;
    }

    /**
     * Select the first Cardpurchaseurls entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null Cardpurchaseurls object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new Cardpurchaseurls($result);
        return $result;
    }
}
