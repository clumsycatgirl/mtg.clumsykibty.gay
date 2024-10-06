<?php

namespace App\Controllers;

use App\Models\UserModel;
use Lib\Database\Database;
use Lib\Systems\Controllers\Controller;
use Lib\Systems\Traits\SessionTrait;
use Lib\Systems\Traits\ViewTrait;

class KillMeController extends Controller {
    use ViewTrait, SessionTrait;
    protected static string $LIB_MODE = 'lib';
    protected static string $CI4_MODE = 'ci4';
    protected string $mode;

    protected $db;

    public function __construct(\Lib\Request $request) {
        parent::__construct($request);
        $this->mode = self::$LIB_MODE;

        if ($this->is_ci4())
            $this->db = (new UserModel())->db;
    }

    public function index() {
        $keywords = [...$this->get_tables()];
        array_map(function ($table) use (&$keywords) {
            $fields = $this->get_field_data($table);
            array_map(function ($field) use (&$keywords) {
                $keywords[] = $field->name;
            }, $fields);
        }, $keywords);
        $keywords = array_values(array_unique($keywords));
        if ($this->is_ci4())
            return view('killme', ['keywords' => $keywords, 'database' => '']);
        else if ($this->is_lib())
            $this->view('killme', ['keywords' => $keywords, 'database' => database_database]);
    }

    public function exec_query() {
        $query = null;
        if ($this->is_ci4()) {
            $query = (string) $this->request->getPost('query');
        } else if ($this->is_lib()) {
            $query = (string) $this->request->get_post('query');
        }
        ?>
        <div>

            <div id="query-output">
                <div id="title-container" style="display: flex; align-items: center; align-content: center;">
                    <?php echo htmlspecialchars($query); ?>
                    <div style="flex: 1;"></div>
                    <button id="toggle-json">Show JSON</button>
                </div>
                <?php
                try {
                    $query_result = [];
                    if ($this->is_ci4())
                        $query_result = (new UserModel())->db->query($query)->getResultArray();
                    elseif ($this->is_lib())
                        $query_result = Database::query($query)->get_result()->fetch_all(MYSQLI_ASSOC);
                    ?>
                    <table>
                        <thead>
                            <tr>
                                <?php
                                if ($query_result !== null && count($query_result) !== 0) {
                                    $headers = array_keys($query_result[0]);
                                    foreach ($headers as $header) {
                                        echo "<th>" . htmlspecialchars($header) . "</th>";
                                    }
                                }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($query_result as $row) {
                                echo "<tr>";
                                foreach ($headers as $header) {
                                    echo "<td>" . htmlspecialchars($row[$header]) . "</td>";
                                }
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <pre id="json-output"
                        style="display: none;"><?php echo htmlspecialchars(json_encode($query_result, JSON_PRETTY_PRINT)); ?></pre>
                    <?php
                } catch (\Throwable $th) {
                    echo $th->getMessage();
                }
                ?>
            </div>
        </div>
        <?php
    }


    public function schema() {
        $tables = $this->get_tables();

        // $tables = ['user_service_reservation', 'companies', 'employees', 'employee_service_offer', 'locations', 'resources', 'rooms', 'services', 'service_resource_use', 'service_room_use', 'timetables', 'users'];

        foreach ($tables as $table) {
            ?>
            <h3><?= $table ?></h3>
            <table>
                <tr>
                    <?php
                    $fields = $this->get_field_data($table);
                    log_debug(print_r($fields, true));
                    foreach ($fields as $field) {
                        ?>
                        <td>
                            <span>
                                <?php if ($field->primary_key): ?> <b> <?php endif; ?>
                                    <?= $field->name ?>: <i><?= $field->type ?><?= $field->null ? '?' : '' ?></i>
                                    <?php if ($field->primary_key): ?> </b> <?php endif; ?>
                            </span>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
            </table>
            <?php
        }
    }

    private function get_tables() {
        $tables = [];
        if ($this->is_ci4()) {
            $tables = $this->db->listTables();
        } else if ($this->is_lib()) {
            $db_name = database_database;
            $temp_tables = Database::query("SELECT table_name FROM information_schema.tables WHERE table_schema = '{$db_name}'")->get_result()->fetch_all(MYSQLI_ASSOC);
            $tables = array();
            array_walk_recursive($temp_tables, function ($a) use (&$tables) {
                $tables[] = $a;
            });
        }
        return $tables;
    }

    private function get_field_data(string $table) {
        if ($this->is_ci4())
            $fields = $this->db->getFieldData($table);
        else if ($this->is_lib()) {
            $fields = [];
            $result = Database::query("SHOW COLUMNS FROM {$table}")->get_result();
            while ($field = $result->fetch_object()) {
                $field->primary_key = $field->Key === 'PRI';
                $field->type = $field->Type;
                $field->name = $field->Field;
                $field->default = $field->Default;
                $field->null = $field->Null === 'YES';
                $fields[] = $field;
            }
        }
        return $fields;
    }

    private function is_lib() {
        return $this->mode === self::$LIB_MODE;
    }

    private function is_ci4() {
        return $this->mode === self::$CI4_MODE;
    }
}
