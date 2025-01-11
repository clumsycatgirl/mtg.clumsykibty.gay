<?php

const app_name = 'phpOurAdmin';

const database_host = 'db';
const database_username = 'admin';
const database_password = 'hatsunemikusaystransrights';
const open_databases = ['mtg'];

define('request_method', $_SERVER['REQUEST_METHOD']);

if (isset($_GET['db']) || isset($_POST['db'])) {
    $db = $_GET['db'] ?? $_POST['db'];
    $database = $db;
    $connection = new mysqli(database_host, database_username, database_password, $db);
} else
    $connection = new mysqli(database_host, database_username, database_password);
if ($connection->connect_error) {
    die('Connection failed: ' . $connection->connect_error);
}
$conn = $connection;

if (request_method === 'GET') {
    try {
        ?>
        <?php

        if (isset($_GET['home'])) {
            ?>
            <div class="home-title-container">
                <div class="home-title">
                    <?= app_name ?>
                </div>
            </div><?php
            exit;
        }

        if (isset($_GET['flag'])) {
            $flag = 'no-reload';
            if (file_exists('./flag')) {
                $flag = file_get_contents('./flag');
                if ($flag === 'reload') {
                    file_put_contents('./flag', 'no-reload');
                }
            }
            echo $flag;
            exit;
        }

        if (isset($_GET['db'], $_GET['table'])) {
            $db = $_GET['db'];
            $database = $db;
            $table = $_GET['table'];

            $fields = [];
            $sql = "SHOW COLUMNS FROM $table";
            $result = $connection->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $fields[] = $row;
                }
            }
            array_map(function ($field) use (&$columns_data) {
                $columns_data[$field['Field']] = $field;
            }, $fields);

            $foreign_keys = [];
            $foreign_key_result = $connection->query("SELECT
    	    COLUMN_NAME,
    	    REFERENCED_TABLE_NAME,
    	    REFERENCED_COLUMN_NAME
    	FROM
    	    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    	WHERE
    	    TABLE_SCHEMA = DATABASE()
    	    AND TABLE_NAME = '$table'
    	    AND REFERENCED_TABLE_NAME IS NOT NULL");
            if ($foreign_key_result->num_rows > 0) {
                while ($row = $foreign_key_result->fetch_assoc()) {
                    $foreign_keys[$row['COLUMN_NAME']] = [
                        'referenced_table' => $row['REFERENCED_TABLE_NAME'],
                        'referenced_column' => $row['REFERENCED_COLUMN_NAME'],
                    ];
                }
            }

            $columns_data = [];
            array_map(function ($field) use (&$columns_data, $foreign_keys) {
                $columns_data[$field['Field']] = $field;

                if (isset($foreign_keys[$field['Field']])) {
                    $columns_data[$field['Field']]['referenced_table'] = $foreign_keys[$field['Field']]['referenced_table'];
                    $columns_data[$field['Field']]['referenced_column'] = $foreign_keys[$field['Field']]['referenced_column'];
                }
            }, $fields);

            if (isset($_GET['structure'])) { ?>
                <?php
                ?>
                <div class="table-commands-container">
                    <div class="button-container">
                        <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&structure" hx-target="#content"
                            hx-swap="innerHTML">structure</button>
                        <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&show" hx-target="#content"
                            hx-swap="innerHTML">show</button>
                        <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&query" hx-target="#content"
                            hx-swap="innerHTML">query</button>
                        <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&add" hx-target="#content"
                            hx-swap="innerHTML">add</button>
                    </div>

                    <div class="line-separator"></div>
                </div>

                <div class="table margin-m">
                    <div class="row t-header">
                        <div class="cell">
                            Field
                        </div>
                        <div class="cell">
                            Type
                        </div>
                        <div class="cell">
                            Null
                        </div>
                        <div class="cell">
                            Key
                        </div>
                        <div class="cell">
                            Reference
                        </div>
                        <div class="cell">
                            Default
                        </div>
                        <div class="cell">
                            Extra
                        </div>
                    </div>
                    <?php foreach ($fields as $field): ?>
                        <div class="row">
                            <div class="cell" data-title="Field">
                                <?= $field['Field'] ?>
                            </div>
                            <div class="cell" data-title="Type"><?= $field['Type'] ?></div>
                            <div class="cell" data-title="Null"><?= $field['Null'] ?></div>
                            <div class="cell" data-title="Key"><?= $field['Key'] ?></div>
                            <div class="cell" data-title="Key">
                                <?php if (isset($columns_data[$field['Field']]['referenced_table'], $columns_data[$field['Field']]['referenced_column'])): ?>
                                    <div hx-get="?db=<?= $db ?>&table=<?= $columns_data[$field['Field']]['referenced_table'] ?>&structure"
                                        hx-target="#content" hx-swap="innerHTML" class="link">
                                        <?= $columns_data[$field['Field']]['referenced_table'] ?>.
                                        <?= $columns_data[$field['Field']]['referenced_column'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="cell" data-title="Default"><?= $field['Default'] ?></div>
                            <div class="cell" data-title="Extra"><?= $field['Extra'] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div><?php
            ?>
            <?php }

            if (isset($_GET['show'])) {
                if (!isset($_GET['page'], $_GET['count'])) {
                    $page = 0;
                    $count = 10;
                    ?>
                    <?php
                    ?>
                    <div id="table-data-content">
                        <div hx-get="?db=<?= $db ?>&table=<?= $table ?>&show&page=<?= $page ?>&count=<?= $count ?>" hx-trigger="load"
                            hx-swap="innerHTML"></div>
                        <script>
                            htmx.process(htmx.find('#table-data-content'))
                        </script>
                    </div><?php
                ?>
                <?php
                } else {
                    $page = intval($_GET['page'] ?? '0');
                    $count = intval($_GET['count'] ?? '0');

                    if (isset($_GET['dec_page'])) {
                        $page = max(0, $page - 1);
                    }

                    $page_start = $page * $count;
                    $sql = "SELECT * FROM $table LIMIT $page_start, $count";
                    $result = $connection->query($sql);
                    $rows = [];
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $rows[] = $row;
                        }
                    }
                    $columns = array_keys(reset($rows));

                    $max_page = 0;
                    $sql = "SELECT COUNT(*) AS `count` FROM $table";
                    $result = $connection->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $max_page = ceil($row['count'] / $count) - 1;
                    }
                    ?>
                    <?php
                    ?>
                    <style>
                        .page-input-container::after {
                            /* content: '/ <?= $max_page + 1 ?>
                            '; */

                        }
                    </style>

                    <div class="table-commands-container">
                        <div class="button-container">
                            <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&structure" hx-target="#content"
                                hx-swap="innerHTML">structure</button>
                            <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&show" hx-target="#content"
                                hx-swap="innerHTML">show</button>
                            <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&query" hx-target="#content"
                                hx-swap="innerHTML">query</button>
                            <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&add" hx-target="#content"
                                hx-swap="innerHTML">add</button>
                        </div>

                        <div class="line-separator"></div>

                        <div class="table-commands">
                            <span class="material-symbols-outlined icon <?= intval($page) === 0 ? 'blocked' : '' ?>" <?php if ($page !== 0): ?> hx-get="?db=<?= $db ?>&table=<?= $table ?>&show&page=<?= max(0, $page - 1) ?>&count=<?= $count ?>"
                                    hx-trigger="click" hx-target="#table-data-content" <?php endif; ?>>
                                arrow_left
                            </span>
                            <div class="page-input-container">
                                <input name="page" type="number" id="page-input" class="page-input" min="1" max="<?= $max_page + 1 ?>"
                                    value="<?= $page + 1 ?>" placeholder="<?= $page + 1 ?>"
                                    hx-get="?db=<?= $db ?>&table=<?= $table ?>&show&count=<?= $count ?>&dec_page"
                                    hx-trigger="change from:input" hx-target="#table-data-content" hx-swap="innerHTML"
                                    hx-include="#page-input">
                            </div>
                            <span class="material-symbols-outlined icon <?= intval($page) === intval($max_page) ? 'blocked' : '' ?>" <?php if ($page !== $max_page): ?>
                                    hx-get="?db=<?= $db ?>&table=<?= $table ?>&show&page=<?= min($max_page, $page + 1) ?>&count=<?= $count ?>"
                                    hx-trigger="click" hx-target="#table-data-content" <?php endif; ?>>
                                arrow_right
                            </span>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <div class="table margin-m">
                            <div class="row t-header">
                                <?php foreach ($columns as $column): ?>
                                    <div class="cell flex align-center justify-center margin-0">
                                        <div class="cell-content-container">
                                            <div class="text-container">
                                                <?= $column ?>
                                                <small>
                                                    <?php if (in_array($columns_data[$column]['Key'], ['PRI', 'MUL'])):
                                                        $class = $columns_data[$column]['Key'] === 'PRI' ? 'golden' : 'silver';
                                                        ?>
                                                        <span class="material-symbols-outlined <?= $class ?>">key</span>

                                                        <?php if ($columns_data[$column]['Key'] === 'MUL' && isset($columns_data[$column]['referenced_table'], $columns_data[$column]['referenced_column'])): ?>
                                                            (<?= $columns_data[$column]['referenced_table'] ?>.<?= $columns_data[$column]['referenced_column'] ?>)
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php foreach ($rows as $row): ?>
                                <div class="row">
                                    <?php foreach ($columns as $column): ?>
                                        <div class="cell" data-title="<?= $column ?>">
                                            <div class="cell-content" title="<?= $row[$column] ?>">
                                                <?= $row[$column] ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php
                }


            }

            if (isset($_GET['query'])) {
                $keywords = [];
                $temp = $connection->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '{$db}'")
                    ->fetch_all(MYSQLI_ASSOC);
                array_walk_recursive($temp, function ($table) use (&$keywords) {
                    $keywords[] = $table;
                });

                array_map(function ($table) use (&$keywords, $connection) {
                    $columns = $connection->query("SHOW COLUMNS FROM {$table}")->fetch_all(MYSQLI_ASSOC);
                    foreach ($columns as $column) {
                        $keywords[] = $column['Field'];
                    }
                }, $keywords);
                $keywords = array_values(array_unique($keywords));
                $keywords[] = $db;

                ?>
                <div class="table-commands-container">
                    <div class="button-container">
                        <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&structure" hx-target="#content"
                            hx-swap="innerHTML">structure</button>
                        <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&show" hx-target="#content"
                            hx-swap="innerHTML">show</button>
                        <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&query" hx-target="#content"
                            hx-swap="innerHTML">query</button>
                        <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&add" hx-target="#content"
                            hx-swap="innerHTML">add</button>
                    </div>

                    <div class="line-separator"></div>

                    <div class="table-commands">
                        <button id="execute-query-btn" class="btn accent padding-xs exec-query-btn">execute query</button>
                    </div>
                </div>

                <input type="hidden" name="db" id="db" value="<?= $db ?>" />

                <div class="query-container">
                    <div class="query-inner-container">
                        <div id="query"></div>
                        <div id="output-container" class="output-container"></div>
                    </div>
                    <div class="structure-container">
                        <textarea name="" id=""></textarea>
                    </div>
                </div>

                <script>
                    new window.QueryExecutor()

                    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.31.1/min/vs' } })
                    require(['vs/editor/editor.main'], function () {
                        monaco.languages.register({ id: 'oursql' })

                        const keywords = [
                            <?php foreach ($keywords as $keyword): ?>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                '<?= $keyword ?>',
                            <?php endforeach ?>
                        ]

                        const keywordRegex = new RegExp(`\\b(?:${window.mysqlKeywords.join('|')})\\b`, 'gi')





















            

                        ;

                                                                                                                                                                                                                        monaco.languages.setMonarchTokensProvider('oursql', {
                                                                                                                                                                                                                            tokenizer: {
                                                                                                                                                                                                                                root: [
                                                                                                                                                                                                                                    [keywordRegex, 'keyword'],
                                                                                                                                                                                                                                    [/\b(?:' + keywords.join('|') + ')\b/, 'keyword']
                                                                                                                                                                                                                                ]
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                        })

                                                                                                                                                                                                                        monaco.languages.setLanguageConfiguration('oursql', {
                                                                                                                                                                                                                            comments: {
                                                                                                                                                                                                                                lineComment: '--',
                                                                                                                                                                                                                                blockComment: ['/*', '*/']
                                                                                                                                                                                                                            },
                                                                                                                                                                                                                            brackets: [['{', '}'], ['[', ']'], ['(', ')']],
                                                                                                                                                                                                                            autoClosingPairs: [
                                                                                                                                                                                                                                { open: '{', close: '}' },
                                                                                                                                                                                                                                { open: '[', close: ']' },
                                                                                                                                                                                                                                { open: '(', close: ')' }
                                                                                                                                                                                                                            ],
                                                                                                                                                                                                                            surroundingPairs: [
                                                                                                                                                                                                                                { open: '{', close: '}' },
                                                                                                                                                                                                                                { open: '[', close: ']' },
                                                                                                                                                                                                                                { open: '(', close: ')' }
                                                                                                                                                                                                                            ]
                                                                                                                                                                                                                        })

                                                                                                                                                                                                                        monaco.languages.registerCompletionItemProvider('oursql', {
                                                                                                                                                                                                                            provideCompletionItems: () => {
                                                                                                                                                                                                                                const completionItems = [...keywords, ...mysqlKeywords].map(keyword => ({
                                                                                                                                                                                                                                    label: keyword,
                                                                                                                                                                                                                                    kind: monaco.languages.CompletionItemKind.Keyword,
                                                                                                                                                                                                                                    insertText: keyword
                                                                                                                                                                                                                                }))
                                                                                                                                                                                                                                return { suggestions: completionItems }
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                        })

                                                                                                                                                                                                                        window.editor = monaco.editor.create(document.getElementById('query'), {
                                                                                                                                                                                                                            // value: "SELECT SUM(table_rows) AS total_rows\n" +
                                                                                                                                                                                                                            // 		"FROM information_schema.tables\n" +
                                                                                                                                                                                                                            // 		"WHERE table_schema = '<?= $db ?>';",
                                                                                                                                                                                                                            value: "SELECT * FROM `<?= $table ?>` LIMIT 20",
                                                                                                                                                                                                                            language: 'oursql',
                                                                                                                                                                                                                            theme: 'vs-dark',
                                                                                                                                                                                                                        })

                                                                                                                                                                                                                        window.editor.addAction({
                                                                                                                                                                                                                            id: 'execute-query',
                                                                                                                                                                                                                            label: 'Execute Query',
                                                                                                                                                                                                                            keybindings: [
                                                                                                                                                                                                                                monaco.KeyMod.CtrlCmd | monaco.KeyCode.Enter
                                                                                                                                                                                                                            ],
                                                                                                                                                                                                                            run: (ed) => {
                                                                                                                                                                                                                                document.getElementById('execute-query-btn').click()
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                        })
                                                                                                                                                                                                                    })
                                                                                                                                                                                                                </script>
                                                                                                                                                                                                                <?php
            }

            if (isset($_GET['add'])) {
                ?>
                                                                                                                                                                                                                <?php
                                                                                                                                                                                                                ?>
                                                                                                                                                                                                                <div class="table-commands-container">
                                                                                                                                                                                                                    <div class="button-container">
                                                                                                                                                                                                                        <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&structure" hx-target="#content"
                                                                                                                                                                                                                            hx-swap="innerHTML">structure</button>
                                                                                                                                                                                                                        <button id="btn-show" class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&show"
                                                                                                                                                                                                                            hx-target="#content" hx-swap="innerHTML">show</button>
                                                                                                                                                                                                                        <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&query" hx-target="#content"
                                                                                                                                                                                                                            hx-swap="innerHTML">query</button>
                                                                                                                                                                                                                        <button class="btn accent padding-xs" hx-get="?db=<?= $db ?>&table=<?= $table ?>&add" hx-target="#content"
                                                                                                                                                                                                                            hx-swap="innerHTML">add</button>
                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                    <div class="line-separator"></div>
                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                <div style="width: 100%;">
                                                                                                                                                                                                                    <div class="card margin-xxl" style="width: 50%;">
                                                                                                                                                                                                                        <form id="form" action="#" class="card-body grid grid-cols-2 grid-gap-m">
                                                                                                                                                                                                                            <input type="hidden" id="setup" name="query-util" value="query-add">
                                                                                                                                                                                                                            <?php foreach ($columns_data as $column => $data): ?>
                                                                                                                                                                                                                                                                                <label for="field-<?= $column ?>">
                                                                                                                                                                                                                                                                                    <small class="text-gray-70">
                                                                                                                                                                                                                                                                                        <?php if (in_array($columns_data[$column]['Key'], ['PRI', 'MUL'])):
                                                                                                                                                                                                                                                                                            $class = $columns_data[$column]['Key'] === 'PRI' ? 'golden' : 'silver';
                                                                                                                                                                                                                                                                                            ?>
                                                                                                                                                                                                                                                                                                                                            <span class="material-symbols-outlined <?= $class ?>">key</span>
                                                                                                                                                                                                                                                                                        <?php endif; ?>
                                                                                                                                                                                                                                                                                    </small>
                                                                                                                                                                                                                                                                                    <?= $column ?>: <span
                                                                                                                                                                                                                                                                                        class="text-gray-70"><?= $data['Type'] ?><?= $data['Null'] === 'YES' ? '?' : '' ?></span>
                                                                                                                                                                                                                                                                                    <?php if ($columns_data[$column]['Key'] === 'MUL' && isset($columns_data[$column]['referenced_table'], $columns_data[$column]['referenced_column'])): ?>
                                                                                                                                                                                                                                                                                                                                        (<?= $columns_data[$column]['referenced_table'] ?>.<?= $columns_data[$column]['referenced_column'] ?>)
                                                                                                                                                                                                                                                                                    <?php endif; ?>
                                                                                                                                                                                                                                                                                </label>
                                                                                                                                                                                                                                                                                <input type="text" id="field-<?= $column ?>" value="" placeholder="<?= $column ?>"
                                                                                                                                                                                                                                                                                    class="bg-background-2 text-primary"
                                                                                                                                                                                                                                                                                    style="box-shadow: 0 0 8px var(--color-accent); border: none; border-radius: 8px"
                                                                                                                                                                                                                                                                                    data-field="<?= $column ?>" />
                                                                                                                                                                                                                            <?php endforeach; ?>
                                                                                                                                                                                                                            <input type="hidden" name="__table__" value="<?= $table ?>">
                                                                                                                                                                                                                            <input type="hidden" name="db" value="<?= $db ?>">
                                                                                                                                                                                                                        </form>
                                                                                                                                                                                                                        <div class="flex justify-end margin-m">
                                                                                                                                                                                                                            <button id="add-button" type="button" class="btn accent margin-xl padding-xs">Add</button>
                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                <script>new window.AddRow()</script><?php
                                                                                                                                                                                                                ?>
                                                                                                                                                                                                            <?php
            }

            $connection->close();
            exit;
        }

        if (isset($_GET['db'])) {
            $database = $_GET['db'];
            $db = $database;
            $tables = [];
            $tables_data = [];
            $connection = new mysqli(database_host, database_username, database_password, $database);
            if ($connection->connect_error) {
                die('Connection failed: ' . $connection->connect_error);
            }
            $sql = "SHOW TABLES";
            $result = $connection->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $tables[] = $row['Tables_in_' . $database];
                }
            }

            array_map(function ($table) use ($connection, $database, &$tables_data) {
                $table_data = [];
                $sql = "
			SELECT
				TABLE_NAME AS `Table`,
				TABLE_ROWS AS `Rows`,
				ENGINE AS `Engine`,
				TABLE_COLLATION AS `Collation`,
				ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS `Size_MB`,
				DATA_FREE AS `Overhead`,
				(SELECT COUNT(*)
				 FROM INFORMATION_SCHEMA.COLUMNS
				 WHERE TABLE_SCHEMA = '$database'
				   AND TABLE_NAME = '$table') AS `Columns`
			FROM
				INFORMATION_SCHEMA.TABLES
			WHERE
				TABLE_SCHEMA = '$database'
				AND TABLE_NAME = '$table'
		";

                $result = $connection->query($sql);
                if ($result && $row = $result->fetch_assoc()) {
                    $table_data = $row;
                }

                $tables_data[$table] = $table_data;
            }, $tables);

            $connection->close();
            ?>
                                                                                                                                                            <?php
                                                                                                                                                            ?>
                                                                                                                                                            <div class="table margin-m">
                                                                                                                                                                <div class="row t-header">
                                                                                                                                                                    <div class="cell">
                                                                                                                                                                        Table Name
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="cell">
                                                                                                                                                                        Actions
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="cell">
                                                                                                                                                                        Rows
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="cell">
                                                                                                                                                                        Columns
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="cell">
                                                                                                                                                                        Engine
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="cell">
                                                                                                                                                                        Collation
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="cell">
                                                                                                                                                                        Size (MB)
                                                                                                                                                                    </div>
                                                                                                                                                                    <div class="cell">
                                                                                                                                                                        Overhead
                                                                                                                                                                    </div>
                                                                                                                                                                </div>
                                                                                                                                                                <?php foreach ($tables as $table): ?>
                                                                                                                                                                                                                    <div class="row">
                                                                                                                                                                                                                        <div class="cell" data-title="Table Name">
                                                                                                                                                                                                                            <?= $table ?>
                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                        <div class="cell" data-title="Actions">
                                                                                                                                                                                                                            <button class="btn padding-xs" hx-get="?db=<?= $database ?>&table=<?= $table ?>&show" hx-target="#content"
                                                                                                                                                                                                                                hx-swap="innerHTML">show</button>
                                                                                                                                                                                                                            <button class="btn padding-xs" hx-get="?db=<?= $database ?>&table=<?= $table ?>&structure"
                                                                                                                                                                                                                                hx-target="#content" hx-swap="innerHTML">structure</button>
                                                                                                                                                                                                                            <button class="btn accent padding-xs" hx-get="?db=<?= $database ?>&table=<?= $table ?>&query"
                                                                                                                                                                                                                                hx-target="#content" hx-swap="innerHTML">query</button>
                                                                                                                                                                                                                            <button class="btn accent padding-xs" hx-get="?db=<?= $database ?>&table=<?= $table ?>&add"
                                                                                                                                                                                                                                hx-target="#content" hx-swap="innerHTML">add</button>
                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                        <div class="cell" data-title="Rows"><?= $tables_data[$table]['Rows'] ?></div>
                                                                                                                                                                                                                        <div class="cell" data-title="Rows"><?= $tables_data[$table]['Columns'] ?></div>
                                                                                                                                                                                                                        <div class="cell" data-title="Rows"><?= $tables_data[$table]['Engine'] ?></div>
                                                                                                                                                                                                                        <div class="cell" data-title="Rows"><?= $tables_data[$table]['Collation'] ?></div>
                                                                                                                                                                                                                        <div class="cell" data-title="Rows"><?= $tables_data[$table]['Size_MB'] ?></div>
                                                                                                                                                                                                                        <div class="cell" data-title="Rows"><?= $tables_data[$table]['Overhead'] ?></div>
                                                                                                                                                                                                                    </div>
                                                                                                                                                                <?php endforeach; ?>
                                                                                                                                                            </div><?php
                                                                                                                                                            ?>
                                                                                                                                                            <?php

                                                                                                                                                            exit;
        }

        ?>

                                                                                                        <?php
                                                                                                        ?>
                                                                                                        <?php
                                                                                                        $sql = "SHOW DATABASES";
                                                                                                        if (!empty(open_databases)) {
                                                                                                            $sql = "SHOW DATABASES WHERE `Database` IN ('" . implode("', '", open_databases) . "')";
                                                                                                        }
                                                                                                        $result = $conn->query($sql);
                                                                                                        $databases = [];
                                                                                                        if ($result->num_rows > 0) {
                                                                                                            while ($row = $result->fetch_assoc()) {
                                                                                                                $databases[] = $row['Database'];
                                                                                                            }
                                                                                                        }

                                                                                                        $db_tables = [];
                                                                                                        array_map(function ($database) use (&$conn, &$db_tables) {
                                                                                                            $conn->select_db($database);
                                                                                                            $sql = "SHOW TABLES";
                                                                                                            $result = $conn->query($sql);
                                                                                                            $tables = [];
                                                                                                            if ($result->num_rows > 0) {
                                                                                                                // var_dump($result->fetch_all(MYSQLI_ASSOC));
                                                                                                                // return;
                                                                                                                while ($row = $result->fetch_assoc()) {
                                                                                                                    $tables[] = $row["Tables_in_$database"];
                                                                                                                }
                                                                                                            }
                                                                                                            $db_tables[$database] = $tables;
                                                                                                        }, $databases);
                                                                                                        ?>

                                                                                                        <!DOCTYPE html>
                                                                                                        <html lang="en">

                                                                                                        <head>
                                                                                                            <meta charset="UTF-8">
                                                                                                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                                                                                            <title><?= app_name ?></title>

                                                                                                            <style>
                                                                                                                @import url("https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap");

                                                                                                                :root {
                                                                                                                    --color-primary: #E0E1DD;
                                                                                                                    --color-secondary: #778DA9;
                                                                                                                    --color-accent: #415A77;
                                                                                                                    --color-ternary: #1B263B;
                                                                                                                    --color-background: #080808;
                                                                                                                    --color-background-2: #101010;
                                                                                                                    --color-info: green;
                                                                                                                    --color-warning: orange;
                                                                                                                    --color-danger: red;

                                                                                                                    --links: bold 18px/18px var(--ff);

                                                                                                                    --padding-xxs: .25rem;
                                                                                                                    --padding-xs: .50rem;
                                                                                                                    --padding-s: .75rem;
                                                                                                                    --padding-m: 1rem;
                                                                                                                    --padding-l: 1.25rem;
                                                                                                                    --padding-xl: 1.75rem;
                                                                                                                    --padding-xxl: 2.5rem;

                                                                                                                    --margin-xxs: .25rem;
                                                                                                                    --margin-xs: .50rem;
                                                                                                                    --margin-s: .75rem;
                                                                                                                    --margin-m: 1rem;
                                                                                                                    --margin-l: 1.25rem;
                                                                                                                    --margin-xl: 1.75rem;
                                                                                                                    --margin-xxl: 2.5rem;

                                                                                                                    --gray-5: hsl(0, 0%, 5%);
                                                                                                                    --gray-10: hsl(0, 0%, 10%);
                                                                                                                    --gray-30: hsl(0, 0%, 30%);
                                                                                                                    --gray-50: hsl(0, 0%, 50%);
                                                                                                                    --gray-70: hsl(0, 0%, 70%);
                                                                                                                    --gray-80: hsl(0, 0%, 80%);

                                                                                                                    /* --ff: "Inter", sans-serif; */
                                                                                                                    --ff: "Montserrat", sans-serif;

                                                                                                                    --h1: bold 4rem/1em var(--ff);
                                                                                                                    --h2: bold 3rem/1.2em var(--ff);
                                                                                                                    --h3: bold 2.25rem/1.2em var(--ff);
                                                                                                                    --h4: bold 1.5rem/1.6em var(--ff);

                                                                                                                    --big: 1.25rem/1.6em var(--ff);
                                                                                                                    --p: 1rem/1.6em var(--ff);
                                                                                                                    --small: .75rem/2em var(--ff);

                                                                                                                    --h1-ui: bold 3rem/1.2em var(--ff);
                                                                                                                    --h2-ui: bold 2.25rem/1.2em var(--ff);
                                                                                                                    --h3-ui: bold 1.5rem/1.2em var(--ff);
                                                                                                                    --h4-ui: bold 1.12rem/1.6em var(--ff);

                                                                                                                    --big-ui: 1rem/1.6em var(--ff);
                                                                                                                    --p-ui: .8rem/1.6em var(--ff);
                                                                                                                    --small-ui: .75rem/1.8em var(--ff);
                                                                                                                }

                                                                                                                /* ------------ DEFAULTS ------------ */
                                                                                                                html {
                                                                                                                    scroll-behavior: smooth;
                                                                                                                    user-select: text;
                                                                                                                }

                                                                                                                body {
                                                                                                                    box-sizing: border-box;
                                                                                                                    font-family: var(--ff);
                                                                                                                    text-wrap: balance;
                                                                                                                    background: var(--color-background);
                                                                                                                    color: var(--color-primary);
                                                                                                                }

                                                                                                                h1 {
                                                                                                                    font: var(--h1);
                                                                                                                }

                                                                                                                h2 {
                                                                                                                    font: var(--h2);
                                                                                                                }

                                                                                                                h3 {
                                                                                                                    font: var(--h3);
                                                                                                                }

                                                                                                                h4 {
                                                                                                                    font: var(--h4);
                                                                                                                }

                                                                                                                big {
                                                                                                                    font: var(--big);
                                                                                                                }

                                                                                                                p {
                                                                                                                    font: var(--p);
                                                                                                                }

                                                                                                                small {
                                                                                                                    font: var(--small);
                                                                                                                }

                                                                                                                big.ui {
                                                                                                                    font: var(--big-ui);
                                                                                                                }

                                                                                                                p.ui {
                                                                                                                    font: var(--p-ui);
                                                                                                                }

                                                                                                                small.ui {
                                                                                                                    font: var(--small-ui);
                                                                                                                }

                                                                                                                h1.ui {
                                                                                                                    font: var(--h1-ui);
                                                                                                                }

                                                                                                                h2.ui {
                                                                                                                    font: var(--h2-ui);
                                                                                                                }

                                                                                                                h3.ui {
                                                                                                                    font: var(--h3-ui);
                                                                                                                }

                                                                                                                h4.ui {
                                                                                                                    font: var(--h4-ui);
                                                                                                                }

                                                                                                                a[href],
                                                                                                                .link {
                                                                                                                    font: var(--links);
                                                                                                                    text-decoration: none;
                                                                                                                    color: var(--color-accent);
                                                                                                                    cursor: pointer;
                                                                                                                }

                                                                                                                a[href]:active,
                                                                                                                .link:active {
                                                                                                                    color: var(--color-primary);
                                                                                                                }

                                                                                                                /* ------------ END DEFAULTS ------------ */

                                                                                                                /* ------------ UTILS ------------ */

                                                                                                                .flex {
                                                                                                                    display: flex;
                                                                                                                    flex-wrap: wrap;
                                                                                                                    gap: 20px;
                                                                                                                }

                                                                                                                /* ------------ CARDS ------------ */

                                                                                                                /* ------------ CARD STYLES ------------ */
                                                                                                                .card {
                                                                                                                    background-color: var(--color-background-2);
                                                                                                                    border: 1px solid var(--color-border);
                                                                                                                    border-radius: .75rem;
                                                                                                                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                                                                                                    padding: var(--padding-m);
                                                                                                                    margin: var(--margin-m) 0;
                                                                                                                    width: 300px;
                                                                                                                }

                                                                                                                .card-header {
                                                                                                                    font: var(--h4);
                                                                                                                }

                                                                                                                .card-body {
                                                                                                                    font: var(--p);

                                                                                                                }

                                                                                                                .card-footer {
                                                                                                                    font: var(--small);
                                                                                                                    border-top: 1px solid var(--color-border);
                                                                                                                    margin-top: 1rem;
                                                                                                                }

                                                                                                                /* Card Variants */
                                                                                                                .card-primary {
                                                                                                                    background-color: var(--color-primary);
                                                                                                                    color: var(--color-background);
                                                                                                                }

                                                                                                                .card-accent {
                                                                                                                    background-color: var(--color-accent);
                                                                                                                    color: var(--color-background);
                                                                                                                }

                                                                                                                .card-shadow-s {
                                                                                                                    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                                                                                                                }

                                                                                                                .card-shadow-l {
                                                                                                                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
                                                                                                                }

                                                                                                                .card-xxs {
                                                                                                                    width: 100px;
                                                                                                                }

                                                                                                                .card-xs {
                                                                                                                    width: 150px;
                                                                                                                }

                                                                                                                .card-s {
                                                                                                                    width: 200px;
                                                                                                                    /* height: 200px; */
                                                                                                                }

                                                                                                                .card-m {
                                                                                                                    width: 300px;
                                                                                                                }

                                                                                                                .card-l {
                                                                                                                    width: 400px;
                                                                                                                }

                                                                                                                .card-xl {
                                                                                                                    width: 500px;
                                                                                                                }

                                                                                                                .card-xxl {
                                                                                                                    width: 600px;
                                                                                                                }

                                                                                                                /* ------------ END CARDS ------------ */

                                                                                                                .hr {
                                                                                                                    border: none;
                                                                                                                    border-top: 1px solid var(--color-accent);
                                                                                                                    margin: var(--margin-m) 0;
                                                                                                                }

                                                                                                                .hr-primary {
                                                                                                                    border-top: 1px solid var(--color-primary);
                                                                                                                }

                                                                                                                .hr-accent {
                                                                                                                    border-top: 1px solid var(--color-accent);
                                                                                                                }

                                                                                                                .hr-thick {
                                                                                                                    border-top: 2px solid var(--color-accent);
                                                                                                                }

                                                                                                                .hr-dashed {
                                                                                                                    border-top: 1px dashed var(--color-accent);
                                                                                                                }

                                                                                                                .hr-dotted {
                                                                                                                    border-top: 1px dotted var(--color-accent);
                                                                                                                }

                                                                                                                /* ------------ GRID LAYOUTS ------------ */
                                                                                                                .grid {
                                                                                                                    display: grid;
                                                                                                                }

                                                                                                                .grid-cols-1 {
                                                                                                                    grid-template-columns: repeat(1, 1fr);
                                                                                                                }

                                                                                                                .grid-cols-2 {
                                                                                                                    grid-template-columns: repeat(2, 1fr);
                                                                                                                }

                                                                                                                .grid-cols-3 {
                                                                                                                    grid-template-columns: repeat(3, 1fr);
                                                                                                                }

                                                                                                                .grid-cols-4 {
                                                                                                                    grid-template-columns: repeat(4, 1fr);
                                                                                                                }

                                                                                                                .grid-cols-6 {
                                                                                                                    grid-template-columns: repeat(6, 1fr);
                                                                                                                }

                                                                                                                .grid-cols-12 {
                                                                                                                    grid-template-columns: repeat(12, 1fr);
                                                                                                                }

                                                                                                                .grid-rows-1 {
                                                                                                                    grid-template-rows: repeat(1, 1fr);
                                                                                                                }

                                                                                                                .grid-rows-2 {
                                                                                                                    grid-template-rows: repeat(2, 1fr);
                                                                                                                }

                                                                                                                .grid-rows-3 {
                                                                                                                    grid-template-rows: repeat(3, 1fr);
                                                                                                                }

                                                                                                                .grid-rows-4 {
                                                                                                                    grid-template-rows: repeat(4, 1fr);
                                                                                                                }

                                                                                                                .grid-gap-xs {
                                                                                                                    gap: var(--padding-xs);
                                                                                                                }

                                                                                                                .grid-gap-s {
                                                                                                                    gap: var(--padding-s);
                                                                                                                }

                                                                                                                .grid-gap-m {
                                                                                                                    gap: var(--padding-m);
                                                                                                                }

                                                                                                                .grid-gap-l {
                                                                                                                    gap: var(--padding-l);
                                                                                                                }

                                                                                                                .grid-gap-xl {
                                                                                                                    gap: var(--padding-xl);
                                                                                                                }

                                                                                                                /* ------------ FLEXBOX LAYOUTS ------------ */
                                                                                                                .flex-row {
                                                                                                                    display: flex;
                                                                                                                    flex-direction: row;
                                                                                                                }

                                                                                                                .flex-column {
                                                                                                                    display: flex;
                                                                                                                    flex-direction: column;
                                                                                                                }

                                                                                                                .flex-wrap {
                                                                                                                    flex-wrap: wrap;
                                                                                                                }

                                                                                                                .justify-start {
                                                                                                                    justify-content: flex-start;
                                                                                                                }

                                                                                                                .justify-center {
                                                                                                                    justify-content: center;
                                                                                                                }

                                                                                                                .justify-end {
                                                                                                                    justify-content: flex-end;
                                                                                                                }

                                                                                                                .justify-between {
                                                                                                                    justify-content: space-between;
                                                                                                                }

                                                                                                                .justify-around {
                                                                                                                    justify-content: space-around;
                                                                                                                }

                                                                                                                .align-start {
                                                                                                                    align-items: flex-start;
                                                                                                                }

                                                                                                                .align-center {
                                                                                                                    align-items: center;
                                                                                                                }

                                                                                                                .align-end {
                                                                                                                    align-items: flex-end;
                                                                                                                }

                                                                                                                .flex-gap-xs {
                                                                                                                    gap: var(--padding-xs);
                                                                                                                }

                                                                                                                .flex-gap-s {
                                                                                                                    gap: var(--padding-s);
                                                                                                                }

                                                                                                                .flex-gap-m {
                                                                                                                    gap: var(--padding-m);
                                                                                                                }

                                                                                                                .flex-gap-l {
                                                                                                                    gap: var(--padding-l);
                                                                                                                }

                                                                                                                .flex-gap-xl {
                                                                                                                    gap: var(--padding-xl);
                                                                                                                }

                                                                                                                /* ------------ RESPONSIVE UTILITIES ------------ */
                                                                                                                @media (min-width: 576px) {
                                                                                                                    .sm-grid-cols-2 {
                                                                                                                        grid-template-columns: repeat(2, 1fr);
                                                                                                                    }

                                                                                                                    .sm-grid-cols-3 {
                                                                                                                        grid-template-columns: repeat(3, 1fr);
                                                                                                                    }

                                                                                                                    .sm-grid-cols-4 {
                                                                                                                        grid-template-columns: repeat(4, 1fr);
                                                                                                                    }

                                                                                                                    .sm-grid-cols-6 {
                                                                                                                        grid-template-columns: repeat(6, 1fr);
                                                                                                                    }

                                                                                                                    .sm-grid-cols-12 {
                                                                                                                        grid-template-columns: repeat(12, 1fr);
                                                                                                                    }

                                                                                                                    .sm-flex-row {
                                                                                                                        flex-direction: row;
                                                                                                                    }

                                                                                                                    .sm-flex-column {
                                                                                                                        flex-direction: column;
                                                                                                                    }
                                                                                                                }

                                                                                                                @media (min-width: 768px) {
                                                                                                                    .md-grid-cols-2 {
                                                                                                                        grid-template-columns: repeat(2, 1fr);
                                                                                                                    }

                                                                                                                    .md-grid-cols-3 {
                                                                                                                        grid-template-columns: repeat(3, 1fr);
                                                                                                                    }

                                                                                                                    .md-grid-cols-4 {
                                                                                                                        grid-template-columns: repeat(4, 1fr);
                                                                                                                    }

                                                                                                                    .md-grid-cols-6 {
                                                                                                                        grid-template-columns: repeat(6, 1fr);
                                                                                                                    }

                                                                                                                    .md-grid-cols-12 {
                                                                                                                        grid-template-columns: repeat(12, 1fr);
                                                                                                                    }

                                                                                                                    .md-flex-row {
                                                                                                                        flex-direction: row;
                                                                                                                    }

                                                                                                                    .md-flex-column {
                                                                                                                        flex-direction: column;
                                                                                                                    }
                                                                                                                }

                                                                                                                @media (min-width: 992px) {
                                                                                                                    .lg-grid-cols-2 {
                                                                                                                        grid-template-columns: repeat(2, 1fr);
                                                                                                                    }

                                                                                                                    .lg-grid-cols-3 {
                                                                                                                        grid-template-columns: repeat(3, 1fr);
                                                                                                                    }

                                                                                                                    .lg-grid-cols-4 {
                                                                                                                        grid-template-columns: repeat(4, 1fr);
                                                                                                                    }

                                                                                                                    .lg-grid-cols-6 {
                                                                                                                        grid-template-columns: repeat(6, 1fr);
                                                                                                                    }

                                                                                                                    .lg-grid-cols-12 {
                                                                                                                        grid-template-columns: repeat(12, 1fr);
                                                                                                                    }

                                                                                                                    .lg-flex-row {
                                                                                                                        flex-direction: row;
                                                                                                                    }

                                                                                                                    .lg-flex-column {
                                                                                                                        flex-direction: column;
                                                                                                                    }
                                                                                                                }

                                                                                                                @media (min-width: 1200px) {
                                                                                                                    .xl-grid-cols-2 {
                                                                                                                        grid-template-columns: repeat(2, 1fr);
                                                                                                                    }

                                                                                                                    .xl-grid-cols-3 {
                                                                                                                        grid-template-columns: repeat(3, 1fr);
                                                                                                                    }

                                                                                                                    .xl-grid-cols-4 {
                                                                                                                        grid-template-columns: repeat(4, 1fr);
                                                                                                                    }

                                                                                                                    .xl-grid-cols-6 {
                                                                                                                        grid-template-columns: repeat(6, 1fr);
                                                                                                                    }

                                                                                                                    .xl-grid-cols-12 {
                                                                                                                        grid-template-columns: repeat(12, 1fr);
                                                                                                                    }

                                                                                                                    .xl-flex-row {
                                                                                                                        flex-direction: row;
                                                                                                                    }

                                                                                                                    .xl-flex-column {
                                                                                                                        flex-direction: column;
                                                                                                                    }
                                                                                                                }

                                                                                                                /* ------------ END UTILS ------------ */

                                                                                                                /* ------------ BUTTONS ------------ */

                                                                                                                .btn {
                                                                                                                    color: var(--color-accent);
                                                                                                                    background-color: var(--color-background);
                                                                                                                    padding: var(--margin-xs) var(--margin-m);
                                                                                                                    font: var(--p-ui);
                                                                                                                    cursor: pointer;
                                                                                                                    padding: 12px 20px;
                                                                                                                    border-radius: 12px;
                                                                                                                    transition: box-shadow .15s ease-in-out;

                                                                                                                    border: none;
                                                                                                                    box-shadow: 0 0 8px var(--color-accent);
                                                                                                                }

                                                                                                                .btn-fill {
                                                                                                                    color: var(--color-background);
                                                                                                                    background-color: var(--color-accent);
                                                                                                                    padding: var(--margin-xs) var(--margin-m);
                                                                                                                    font: var(--p-ui);
                                                                                                                    cursor: pointer;
                                                                                                                    padding: 12px 20px;
                                                                                                                    border-radius: 12px;
                                                                                                                    transition: .3s ease-in-out;

                                                                                                                    border: none;
                                                                                                                    box-shadow: 0 0 8px var(--color-accent);
                                                                                                                }

                                                                                                                .btn:hover,
                                                                                                                .btn-fill:hover {
                                                                                                                    box-shadow: 0 0 24px var(--color-accent);
                                                                                                                }

                                                                                                                .btn:active,
                                                                                                                .btn-fill:active {
                                                                                                                    box-shadow: 0 0 36px var(--color-accent);
                                                                                                                }

                                                                                                                .btn.primary {
                                                                                                                    color: var(--color-primary);
                                                                                                                    background-color: var(--color-background);
                                                                                                                    box-shadow: 0 0 8px var(--color-primary);
                                                                                                                }

                                                                                                                .btn-fill.primary {
                                                                                                                    color: var(--color-background);
                                                                                                                    background-color: var(--color-primary);
                                                                                                                    box-shadow: 0 0 8px var(--color-primary);
                                                                                                                }

                                                                                                                .btn.primary:hover,
                                                                                                                .btn-fill.primary:hover {
                                                                                                                    box-shadow: 0 0 24px var(--color-primary);
                                                                                                                }

                                                                                                                .btn.primary:active,
                                                                                                                .btn-fill.primary:active {
                                                                                                                    box-shadow: 0 0 36px var(--color-primary);
                                                                                                                }

                                                                                                                .btn.accent {
                                                                                                                    color: var(--color-accent);
                                                                                                                    background-color: var(--color-background);
                                                                                                                    box-shadow: 0 0 8px var(--color-accent);
                                                                                                                }

                                                                                                                .btn-fill.accent {
                                                                                                                    color: var(--color-background);
                                                                                                                    background-color: var(--color-accent);
                                                                                                                    box-shadow: 0 0 8px var(--color-accent);
                                                                                                                }

                                                                                                                .btn.accent:hover,
                                                                                                                .btn-fill.accent:hover {
                                                                                                                    box-shadow: 0 0 24px var(--color-accent);
                                                                                                                }

                                                                                                                .btn.accent:active,
                                                                                                                .btn-fill.accent:active {
                                                                                                                    box-shadow: 0 0 36px var(--color-accent);
                                                                                                                }

                                                                                                                .btn.info {
                                                                                                                    color: var(--color-info);
                                                                                                                    background-color: var(--color-background);
                                                                                                                    box-shadow: 0 0 8px var(--color-info);
                                                                                                                }

                                                                                                                .btn-fill.info {
                                                                                                                    color: var(--color-background);
                                                                                                                    background-color: var(--color-info);
                                                                                                                    box-shadow: 0 0 8px var(--color-info);
                                                                                                                }

                                                                                                                .btn.info:hover,
                                                                                                                .btn-fill.info:hover {
                                                                                                                    box-shadow: 0 0 24px var(--color-info);
                                                                                                                }

                                                                                                                .btn.info:active,
                                                                                                                .btn-fill.info:active {
                                                                                                                    box-shadow: 0 0 36px var(--color-info);
                                                                                                                }

                                                                                                                .btn.warning {
                                                                                                                    color: var(--color-warning);
                                                                                                                    background-color: var(--color-background);
                                                                                                                    box-shadow: 0 0 8px var(--color-warning);
                                                                                                                }

                                                                                                                .btn-fill.warning {
                                                                                                                    color: var(--color-background);
                                                                                                                    background-color: var(--color-warning);
                                                                                                                    box-shadow: 0 0 8px var(--color-warning);
                                                                                                                }

                                                                                                                .btn.warning:hover,
                                                                                                                .btn-fill.warning:hover {
                                                                                                                    box-shadow: 0 0 24px var(--color-warning);
                                                                                                                }

                                                                                                                .btn.warning:active,
                                                                                                                .btn-fill.warning:active {
                                                                                                                    box-shadow: 0 0 36px var(--color-warning);
                                                                                                                }

                                                                                                                .btn.danger {
                                                                                                                    color: var(--color-danger);
                                                                                                                    background-color: var(--color-background);
                                                                                                                    box-shadow: 0 0 8px var(--color-danger);
                                                                                                                }

                                                                                                                .btn-fill.danger {
                                                                                                                    color: var(--color-background);
                                                                                                                    background-color: var(--color-danger);
                                                                                                                    box-shadow: 0 0 8px var(--color-danger);
                                                                                                                }

                                                                                                                .btn.danger:hover,
                                                                                                                .btn-fill.danger:hover {
                                                                                                                    box-shadow: 0 0 24px var(--color-danger);
                                                                                                                }

                                                                                                                .btn.danger:active,
                                                                                                                .btn-fill.danger:active {
                                                                                                                    box-shadow: 0 0 36px var(--color-danger);
                                                                                                                }

                                                                                                                /* ------------ END BUTTONS ------------ */

                                                                                                                /* ------------ COLOURS ------------ */
                                                                                                                .text-primary {
                                                                                                                    color: var(--color-primary);
                                                                                                                }

                                                                                                                .bg-primary {
                                                                                                                    background-color: var(--color-primary);
                                                                                                                }

                                                                                                                .text-accent {
                                                                                                                    color: var(--color-accent);
                                                                                                                }

                                                                                                                .bg-accent {
                                                                                                                    background-color: var(--color-accent);
                                                                                                                }

                                                                                                                .text-background {
                                                                                                                    color: var(--color-background);
                                                                                                                }

                                                                                                                .bg-background {
                                                                                                                    background-color: var(--color-background);
                                                                                                                }

                                                                                                                .text-background-2 {
                                                                                                                    color: var(--color-background-2);
                                                                                                                }

                                                                                                                .bg-background-2 {
                                                                                                                    background-color: var(--color-background-2);
                                                                                                                }

                                                                                                                .text-info {
                                                                                                                    color: var(--color-info);
                                                                                                                }

                                                                                                                .bg-info {
                                                                                                                    background-color: var(--color-info);
                                                                                                                }

                                                                                                                .text-warning {
                                                                                                                    color: var(--color-warning);
                                                                                                                }

                                                                                                                .bg-warning {
                                                                                                                    background-color: var(--color-warning);
                                                                                                                }

                                                                                                                .text-danger {
                                                                                                                    color: var(--color-danger);
                                                                                                                }

                                                                                                                .bg-danger {
                                                                                                                    background-color: var(--color-danger);
                                                                                                                }

                                                                                                                .text-gray-5 {
                                                                                                                    color: var(--gray-5);
                                                                                                                }

                                                                                                                .text-gray-10 {
                                                                                                                    color: var(--gray-10);
                                                                                                                }

                                                                                                                .text-gray-30 {
                                                                                                                    color: var(--gray-30);
                                                                                                                }

                                                                                                                .text-gray-50 {
                                                                                                                    color: var(--gray-50);
                                                                                                                }

                                                                                                                .text-gray-70 {
                                                                                                                    color: var(--gray-70);
                                                                                                                }

                                                                                                                .text-gray-80 {
                                                                                                                    color: var(--gray-80);
                                                                                                                }


                                                                                                                /* ------------ END COLOURS ------------ */

                                                                                                                /* ------------ MARGIN ------------ */
                                                                                                                .margin-auto {
                                                                                                                    margin: auto;
                                                                                                                }

                                                                                                                .margin-xxs {
                                                                                                                    margin: var(--margin-xxs);
                                                                                                                }

                                                                                                                .margin-left-xxs {
                                                                                                                    margin-left: var(--margin-xxs);
                                                                                                                }

                                                                                                                .margin-right-xxs {
                                                                                                                    margin-right: var(--margin-xxs);
                                                                                                                }

                                                                                                                .margin-top-xxs {
                                                                                                                    margin-top: var(--margin-xxs);
                                                                                                                }

                                                                                                                .margin-bottom-xxs {
                                                                                                                    margin-bottom: var(--margin-xxs);
                                                                                                                }

                                                                                                                .margin-xs {
                                                                                                                    margin: var(--margin-xs);
                                                                                                                }

                                                                                                                .margin-left-xs {
                                                                                                                    margin-left: var(--margin-xs);
                                                                                                                }

                                                                                                                .margin-right-xs {
                                                                                                                    margin-right: var(--margin-xs);
                                                                                                                }

                                                                                                                .margin-top-xs {
                                                                                                                    margin-top: var(--margin-xs);
                                                                                                                }

                                                                                                                .margin-bottom-xs {
                                                                                                                    margin-bottom: var(--margin-xs);
                                                                                                                }

                                                                                                                .margin-s {
                                                                                                                    margin: var(--margin-s);
                                                                                                                }

                                                                                                                .margin-left-s {
                                                                                                                    margin-left: var(--margin-s);
                                                                                                                }

                                                                                                                .margin-right-s {
                                                                                                                    margin-right: var(--margin-s);
                                                                                                                }

                                                                                                                .margin-top-s {
                                                                                                                    margin-top: var(--margin-s);
                                                                                                                }

                                                                                                                .margin-bottom-s {
                                                                                                                    margin-bottom: var(--margin-s);
                                                                                                                }

                                                                                                                .margin-m {
                                                                                                                    margin: var(--margin-m);
                                                                                                                }

                                                                                                                .margin-left-m {
                                                                                                                    margin-left: var(--margin-m);
                                                                                                                }

                                                                                                                .margin-right-m {
                                                                                                                    margin-right: var(--margin-m);
                                                                                                                }

                                                                                                                .margin-top-m {
                                                                                                                    margin-top: var(--margin-m);
                                                                                                                }

                                                                                                                .margin-bottom-m {
                                                                                                                    margin-bottom: var(--margin-m);
                                                                                                                }

                                                                                                                .margin-l {
                                                                                                                    margin: var(--margin-l);
                                                                                                                }

                                                                                                                .margin-left-l {
                                                                                                                    margin-left: var(--margin-l);
                                                                                                                }

                                                                                                                .margin-right-l {
                                                                                                                    margin-right: var(--margin-l);
                                                                                                                }

                                                                                                                .margin-top-l {
                                                                                                                    margin-top: var(--margin-l);
                                                                                                                }

                                                                                                                .margin-bottom-l {
                                                                                                                    margin-bottom: var(--margin-l);
                                                                                                                }

                                                                                                                .margin-xl {
                                                                                                                    margin: var(--margin-xl);
                                                                                                                }

                                                                                                                .margin-left-xl {
                                                                                                                    margin-left: var(--margin-xl);
                                                                                                                }

                                                                                                                .margin-right-xl {
                                                                                                                    margin-right: var(--margin-xl);
                                                                                                                }

                                                                                                                .margin-top-xl {
                                                                                                                    margin-top: var(--margin-xl);
                                                                                                                }

                                                                                                                .margin-bottom-xl {
                                                                                                                    margin-bottom: var(--margin-xl);
                                                                                                                }

                                                                                                                .margin-xxl {
                                                                                                                    margin: var(--margin-xxl);
                                                                                                                }

                                                                                                                .margin-left-xxl {
                                                                                                                    margin-left: var(--margin-xxl);
                                                                                                                }

                                                                                                                .margin-right-xxl {
                                                                                                                    margin-right: var(--margin-xxl);
                                                                                                                }

                                                                                                                .margin-top-xxl {
                                                                                                                    margin-top: var(--margin-xxl);
                                                                                                                }

                                                                                                                .margin-bottom-xxl {
                                                                                                                    margin-bottom: var(--margin-xxl);
                                                                                                                }

                                                                                                                .margin-0 {
                                                                                                                    margin: 0;
                                                                                                                }

                                                                                                                .padding-0 {
                                                                                                                    padding: 0;
                                                                                                                }

                                                                                                                /* ------------ END MARGIN ------------ */

                                                                                                                /* ------------ PADDING ------------ */
                                                                                                                .padding-xxs {
                                                                                                                    padding: var(--padding-xxs);
                                                                                                                }

                                                                                                                .padding-left-xxs {
                                                                                                                    padding-left: var(--padding-xxs);
                                                                                                                }

                                                                                                                .padding-right-xxs {
                                                                                                                    padding-right: var(--padding-xxs);
                                                                                                                }

                                                                                                                .padding-top-xxs {
                                                                                                                    padding-top: var(--padding-xxs);
                                                                                                                }

                                                                                                                .padding-bottom-xxs {
                                                                                                                    padding-bottom: var(--padding-xxs);
                                                                                                                }

                                                                                                                .padding-xs {
                                                                                                                    padding: var(--padding-xs);
                                                                                                                }

                                                                                                                .padding-left-xs {
                                                                                                                    padding-left: var(--padding-xs);
                                                                                                                }

                                                                                                                .padding-right-xs {
                                                                                                                    padding-right: var(--padding-xs);
                                                                                                                }

                                                                                                                .padding-top-xs {
                                                                                                                    padding-top: var(--padding-xs);
                                                                                                                }

                                                                                                                .padding-bottom-xs {
                                                                                                                    padding-bottom: var(--padding-xs);
                                                                                                                }

                                                                                                                .padding-s {
                                                                                                                    padding: var(--padding-s);
                                                                                                                }

                                                                                                                .padding-left-s {
                                                                                                                    padding-left: var(--padding-s);
                                                                                                                }

                                                                                                                .padding-right-s {
                                                                                                                    padding-right: var(--padding-s);
                                                                                                                }

                                                                                                                .padding-top-s {
                                                                                                                    padding-top: var(--padding-s);
                                                                                                                }

                                                                                                                .padding-bottom-s {
                                                                                                                    padding-bottom: var(--padding-s);
                                                                                                                }

                                                                                                                .padding-m {
                                                                                                                    padding: var(--padding-m);
                                                                                                                }

                                                                                                                .padding-left-m {
                                                                                                                    padding-left: var(--padding-m);
                                                                                                                }

                                                                                                                .padding-right-m {
                                                                                                                    padding-right: var(--padding-m);
                                                                                                                }

                                                                                                                .padding-top-m {
                                                                                                                    padding-top: var(--padding-m);
                                                                                                                }

                                                                                                                .padding-bottom-m {
                                                                                                                    padding-bottom: var(--padding-m);
                                                                                                                }

                                                                                                                .padding-l {
                                                                                                                    padding: var(--padding-l);
                                                                                                                }

                                                                                                                .padding-left-l {
                                                                                                                    padding-left: var(--padding-l);
                                                                                                                }

                                                                                                                .padding-right-l {
                                                                                                                    padding-right: var(--padding-l);
                                                                                                                }

                                                                                                                .padding-top-l {
                                                                                                                    padding-top: var(--padding-l);
                                                                                                                }

                                                                                                                .padding-bottom-l {
                                                                                                                    padding-bottom: var(--padding-l);
                                                                                                                }

                                                                                                                .padding-xl {
                                                                                                                    padding: var(--padding-xl);
                                                                                                                }

                                                                                                                .padding-left-xl {
                                                                                                                    padding-left: var(--padding-xl);
                                                                                                                }

                                                                                                                .padding-right-xl {
                                                                                                                    padding-right: var(--padding-xl);
                                                                                                                }

                                                                                                                .padding-top-xl {
                                                                                                                    padding-top: var(--padding-xl);
                                                                                                                }

                                                                                                                .padding-bottom-xl {
                                                                                                                    padding-bottom: var(--padding-xl);
                                                                                                                }

                                                                                                                .padding-xxl {
                                                                                                                    padding: var(--padding-xxl);
                                                                                                                }

                                                                                                                .padding-left-xxl {
                                                                                                                    padding-left: var(--padding-xxl);
                                                                                                                }

                                                                                                                .padding-right-xxl {
                                                                                                                    padding-right: var(--padding-xxl);
                                                                                                                }

                                                                                                                .padding-top-xxl {
                                                                                                                    padding-top: var(--padding-xxl);
                                                                                                                }

                                                                                                                .padding-bottom-xxl {
                                                                                                                    padding-bottom: var(--padding-xxl);
                                                                                                                }

                                                                                                                /* ------------ END PADDING ------------ */

                                                                                                                .preloader {
                                                                                                                    position: fixed;
                                                                                                                    top: 0;
                                                                                                                    left: 0;
                                                                                                                    width: 100%;
                                                                                                                    height: 100%;
                                                                                                                    background-color: var(--color-background);
                                                                                                                    display: flex;
                                                                                                                    justify-content: center;
                                                                                                                    align-items: center;
                                                                                                                    z-index: 9999;
                                                                                                                }

                                                                                                                .preloader-inner {
                                                                                                                    background: linear-gradient(var(--color-background), var(--color-background)) padding-box,
                                                                                                                        linear-gradient(45deg, var(--color-secondary) 0%, var(--color-ternary) 100%) border-box;
                                                                                                                    border: solid 8px transparent;
                                                                                                                    margin-top: 80px;
                                                                                                                    border-radius: 50%;
                                                                                                                    width: 100px;
                                                                                                                    height: 100px;
                                                                                                                    display: inline-block;
                                                                                                                    transform-origin: center;
                                                                                                                    animation: swing 4s ease-in-out infinite;
                                                                                                                }

                                                                                                                .preloader-icon {
                                                                                                                    margin-top: 40px;
                                                                                                                    background: var(--color-secondary);
                                                                                                                    width: 8px;
                                                                                                                    height: 8px;
                                                                                                                    border-radius: 50%;
                                                                                                                    display: inline-block;
                                                                                                                    transform-origin: center;
                                                                                                                    animation: swing 4s ease-in-out infinite;
                                                                                                                }

                                                                                                                @keyframes swing {
                                                                                                                    0% {
                                                                                                                        transform: rotate(0deg);
                                                                                                                    }

                                                                                                                    100% {
                                                                                                                        transform: rotate(360deg);
                                                                                                                    }
                                                                                                                }

                                                                                                                /* https://codepen.io/sarazond/pen/LYGbwj */
                                                                                                                #stars-container {
                                                                                                                    position: absolute;
                                                                                                                    top: 0;
                                                                                                                    left: 0;
                                                                                                                    width: 100%;
                                                                                                                    height: 100%;
                                                                                                                    overflow: hidden;
                                                                                                                }

                                                                                                                #stars {
                                                                                                                    width: 1px;
                                                                                                                    height: 1px;
                                                                                                                    background: transparent;
                                                                                                                    box-shadow: 1595px 1903px #FFF, 1556px 1653px #FFF, 1385px 1442px #FFF, 1445px 534px #FFF, 173px 879px #FFF, 1558px 725px #FFF, 1223px 105px #FFF, 1683px 1653px #FFF, 852px 495px #FFF, 355px 1571px #FFF, 1742px 459px #FFF, 1000px 358px #FFF, 1711px 579px #FFF, 1768px 1315px #FFF, 1918px 171px #FFF, 1424px 1867px #FFF, 1768px 1485px #FFF, 1604px 992px #FFF, 1956px 1218px #FFF, 981px 248px #FFF, 448px 1204px #FFF, 747px 1248px #FFF, 940px 1148px #FFF, 1144px 1719px #FFF, 1303px 1802px #FFF, 428px 1136px #FFF, 1972px 1600px #FFF, 1040px 1208px #FFF, 275px 1107px #FFF, 1425px 1294px #FFF, 958px 1396px #FFF, 1996px 250px #FFF, 929px 490px #FFF, 1308px 1913px #FFF, 738px 1186px #FFF, 1699px 1422px #FFF, 1529px 1499px #FFF, 1921px 1161px #FFF, 338px 694px #FFF, 138px 1644px #FFF, 1625px 5px #FFF, 956px 1135px #FFF, 1676px 1265px #FFF, 388px 947px #FFF, 650px 167px #FFF, 6px 1425px #FFF, 775px 1342px #FFF, 955px 961px #FFF, 836px 806px #FFF, 1159px 840px #FFF, 444px 198px #FFF, 193px 1066px #FFF, 1772px 637px #FFF, 1228px 1766px #FFF, 1854px 366px #FFF, 116px 276px #FFF, 1861px 1644px #FFF, 1037px 1033px #FFF, 1348px 1312px #FFF, 1559px 1993px #FFF, 1175px 920px #FFF, 28px 517px #FFF, 293px 184px #FFF, 946px 1543px #FFF, 1074px 1987px #FFF, 844px 260px #FFF, 868px 1707px #FFF, 10px 935px #FFF, 1926px 526px #FFF, 377px 1941px #FFF, 1141px 852px #FFF, 7px 1004px #FFF, 1876px 1519px #FFF, 134px 1736px #FFF, 501px 454px #FFF, 154px 1511px #FFF, 1382px 1368px #FFF, 563px 761px #FFF, 662px 1903px #FFF, 817px 683px #FFF, 1528px 60px #FFF, 1005px 1666px #FFF, 94px 271px #FFF, 315px 432px #FFF, 644px 756px #FFF, 1528px 1084px #FFF, 1485px 1884px #FFF, 1571px 513px #FFF, 54px 1079px #FFF, 329px 628px #FFF, 1637px 471px #FFF, 1824px 1457px #FFF, 1902px 486px #FFF, 921px 643px #FFF, 1682px 999px #FFF, 1230px 274px #FFF, 1561px 462px #FFF, 237px 256px #FFF, 1261px 166px #FFF, 443px 1353px #FFF, 1595px 945px #FFF, 1860px 1515px #FFF, 1816px 758px #FFF, 1411px 1603px #FFF, 598px 1368px #FFF, 1260px 1164px #FFF, 1701px 1667px #FFF, 1570px 1098px #FFF, 1514px 284px #FFF, 845px 419px #FFF, 1976px 1266px #FFF, 1012px 1849px #FFF, 1601px 252px #FFF, 572px 1695px #FFF, 669px 604px #FFF, 365px 1936px #FFF, 1878px 1677px #FFF, 1599px 1902px #FFF, 1502px 1047px #FFF, 1144px 1246px #FFF, 1184px 530px #FFF, 928px 778px #FFF, 1031px 1410px #FFF, 757px 129px #FFF, 198px 1248px #FFF, 1453px 115px #FFF, 340px 1370px #FFF, 271px 1420px #FFF, 632px 723px #FFF, 65px 1756px #FFF, 232px 493px #FFF, 579px 314px #FFF, 856px 1522px #FFF, 930px 146px #FFF, 1051px 1520px #FFF, 451px 1067px #FFF, 717px 587px #FFF, 364px 1674px #FFF, 1967px 248px #FFF, 388px 953px #FFF, 114px 1254px #FFF, 1504px 159px #FFF, 285px 213px #FFF, 345px 660px #FFF, 1497px 903px #FFF, 1815px 1357px #FFF, 1312px 652px #FFF, 641px 1198px #FFF, 103px 1671px #FFF, 1676px 558px #FFF, 1074px 245px #FFF, 226px 1468px #FFF, 1667px 397px #FFF, 1214px 1147px #FFF, 1781px 675px #FFF, 992px 652px #FFF, 1574px 413px #FFF, 21px 1443px #FFF, 1423px 742px #FFF, 1004px 1210px #FFF, 1367px 1636px #FFF, 1853px 1574px #FFF, 72px 1189px #FFF, 244px 957px #FFF, 360px 1251px #FFF, 624px 531px #FFF, 9px 1058px #FFF, 846px 1825px #FFF, 1289px 924px #FFF, 1462px 573px #FFF, 1521px 865px #FFF, 884px 140px #FFF, 501px 1025px #FFF, 498px 59px #FFF, 1157px 1560px #FFF, 1527px 1065px #FFF, 1021px 1904px #FFF, 925px 470px #FFF, 1818px 819px #FFF, 1022px 636px #FFF, 1900px 538px #FFF, 1194px 422px #FFF, 107px 1731px #FFF, 246px 1408px #FFF, 754px 570px #FFF, 1567px 1737px #FFF, 664px 11px #FFF, 539px 135px #FFF, 129px 1577px #FFF, 626px 1215px #FFF, 356px 922px #FFF, 713px 587px #FFF, 144px 718px #FFF, 1787px 438px #FFF, 1211px 169px #FFF, 883px 222px #FFF, 586px 8px #FFF, 1848px 853px #FFF, 1425px 136px #FFF, 159px 343px #FFF, 134px 142px #FFF, 424px 1847px #FFF, 711px 1976px #FFF, 1104px 1846px #FFF, 792px 388px #FFF, 1304px 619px #FFF, 540px 1395px #FFF, 1253px 1515px #FFF, 1666px 354px #FFF, 1587px 161px #FFF, 528px 862px #FFF, 936px 1098px #FFF, 1720px 1389px #FFF, 335px 1193px #FFF, 34px 1057px #FFF, 680px 1676px #FFF, 1923px 1296px #FFF, 398px 1931px #FFF, 1894px 858px #FFF, 690px 1090px #FFF, 1165px 1085px #FFF, 1392px 381px #FFF, 1790px 512px #FFF, 1442px 429px #FFF, 486px 173px #FFF, 625px 1296px #FFF, 910px 1835px #FFF, 1202px 789px #FFF, 684px 66px #FFF, 1030px 1711px #FFF, 870px 1800px #FFF, 65px 1480px #FFF, 246px 564px #FFF, 780px 1028px #FFF, 1247px 461px #FFF, 644px 1428px #FFF, 1676px 1853px #FFF, 1103px 115px #FFF, 328px 1681px #FFF, 711px 1274px #FFF, 1666px 1295px #FFF, 504px 13px #FFF, 480px 1129px #FFF, 1279px 866px #FFF, 1397px 696px #FFF, 1133px 1368px #FFF, 1529px 703px #FFF, 1476px 1608px #FFF, 1225px 1528px #FFF, 146px 433px #FFF, 1642px 1042px #FFF, 1819px 1710px #FFF, 118px 252px #FFF, 1875px 1825px #FFF, 1473px 1806px #FFF, 1596px 723px #FFF, 783px 52px #FFF, 807px 1044px #FFF, 1712px 1124px #FFF, 463px 1006px #FFF, 1673px 845px #FFF, 1542px 1044px #FFF, 739px 1512px #FFF, 1255px 447px #FFF, 329px 700px #FFF, 995px 945px #FFF, 519px 495px #FFF, 309px 590px #FFF, 69px 1689px #FFF, 1049px 8px #FFF, 1717px 1660px #FFF, 1817px 1919px #FFF, 1533px 1854px #FFF, 308px 1130px #FFF, 1696px 551px #FFF, 359px 1597px #FFF, 1321px 1644px #FFF, 1006px 71px #FFF, 1904px 220px #FFF, 1533px 1334px #FFF, 713px 1864px #FFF, 829px 997px #FFF, 167px 1630px #FFF, 1446px 40px #FFF, 285px 1597px #FFF, 751px 942px #FFF, 444px 309px #FFF, 824px 1275px #FFF, 562px 1458px #FFF, 947px 585px #FFF, 362px 1406px #FFF, 788px 223px #FFF, 1172px 842px #FFF, 1909px 233px #FFF, 1946px 36px #FFF, 934px 1691px #FFF, 1218px 1965px #FFF, 1663px 502px #FFF, 355px 1860px #FFF, 1995px 1385px #FFF, 731px 598px #FFF, 159px 1619px #FFF, 1699px 1587px #FFF, 1299px 1697px #FFF, 1518px 888px #FFF, 659px 1282px #FFF, 171px 1409px #FFF, 1931px 883px #FFF, 1766px 1670px #FFF, 1540px 1548px #FFF, 1368px 521px #FFF, 1624px 1957px #FFF, 1114px 854px #FFF, 1593px 1427px #FFF, 1986px 222px #FFF, 250px 740px #FFF, 707px 537px #FFF, 1586px 230px #FFF, 108px 1346px #FFF, 634px 1044px #FFF, 230px 996px #FFF, 1760px 1721px #FFF, 1053px 1709px #FFF, 335px 1143px #FFF, 292px 1384px #FFF, 1406px 1190px #FFF, 1167px 1183px #FFF, 1765px 1945px #FFF, 913px 1022px #FFF, 733px 928px #FFF, 1052px 1179px #FFF, 140px 1013px #FFF, 1185px 1858px #FFF, 1223px 1956px #FFF, 700px 1844px #FFF, 829px 953px #FFF, 1502px 505px #FFF, 1035px 1384px #FFF, 1710px 802px #FFF, 775px 1023px #FFF, 1571px 239px #FFF, 590px 1826px #FFF, 819px 464px #FFF, 317px 1079px #FFF, 1708px 428px #FFF, 1701px 920px #FFF, 259px 1399px #FFF, 626px 802px #FFF, 524px 136px #FFF, 562px 782px #FFF, 729px 1051px #FFF, 109px 1954px #FFF, 1850px 381px #FFF, 177px 454px #FFF, 1013px 1626px #FFF, 478px 1607px #FFF, 1698px 1368px #FFF, 856px 760px #FFF, 1693px 211px #FFF, 1377px 1372px #FFF, 7px 1583px #FFF, 369px 118px #FFF, 1049px 138px #FFF, 1806px 589px #FFF, 318px 102px #FFF, 740px 949px #FFF, 504px 433px #FFF, 1498px 310px #FFF, 1622px 1437px #FFF, 1958px 175px #FFF, 76px 336px #FFF, 1124px 890px #FFF, 1101px 584px #FFF, 981px 372px #FFF, 912px 1086px #FFF, 1409px 1951px #FFF, 1612px 1599px #FFF, 1550px 1802px #FFF, 630px 689px #FFF, 186px 1093px #FFF, 781px 784px #FFF, 973px 1463px #FFF, 803px 1897px #FFF, 1113px 1852px #FFF, 1506px 491px #FFF, 292px 1500px #FFF, 667px 816px #FFF, 494px 983px #FFF, 938px 1816px #FFF, 1340px 76px #FFF, 817px 127px #FFF, 121px 556px #FFF, 1848px 842px #FFF, 896px 215px #FFF, 1092px 320px #FFF, 1506px 1191px #FFF, 521px 139px #FFF, 1592px 860px #FFF, 738px 916px #FFF, 679px 947px #FFF, 1369px 1404px #FFF, 1460px 974px #FFF, 669px 1222px #FFF, 829px 599px #FFF, 1902px 1563px #FFF, 150px 1675px #FFF, 997px 63px #FFF, 204px 988px #FFF, 1601px 1068px #FFF, 1192px 1677px #FFF, 1971px 1440px #FFF, 651px 1125px #FFF, 397px 872px #FFF, 1181px 38px #FFF, 1442px 39px #FFF, 612px 1827px #FFF, 1629px 563px #FFF, 370px 400px #FFF, 517px 1216px #FFF, 1470px 372px #FFF, 1969px 1377px #FFF, 1154px 894px #FFF, 1677px 1284px #FFF, 431px 800px #FFF, 546px 936px #FFF, 1802px 886px #FFF, 1831px 762px #FFF, 1144px 1307px #FFF, 354px 1804px #FFF, 1479px 1118px #FFF, 305px 155px #FFF, 1285px 1735px #FFF, 427px 588px #FFF, 494px 332px #FFF, 785px 26px #FFF, 942px 1372px #FFF, 466px 1037px #FFF, 1636px 1761px #FFF, 600px 987px #FFF, 50px 631px #FFF, 949px 817px #FFF, 111px 1634px #FFF, 1104px 1533px #FFF, 1821px 567px #FFF, 543px 1171px #FFF, 1514px 4px #FFF, 1816px 1706px #FFF, 102px 1889px #FFF, 107px 164px #FFF, 252px 923px #FFF, 1948px 1367px #FFF, 1573px 562px #FFF, 1571px 394px #FFF, 1292px 163px #FFF, 1832px 1932px #FFF, 1500px 551px #FFF, 1848px 813px #FFF, 214px 1985px #FFF, 1337px 289px #FFF, 1736px 1898px #FFF, 1996px 1681px #FFF, 1506px 156px #FFF, 901px 1603px #FFF, 1191px 964px #FFF, 1269px 908px #FFF, 1836px 1459px #FFF, 1975px 1468px #FFF, 1941px 1673px #FFF, 817px 1914px #FFF, 1477px 1851px #FFF, 851px 293px #FFF, 1041px 937px #FFF, 482px 319px #FFF, 915px 555px #FFF, 758px 1247px #FFF, 488px 339px #FFF, 1055px 1738px #FFF, 1624px 1972px #FFF, 505px 190px #FFF, 430px 1144px #FFF, 128px 263px #FFF, 1544px 918px #FFF, 325px 1603px #FFF, 174px 205px #FFF, 1483px 1424px #FFF, 1074px 491px #FFF, 1871px 1838px #FFF, 1334px 994px #FFF, 802px 1156px #FFF, 616px 631px #FFF, 1856px 294px #FFF, 15px 1687px #FFF, 1635px 1237px #FFF, 278px 1537px #FFF, 1397px 1153px #FFF, 1485px 1205px #FFF, 806px 1152px #FFF, 1039px 1402px #FFF, 439px 449px #FFF, 1735px 1481px #FFF, 450px 1976px #FFF, 1826px 1595px #FFF, 302px 1004px #FFF, 1464px 172px #FFF, 341px 1872px #FFF, 1462px 563px #FFF, 1511px 103px #FFF, 1656px 867px #FFF, 1539px 625px #FFF, 108px 1645px #FFF, 205px 41px #FFF, 273px 1462px #FFF, 81px 1827px #FFF, 317px 889px #FFF, 424px 872px #FFF, 466px 1065px #FFF, 1303px 1007px #FFF, 114px 823px #FFF, 1032px 1854px #FFF, 585px 742px #FFF, 1834px 1070px #FFF, 370px 1389px #FFF, 1245px 164px #FFF, 495px 1592px #FFF, 1116px 203px #FFF, 236px 1713px #FFF, 868px 1372px #FFF, 1661px 1952px #FFF, 434px 686px #FFF, 775px 641px #FFF, 1827px 146px #FFF, 1231px 470px #FFF, 1368px 1568px #FFF, 540px 1212px #FFF, 980px 1343px #FFF, 1214px 1790px #FFF, 720px 159px #FFF, 668px 1097px #FFF, 349px 542px #FFF, 31px 223px #FFF, 733px 1785px #FFF, 1218px 1728px #FFF, 976px 492px #FFF, 1287px 813px #FFF, 1295px 1046px #FFF, 1443px 1867px #FFF, 774px 1065px #FFF, 1525px 512px #FFF, 1302px 1493px #FFF, 349px 800px #FFF, 1177px 655px #FFF, 1273px 1087px #FFF, 1479px 1638px #FFF, 1190px 50px #FFF, 543px 585px #FFF, 1090px 776px #FFF, 1064px 1584px #FFF, 981px 903px #FFF, 1254px 595px #FFF, 199px 1617px #FFF, 354px 1604px #FFF, 867px 2000px #FFF, 1873px 1279px #FFF, 863px 297px #FFF, 144px 945px #FFF, 1175px 1321px #FFF, 1727px 1170px #FFF, 567px 813px #FFF, 94px 128px #FFF, 1834px 259px #FFF, 46px 104px #FFF, 1152px 909px #FFF, 1782px 1701px #FFF, 490px 346px #FFF, 690px 1791px #FFF, 462px 463px #FFF, 905px 1390px #FFF, 1789px 297px #FFF, 1474px 656px #FFF, 1789px 437px #FFF, 953px 1364px #FFF, 1205px 1588px #FFF, 956px 228px #FFF, 897px 1570px #FFF, 1982px 86px #FFF, 817px 1460px #FFF, 782px 899px #FFF, 109px 1123px #FFF, 957px 618px #FFF, 1317px 232px #FFF, 303px 1808px #FFF, 1782px 1919px #FFF, 247px 1030px #FFF, 70px 1387px #FFF, 693px 1822px #FFF, 440px 1309px #FFF, 902px 808px #FFF, 67px 467px #FFF, 1103px 1113px #FFF, 1054px 104px #FFF, 1776px 34px #FFF, 1385px 336px #FFF, 1805px 1696px #FFF, 1897px 1403px #FFF, 1006px 1460px #FFF, 499px 1589px #FFF, 334px 1932px #FFF, 694px 1310px #FFF, 1638px 304px #FFF, 309px 1139px #FFF, 457px 1450px #FFF, 1161px 456px #FFF, 481px 1672px #FFF, 1088px 750px #FFF, 104px 942px #FFF, 1323px 712px #FFF, 1826px 850px #FFF, 847px 642px #FFF, 922px 1403px #FFF, 1910px 1994px #FFF, 523px 364px #FFF, 713px 1134px #FFF, 1438px 778px #FFF, 772px 1007px #FFF, 1810px 1008px #FFF, 208px 1666px #FFF, 171px 270px #FFF, 1565px 1345px #FFF, 1096px 173px #FFF, 1097px 261px #FFF, 63px 1281px #FFF, 199px 331px #FFF, 395px 1398px #FFF, 321px 1322px #FFF, 1446px 711px #FFF, 1879px 1240px #FFF, 469px 545px #FFF, 224px 37px #FFF, 1955px 1242px #FFF, 1406px 881px #FFF, 1523px 1411px #FFF, 1032px 1481px #FFF, 1383px 1408px #FFF, 1933px 1530px #FFF, 881px 237px #FFF, 1997px 476px #FFF, 104px 1631px #FFF, 945px 566px #FFF, 842px 49px #FFF, 651px 167px #FFF, 143px 207px #FFF, 1680px 1966px #FFF, 45px 711px #FFF, 1657px 1756px #FFF, 1802px 206px #FFF, 266px 804px #FFF, 297px 1301px #FFF, 1082px 728px #FFF, 970px 1174px #FFF, 1184px 252px #FFF, 651px 1157px #FFF, 62px 506px #FFF, 1444px 1304px #FFF, 1921px 719px #FFF, 340px 1718px #FFF, 271px 1390px #FFF, 1065px 251px #FFF, 534px 453px #FFF, 160px 587px #FFF, 1709px 1140px #FFF, 1387px 1619px #FFF, 592px 1381px #FFF, 1343px 639px #FFF, 831px 293px #FFF, 1594px 953px #FFF, 1296px 1833px #FFF, 460px 183px #FFF, 1347px 588px #FFF, 1866px 802px #FFF, 341px 671px #FFF, 777px 1876px #FFF, 150px 1264px #FFF, 1866px 846px #FFF, 1165px 482px #FFF, 104px 1708px #FFF, 821px 1516px #FFF, 1221px 1443px #FFF, 14px 585px #FFF, 553px 282px #FFF, 1995px 1463px #FFF, 1183px 291px #FFF, 1340px 758px #FFF, 978px 8px #FFF, 569px 1997px #FFF, 1791px 840px #FFF, 1905px 1319px #FFF, 847px 598px #FFF, 674px 172px #FFF, 1474px 1494px #FFF, 1302px 25px #FFF;
                                                                                                                    animation: animStar 50s linear infinite;
                                                                                                                }

                                                                                                                #stars:after {
                                                                                                                    content: " ";
                                                                                                                    position: absolute;
                                                                                                                    top: 2000px;
                                                                                                                    width: 1px;
                                                                                                                    height: 1px;
                                                                                                                    background: transparent;
                                                                                                                    box-shadow: 1595px 1903px #FFF, 1556px 1653px #FFF, 1385px 1442px #FFF, 1445px 534px #FFF, 173px 879px #FFF, 1558px 725px #FFF, 1223px 105px #FFF, 1683px 1653px #FFF, 852px 495px #FFF, 355px 1571px #FFF, 1742px 459px #FFF, 1000px 358px #FFF, 1711px 579px #FFF, 1768px 1315px #FFF, 1918px 171px #FFF, 1424px 1867px #FFF, 1768px 1485px #FFF, 1604px 992px #FFF, 1956px 1218px #FFF, 981px 248px #FFF, 448px 1204px #FFF, 747px 1248px #FFF, 940px 1148px #FFF, 1144px 1719px #FFF, 1303px 1802px #FFF, 428px 1136px #FFF, 1972px 1600px #FFF, 1040px 1208px #FFF, 275px 1107px #FFF, 1425px 1294px #FFF, 958px 1396px #FFF, 1996px 250px #FFF, 929px 490px #FFF, 1308px 1913px #FFF, 738px 1186px #FFF, 1699px 1422px #FFF, 1529px 1499px #FFF, 1921px 1161px #FFF, 338px 694px #FFF, 138px 1644px #FFF, 1625px 5px #FFF, 956px 1135px #FFF, 1676px 1265px #FFF, 388px 947px #FFF, 650px 167px #FFF, 6px 1425px #FFF, 775px 1342px #FFF, 955px 961px #FFF, 836px 806px #FFF, 1159px 840px #FFF, 444px 198px #FFF, 193px 1066px #FFF, 1772px 637px #FFF, 1228px 1766px #FFF, 1854px 366px #FFF, 116px 276px #FFF, 1861px 1644px #FFF, 1037px 1033px #FFF, 1348px 1312px #FFF, 1559px 1993px #FFF, 1175px 920px #FFF, 28px 517px #FFF, 293px 184px #FFF, 946px 1543px #FFF, 1074px 1987px #FFF, 844px 260px #FFF, 868px 1707px #FFF, 10px 935px #FFF, 1926px 526px #FFF, 377px 1941px #FFF, 1141px 852px #FFF, 7px 1004px #FFF, 1876px 1519px #FFF, 134px 1736px #FFF, 501px 454px #FFF, 154px 1511px #FFF, 1382px 1368px #FFF, 563px 761px #FFF, 662px 1903px #FFF, 817px 683px #FFF, 1528px 60px #FFF, 1005px 1666px #FFF, 94px 271px #FFF, 315px 432px #FFF, 644px 756px #FFF, 1528px 1084px #FFF, 1485px 1884px #FFF, 1571px 513px #FFF, 54px 1079px #FFF, 329px 628px #FFF, 1637px 471px #FFF, 1824px 1457px #FFF, 1902px 486px #FFF, 921px 643px #FFF, 1682px 999px #FFF, 1230px 274px #FFF, 1561px 462px #FFF, 237px 256px #FFF, 1261px 166px #FFF, 443px 1353px #FFF, 1595px 945px #FFF, 1860px 1515px #FFF, 1816px 758px #FFF, 1411px 1603px #FFF, 598px 1368px #FFF, 1260px 1164px #FFF, 1701px 1667px #FFF, 1570px 1098px #FFF, 1514px 284px #FFF, 845px 419px #FFF, 1976px 1266px #FFF, 1012px 1849px #FFF, 1601px 252px #FFF, 572px 1695px #FFF, 669px 604px #FFF, 365px 1936px #FFF, 1878px 1677px #FFF, 1599px 1902px #FFF, 1502px 1047px #FFF, 1144px 1246px #FFF, 1184px 530px #FFF, 928px 778px #FFF, 1031px 1410px #FFF, 757px 129px #FFF, 198px 1248px #FFF, 1453px 115px #FFF, 340px 1370px #FFF, 271px 1420px #FFF, 632px 723px #FFF, 65px 1756px #FFF, 232px 493px #FFF, 579px 314px #FFF, 856px 1522px #FFF, 930px 146px #FFF, 1051px 1520px #FFF, 451px 1067px #FFF, 717px 587px #FFF, 364px 1674px #FFF, 1967px 248px #FFF, 388px 953px #FFF, 114px 1254px #FFF, 1504px 159px #FFF, 285px 213px #FFF, 345px 660px #FFF, 1497px 903px #FFF, 1815px 1357px #FFF, 1312px 652px #FFF, 641px 1198px #FFF, 103px 1671px #FFF, 1676px 558px #FFF, 1074px 245px #FFF, 226px 1468px #FFF, 1667px 397px #FFF, 1214px 1147px #FFF, 1781px 675px #FFF, 992px 652px #FFF, 1574px 413px #FFF, 21px 1443px #FFF, 1423px 742px #FFF, 1004px 1210px #FFF, 1367px 1636px #FFF, 1853px 1574px #FFF, 72px 1189px #FFF, 244px 957px #FFF, 360px 1251px #FFF, 624px 531px #FFF, 9px 1058px #FFF, 846px 1825px #FFF, 1289px 924px #FFF, 1462px 573px #FFF, 1521px 865px #FFF, 884px 140px #FFF, 501px 1025px #FFF, 498px 59px #FFF, 1157px 1560px #FFF, 1527px 1065px #FFF, 1021px 1904px #FFF, 925px 470px #FFF, 1818px 819px #FFF, 1022px 636px #FFF, 1900px 538px #FFF, 1194px 422px #FFF, 107px 1731px #FFF, 246px 1408px #FFF, 754px 570px #FFF, 1567px 1737px #FFF, 664px 11px #FFF, 539px 135px #FFF, 129px 1577px #FFF, 626px 1215px #FFF, 356px 922px #FFF, 713px 587px #FFF, 144px 718px #FFF, 1787px 438px #FFF, 1211px 169px #FFF, 883px 222px #FFF, 586px 8px #FFF, 1848px 853px #FFF, 1425px 136px #FFF, 159px 343px #FFF, 134px 142px #FFF, 424px 1847px #FFF, 711px 1976px #FFF, 1104px 1846px #FFF, 792px 388px #FFF, 1304px 619px #FFF, 540px 1395px #FFF, 1253px 1515px #FFF, 1666px 354px #FFF, 1587px 161px #FFF, 528px 862px #FFF, 936px 1098px #FFF, 1720px 1389px #FFF, 335px 1193px #FFF, 34px 1057px #FFF, 680px 1676px #FFF, 1923px 1296px #FFF, 398px 1931px #FFF, 1894px 858px #FFF, 690px 1090px #FFF, 1165px 1085px #FFF, 1392px 381px #FFF, 1790px 512px #FFF, 1442px 429px #FFF, 486px 173px #FFF, 625px 1296px #FFF, 910px 1835px #FFF, 1202px 789px #FFF, 684px 66px #FFF, 1030px 1711px #FFF, 870px 1800px #FFF, 65px 1480px #FFF, 246px 564px #FFF, 780px 1028px #FFF, 1247px 461px #FFF, 644px 1428px #FFF, 1676px 1853px #FFF, 1103px 115px #FFF, 328px 1681px #FFF, 711px 1274px #FFF, 1666px 1295px #FFF, 504px 13px #FFF, 480px 1129px #FFF, 1279px 866px #FFF, 1397px 696px #FFF, 1133px 1368px #FFF, 1529px 703px #FFF, 1476px 1608px #FFF, 1225px 1528px #FFF, 146px 433px #FFF, 1642px 1042px #FFF, 1819px 1710px #FFF, 118px 252px #FFF, 1875px 1825px #FFF, 1473px 1806px #FFF, 1596px 723px #FFF, 783px 52px #FFF, 807px 1044px #FFF, 1712px 1124px #FFF, 463px 1006px #FFF, 1673px 845px #FFF, 1542px 1044px #FFF, 739px 1512px #FFF, 1255px 447px #FFF, 329px 700px #FFF, 995px 945px #FFF, 519px 495px #FFF, 309px 590px #FFF, 69px 1689px #FFF, 1049px 8px #FFF, 1717px 1660px #FFF, 1817px 1919px #FFF, 1533px 1854px #FFF, 308px 1130px #FFF, 1696px 551px #FFF, 359px 1597px #FFF, 1321px 1644px #FFF, 1006px 71px #FFF, 1904px 220px #FFF, 1533px 1334px #FFF, 713px 1864px #FFF, 829px 997px #FFF, 167px 1630px #FFF, 1446px 40px #FFF, 285px 1597px #FFF, 751px 942px #FFF, 444px 309px #FFF, 824px 1275px #FFF, 562px 1458px #FFF, 947px 585px #FFF, 362px 1406px #FFF, 788px 223px #FFF, 1172px 842px #FFF, 1909px 233px #FFF, 1946px 36px #FFF, 934px 1691px #FFF, 1218px 1965px #FFF, 1663px 502px #FFF, 355px 1860px #FFF, 1995px 1385px #FFF, 731px 598px #FFF, 159px 1619px #FFF, 1699px 1587px #FFF, 1299px 1697px #FFF, 1518px 888px #FFF, 659px 1282px #FFF, 171px 1409px #FFF, 1931px 883px #FFF, 1766px 1670px #FFF, 1540px 1548px #FFF, 1368px 521px #FFF, 1624px 1957px #FFF, 1114px 854px #FFF, 1593px 1427px #FFF, 1986px 222px #FFF, 250px 740px #FFF, 707px 537px #FFF, 1586px 230px #FFF, 108px 1346px #FFF, 634px 1044px #FFF, 230px 996px #FFF, 1760px 1721px #FFF, 1053px 1709px #FFF, 335px 1143px #FFF, 292px 1384px #FFF, 1406px 1190px #FFF, 1167px 1183px #FFF, 1765px 1945px #FFF, 913px 1022px #FFF, 733px 928px #FFF, 1052px 1179px #FFF, 140px 1013px #FFF, 1185px 1858px #FFF, 1223px 1956px #FFF, 700px 1844px #FFF, 829px 953px #FFF, 1502px 505px #FFF, 1035px 1384px #FFF, 1710px 802px #FFF, 775px 1023px #FFF, 1571px 239px #FFF, 590px 1826px #FFF, 819px 464px #FFF, 317px 1079px #FFF, 1708px 428px #FFF, 1701px 920px #FFF, 259px 1399px #FFF, 626px 802px #FFF, 524px 136px #FFF, 562px 782px #FFF, 729px 1051px #FFF, 109px 1954px #FFF, 1850px 381px #FFF, 177px 454px #FFF, 1013px 1626px #FFF, 478px 1607px #FFF, 1698px 1368px #FFF, 856px 760px #FFF, 1693px 211px #FFF, 1377px 1372px #FFF, 7px 1583px #FFF, 369px 118px #FFF, 1049px 138px #FFF, 1806px 589px #FFF, 318px 102px #FFF, 740px 949px #FFF, 504px 433px #FFF, 1498px 310px #FFF, 1622px 1437px #FFF, 1958px 175px #FFF, 76px 336px #FFF, 1124px 890px #FFF, 1101px 584px #FFF, 981px 372px #FFF, 912px 1086px #FFF, 1409px 1951px #FFF, 1612px 1599px #FFF, 1550px 1802px #FFF, 630px 689px #FFF, 186px 1093px #FFF, 781px 784px #FFF, 973px 1463px #FFF, 803px 1897px #FFF, 1113px 1852px #FFF, 1506px 491px #FFF, 292px 1500px #FFF, 667px 816px #FFF, 494px 983px #FFF, 938px 1816px #FFF, 1340px 76px #FFF, 817px 127px #FFF, 121px 556px #FFF, 1848px 842px #FFF, 896px 215px #FFF, 1092px 320px #FFF, 1506px 1191px #FFF, 521px 139px #FFF, 1592px 860px #FFF, 738px 916px #FFF, 679px 947px #FFF, 1369px 1404px #FFF, 1460px 974px #FFF, 669px 1222px #FFF, 829px 599px #FFF, 1902px 1563px #FFF, 150px 1675px #FFF, 997px 63px #FFF, 204px 988px #FFF, 1601px 1068px #FFF, 1192px 1677px #FFF, 1971px 1440px #FFF, 651px 1125px #FFF, 397px 872px #FFF, 1181px 38px #FFF, 1442px 39px #FFF, 612px 1827px #FFF, 1629px 563px #FFF, 370px 400px #FFF, 517px 1216px #FFF, 1470px 372px #FFF, 1969px 1377px #FFF, 1154px 894px #FFF, 1677px 1284px #FFF, 431px 800px #FFF, 546px 936px #FFF, 1802px 886px #FFF, 1831px 762px #FFF, 1144px 1307px #FFF, 354px 1804px #FFF, 1479px 1118px #FFF, 305px 155px #FFF, 1285px 1735px #FFF, 427px 588px #FFF, 494px 332px #FFF, 785px 26px #FFF, 942px 1372px #FFF, 466px 1037px #FFF, 1636px 1761px #FFF, 600px 987px #FFF, 50px 631px #FFF, 949px 817px #FFF, 111px 1634px #FFF, 1104px 1533px #FFF, 1821px 567px #FFF, 543px 1171px #FFF, 1514px 4px #FFF, 1816px 1706px #FFF, 102px 1889px #FFF, 107px 164px #FFF, 252px 923px #FFF, 1948px 1367px #FFF, 1573px 562px #FFF, 1571px 394px #FFF, 1292px 163px #FFF, 1832px 1932px #FFF, 1500px 551px #FFF, 1848px 813px #FFF, 214px 1985px #FFF, 1337px 289px #FFF, 1736px 1898px #FFF, 1996px 1681px #FFF, 1506px 156px #FFF, 901px 1603px #FFF, 1191px 964px #FFF, 1269px 908px #FFF, 1836px 1459px #FFF, 1975px 1468px #FFF, 1941px 1673px #FFF, 817px 1914px #FFF, 1477px 1851px #FFF, 851px 293px #FFF, 1041px 937px #FFF, 482px 319px #FFF, 915px 555px #FFF, 758px 1247px #FFF, 488px 339px #FFF, 1055px 1738px #FFF, 1624px 1972px #FFF, 505px 190px #FFF, 430px 1144px #FFF, 128px 263px #FFF, 1544px 918px #FFF, 325px 1603px #FFF, 174px 205px #FFF, 1483px 1424px #FFF, 1074px 491px #FFF, 1871px 1838px #FFF, 1334px 994px #FFF, 802px 1156px #FFF, 616px 631px #FFF, 1856px 294px #FFF, 15px 1687px #FFF, 1635px 1237px #FFF, 278px 1537px #FFF, 1397px 1153px #FFF, 1485px 1205px #FFF, 806px 1152px #FFF, 1039px 1402px #FFF, 439px 449px #FFF, 1735px 1481px #FFF, 450px 1976px #FFF, 1826px 1595px #FFF, 302px 1004px #FFF, 1464px 172px #FFF, 341px 1872px #FFF, 1462px 563px #FFF, 1511px 103px #FFF, 1656px 867px #FFF, 1539px 625px #FFF, 108px 1645px #FFF, 205px 41px #FFF, 273px 1462px #FFF, 81px 1827px #FFF, 317px 889px #FFF, 424px 872px #FFF, 466px 1065px #FFF, 1303px 1007px #FFF, 114px 823px #FFF, 1032px 1854px #FFF, 585px 742px #FFF, 1834px 1070px #FFF, 370px 1389px #FFF, 1245px 164px #FFF, 495px 1592px #FFF, 1116px 203px #FFF, 236px 1713px #FFF, 868px 1372px #FFF, 1661px 1952px #FFF, 434px 686px #FFF, 775px 641px #FFF, 1827px 146px #FFF, 1231px 470px #FFF, 1368px 1568px #FFF, 540px 1212px #FFF, 980px 1343px #FFF, 1214px 1790px #FFF, 720px 159px #FFF, 668px 1097px #FFF, 349px 542px #FFF, 31px 223px #FFF, 733px 1785px #FFF, 1218px 1728px #FFF, 976px 492px #FFF, 1287px 813px #FFF, 1295px 1046px #FFF, 1443px 1867px #FFF, 774px 1065px #FFF, 1525px 512px #FFF, 1302px 1493px #FFF, 349px 800px #FFF, 1177px 655px #FFF, 1273px 1087px #FFF, 1479px 1638px #FFF, 1190px 50px #FFF, 543px 585px #FFF, 1090px 776px #FFF, 1064px 1584px #FFF, 981px 903px #FFF, 1254px 595px #FFF, 199px 1617px #FFF, 354px 1604px #FFF, 867px 2000px #FFF, 1873px 1279px #FFF, 863px 297px #FFF, 144px 945px #FFF, 1175px 1321px #FFF, 1727px 1170px #FFF, 567px 813px #FFF, 94px 128px #FFF, 1834px 259px #FFF, 46px 104px #FFF, 1152px 909px #FFF, 1782px 1701px #FFF, 490px 346px #FFF, 690px 1791px #FFF, 462px 463px #FFF, 905px 1390px #FFF, 1789px 297px #FFF, 1474px 656px #FFF, 1789px 437px #FFF, 953px 1364px #FFF, 1205px 1588px #FFF, 956px 228px #FFF, 897px 1570px #FFF, 1982px 86px #FFF, 817px 1460px #FFF, 782px 899px #FFF, 109px 1123px #FFF, 957px 618px #FFF, 1317px 232px #FFF, 303px 1808px #FFF, 1782px 1919px #FFF, 247px 1030px #FFF, 70px 1387px #FFF, 693px 1822px #FFF, 440px 1309px #FFF, 902px 808px #FFF, 67px 467px #FFF, 1103px 1113px #FFF, 1054px 104px #FFF, 1776px 34px #FFF, 1385px 336px #FFF, 1805px 1696px #FFF, 1897px 1403px #FFF, 1006px 1460px #FFF, 499px 1589px #FFF, 334px 1932px #FFF, 694px 1310px #FFF, 1638px 304px #FFF, 309px 1139px #FFF, 457px 1450px #FFF, 1161px 456px #FFF, 481px 1672px #FFF, 1088px 750px #FFF, 104px 942px #FFF, 1323px 712px #FFF, 1826px 850px #FFF, 847px 642px #FFF, 922px 1403px #FFF, 1910px 1994px #FFF, 523px 364px #FFF, 713px 1134px #FFF, 1438px 778px #FFF, 772px 1007px #FFF, 1810px 1008px #FFF, 208px 1666px #FFF, 171px 270px #FFF, 1565px 1345px #FFF, 1096px 173px #FFF, 1097px 261px #FFF, 63px 1281px #FFF, 199px 331px #FFF, 395px 1398px #FFF, 321px 1322px #FFF, 1446px 711px #FFF, 1879px 1240px #FFF, 469px 545px #FFF, 224px 37px #FFF, 1955px 1242px #FFF, 1406px 881px #FFF, 1523px 1411px #FFF, 1032px 1481px #FFF, 1383px 1408px #FFF, 1933px 1530px #FFF, 881px 237px #FFF, 1997px 476px #FFF, 104px 1631px #FFF, 945px 566px #FFF, 842px 49px #FFF, 651px 167px #FFF, 143px 207px #FFF, 1680px 1966px #FFF, 45px 711px #FFF, 1657px 1756px #FFF, 1802px 206px #FFF, 266px 804px #FFF, 297px 1301px #FFF, 1082px 728px #FFF, 970px 1174px #FFF, 1184px 252px #FFF, 651px 1157px #FFF, 62px 506px #FFF, 1444px 1304px #FFF, 1921px 719px #FFF, 340px 1718px #FFF, 271px 1390px #FFF, 1065px 251px #FFF, 534px 453px #FFF, 160px 587px #FFF, 1709px 1140px #FFF, 1387px 1619px #FFF, 592px 1381px #FFF, 1343px 639px #FFF, 831px 293px #FFF, 1594px 953px #FFF, 1296px 1833px #FFF, 460px 183px #FFF, 1347px 588px #FFF, 1866px 802px #FFF, 341px 671px #FFF, 777px 1876px #FFF, 150px 1264px #FFF, 1866px 846px #FFF, 1165px 482px #FFF, 104px 1708px #FFF, 821px 1516px #FFF, 1221px 1443px #FFF, 14px 585px #FFF, 553px 282px #FFF, 1995px 1463px #FFF, 1183px 291px #FFF, 1340px 758px #FFF, 978px 8px #FFF, 569px 1997px #FFF, 1791px 840px #FFF, 1905px 1319px #FFF, 847px 598px #FFF, 674px 172px #FFF, 1474px 1494px #FFF, 1302px 25px #FFF;
                                                                                                                }

                                                                                                                #stars2 {
                                                                                                                    width: 2px;
                                                                                                                    height: 2px;
                                                                                                                    background: transparent;
                                                                                                                    box-shadow: 840px 1694px #FFF, 875px 1075px #FFF, 1589px 110px #FFF, 402px 1983px #FFF, 1239px 1009px #FFF, 1820px 1556px #FFF, 687px 970px #FFF, 1803px 1723px #FFF, 1076px 788px #FFF, 1996px 794px #FFF, 498px 734px #FFF, 1156px 1438px #FFF, 1875px 1585px #FFF, 195px 1333px #FFF, 565px 1192px #FFF, 1507px 1175px #FFF, 728px 1284px #FFF, 709px 992px #FFF, 600px 1422px #FFF, 764px 1154px #FFF, 1350px 523px #FFF, 1618px 333px #FFF, 872px 109px #FFF, 787px 688px #FFF, 530px 644px #FFF, 665px 163px #FFF, 1018px 218px #FFF, 1134px 511px #FFF, 1352px 482px #FFF, 864px 1311px #FFF, 1603px 532px #FFF, 1250px 1836px #FFF, 252px 845px #FFF, 601px 1274px #FFF, 1516px 1133px #FFF, 6px 1282px #FFF, 1748px 1215px #FFF, 367px 994px #FFF, 520px 1404px #FFF, 813px 1880px #FFF, 1876px 819px #FFF, 815px 90px #FFF, 1285px 908px #FFF, 1518px 534px #FFF, 248px 712px #FFF, 310px 777px #FFF, 1446px 455px #FFF, 1301px 187px #FFF, 1020px 1189px #FFF, 1850px 729px #FFF, 1318px 1378px #FFF, 1085px 1072px #FFF, 914px 1202px #FFF, 224px 1015px #FFF, 29px 737px #FFF, 321px 1317px #FFF, 1753px 1672px #FFF, 1998px 1030px #FFF, 1439px 539px #FFF, 43px 732px #FFF, 595px 1254px #FFF, 309px 1858px #FFF, 1363px 1180px #FFF, 1863px 1366px #FFF, 1403px 1154px #FFF, 570px 604px #FFF, 479px 800px #FFF, 769px 843px #FFF, 1609px 1674px #FFF, 1742px 937px #FFF, 900px 690px #FFF, 617px 739px #FFF, 1757px 1080px #FFF, 591px 1093px #FFF, 433px 30px #FFF, 1560px 695px #FFF, 669px 52px #FFF, 1306px 1426px #FFF, 1566px 1223px #FFF, 1226px 114px #FFF, 332px 1897px #FFF, 1576px 1617px #FFF, 995px 1212px #FFF, 1133px 1513px #FFF, 1466px 686px #FFF, 1124px 1458px #FFF, 1219px 687px #FFF, 1161px 1026px #FFF, 1788px 988px #FFF, 1560px 202px #FFF, 1938px 1936px #FFF, 1402px 1552px #FFF, 1843px 1861px #FFF, 1123px 1068px #FFF, 380px 1437px #FFF, 1025px 1673px #FFF, 1951px 1248px #FFF, 292px 806px #FFF, 1468px 849px #FFF, 1793px 178px #FFF, 1604px 1743px #FFF, 1060px 1404px #FFF, 49px 1772px #FFF, 950px 1764px #FFF, 1902px 61px #FFF, 1761px 1764px #FFF, 1869px 255px #FFF, 1725px 236px #FFF, 1859px 1709px #FFF, 1002px 1161px #FFF, 1344px 1990px #FFF, 1497px 1143px #FFF, 1086px 1512px #FFF, 30px 1813px #FFF, 48px 981px #FFF, 1869px 130px #FFF, 1486px 559px #FFF, 351px 1910px #FFF, 790px 1898px #FFF, 507px 1882px #FFF, 1403px 1805px #FFF, 327px 395px #FFF, 1594px 587px #FFF, 1939px 1827px #FFF, 1232px 867px #FFF, 420px 841px #FFF, 1974px 797px #FFF, 1148px 226px #FFF, 1143px 1985px #FFF, 1079px 1845px #FFF, 1496px 874px #FFF, 1299px 1949px #FFF, 372px 277px #FFF, 172px 1668px #FFF, 302px 1030px #FFF, 971px 708px #FFF, 1735px 1880px #FFF, 880px 1661px #FFF, 1400px 1898px #FFF, 1149px 1428px #FFF, 1426px 1414px #FFF, 716px 1360px #FFF, 457px 1477px #FFF, 927px 1930px #FFF, 986px 888px #FFF, 1473px 738px #FFF, 1086px 648px #FFF, 798px 176px #FFF, 658px 986px #FFF, 1594px 72px #FFF, 615px 926px #FFF, 430px 1196px #FFF, 186px 1260px #FFF, 1754px 1494px #FFF, 863px 747px #FFF, 1750px 503px #FFF, 575px 668px #FFF, 482px 576px #FFF, 309px 320px #FFF, 256px 1283px #FFF, 341px 883px #FFF, 588px 671px #FFF, 247px 897px #FFF, 751px 1948px #FFF, 364px 1844px #FFF, 1537px 938px #FFF, 1886px 981px #FFF, 1960px 100px #FFF, 160px 1265px #FFF, 1693px 1607px #FFF, 626px 856px #FFF, 505px 416px #FFF, 1400px 172px #FFF, 484px 736px #FFF, 1347px 10px #FFF, 619px 171px #FFF, 215px 166px #FFF, 1679px 1025px #FFF, 1040px 40px #FFF, 1316px 1446px #FFF, 531px 105px #FFF, 942px 1319px #FFF, 238px 610px #FFF, 1072px 458px #FFF, 859px 1485px #FFF, 520px 826px #FFF, 1437px 1045px #FFF, 1788px 1918px #FFF, 888px 1934px #FFF, 641px 1736px #FFF, 934px 1494px #FFF, 531px 596px #FFF, 1462px 695px #FFF, 1537px 1741px #FFF, 273px 743px #FFF, 833px 313px #FFF, 358px 1186px #FFF, 481px 1420px #FFF, 1922px 713px #FFF, 75px 588px #FFF;
                                                                                                                    animation: animStar 100s linear infinite;
                                                                                                                }

                                                                                                                #stars2:after {
                                                                                                                    content: " ";
                                                                                                                    position: absolute;
                                                                                                                    top: 2000px;
                                                                                                                    width: 2px;
                                                                                                                    height: 2px;
                                                                                                                    background: transparent;
                                                                                                                    box-shadow: 840px 1694px #FFF, 875px 1075px #FFF, 1589px 110px #FFF, 402px 1983px #FFF, 1239px 1009px #FFF, 1820px 1556px #FFF, 687px 970px #FFF, 1803px 1723px #FFF, 1076px 788px #FFF, 1996px 794px #FFF, 498px 734px #FFF, 1156px 1438px #FFF, 1875px 1585px #FFF, 195px 1333px #FFF, 565px 1192px #FFF, 1507px 1175px #FFF, 728px 1284px #FFF, 709px 992px #FFF, 600px 1422px #FFF, 764px 1154px #FFF, 1350px 523px #FFF, 1618px 333px #FFF, 872px 109px #FFF, 787px 688px #FFF, 530px 644px #FFF, 665px 163px #FFF, 1018px 218px #FFF, 1134px 511px #FFF, 1352px 482px #FFF, 864px 1311px #FFF, 1603px 532px #FFF, 1250px 1836px #FFF, 252px 845px #FFF, 601px 1274px #FFF, 1516px 1133px #FFF, 6px 1282px #FFF, 1748px 1215px #FFF, 367px 994px #FFF, 520px 1404px #FFF, 813px 1880px #FFF, 1876px 819px #FFF, 815px 90px #FFF, 1285px 908px #FFF, 1518px 534px #FFF, 248px 712px #FFF, 310px 777px #FFF, 1446px 455px #FFF, 1301px 187px #FFF, 1020px 1189px #FFF, 1850px 729px #FFF, 1318px 1378px #FFF, 1085px 1072px #FFF, 914px 1202px #FFF, 224px 1015px #FFF, 29px 737px #FFF, 321px 1317px #FFF, 1753px 1672px #FFF, 1998px 1030px #FFF, 1439px 539px #FFF, 43px 732px #FFF, 595px 1254px #FFF, 309px 1858px #FFF, 1363px 1180px #FFF, 1863px 1366px #FFF, 1403px 1154px #FFF, 570px 604px #FFF, 479px 800px #FFF, 769px 843px #FFF, 1609px 1674px #FFF, 1742px 937px #FFF, 900px 690px #FFF, 617px 739px #FFF, 1757px 1080px #FFF, 591px 1093px #FFF, 433px 30px #FFF, 1560px 695px #FFF, 669px 52px #FFF, 1306px 1426px #FFF, 1566px 1223px #FFF, 1226px 114px #FFF, 332px 1897px #FFF, 1576px 1617px #FFF, 995px 1212px #FFF, 1133px 1513px #FFF, 1466px 686px #FFF, 1124px 1458px #FFF, 1219px 687px #FFF, 1161px 1026px #FFF, 1788px 988px #FFF, 1560px 202px #FFF, 1938px 1936px #FFF, 1402px 1552px #FFF, 1843px 1861px #FFF, 1123px 1068px #FFF, 380px 1437px #FFF, 1025px 1673px #FFF, 1951px 1248px #FFF, 292px 806px #FFF, 1468px 849px #FFF, 1793px 178px #FFF, 1604px 1743px #FFF, 1060px 1404px #FFF, 49px 1772px #FFF, 950px 1764px #FFF, 1902px 61px #FFF, 1761px 1764px #FFF, 1869px 255px #FFF, 1725px 236px #FFF, 1859px 1709px #FFF, 1002px 1161px #FFF, 1344px 1990px #FFF, 1497px 1143px #FFF, 1086px 1512px #FFF, 30px 1813px #FFF, 48px 981px #FFF, 1869px 130px #FFF, 1486px 559px #FFF, 351px 1910px #FFF, 790px 1898px #FFF, 507px 1882px #FFF, 1403px 1805px #FFF, 327px 395px #FFF, 1594px 587px #FFF, 1939px 1827px #FFF, 1232px 867px #FFF, 420px 841px #FFF, 1974px 797px #FFF, 1148px 226px #FFF, 1143px 1985px #FFF, 1079px 1845px #FFF, 1496px 874px #FFF, 1299px 1949px #FFF, 372px 277px #FFF, 172px 1668px #FFF, 302px 1030px #FFF, 971px 708px #FFF, 1735px 1880px #FFF, 880px 1661px #FFF, 1400px 1898px #FFF, 1149px 1428px #FFF, 1426px 1414px #FFF, 716px 1360px #FFF, 457px 1477px #FFF, 927px 1930px #FFF, 986px 888px #FFF, 1473px 738px #FFF, 1086px 648px #FFF, 798px 176px #FFF, 658px 986px #FFF, 1594px 72px #FFF, 615px 926px #FFF, 430px 1196px #FFF, 186px 1260px #FFF, 1754px 1494px #FFF, 863px 747px #FFF, 1750px 503px #FFF, 575px 668px #FFF, 482px 576px #FFF, 309px 320px #FFF, 256px 1283px #FFF, 341px 883px #FFF, 588px 671px #FFF, 247px 897px #FFF, 751px 1948px #FFF, 364px 1844px #FFF, 1537px 938px #FFF, 1886px 981px #FFF, 1960px 100px #FFF, 160px 1265px #FFF, 1693px 1607px #FFF, 626px 856px #FFF, 505px 416px #FFF, 1400px 172px #FFF, 484px 736px #FFF, 1347px 10px #FFF, 619px 171px #FFF, 215px 166px #FFF, 1679px 1025px #FFF, 1040px 40px #FFF, 1316px 1446px #FFF, 531px 105px #FFF, 942px 1319px #FFF, 238px 610px #FFF, 1072px 458px #FFF, 859px 1485px #FFF, 520px 826px #FFF, 1437px 1045px #FFF, 1788px 1918px #FFF, 888px 1934px #FFF, 641px 1736px #FFF, 934px 1494px #FFF, 531px 596px #FFF, 1462px 695px #FFF, 1537px 1741px #FFF, 273px 743px #FFF, 833px 313px #FFF, 358px 1186px #FFF, 481px 1420px #FFF, 1922px 713px #FFF, 75px 588px #FFF;
                                                                                                                }

                                                                                                                #stars3 {
                                                                                                                    width: 3px;
                                                                                                                    height: 3px;
                                                                                                                    background: transparent;
                                                                                                                    box-shadow: 1125px 1303px #FFF, 510px 1993px #FFF, 1526px 1367px #FFF, 1764px 1013px #FFF, 1305px 500px #FFF, 1629px 1543px #FFF, 733px 924px #FFF, 1017px 578px #FFF, 1269px 230px #FFF, 213px 1522px #FFF, 1328px 597px #FFF, 1829px 111px #FFF, 1018px 418px #FFF, 416px 397px #FFF, 470px 593px #FFF, 1964px 845px #FFF, 1906px 1136px #FFF, 1740px 1219px #FFF, 1008px 586px #FFF, 784px 337px #FFF, 453px 1066px #FFF, 1327px 518px #FFF, 1426px 1038px #FFF, 1329px 1649px #FFF, 1654px 1359px #FFF, 643px 224px #FFF, 564px 1444px #FFF, 1927px 286px #FFF, 1708px 202px #FFF, 815px 360px #FFF, 136px 469px #FFF, 1104px 717px #FFF, 1271px 1791px #FFF, 105px 1543px #FFF, 821px 1115px #FFF, 1615px 1509px #FFF, 1962px 1660px #FFF, 128px 259px #FFF, 1798px 1275px #FFF, 480px 1467px #FFF, 72px 1117px #FFF, 832px 480px #FFF, 1203px 759px #FFF, 462px 1473px #FFF, 1791px 912px #FFF, 3px 665px #FFF, 380px 810px #FFF, 1273px 1450px #FFF, 1342px 1145px #FFF, 836px 359px #FFF, 206px 131px #FFF, 1652px 37px #FFF, 519px 65px #FFF, 1556px 1163px #FFF, 751px 858px #FFF, 51px 459px #FFF, 833px 146px #FFF, 1912px 1115px #FFF, 1717px 682px #FFF, 1619px 1411px #FFF, 1923px 751px #FFF, 1758px 1938px #FFF, 1692px 645px #FFF, 1369px 594px #FFF, 150px 847px #FFF, 1901px 225px #FFF, 224px 1319px #FFF, 160px 1087px #FFF, 1394px 726px #FFF, 1102px 1820px #FFF, 1520px 357px #FFF, 311px 1272px #FFF, 1197px 511px #FFF, 580px 1315px #FFF, 69px 1814px #FFF, 1599px 1561px #FFF, 1727px 1624px #FFF, 1347px 1823px #FFF, 778px 1156px #FFF, 1342px 694px #FFF, 802px 1128px #FFF, 1697px 1141px #FFF, 566px 1916px #FFF, 1261px 136px #FFF, 1476px 635px #FFF, 1641px 1115px #FFF, 184px 1917px #FFF, 226px 495px #FFF, 1011px 575px #FFF, 1790px 451px #FFF, 1612px 673px #FFF, 1892px 1688px #FFF, 449px 1846px #FFF, 1286px 526px #FFF, 1317px 1584px #FFF, 402px 59px #FFF, 1405px 129px #FFF, 1328px 1312px #FFF, 41px 1493px #FFF, 1032px 534px #FFF;
                                                                                                                    animation: animStar 150s linear infinite;
                                                                                                                }

                                                                                                                #stars3:after {
                                                                                                                    content: " ";
                                                                                                                    position: absolute;
                                                                                                                    top: 2000px;
                                                                                                                    width: 3px;
                                                                                                                    height: 3px;
                                                                                                                    background: transparent;
                                                                                                                    box-shadow: 1125px 1303px #FFF, 510px 1993px #FFF, 1526px 1367px #FFF, 1764px 1013px #FFF, 1305px 500px #FFF, 1629px 1543px #FFF, 733px 924px #FFF, 1017px 578px #FFF, 1269px 230px #FFF, 213px 1522px #FFF, 1328px 597px #FFF, 1829px 111px #FFF, 1018px 418px #FFF, 416px 397px #FFF, 470px 593px #FFF, 1964px 845px #FFF, 1906px 1136px #FFF, 1740px 1219px #FFF, 1008px 586px #FFF, 784px 337px #FFF, 453px 1066px #FFF, 1327px 518px #FFF, 1426px 1038px #FFF, 1329px 1649px #FFF, 1654px 1359px #FFF, 643px 224px #FFF, 564px 1444px #FFF, 1927px 286px #FFF, 1708px 202px #FFF, 815px 360px #FFF, 136px 469px #FFF, 1104px 717px #FFF, 1271px 1791px #FFF, 105px 1543px #FFF, 821px 1115px #FFF, 1615px 1509px #FFF, 1962px 1660px #FFF, 128px 259px #FFF, 1798px 1275px #FFF, 480px 1467px #FFF, 72px 1117px #FFF, 832px 480px #FFF, 1203px 759px #FFF, 462px 1473px #FFF, 1791px 912px #FFF, 3px 665px #FFF, 380px 810px #FFF, 1273px 1450px #FFF, 1342px 1145px #FFF, 836px 359px #FFF, 206px 131px #FFF, 1652px 37px #FFF, 519px 65px #FFF, 1556px 1163px #FFF, 751px 858px #FFF, 51px 459px #FFF, 833px 146px #FFF, 1912px 1115px #FFF, 1717px 682px #FFF, 1619px 1411px #FFF, 1923px 751px #FFF, 1758px 1938px #FFF, 1692px 645px #FFF, 1369px 594px #FFF, 150px 847px #FFF, 1901px 225px #FFF, 224px 1319px #FFF, 160px 1087px #FFF, 1394px 726px #FFF, 1102px 1820px #FFF, 1520px 357px #FFF, 311px 1272px #FFF, 1197px 511px #FFF, 580px 1315px #FFF, 69px 1814px #FFF, 1599px 1561px #FFF, 1727px 1624px #FFF, 1347px 1823px #FFF, 778px 1156px #FFF, 1342px 694px #FFF, 802px 1128px #FFF, 1697px 1141px #FFF, 566px 1916px #FFF, 1261px 136px #FFF, 1476px 635px #FFF, 1641px 1115px #FFF, 184px 1917px #FFF, 226px 495px #FFF, 1011px 575px #FFF, 1790px 451px #FFF, 1612px 673px #FFF, 1892px 1688px #FFF, 449px 1846px #FFF, 1286px 526px #FFF, 1317px 1584px #FFF, 402px 59px #FFF, 1405px 129px #FFF, 1328px 1312px #FFF, 41px 1493px #FFF, 1032px 534px #FFF;
                                                                                                                }

                                                                                                                @keyframes animStar {
                                                                                                                    from {
                                                                                                                        transform: translateY(0px);
                                                                                                                    }

                                                                                                                    to {
                                                                                                                        transform: translateY(-2000px);
                                                                                                                    }
                                                                                                                }

                                                                                                                * {
                                                                                                                    user-select: none;
                                                                                                                }
                                                                                                            </style>
                                                                                                            <style>
                                                                                                                :root {
                                                                                                                    --color-primary: #E0E1DD;
                                                                                                                    --color-secondary: #778DA9;
                                                                                                                    --color-accent: #415A77;
                                                                                                                    --color-ternary: #1B263B;
                                                                                                                    --color-background: #080808;
                                                                                                                    --color-background-2: #101010;
                                                                                                                    --color-info: green;
                                                                                                                    --color-warning: orange;
                                                                                                                    --color-danger: red;

                                                                                                                    --links: bold 18px/18px var(--ff);

                                                                                                                    --padding-xxs: .25rem;
                                                                                                                    --padding-xs: .50rem;
                                                                                                                    --padding-s: .75rem;
                                                                                                                    --padding-m: 1rem;
                                                                                                                    --padding-l: 1.25rem;
                                                                                                                    --padding-xl: 1.75rem;
                                                                                                                    --padding-xxl: 2.5rem;

                                                                                                                    --margin-xxs: .25rem;
                                                                                                                    --margin-xs: .50rem;
                                                                                                                    --margin-s: .75rem;
                                                                                                                    --margin-m: 1rem;
                                                                                                                    --margin-l: 1.25rem;
                                                                                                                    --margin-xl: 1.75rem;
                                                                                                                    --margin-xxl: 2.5rem;

                                                                                                                    --gray-5: hsl(0, 0%, 5%);
                                                                                                                    --gray-10: hsl(0, 0%, 10%);
                                                                                                                    --gray-30: hsl(0, 0%, 30%);
                                                                                                                    --gray-50: hsl(0, 0%, 50%);
                                                                                                                    --gray-70: hsl(0, 0%, 70%);
                                                                                                                    --gray-80: hsl(0, 0%, 80%);

                                                                                                                    /* --ff: "Inter", sans-serif; */
                                                                                                                    --ff: "Montserrat", sans-serif;

                                                                                                                    --h1: bold 4rem/1em var(--ff);
                                                                                                                    --h2: bold 3rem/1.2em var(--ff);
                                                                                                                    --h3: bold 2.25rem/1.2em var(--ff);
                                                                                                                    --h4: bold 1.5rem/1.6em var(--ff);

                                                                                                                    --big: 1.25rem/1.6em var(--ff);
                                                                                                                    --p: 1rem/1.6em var(--ff);
                                                                                                                    --small: .75rem/2em var(--ff);

                                                                                                                    --h1-ui: bold 3rem/1.2em var(--ff);
                                                                                                                    --h2-ui: bold 2.25rem/1.2em var(--ff);
                                                                                                                    --h3-ui: bold 1.5rem/1.2em var(--ff);
                                                                                                                    --h4-ui: bold 1.12rem/1.6em var(--ff);

                                                                                                                    --big-ui: 1rem/1.6em var(--ff);
                                                                                                                    --p-ui: .8rem/1.6em var(--ff);
                                                                                                                    --small-ui: .75rem/1.8em var(--ff);
                                                                                                                }


                                                                                                                :root {
                                                                                                                    --sidebar-size: max(10vw, 280px);
                                                                                                                    --header-size: max(4vh, 60px);
                                                                                                                }

                                                                                                                html {
                                                                                                                    scroll-behavior: smooth;
                                                                                                                }

                                                                                                                body {
                                                                                                                    margin: 0;
                                                                                                                }

                                                                                                                .sidebar {
                                                                                                                    background: var(--color-background-2);
                                                                                                                    width: var(--sidebar-size);
                                                                                                                    height: 100vh;
                                                                                                                    top: 0;
                                                                                                                    left: 0;
                                                                                                                    position: fixed;
                                                                                                                }

                                                                                                                .sidebar .title {
                                                                                                                    color: var(--color-primary);
                                                                                                                    font: var(--h2);
                                                                                                                    width: 100%;
                                                                                                                    text-align: center;
                                                                                                                    cursor: default;
                                                                                                                    user-select: none;
                                                                                                                }

                                                                                                                .sidebar .icons-container {
                                                                                                                    margin: auto;
                                                                                                                    display: flex;
                                                                                                                    flex-direction: row;
                                                                                                                    flex-wrap: wrap;
                                                                                                                    width: max-content;
                                                                                                                    justify-content: space-between;
                                                                                                                    gap: var(--padding-xs);
                                                                                                                }

                                                                                                                .sidebar .icons-container span {
                                                                                                                    color: var(--color-accent);
                                                                                                                    opacity: 75%;
                                                                                                                    transition: all .3s;
                                                                                                                    cursor: pointer;

                                                                                                                    border: none;
                                                                                                                }

                                                                                                                .sidebar .icons-container span:hover {
                                                                                                                    color: var(--color-secondary);
                                                                                                                    opacity: 85%;
                                                                                                                    scale: 1.2;
                                                                                                                    text-shadow: 0 0 24px var(--color-secondary);
                                                                                                                }

                                                                                                                .main-content-container {
                                                                                                                    top: 0;
                                                                                                                    left: var(--sidebar-size);
                                                                                                                    position: fixed;
                                                                                                                    width: calc(100% - var(--sidebar-size));
                                                                                                                    height: 100vh;
                                                                                                                    overflow-y: scroll;
                                                                                                                }

                                                                                                                .header {
                                                                                                                    background: var(--color-background-2);
                                                                                                                    height: var(--header-size);
                                                                                                                    top: 0;
                                                                                                                    left: var(--sidebar-size);
                                                                                                                    position: fixed;
                                                                                                                    width: calc(100% - var(--sidebar-size));
                                                                                                                    align-items: center;
                                                                                                                    text-align: center;
                                                                                                                    cursor: default;
                                                                                                                    z-index: 1000;
                                                                                                                }

                                                                                                                .content {
                                                                                                                    position: relative;
                                                                                                                    top: var(--header-size);
                                                                                                                    background: var(--color-background);
                                                                                                                    color: var(--color-primary);
                                                                                                                    font: var(--p);
                                                                                                                    bottom: 0;
                                                                                                                    overflow: auto;
                                                                                                                    height: 90%;
                                                                                                                    display: flex;
                                                                                                                    flex-direction: column;
                                                                                                                }

                                                                                                                .sidebar .dbs-container {
                                                                                                                    margin-top: var(--margin-l);
                                                                                                                    margin-right: var(--margin-m);
                                                                                                                    height: 100%;
                                                                                                                    overflow: scroll;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul {
                                                                                                                    margin-top: 0;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li {
                                                                                                                    display: flex;
                                                                                                                    flex-direction: column;
                                                                                                                    flex-wrap: wrap;
                                                                                                                    justify-content: space-between;
                                                                                                                    align-items: center;
                                                                                                                    cursor: pointer;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li .db-text {
                                                                                                                    display: flex;
                                                                                                                    flex-direction: row;
                                                                                                                    flex-wrap: wrap;
                                                                                                                    justify-content: space-between;
                                                                                                                    align-items: center;
                                                                                                                    width: 100%;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li .db-text span {
                                                                                                                    scale: .75;
                                                                                                                    flex: .3;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li .db-text .add {
                                                                                                                    color: var(--gray-30);
                                                                                                                    transition: all .3s;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li:hover .db-text .add {
                                                                                                                    color: var(--gray-70);
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li::after {
                                                                                                                    content: "";
                                                                                                                    display: block;
                                                                                                                    width: 90%;
                                                                                                                    margin: auto;
                                                                                                                    margin-top: 0;
                                                                                                                    margin-bottom: var(--margin-xxs);
                                                                                                                    border-radius: 24px;
                                                                                                                    height: 1px;
                                                                                                                    background: var(--color-secondary);
                                                                                                                    opacity: 50%;
                                                                                                                    transition: all .3s ease-in;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li:hover:after {
                                                                                                                    opacity: 100%;
                                                                                                                    transition: all .1s ease-out;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li .db-text .name {
                                                                                                                    flex: 2;
                                                                                                                    color: var(--gray-50);
                                                                                                                    transition: all .3s ease-in;
                                                                                                                    margin: var(--margin-xs);
                                                                                                                    overflow: hidden;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li .db-text .db-icon {
                                                                                                                    color: var(--gray-50);
                                                                                                                    transition: all .3s ease-in;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li:hover .db-text .name,
                                                                                                                .sidebar .dbs-container ul .db-li:hover .db-text .db-icon {
                                                                                                                    color: var(--color-primary);
                                                                                                                    transition: all .1s ease-out;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li.active::after {
                                                                                                                    background: var(--color-primary);
                                                                                                                    opacity: 100%;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li.active .db-text .db-icon {
                                                                                                                    color: var(--color-primary);
                                                                                                                    opacity: 100%;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li.active .db-text .name {
                                                                                                                    color: var(--color-primary);
                                                                                                                    opacity: 100%;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li.active .db-text .add {
                                                                                                                    color: var(--color-primary);
                                                                                                                    opacity: 100%;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li .tables-list {
                                                                                                                    display: none;
                                                                                                                    overflow: hidden;
                                                                                                                    height: auto;
                                                                                                                    width: 70%;
                                                                                                                    transition: all .5s ease;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li.active .tables-list {
                                                                                                                    display: flex;
                                                                                                                    flex-direction: column;
                                                                                                                    text-align: left;
                                                                                                                    align-items: self-start;
                                                                                                                    align-content: flex-start;
                                                                                                                    animation: expandHeight .5s ease forwards;
                                                                                                                    height: auto;
                                                                                                                    transition: all .5s ease;
                                                                                                                }

                                                                                                                @keyframes expandHeight {
                                                                                                                    from {
                                                                                                                        max-height: 00;
                                                                                                                        opacity: 0;
                                                                                                                    }

                                                                                                                    to {
                                                                                                                        max-height: 10;
                                                                                                                        opacity: 1;
                                                                                                                    }
                                                                                                                }

                                                                                                                @keyframes shrinkHeight {
                                                                                                                    from {
                                                                                                                        max-height: 10px;
                                                                                                                        opacity: 1;
                                                                                                                    }

                                                                                                                    to {
                                                                                                                        max-height: 0;
                                                                                                                        opacity: 0;
                                                                                                                    }
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li.active .tables-list li {
                                                                                                                    font: var(--p-ui);
                                                                                                                    color: var(--gray-70);
                                                                                                                    margin-left: calc(var(--margin-xxl) * -1);
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li.active .tables-list li:hover {
                                                                                                                    font: var(--p-ui);
                                                                                                                    color: var(--gray-70);
                                                                                                                    text-decoration: underline;
                                                                                                                }

                                                                                                                .sidebar .dbs-container ul .db-li.active .tables-list li::after {
                                                                                                                    background: none;
                                                                                                                }

                                                                                                                .tables-list li {
                                                                                                                    display: flex;
                                                                                                                    flex-direction: row;
                                                                                                                    align-items: center;
                                                                                                                    width: 100%;
                                                                                                                    margin: 0;
                                                                                                                    padding: 0;
                                                                                                                }

                                                                                                                .tables-list li .table-icon {
                                                                                                                    scale: .6;
                                                                                                                    flex: .3;
                                                                                                                }

                                                                                                                .tables-list li .table-name {
                                                                                                                    flex: 2;
                                                                                                                    margin-top: 0;
                                                                                                                    margin: var(--margin-xxs);
                                                                                                                    padding: 0;
                                                                                                                }

                                                                                                                .table-wrapper {
                                                                                                                    overflow-x: auto;
                                                                                                                    width: 100%;
                                                                                                                }

                                                                                                                .table {
                                                                                                                    width: calc(100% - var(--margin-m) * 2);
                                                                                                                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
                                                                                                                    display: table;
                                                                                                                }

                                                                                                                @media screen and (max-width: 580px) {
                                                                                                                    .table {
                                                                                                                        display: block;
                                                                                                                    }
                                                                                                                }

                                                                                                                .row {
                                                                                                                    display: table-row;
                                                                                                                    background: var(--color-background-2);
                                                                                                                    height: 5vh;
                                                                                                                    overflow: hidden;
                                                                                                                    width: 100%;
                                                                                                                }

                                                                                                                .row:nth-of-type(odd) {
                                                                                                                    background: var(--color-background);
                                                                                                                }

                                                                                                                .row.t-header {
                                                                                                                    font-weight: 900;
                                                                                                                    color: var(--color-primary);
                                                                                                                    background: var(--color-ternary);
                                                                                                                    height: 5vh;
                                                                                                                    overflow: hidden;
                                                                                                                }

                                                                                                                @media screen and (max-width: 580px) {
                                                                                                                    .row {
                                                                                                                        padding: 0;
                                                                                                                        display: block;
                                                                                                                        height: 5vh;
                                                                                                                        overflow: hidden;
                                                                                                                    }

                                                                                                                    .row.t-header {
                                                                                                                        padding: 0;
                                                                                                                        height: 5vh;
                                                                                                                    }

                                                                                                                    .row.t-header .cell {
                                                                                                                        display: none;
                                                                                                                    }

                                                                                                                    .row .cell {
                                                                                                                        margin-bottom: 10px;
                                                                                                                        overflow: hidden;
                                                                                                                    }

                                                                                                                    .row .cell:before {
                                                                                                                        margin-bottom: 3px;
                                                                                                                        content: attr(data-title);
                                                                                                                        min-width: 98px;
                                                                                                                        font-size: 10px;
                                                                                                                        line-height: 10px;
                                                                                                                        font-weight: bold;
                                                                                                                        text-transform: uppercase;
                                                                                                                        color: var(--color-accent);
                                                                                                                        display: block;
                                                                                                                        overflow: hidden;
                                                                                                                    }
                                                                                                                }

                                                                                                                .cell {
                                                                                                                    padding: 6px 12px;
                                                                                                                    display: table-cell;
                                                                                                                    overflow: hidden;
                                                                                                                    max-width: min(20vw, 200px);
                                                                                                                }

                                                                                                                @media screen and (max-width: 580px) {
                                                                                                                    .cell {
                                                                                                                        padding: 2px 16px;
                                                                                                                        display: block;
                                                                                                                        overflow: hidden;
                                                                                                                    }
                                                                                                                }

                                                                                                                .cell .cell-content {
                                                                                                                    /* background: red; */
                                                                                                                    /* height: min(4vh, 40px); */
                                                                                                                    overflow: hidden;
                                                                                                                    text-overflow: ellipsis;
                                                                                                                    white-space: nowrap;
                                                                                                                }

                                                                                                                .table-commands-container {
                                                                                                                    width: 95%;
                                                                                                                    margin: auto;
                                                                                                                    margin-top: calc(var(--margin-s) * -1);
                                                                                                                    margin-bottom: 0;
                                                                                                                    display: flex;
                                                                                                                    flex-direction: row;
                                                                                                                    justify-content: space-between;
                                                                                                                    gap: var(--padding-m);
                                                                                                                }

                                                                                                                .table-commands-container .table-commands {
                                                                                                                    display: flex;
                                                                                                                    flex-direction: row;
                                                                                                                    justify-content: flex-end;
                                                                                                                    align-items: end;
                                                                                                                    text-align: center;
                                                                                                                }

                                                                                                                .table-commands-container .table-commands p {
                                                                                                                    width: min(12%, 80px);
                                                                                                                }

                                                                                                                .table-commands-container .icon {
                                                                                                                    color: var(--color-accent);
                                                                                                                    opacity: 75%;
                                                                                                                    transition: all .3s;
                                                                                                                    cursor: pointer;
                                                                                                                    transform: translateY(calc(var(--padding-xxs) * -1));
                                                                                                                }

                                                                                                                .table-commands-container .icon:hover {
                                                                                                                    color: var(--color-secondary);
                                                                                                                    opacity: 85%;
                                                                                                                    scale: 1.2;
                                                                                                                    text-shadow: 0 0 24px var(--color-secondary);
                                                                                                                }

                                                                                                                .table-commands-container .icon.blocked {
                                                                                                                    color: var(--gray-30);
                                                                                                                    opacity: 50%;
                                                                                                                    cursor: default;
                                                                                                                }

                                                                                                                .page-input {
                                                                                                                    padding-top: var(--padding-s);
                                                                                                                    padding-bottom: var(--padding-s);
                                                                                                                    padding-left: var(--padding-xxs);
                                                                                                                    padding-right: var(--padding-xxs);

                                                                                                                    background-color: transparent;
                                                                                                                    border: 1px solid transparent;
                                                                                                                    border-radius: 0.5rem;
                                                                                                                    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);

                                                                                                                    font: var(--ff);
                                                                                                                    color: var(--color-primary);

                                                                                                                    width: min(32%, 80px);
                                                                                                                    height: min(1vh, 8px);

                                                                                                                    text-align: center;
                                                                                                                    transform: translateY(25%);
                                                                                                                }

                                                                                                                .table-commands-container .page-input:focus {
                                                                                                                    outline: 1px solid transparent;
                                                                                                                }

                                                                                                                .table-commands-container .page-input::placeholder {
                                                                                                                    color: var(--gray-70);
                                                                                                                }

                                                                                                                .table-commands-container .page-input:invalid,
                                                                                                                .table-commands-container .page-input:out-of-range {
                                                                                                                    color: var(--color-danger);
                                                                                                                }

                                                                                                                input[type="number"]::-webkit-outer-spin-button,
                                                                                                                input[type="number"]::-webkit-inner-spin-button {
                                                                                                                    -webkit-appearance: none;
                                                                                                                    margin: 0;
                                                                                                                    appearance: textfield;
                                                                                                                }

                                                                                                                input[type="number"] {
                                                                                                                    -moz-appearance: textfield;
                                                                                                                    appearance: textfield;
                                                                                                                }

                                                                                                                .page-input-container {
                                                                                                                    position: relative;
                                                                                                                    display: inline-flex;
                                                                                                                    flex-direction: row;
                                                                                                                    justify-content: baseline;
                                                                                                                    align-items: center;
                                                                                                                    width: fit-content;
                                                                                                                    transform: translateY(calc(var(--padding-xxs) * -1));
                                                                                                                }

                                                                                                                .page-input-container::after {
                                                                                                                    padding-top: var(--padding-l);
                                                                                                                    position: relative;
                                                                                                                    color: var(--gray-70);
                                                                                                                }

                                                                                                                .button-container {
                                                                                                                    display: flex;
                                                                                                                    flex-direction: row;
                                                                                                                    justify-content: flex-start;
                                                                                                                    align-items: center;
                                                                                                                    gap: var(--padding-xs);
                                                                                                                    margin-top: var(--margin-l);
                                                                                                                }

                                                                                                                .table-commands-container .line-separator {
                                                                                                                    width: 90%;
                                                                                                                    margin: auto;
                                                                                                                    margin-top: 2.5rem;
                                                                                                                    height: 2px;
                                                                                                                    background: var(--color-accent);
                                                                                                                    border-radius: 24px;
                                                                                                                    background-size: 300% 100%;
                                                                                                                }

                                                                                                                .golden {
                                                                                                                    color: goldenrod;
                                                                                                                }

                                                                                                                .silver {
                                                                                                                    color: silver;
                                                                                                                }

                                                                                                                .cell-content-container {
                                                                                                                    display: flex;
                                                                                                                    flex-direction: row;
                                                                                                                    justify-content: center;
                                                                                                                    align-items: center;
                                                                                                                }

                                                                                                                .text-container {
                                                                                                                    display: flex;
                                                                                                                    flex-direction: column;
                                                                                                                    align-items: flex-start;
                                                                                                                    justify-content: center;
                                                                                                                }

                                                                                                                .text-container small {
                                                                                                                    color: var(--gray-80);
                                                                                                                    display: flex;
                                                                                                                    flex-direction: row;
                                                                                                                    align-items: center;
                                                                                                                    gap: var(--padding-xs);
                                                                                                                }

                                                                                                                .exec-query-btn {
                                                                                                                    width: max-content;
                                                                                                                }

                                                                                                                .query-container {
                                                                                                                    margin: var(--margin-m);
                                                                                                                    display: flex;
                                                                                                                    flex-wrap: nowrap;
                                                                                                                    flex-direction: row;
                                                                                                                    gap: var(--padding-s);
                                                                                                                    height: 100%;
                                                                                                                    width: 98%;
                                                                                                                    overflow: hidden;
                                                                                                                }

                                                                                                                #query {
                                                                                                                    width: 100%;
                                                                                                                    height: 25%;
                                                                                                                    border: 1px solid var(--color-ternary);
                                                                                                                    border-radius: 8px;
                                                                                                                    overflow: hidden;
                                                                                                                }

                                                                                                                .structure-container {
                                                                                                                    width: 12%;
                                                                                                                    border: 1px solid var(--color-ternary);
                                                                                                                    border-radius: 8px;
                                                                                                                    background: #1e1e1e;
                                                                                                                }

                                                                                                                .structure-container textarea {
                                                                                                                    width: 90%;
                                                                                                                    height: 90%;
                                                                                                                    resize: none;
                                                                                                                    color: var(--color-primary);
                                                                                                                    font: var(--p);
                                                                                                                    background: transparent;
                                                                                                                    border: transparent;
                                                                                                                    outline: none;
                                                                                                                    padding: var(--padding-m);
                                                                                                                    margin: auto;
                                                                                                                }

                                                                                                                .query-inner-container {
                                                                                                                    display: flex;
                                                                                                                    flex-grow: 1;
                                                                                                                    flex-direction: column;
                                                                                                                    gap: var(--padding-s);
                                                                                                                    overflow: hidden;
                                                                                                                    width: 76%;
                                                                                                                    height: 100%;
                                                                                                                }

                                                                                                                .output-container {
                                                                                                                    flex-grow: 1;
                                                                                                                    background: #1e1e1e;
                                                                                                                    border: 1px solid var(--color-ternary);
                                                                                                                    border-radius: 8px;
                                                                                                                    width: 100%;
                                                                                                                    height: 100%;
                                                                                                                    display: block;
                                                                                                                    overflow: scroll;
                                                                                                                    overflow-x: hidden;
                                                                                                                }

                                                                                                                pre .string {
                                                                                                                    color: greenyellow;
                                                                                                                }

                                                                                                                pre .number {
                                                                                                                    color: orange;
                                                                                                                }

                                                                                                                pre .boolean {
                                                                                                                    color: lightskyblue;
                                                                                                                }

                                                                                                                pre .null {
                                                                                                                    color: magenta;
                                                                                                                }

                                                                                                                pre .key {
                                                                                                                    color: crimson;
                                                                                                                }

                                                                                                                .query-output-container {
                                                                                                                    height: 400px;
                                                                                                                    display: block;
                                                                                                                    width: 100%;
                                                                                                                    /* overflow: hidden; */
                                                                                                                    overflow: visible;
                                                                                                                }

                                                                                                                .query-output-container .table {
                                                                                                                    /* max-height: 100%;
                                                                                                                                                                                                                    display: block;
                                                                                                                                                                                                                    white-space: nowrap;
                                                                                                                                                                                                                    box-sizing: border-box;
                                                                                                                                                                                                                    overflow: scroll;
                                                                                                                                                                                                                    table-layout: fixed; */
                                                                                                                    overflow: scroll;
                                                                                                                }

                                                                                                                #query-output {
                                                                                                                    height: 100%;
                                                                                                                    width: 96%;
                                                                                                                    margin: auto;
                                                                                                                    margin-top: var(--margin-m);
                                                                                                                    display: flex;
                                                                                                                    flex-direction: column;
                                                                                                                    gap: var(--padding-m);
                                                                                                                    overflow: visible;
                                                                                                                }

                                                                                                                .json-output {
                                                                                                                    display: block;
                                                                                                                    overflow: scroll;
                                                                                                                }

                                                                                                                .scroller {
                                                                                                                    width: 100%;
                                                                                                                    height: 100%;
                                                                                                                    overflow: scroll;
                                                                                                                }

                                                                                                                .sql-keyword {
                                                                                                                    color: orchid;
                                                                                                                }

                                                                                                                .sql-string {
                                                                                                                    color: yellowgreen;
                                                                                                                }

                                                                                                                .sql-identifier {
                                                                                                                    color: white;
                                                                                                                }

                                                                                                                .sql-number {
                                                                                                                    color: yellowgreen;
                                                                                                                }

                                                                                                                .monaco-editor {
                                                                                                                    width: 100% !important;
                                                                                                                    height: 100% !important;
                                                                                                                }

                                                                                                                .popup-container {
                                                                                                                    position: absolute;
                                                                                                                    top: 0;
                                                                                                                    right: 0;
                                                                                                                    display: flex;
                                                                                                                    flex-direction: column;
                                                                                                                }

                                                                                                                .popup {
                                                                                                                    margin: var(--margin-m);
                                                                                                                    display: block;
                                                                                                                    justify-content: center;
                                                                                                                    align-items: center;
                                                                                                                    background: var(--color-background-2);
                                                                                                                    box-shadow: 0 0 12px var(--color-accent);
                                                                                                                    border-radius: 8px;
                                                                                                                    transform: translateX(200%);
                                                                                                                    transition: all .2s ease-in-out;
                                                                                                                }

                                                                                                                .popup-open {
                                                                                                                    transform: translateX(0);
                                                                                                                }

                                                                                                                .popup-content {
                                                                                                                    padding: var(--padding-m);
                                                                                                                }

                                                                                                                .popup-text {
                                                                                                                    font: var(--p-ui);
                                                                                                                }

                                                                                                                .home-title-container {
                                                                                                                    text-align: center;
                                                                                                                    align-content: center;
                                                                                                                    margin-top: var(--margin-xxl);
                                                                                                                }

                                                                                                                .home-title {
                                                                                                                    font-style: italic;
                                                                                                                    font: var(--h4);
                                                                                                                    font-size: 8rem;
                                                                                                                    margin-top: 15%;
                                                                                                                }
                                                                                                            </style>

                                                                                                            <link rel="stylesheet"
                                                                                                                href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
                                                                                                            <script src="https://unpkg.com/htmx.org@2.0.2"
                                                                                                                integrity="sha384-Y7hw+L/jvKeWIRRkqWYfPcvVxHzVzn5REgzbawhxAuQGwX1XWe70vji+VSeHOThJ"
                                                                                                                crossorigin="anonymous"></script>

                                                                                                            <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.31.1/min/vs/loader.min.js"></script>
                                                                                                            <link rel="stylesheet" data-name="vs/editor/editor.main"
                                                                                                                href="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs/editor/editor.main.min.css">

                                                                                                            <script>
                                                                                                                window.mysqlKeywords = [
                                                                                                                    'SELECT', 'FROM', 'WHERE', 'JOIN', 'ORDER', 'GROUP', 'INSERT', 'UPDATE', 'DELETE', 'ON',
                                                                                                                    'AND', 'OR', 'NOT', 'BETWEEN', 'IN', 'LIKE', 'IS', 'NULL', 'AS', 'DISTINCT', 'ALL', 'ANY',
                                                                                                                    'EXISTS', 'HAVING', 'UNION', 'INTERSECT', 'EXCEPT', 'CASE', 'WHEN', 'THEN', 'ELSE', 'END',
                                                                                                                    'LIMIT', 'OFFSET', 'FETCH', 'ROWNUM', 'SET', 'VALUES', 'SHOW', 'ALTER', 'DROP', 'CREATE',
                                                                                                                    'RENAME', 'TRUNCATE', 'ALTER', 'TABLE', 'VIEW', 'INDEX', 'DATABASE', 'SCHEMA', 'USE',
                                                                                                                    'GRANT', 'REVOKE', 'TRIGGER', 'PROCEDURE', 'FUNCTION', 'EVENT', 'LOCK', 'UNLOCK', 'PROCEDURE',
                                                                                                                    'CALL', 'BEGIN', 'COMMIT', 'ROLLBACK', 'TRANSACTION', 'AUTO_INCREMENT', 'CHAR', 'VARCHAR',
                                                                                                                    'TEXT', 'BLOB', 'DATE', 'DATETIME', 'TIME', 'TIMESTAMP', 'YEAR', 'INT', 'BIGINT', 'FLOAT',
                                                                                                                    'DOUBLE', 'DECIMAL', 'BOOLEAN', 'ENUM', 'SET', 'PRIMARY', 'KEY', 'UNIQUE', 'FOREIGN', 'REFERENCES',
                                                                                                                    'CHECK', 'DEFAULT', 'NOT NULL', 'NULL', 'AFTER', 'BEFORE', 'CREATE', 'ENGINE', 'ALTER', 'DATABASE'
                                                                                                                ];

                                                                                                                window.jsonHighlight = (json) => {
                                                                                                                    return json.replace(/"(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(:\s*)?|true|false|null|\b-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?\b/g, function (match) {
                                                                                                                        match = match.trim()
                                                                                                                        let cls = 'value'
                                                                                                                        if (/^"/.test(match)) {
                                                                                                                            if (/.*:$/.test(match)) {
                                                                                                                                cls = 'key'
                                                                                                                            } else {
                                                                                                                                cls = 'string'
                                                                                                                            }
                                                                                                                        } else if (/true|false/.test(match)) {
                                                                                                                            cls = 'boolean'
                                                                                                                        } else if (/null/.test(match)) {
                                                                                                                            cls = 'null'
                                                                                                                        } else if (/[0-9]/.test(match)) {
                                                                                                                            cls = 'number'
                                                                                                                        }
                                                                                                                        return '<span class="' + cls + '">' + match + ' </span>'
                                                                                                                    })
                                                                                                                }

                                                                                                                window.sqlHighlight = (/** @type {string} */ sql) => {
                                                                                                                    const keywords = window.mysqlKeywords || [];
                                                                                                                    const keywordPattern = `\\b(${keywords.join('|')})\\b`;
                                                                                                                    const keywordRegex = new RegExp(keywordPattern, 'gi');
                                                                                                                    const stringRegex = /'(?:[^'\\]|\\.)*'|"[^"\\]*(?:\\.[^"\\]*)*"/g;
                                                                                                                    const identifierRegex = /`[^`]*`/g;
                                                                                                                    const numberRegex = /\b-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?\b/g;

                                                                                                                    const escapeHtml = (string) =>
                                                                                                                        string.replace(/&/g, '&amp;')
                                                                                                                            .replace(/</g, '&lt;')
                                                                                                                            .replace(/>/g, '&gt;')
                                                                                                                            .replace(/"/g, '&quot;')
                                                                                                                            .replace(/'/g, '&#039;');

                                                                                                                    const wrap = (text, className) => `<span class="${className}">${escapeHtml(text)}</span>`;

                                                                                                                    console.log(sql);
                                                                                                                    console.log(sql.split(' '));
                                                                                                                    return sql.split(' ').map((word) => {
                                                                                                                        switch (true) {
                                                                                                                            case keywords.includes(word):
                                                                                                                                return word = wrap(word, 'sql-keyword');
                                                                                                                            case stringRegex.test(word):
                                                                                                                                return word = wrap(word, 'sql-string');
                                                                                                                            case identifierRegex.test(word):
                                                                                                                                return word = wrap(word, 'sql-identifier');
                                                                                                                            case numberRegex.test(word):
                                                                                                                                return word = wrap(word, 'sql-number');
                                                                                                                            default:
                                                                                                                                return word;
                                                                                                                        }
                                                                                                                    }).join(' ');

                                                                                                                }
                                                                                                            </script>
                                                                                                        </head>

                                                                                                        <body>
                                                                                                            <aside class="sidebar">
                                                                                                                <div class="title">
                                                                                                                    <h3 class="ui"><?= app_name ?></h3>
                                                                                                                </div>

                                                                                                                <div class="icons-container">
                                                                                                                    <span id="home-icon" class="material-symbols-outlined">
                                                                                                                        home
                                                                                                                    </span>
                                                                                                                    <span id="database-icon" class="material-symbols-outlined">
                                                                                                                        database
                                                                                                                    </span>
                                                                                                                    <span id="settings-icon" class="material-symbols-outlined">
                                                                                                                        settings
                                                                                                                    </span>
                                                                                                                    <span id="reload-icon" class="material-symbols-outlined">
                                                                                                                        sync
                                                                                                                    </span>
                                                                                                                </div>

                                                                                                                <div id="sidebar-databases" class="dbs-container">
                                                                                                                    <p class="margin-top-xxl margin-left-m margin-right-m"><big>Databases</big></p>
                                                                                                                    <ul>
                                                                                                                        <?php foreach ($databases as $database): ?>
                                                                                                                                                                            <li class="db-li">
                                                                                                                                                                                <div class="db-text" hx-get="?db=<?= $database ?>" hx-target="#content" hx-swap="innerHTML">
                                                                                                                                                                                    <span class="material-symbols-outlined db-icon">database</span>
                                                                                                                                                                                    <p class="name"><?= $database ?></p>
                                                                                                                                                                                    <span class="material-symbols-outlined add">keyboard_arrow_down</span>
                                                                                                                                                                                </div>

                                                                                                                                                                                <ul class="tables-list">
                                                                                                                                                                                    <?php foreach ($db_tables[$database] as $table): ?>
                                                                                                                                                                                                                                        <li hx-get="?db=<?= $database ?>&table=<?= $table ?>&show" hx-target="#content"
                                                                                                                                                                                                                                            hx-swap="innerHTML">
                                                                                                                                                                                                                                            <span class="material-symbols-outlined table-icon">table_view</span>
                                                                                                                                                                                                                                            <p class="ui table-name"><?= $table ?></p>
                                                                                                                                                                                                                                        </li>
                                                                                                                                                                                    <?php endforeach; ?>
                                                                                                                                                                                </ul>
                                                                                                                                                                            </li>
                                                                                                                        <?php endforeach; ?>
                                                                                                                    </ul>
                                                                                                                </div>
                                                                                                            </aside>

                                                                                                            <div class="main-content-container">
                                                                                                                <div class="header">
                                                                                                                    <p class="p-ui">
                                                                                                                        web: <i><?= $_SERVER['SERVER_ADDR'] ?></i> | db: <?= database_host ?>
                                                                                                                    </p>
                                                                                                                    <hr class="hr">
                                                                                                                </div>
                                                                                                                <div id="content" class="content">
                                                                                                                    <?php
                                                                                                                    ?>
                                                                                                                    <div class="home-title-container">
                                                                                                                        <div class="home-title">
                                                                                                                            <?= app_name ?>
                                                                                                                        </div>
                                                                                                                    </div><?php
                                                                                                                    ?>
                                                                                                                </div>
                                                                                                            </div>

                                                                                                            <div class="popup-container">
                                                                                                                <!-- <div class="popup"> -->
                                                                                                                <!-- <div class="popup-content"> -->
                                                                                                                <!-- <div class="popup-text"> -->
                                                                                                                <!-- Copied to clipboard! -->
                                                                                                                <!-- </div> -->
                                                                                                                <!-- </div> -->
                                                                                                                <!-- </div> -->
                                                                                                            </div>

                                                                                                            <script>
                                                                                                                // Generated by Haxe 4.3.6
                                                                                                                (function ($global) {
                                                                                                                    "use strict";
                                                                                                                    function $extend(from, fields) {
                                                                                                                        var proto = Object.create(from);
                                                                                                                        for (var name in fields) proto[name] = fields[name];
                                                                                                                        if (fields.toString !== Object.prototype.toString) proto.toString = fields.toString;
                                                                                                                        return proto;
                                                                                                                    }
                                                                                                                    var HxOverrides = function () { };
                                                                                                                    HxOverrides.__name__ = true;
                                                                                                                    HxOverrides.cca = function (s, index) {
                                                                                                                        var x = s.charCodeAt(index);
                                                                                                                        if (x != x) {
                                                                                                                            return undefined;
                                                                                                                        }
                                                                                                                        return x;
                                                                                                                    };
                                                                                                                    HxOverrides.substr = function (s, pos, len) {
                                                                                                                        if (len == null) {
                                                                                                                            len = s.length;
                                                                                                                        } else if (len < 0) {
                                                                                                                            if (pos == 0) {
                                                                                                                                len = s.length + len;
                                                                                                                            } else {
                                                                                                                                return "";
                                                                                                                            }
                                                                                                                        }
                                                                                                                        return s.substr(pos, len);
                                                                                                                    };
                                                                                                                    HxOverrides.now = function () {
                                                                                                                        return Date.now();
                                                                                                                    };
                                                                                                                    Math.__name__ = true;
                                                                                                                    var Std = function () { };
                                                                                                                    Std.__name__ = true;
                                                                                                                    Std.string = function (s) {
                                                                                                                        return js_Boot.__string_rec(s, "");
                                                                                                                    };
                                                                                                                    var StringTools = function () { };
                                                                                                                    StringTools.__name__ = true;
                                                                                                                    StringTools.isSpace = function (s, pos) {
                                                                                                                        var c = HxOverrides.cca(s, pos);
                                                                                                                        if (!(c > 8 && c < 14)) {
                                                                                                                            return c == 32;
                                                                                                                        } else {
                                                                                                                            return true;
                                                                                                                        }
                                                                                                                    };
                                                                                                                    StringTools.ltrim = function (s) {
                                                                                                                        var l = s.length;
                                                                                                                        var r = 0;
                                                                                                                        while (r < l && StringTools.isSpace(s, r)) ++r;
                                                                                                                        if (r > 0) {
                                                                                                                            return HxOverrides.substr(s, r, l - r);
                                                                                                                        } else {
                                                                                                                            return s;
                                                                                                                        }
                                                                                                                    };
                                                                                                                    StringTools.rtrim = function (s) {
                                                                                                                        var l = s.length;
                                                                                                                        var r = 0;
                                                                                                                        while (r < l && StringTools.isSpace(s, l - r - 1)) ++r;
                                                                                                                        if (r > 0) {
                                                                                                                            return HxOverrides.substr(s, 0, l - r);
                                                                                                                        } else {
                                                                                                                            return s;
                                                                                                                        }
                                                                                                                    };
                                                                                                                    StringTools.trim = function (s) {
                                                                                                                        return StringTools.ltrim(StringTools.rtrim(s));
                                                                                                                    };
                                                                                                                    var haxe_Exception = function (message, previous, native) {
                                                                                                                        Error.call(this, message);
                                                                                                                        this.message = message;
                                                                                                                        this.__previousException = previous;
                                                                                                                        this.__nativeException = native != null ? native : this;
                                                                                                                    };
                                                                                                                    haxe_Exception.__name__ = true;
                                                                                                                    haxe_Exception.thrown = function (value) {
                                                                                                                        if (((value) instanceof haxe_Exception)) {
                                                                                                                            return value.get_native();
                                                                                                                        } else if (((value) instanceof Error)) {
                                                                                                                            return value;
                                                                                                                        } else {
                                                                                                                            var e = new haxe_ValueException(value);
                                                                                                                            return e;
                                                                                                                        }
                                                                                                                    };
                                                                                                                    haxe_Exception.__super__ = Error;
                                                                                                                    haxe_Exception.prototype = $extend(Error.prototype, {
                                                                                                                        toString: function () {
                                                                                                                            return this.get_message();
                                                                                                                        }
                                                                                                                        , get_message: function () {
                                                                                                                            return this.message;
                                                                                                                        }
                                                                                                                        , get_native: function () {
                                                                                                                            return this.__nativeException;
                                                                                                                        }
                                                                                                                        , __class__: haxe_Exception
                                                                                                                    });
                                                                                                                    var haxe_ValueException = function (value, previous, native) {
                                                                                                                        haxe_Exception.call(this, String(value), previous, native);
                                                                                                                        this.value = value;
                                                                                                                    };
                                                                                                                    haxe_ValueException.__name__ = true;
                                                                                                                    haxe_ValueException.__super__ = haxe_Exception;
                                                                                                                    haxe_ValueException.prototype = $extend(haxe_Exception.prototype, {
                                                                                                                        __class__: haxe_ValueException
                                                                                                                    });
                                                                                                                    var haxe_iterators_ArrayIterator = function (array) {
                                                                                                                        this.current = 0;
                                                                                                                        this.array = array;
                                                                                                                    };
                                                                                                                    haxe_iterators_ArrayIterator.__name__ = true;
                                                                                                                    haxe_iterators_ArrayIterator.prototype = {
                                                                                                                        hasNext: function () {
                                                                                                                            return this.current < this.array.length;
                                                                                                                        }
                                                                                                                        , next: function () {
                                                                                                                            return this.array[this.current++];
                                                                                                                        }
                                                                                                                        , __class__: haxe_iterators_ArrayIterator
                                                                                                                    };
                                                                                                                    var js_Boot = function () { };
                                                                                                                    js_Boot.__name__ = true;
                                                                                                                    js_Boot.getClass = function (o) {
                                                                                                                        if (o == null) {
                                                                                                                            return null;
                                                                                                                        } else if (((o) instanceof Array)) {
                                                                                                                            return Array;
                                                                                                                        } else {
                                                                                                                            var cl = o.__class__;
                                                                                                                            if (cl != null) {
                                                                                                                                return cl;
                                                                                                                            }
                                                                                                                            var name = js_Boot.__nativeClassName(o);
                                                                                                                            if (name != null) {
                                                                                                                                return js_Boot.__resolveNativeClass(name);
                                                                                                                            }
                                                                                                                            return null;
                                                                                                                        }
                                                                                                                    };
                                                                                                                    js_Boot.__string_rec = function (o, s) {
                                                                                                                        if (o == null) {
                                                                                                                            return "null";
                                                                                                                        }
                                                                                                                        if (s.length >= 5) {
                                                                                                                            return "<...>";
                                                                                                                        }
                                                                                                                        var t = typeof (o);
                                                                                                                        if (t == "function" && (o.__name__ || o.__ename__)) {
                                                                                                                            t = "object";
                                                                                                                        }
                                                                                                                        switch (t) {
                                                                                                                            case "function":
                                                                                                                                return "<function>";
                                                                                                                            case "object":
                                                                                                                                if (((o) instanceof Array)) {
                                                                                                                                    var str = "[";
                                                                                                                                    s += "\t";
                                                                                                                                    var _g = 0;
                                                                                                                                    var _g1 = o.length;
                                                                                                                                    while (_g < _g1) {
                                                                                                                                        var i = _g++;
                                                                                                                                        str += (i > 0 ? "," : "") + js_Boot.__string_rec(o[i], s);
                                                                                                                                    }
                                                                                                                                    str += "]";
                                                                                                                                    return str;
                                                                                                                                }
                                                                                                                                var tostr;
                                                                                                                                try {
                                                                                                                                    tostr = o.toString;
                                                                                                                                } catch (_g) {
                                                                                                                                    return "???";
                                                                                                                                }
                                                                                                                                if (tostr != null && tostr != Object.toString && typeof (tostr) == "function") {
                                                                                                                                    var s2 = o.toString();
                                                                                                                                    if (s2 != "[object Object]") {
                                                                                                                                        return s2;
                                                                                                                                    }
                                                                                                                                }
                                                                                                                                var str = "{\n";
                                                                                                                                s += "\t";
                                                                                                                                var hasp = o.hasOwnProperty != null;
                                                                                                                                var k = null;
                                                                                                                                for (k in o) {
                                                                                                                                    if (hasp && !o.hasOwnProperty(k)) {
                                                                                                                                        continue;
                                                                                                                                    }
                                                                                                                                    if (k == "prototype" || k == "__class__" || k == "__super__" || k == "__interfaces__" || k == "__properties__") {
                                                                                                                                        continue;
                                                                                                                                    }
                                                                                                                                    if (str.length != 2) {
                                                                                                                                        str += ", \n";
                                                                                                                                    }
                                                                                                                                    str += s + k + " : " + js_Boot.__string_rec(o[k], s);
                                                                                                                                }
                                                                                                                                s = s.substring(1);
                                                                                                                                str += "\n" + s + "}";
                                                                                                                                return str;
                                                                                                                            case "string":
                                                                                                                                return o;
                                                                                                                            default:
                                                                                                                                return String(o);
                                                                                                                        }
                                                                                                                    };
                                                                                                                    js_Boot.__interfLoop = function (cc, cl) {
                                                                                                                        if (cc == null) {
                                                                                                                            return false;
                                                                                                                        }
                                                                                                                        if (cc == cl) {
                                                                                                                            return true;
                                                                                                                        }
                                                                                                                        var intf = cc.__interfaces__;
                                                                                                                        if (intf != null) {
                                                                                                                            var _g = 0;
                                                                                                                            var _g1 = intf.length;
                                                                                                                            while (_g < _g1) {
                                                                                                                                var i = _g++;
                                                                                                                                var i1 = intf[i];
                                                                                                                                if (i1 == cl || js_Boot.__interfLoop(i1, cl)) {
                                                                                                                                    return true;
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                        return js_Boot.__interfLoop(cc.__super__, cl);
                                                                                                                    };
                                                                                                                    js_Boot.__instanceof = function (o, cl) {
                                                                                                                        if (cl == null) {
                                                                                                                            return false;
                                                                                                                        }
                                                                                                                        switch (cl) {
                                                                                                                            case Array:
                                                                                                                                return ((o) instanceof Array);
                                                                                                                            case Bool:
                                                                                                                                return typeof (o) == "boolean";
                                                                                                                            case Dynamic:
                                                                                                                                return o != null;
                                                                                                                            case Float:
                                                                                                                                return typeof (o) == "number";
                                                                                                                            case Int:
                                                                                                                                if (typeof (o) == "number") {
                                                                                                                                    return ((o | 0) === o);
                                                                                                                                } else {
                                                                                                                                    return false;
                                                                                                                                }
                                                                                                                                break;
                                                                                                                            case String:
                                                                                                                                return typeof (o) == "string";
                                                                                                                            default:
                                                                                                                                if (o != null) {
                                                                                                                                    if (typeof (cl) == "function") {
                                                                                                                                        if (js_Boot.__downcastCheck(o, cl)) {
                                                                                                                                            return true;
                                                                                                                                        }
                                                                                                                                    } else if (typeof (cl) == "object" && js_Boot.__isNativeObj(cl)) {
                                                                                                                                        if (((o) instanceof cl)) {
                                                                                                                                            return true;
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                } else {
                                                                                                                                    return false;
                                                                                                                                }
                                                                                                                                if (cl == Class ? o.__name__ != null : false) {
                                                                                                                                    return true;
                                                                                                                                }
                                                                                                                                if (cl == Enum ? o.__ename__ != null : false) {
                                                                                                                                    return true;
                                                                                                                                }
                                                                                                                                return false;
                                                                                                                        }
                                                                                                                    };
                                                                                                                    js_Boot.__downcastCheck = function (o, cl) {
                                                                                                                        if (!((o) instanceof cl)) {
                                                                                                                            if (cl.__isInterface__) {
                                                                                                                                return js_Boot.__interfLoop(js_Boot.getClass(o), cl);
                                                                                                                            } else {
                                                                                                                                return false;
                                                                                                                            }
                                                                                                                        } else {
                                                                                                                            return true;
                                                                                                                        }
                                                                                                                    };
                                                                                                                    js_Boot.__cast = function (o, t) {
                                                                                                                        if (o == null || js_Boot.__instanceof(o, t)) {
                                                                                                                            return o;
                                                                                                                        } else {
                                                                                                                            throw haxe_Exception.thrown("Cannot cast " + Std.string(o) + " to " + Std.string(t));
                                                                                                                        }
                                                                                                                    };
                                                                                                                    js_Boot.__nativeClassName = function (o) {
                                                                                                                        var name = js_Boot.__toStr.call(o).slice(8, -1);
                                                                                                                        if (name == "Object" || name == "Function" || name == "Math" || name == "JSON") {
                                                                                                                            return null;
                                                                                                                        }
                                                                                                                        return name;
                                                                                                                    };
                                                                                                                    js_Boot.__isNativeObj = function (o) {
                                                                                                                        return js_Boot.__nativeClassName(o) != null;
                                                                                                                    };
                                                                                                                    js_Boot.__resolveNativeClass = function (name) {
                                                                                                                        return $global[name];
                                                                                                                    };
                                                                                                                    var scripts_Add = function () {
                                                                                                                        var addButton = window.document.querySelector("#add-button");
                                                                                                                        addButton.onclick = function () {
                                                                                                                            var formData = new FormData(window.document.querySelector("#form"));
                                                                                                                            var nodes = window.document.querySelectorAll("[data-field]");
                                                                                                                            var _g = 0;
                                                                                                                            while (_g < nodes.length) {
                                                                                                                                var node = nodes[_g];
                                                                                                                                ++_g;
                                                                                                                                var field = node;
                                                                                                                                var value = field.value;
                                                                                                                                formData.append(field.dataset.field, value);
                                                                                                                            }
                                                                                                                            var response = window.fetch(window.location.href, { method: "POST", body: formData });
                                                                                                                            response.then(function (response) {
                                                                                                                                return response.json();
                                                                                                                            }).then(function (response) {
                                                                                                                                if (response.status == "success") {
                                                                                                                                    console.log("scripts/Add.hx:37:", response.message);
                                                                                                                                    scripts_Main.openPopup(response.message, 5000);
                                                                                                                                    (js_Boot.__cast(window.document.querySelector("#btn-show"), HTMLButtonElement)).click();
                                                                                                                                } else {
                                                                                                                                    throw new haxe_Exception(response.message);
                                                                                                                                }
                                                                                                                            }).catch(function (error) {
                                                                                                                                console.log("scripts/Add.hx:44:", "Error executing query: " + Std.string(error));
                                                                                                                                scripts_Main.openPopup("Error executing query: " + Std.string(error), 5000);
                                                                                                                            });
                                                                                                                        };
                                                                                                                    };
                                                                                                                    scripts_Add.__name__ = true;
                                                                                                                    scripts_Add.expose = function () {
                                                                                                                        window.AddRow = scripts_Add;
                                                                                                                    };
                                                                                                                    scripts_Add.prototype = {
                                                                                                                        __class__: scripts_Add
                                                                                                                    };
                                                                                                                    var scripts_Main = function () { };
                                                                                                                    scripts_Main.__name__ = true;
                                                                                                                    scripts_Main.main = function () {
                                                                                                                        scripts_Main.devReload();
                                                                                                                        scripts_Main.setupIcons();
                                                                                                                        scripts_Main.setupSidebar();
                                                                                                                        scripts_QueryExecutor.expose();
                                                                                                                        scripts_Add.expose();
                                                                                                                        window.openPopup = scripts_Main.openPopup;
                                                                                                                        window.addEventListener("contextmenu", function (event) {
                                                                                                                            var cell = (js_Boot.__cast(event.target, HTMLDivElement)).closest(".cell");
                                                                                                                            if (cell == null) {
                                                                                                                                return;
                                                                                                                            }
                                                                                                                            var cellContent = cell.querySelector(".cell-content");
                                                                                                                            if (cellContent == null) {
                                                                                                                                return;
                                                                                                                            }
                                                                                                                            event.preventDefault();
                                                                                                                            var text = cellContent.innerHTML;
                                                                                                                            window.navigator.clipboard.writeText(StringTools.trim(text));
                                                                                                                            scripts_Main.openPopup("Copied to clipboard!");
                                                                                                                        });
                                                                                                                    };
                                                                                                                    scripts_Main.devReload = function () {
                                                                                                                        return;
                                                                                                                    };
                                                                                                                    scripts_Main.setupIcons = function () {
                                                                                                                        window.document.querySelector("#reload-icon").onclick = function () {
                                                                                                                            window.location.reload();
                                                                                                                        };
                                                                                                                        window.document.querySelector("#settings-icon").onclick = function () {
                                                                                                                            window.location.reload();
                                                                                                                        };
                                                                                                                        window.document.querySelector("#database-icon").onclick = function () {
                                                                                                                            window.location.reload();
                                                                                                                        };
                                                                                                                        window.document.querySelector("#home-icon").onclick = function () {
                                                                                                                            var content = window.document.querySelector("#content");
                                                                                                                            window.fetch("?home").then(function (response) {
                                                                                                                                return response.text();
                                                                                                                            }).then(function (contentText) {
                                                                                                                                content.innerHTML = contentText;
                                                                                                                                var openSidebarDb = window.document.querySelector(".db-li.active");
                                                                                                                                if (openSidebarDb != null) {
                                                                                                                                    openSidebarDb.classList.remove("active");
                                                                                                                                }
                                                                                                                            }).catch(function (error) {
                                                                                                                                console.log("scripts/Main.hx:91:", "Error fetching home page: " + error);
                                                                                                                            });
                                                                                                                        };
                                                                                                                    };
                                                                                                                    scripts_Main.setupSidebar = function () {
                                                                                                                        var lis = window.document.querySelector("#sidebar-databases").querySelectorAll(".db-li");
                                                                                                                        var _g = 0;
                                                                                                                        while (_g < lis.length) {
                                                                                                                            var li = [lis[_g]];
                                                                                                                            ++_g;
                                                                                                                            li[0].onclick = (function (li) {
                                                                                                                                return function () {
                                                                                                                                    var _g = 0;
                                                                                                                                    while (_g < lis.length) {
                                                                                                                                        var l = lis[_g];
                                                                                                                                        ++_g;
                                                                                                                                        l.className = "db-li";
                                                                                                                                    }
                                                                                                                                    if (li[0].classList.contains("active")) {
                                                                                                                                        li[0].className = "db-li";
                                                                                                                                    } else {
                                                                                                                                        li[0].className = "db-li active";
                                                                                                                                    }
                                                                                                                                };
                                                                                                                            })(li);
                                                                                                                        }
                                                                                                                    };
                                                                                                                    scripts_Main.openPopup = function (text, interval) {
                                                                                                                        if (interval == null) {
                                                                                                                            interval = 500;
                                                                                                                        }
                                                                                                                        var popup = window.document.createElement("div");
                                                                                                                        popup.className = "popup popup-open";
                                                                                                                        var popupContent = window.document.createElement("div");
                                                                                                                        popupContent.className = "popup-content";
                                                                                                                        var popupText = window.document.createElement("div");
                                                                                                                        popupText.className = "popup-text";
                                                                                                                        popupText.innerHTML = text;
                                                                                                                        popupContent.append(popupText);
                                                                                                                        popup.append(popupContent);
                                                                                                                        window.document.querySelector(".popup-container").append(popup);
                                                                                                                        popup.addEventListener("transitionend", function () {
                                                                                                                            popup.remove();
                                                                                                                        });
                                                                                                                        window.setTimeout(function () {
                                                                                                                            popup.classList.remove("popup-open");
                                                                                                                        }, interval);
                                                                                                                    };
                                                                                                                    var scripts_QueryExecutor = function () {
                                                                                                                        var executeQueryButton = window.document.querySelector("#execute-query-btn");
                                                                                                                        if (executeQueryButton == null) {
                                                                                                                            console.log("scripts/QueryExecutor.hx:15:", "Error: executeQueryButton is null");
                                                                                                                            return;
                                                                                                                        }
                                                                                                                        executeQueryButton.onclick = function () {
                                                                                                                            var queryElement = window.editor.getValue();
                                                                                                                            var formBody = new FormData();
                                                                                                                            formBody.append("query", queryElement);
                                                                                                                            formBody.append("db", (js_Boot.__cast(window.document.querySelector("#db"), HTMLInputElement)).value);
                                                                                                                            var response = window.fetch(window.location.href, { method: "POST", body: formBody });
                                                                                                                            response.then(function (response) {
                                                                                                                                return response.text();
                                                                                                                            }).then(function (text) {
                                                                                                                                var outputContainer = window.document.querySelector("#output-container");
                                                                                                                                var element = window.document.createElement("div");
                                                                                                                                element.className = "query-output-container";
                                                                                                                                element.innerHTML = text;
                                                                                                                                outputContainer.append(element);
                                                                                                                                var jsonOutput = element.querySelector("#json-output");
                                                                                                                                jsonOutput.innerHTML = window.jsonHighlight(jsonOutput.innerHTML);
                                                                                                                                var queryText = element.querySelector("#title-container").querySelector("small");
                                                                                                                                queryText.innerHTML = window.sqlHighlight(queryText.innerHTML);
                                                                                                                                var toggleJsonButton = element.querySelector("#toggle-json");
                                                                                                                                toggleJsonButton.onclick = function () {
                                                                                                                                    jsonOutput.style.display = jsonOutput.style.display == "none" ? "block" : "none";
                                                                                                                                    element.querySelector(".table").style.display = jsonOutput.style.display == "block" ? "none" : "table";
                                                                                                                                    toggleJsonButton.innerHTML = toggleJsonButton.innerHTML == "Show JSON" ? "Hide  JSON" : "Show JSON";
                                                                                                                                };
                                                                                                                            }).catch(function (error) {
                                                                                                                                console.log("scripts/QueryExecutor.hx:55:", "Error executing query: " + Std.string(error));
                                                                                                                            });
                                                                                                                        };
                                                                                                                    };
                                                                                                                    scripts_QueryExecutor.__name__ = true;
                                                                                                                    scripts_QueryExecutor.expose = function () {
                                                                                                                        window.QueryExecutor = scripts_QueryExecutor;
                                                                                                                    };
                                                                                                                    scripts_QueryExecutor.prototype = {
                                                                                                                        __class__: scripts_QueryExecutor
                                                                                                                    };
                                                                                                                    if (typeof (performance) != "undefined" ? typeof (performance.now) == "function" : false) {
                                                                                                                        HxOverrides.now = performance.now.bind(performance);
                                                                                                                    }
                                                                                                                    Object.defineProperty(String.prototype, "__class__", { value: String, enumerable: false, writable: true });
                                                                                                                    String.__name__ = true;
                                                                                                                    Array.__name__ = true;
                                                                                                                    var Int = {};
                                                                                                                    var Dynamic = {};
                                                                                                                    var Float = Number;
                                                                                                                    var Bool = Boolean;
                                                                                                                    var Class = {};
                                                                                                                    var Enum = {};
                                                                                                                    js_Boot.__toStr = ({}).toString;
                                                                                                                    scripts_Main.interval = 500;
                                                                                                                    scripts_Main.main();
                                                                                                                })(typeof window != "undefined" ? window : typeof global != "undefined" ? global : typeof self != "undefined" ? self : this);

                                                                                                            </script>
                                                                                                        </body>

                                                                                                        </html>
                                                                                                        <?php
                                                                                                        ?>
                                                                                                    <?php
    } catch (\Throwable $e) {
        ?>
                                                                                                        <?php /** @var \Throwable $e */ ?>

                                                                                                        <div>
                                                                                                            <h2>Error <?= $e->getCode() ?>:</h2>
                                                                                                            <h3><?= $e->getMessage() ?></h3>
                                                                                                            <big><?= $e->getFile() ?>:<?= $e->getLine() ?></big>
                                                                                                        </div>
                                                                                                        <?php
    }
    goto end;
}

if (request_method === 'POST') {
    try {
        ?>
                                                                                                        <?php

                                                                                                        if (isset($_POST['query'])) {
                                                                                                            $query = $_POST['query'];

                                                                                                            $result = $connection->query($query);

                                                                                                            $rows = [];
                                                                                                            if ($result->num_rows > 0) {
                                                                                                                while ($row = $result->fetch_assoc()) {
                                                                                                                    $rows[] = $row;
                                                                                                                }
                                                                                                            }

                                                                                                            ?>
                                                                                                                                                            <?php
                                                                                                                                                            ?>
                                                                                                                                                            <?php $json_string = htmlspecialchars(json_encode($rows, JSON_PRETTY_PRINT)); ?>
                                                                                                                                                            <div id="query-output">
                                                                                                                                                                <div id="title-container" style="display: flex; align-items: center; align-content: center;">
                                                                                                                                                                    <small><?php echo htmlspecialchars($query); ?></small>
                                                                                                                                                                    <div style="flex: 1;"></div>
                                                                                                                                                                    <button class="btn danger margin-xxs padding-xs"
                                                                                                                                                                        onclick="this.parentElement.parentElement.parentElement.remove(this.parentElement.parentElement)">clear</button>
                                                                                                                                                                    <button id="copy-json" class="btn accent margin-xxs padding-xs"
                                                                                                                                                                        onclick="navigator.clipboard.writeText(`<?= $json_string ?>`); openPopup('Copied to clipboard!');">
                                                                                                                                                                        Copy JSON</button>
                                                                                                                                                                    <button id="toggle-json" class="btn accent margin-xxs padding-xs">Show JSON</button>
                                                                                                                                                                </div>
                                                                                                                                                                <?php
                                                                                                                                                                ?>
                                                                                                                                                                <div class="scroller">
                                                                                                                                                                    <div class="table">
                                                                                                                                                                        <div class="row t-header">
                                                                                                                                                                            <?php
                                                                                                                                                                            $headers = array_keys(reset($rows));
                                                                                                                                                                            foreach ($headers as $header) {
                                                                                                                                                                                echo "<div class=\"cell\">" . htmlspecialchars($header) . "</div>";
                                                                                                                                                                            }
                                                                                                                                                                            ?>
                                                                                                                                                                        </div>
                                                                                                                                                                        <?php
                                                                                                                                                                        foreach ($rows as $row): ?>
                                                                                                                                                                                                                            <div class="row"> <?php
                                                                                                                                                                                                                            foreach ($headers as $header) {
                                                                                                                                                                                                                                echo "<div class=\"cell\" data-title=\"$header\"><div class=\"cell-content\">" . htmlspecialchars($row[$header]) . "</div></div>";
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            ?> </div>
                                                                                                                                                                        <?php endforeach; ?>
                                                                                                                                                                    </div>
                                                                                                                                                                    <pre id="json-output" class="json-output" style="display: none;"><?= $json_string; ?></pre>
                                                                                                                                                                </div>
                                                                                                                                                            </div>
                                                                                                                                                            <?php
                                                                                                                                                            ?>
                                                                                                                                                        <?php
                                                                                                        }

                                                                                                        if (isset($_POST['query-util'])) {
                                                                                                            $typeof_query = $_POST['query-util'];

                                                                                                            switch ($typeof_query) {
                                                                                                                case 'query-add':
                                                                                                                    ?>
                                                                                                                                                                                                                                                                    <?php

                                                                                                                                                                                                                                                                    $columns = $_POST;
                                                                                                                                                                                                                                                                    unset($columns['query-util']);

                                                                                                                                                                                                                                                                    $table = $columns['__table__'];
                                                                                                                                                                                                                                                                    unset($columns['__table__']);

                                                                                                                                                                                                                                                                    $db = $columns['db'];
                                                                                                                                                                                                                                                                    unset($columns['db']);

                                                                                                                                                                                                                                                                    $columns = array_filter($columns);

                                                                                                                                                                                                                                                                    $column_names = array_map(function ($col) {
                                                                                                                                                                                                                                                                        return str_replace('field-', '', $col);
                                                                                                                                                                                                                                                                    }, array_keys($columns));

                                                                                                                                                                                                                                                                    $column_placeholders = array_fill(0, count($column_names), '?');
                                                                                                                                                                                                                                                                    $values = array_values($columns);

                                                                                                                                                                                                                                                                    $query = "INSERT INTO {$table} (" . implode(',', $column_names) . ") VALUES (" . implode(',', $column_placeholders) . ")";

                                                                                                                                                                                                                                                                    $stmt = $connection->prepare($query);

                                                                                                                                                                                                                                                                    if (empty($values)) {
                                                                                                                                                                                                                                                                        header('Content-type: application/json');
                                                                                                                                                                                                                                                                        echo json_encode([
                                                                                                                                                                                                                                                                            'status' => 'error',
                                                                                                                                                                                                                                                                            'message' => 'No values provided',
                                                                                                                                                                                                                                                                            'query' => $query,
                                                                                                                                                                                                                                                                        ]);
                                                                                                                                                                                                                                                                        goto end;
                                                                                                                                                                                                                                                                    }

                                                                                                                                                                                                                                                                    if ($stmt === false) {
                                                                                                                                                                                                                                                                        header('Content-type: application/json');
                                                                                                                                                                                                                                                                        echo json_encode([
                                                                                                                                                                                                                                                                            'status' => 'error',
                                                                                                                                                                                                                                                                            'message' => 'Error preparing statement: ' . $connection->error,
                                                                                                                                                                                                                                                                            'query' => $query,
                                                                                                                                                                                                                                                                        ]);
                                                                                                                                                                                                                                                                        goto end;
                                                                                                                                                                                                                                                                    }

                                                                                                                                                                                                                                                                    $types = str_repeat('s', count($values));
                                                                                                                                                                                                                                                                    $stmt->bind_param($types, ...$values);

                                                                                                                                                                                                                                                                    $result = $stmt->execute();

                                                                                                                                                                                                                                                                    header('Content-type: application/json');
                                                                                                                                                                                                                                                                    if ($result === false) {
                                                                                                                                                                                                                                                                        echo json_encode([
                                                                                                                                                                                                                                                                            'status' => 'error',
                                                                                                                                                                                                                                                                            'message' => 'Error executing query: ' . $stmt->error,
                                                                                                                                                                                                                                                                            'query' => $query,
                                                                                                                                                                                                                                                                        ]);
                                                                                                                                                                                                                                                                    } else {
                                                                                                                                                                                                                                                                        echo json_encode([
                                                                                                                                                                                                                                                                            'status' => 'success',
                                                                                                                                                                                                                                                                            'message' => "Query executed successfully: $query",
                                                                                                                                                                                                                                                                            'query' => $query,
                                                                                                                                                                                                                                                                        ]);
                                                                                                                                                                                                                                                                    }

                                                                                                                                                                                                                                                                    $stmt->close();

                                                                                                                                                                                                                                                                    ?>
                                                                                                                                                                                                                                                                    <?php
                                                                                                                                                                                                                                                                    break;
                                                                                                            }
                                                                                                        }

                                                                                                        ?>
                                                                                                    <?php
    } catch (\Throwable $e) {
        ?>
                                                                                                    <?php
    }
    goto end;
}

end:
$connection->close();
