<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>meow meow</title>

    <script src="https://unpkg.com/htmx.org@2.0.1"
        integrity="sha384-QWGpdj554B4ETpJJC9z+ZHJcA/i59TyjxEPXiiUgN2WmTyV5OEZWCD6gQhgkdpB/"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.31.1/min/vs/loader.min.js"></script>
    <link rel="stylesheet" data-name="vs/editor/editor.main"
        href="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs/editor/editor.main.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <style>
        :root {
            --primary-color: #ff69b4;
            --primary-color-dark: #cc2570;
            --background-color-light: #f9f9f9;
            --background-color-dark: #2e2e2e;
            --text-color-light: #000;
            --text-color-dark: #fff;
            --container-background-light: #fff;
            --container-background-dark: #3e3e3e;
            --table-background-light: #f4f4f4;
            --table-background-dark: #2a2a2a;

            --pre-string: green;
            --pre-number: darkorange;
            --pre-boolean: blue;
            --pre-null: magenta;
            --pre-key: crimson;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: var(--background-color-light);
            color: var(--text-color-light);
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
            height: 100vh;
            transition: background-color 0.3s, color 0.3s;
        }

        .center {
            position: fixed;
            padding: 20px;
            left: 25%;
            right: 25%;
            justify-content: center;
            align-items: start;
        }

        .container {
            background-color: var(--container-background-light);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s;
        }

        .flex {
            display: flex;
            gap: 10px;
        }

        button {
            margin: 20px 0px 20px 0px;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: var(--primary-color-dark);
        }

        #result {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding: 10px;
        }

        pre {
            background-color: var(--table-background-light);
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            line-height: 1.5;
            max-height: 400px;
            overflow: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s, color 0.3s;
        }

        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
        }

        #schema {
            background-color: var(--container-background-light);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-height: 90vh;
            max-width: 90vw;
            overflow: auto;
            display: flex;
            flex-direction: column;
            transition: background-color 0.3s, color 0.3s;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
            margin-bottom: 20px;
            font-family: 'Courier New', Courier, monospace;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: var(--table-background-light);
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: background-color 0.3s, color 0.3s;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        h3 {
            margin-top: 0;
        }

        i {
            color: #747474;
        }

        #query {
            width: 100%;
            height: 80px;
            border: 1px solid grey;
        }

        #mode-switch {
            position: fixed;
            top: 10px;
            right: 10px;
            cursor: pointer;
            z-index: 1000;
        }

        pre .string {
            color: var(--pre-string);
        }

        pre .number {
            color: var(--pre-number);
        }

        pre .boolean {
            color: var(--pre-boolean);
        }

        pre .null {
            color: var(--pre-null);
        }

        pre .key {
            color: var(--pre-key);
        }

        .dark-mode {
            --background-color-light: #1a1a1a;
            --text-color-light: #fff;
            --container-background-light: #1a1a1a;
            --table-background-light: #2e2e2e;

            --pre-string: greenyellow;
            --pre-number: orange;
            --pre-boolean: lightskyblue;
            --pre-null: magenta;
            --pre-key: crimson;
        }
    </style>
</head>

<body>
    <div style="height: 7.5vh;"><br></div>
    <button id="mode-switch">Switch to Dark Mode</button>
    <div class="center">
        <div class="container">
            <div class="flex">
                <div id="query"></div>
                <button id="send-query">Execute</button>
                <button id="clear-result">Clear</button>
                <button id="show-schema">Schema</button>
            </div>
        </div>
    </div>
    <div style="height: 80px;"></div>
    <div id="result"></div>
    <div id="overlay">
        <div id="schema" hx-get="/killme/schema" hx-trigger="load" hx-swap="innerHTML"></div>
    </div>

    <script>
        document.querySelector('#clear-result').addEventListener('click', function () {
            document.getElementById('result').innerHTML = ''
        })

        document.getElementById('show-schema').addEventListener('click', function () {
            document.getElementById('overlay').style.display = 'flex'
        })

        document.addEventListener('keydown', function (event) {
            if (event.which === 27) {
                document.getElementById('overlay').style.display = 'none'
            } else if (event.which === 13 && event.ctrlKey) {
                document.getElementById('send-query').click()
            }
        })

        document.getElementById('send-query').onclick = async function (event) {
            const query = window.editor.getValue()
            const formBody = new FormData()
            formBody.append('query', query)
            const response = await fetch('/killme/query', {
                method: 'POST',
                body: formBody
            })
            const result = await response.text()
            const el = document.getElementById('result')
            $(el).append(result)
            const element = el.lastElementChild
            $(element).find('#json-output').html(syntaxHighlight($(element).find('#json-output').html()))
            $(element).find('#toggle-json').on('click', function () {
                console.log(element)
                $(element).find('#json-output').slideToggle(100)
                this.innerHTML = this.innerHTML === 'Show JSON' ? 'Hide  JSON' : 'Show JSON'
            })
            window.scroll({
                top: document.body.scrollHeight,
                behavior: 'smooth'
            })
        }

        require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.31.1/min/vs' } })
        require(['vs/editor/editor.main'], function () {
            monaco.languages.register({ id: 'custom-sql' })

            const keywords = [
                <?php foreach ($keywords as $keyword): ?>
                    '<?= $keyword ?>',
                <?php endforeach ?>
            ]

            const mysqlKeywords = [
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
            const keywordRegex = new RegExp(`\\b(?:${mysqlKeywords.join('|')})\\b`, 'gi');
            monaco.languages.setMonarchTokensProvider('custom-sql', {
                tokenizer: {
                    root: [
                        [keywordRegex, 'keyword'],
                        [/\b(?:' + keywords.join('|') + ')\b/, 'keyword']
                    ]
                }
            })

            monaco.languages.setLanguageConfiguration('custom-sql', {
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

            monaco.languages.registerCompletionItemProvider('custom-sql', {
                provideCompletionItems: () => {
                    const completionItems = keywords.map(keyword => ({
                        label: keyword,
                        kind: monaco.languages.CompletionItemKind.Keyword,
                        insertText: keyword
                    }))
                    return { suggestions: completionItems }
                }
            })

            window.editor = monaco.editor.create(document.getElementById('query'), {
                value: "SELECT SUM(table_rows) AS total_rows\n" +
                        "FROM information_schema.tables\n" +
                        "WHERE table_schema = '<?= $database ?>';",
                language: 'custom-sql',
                theme: 'vs-dark',
            })

            window.editor.addAction({
                id: 'logMeow',
                label: 'Log Meow',
                keybindings: [
                    monaco.KeyMod.CtrlCmd | monaco.KeyCode.Enter
                ],
                run: (ed) => {
                    document.getElementById('send-query').click()
                }
            })
        })

        document.getElementById('mode-switch').addEventListener('click', function modeSwitch() {
            try {
                document.body.classList.toggle('dark-mode')
            const isDarkMode = document.body.classList.contains('dark-mode')
            document.getElementById('mode-switch').textContent = isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'

            if (isDarkMode) {
                monaco.editor.setTheme('vs-dark')
            } else {
                monaco.editor.setTheme('vs-light')
            }

            document.querySelectorAll('table, pre, #schema, .container').forEach(element => {
                element.style.backgroundColor = isDarkMode ? 'var(--table-background-dark)' : 'var(--table-background-light)'
                element.style.color = isDarkMode ? 'var(--text-color-dark)' : 'var(--text-color-light)'
            })
            } catch (error) {
            }
        })

        const syntaxHighlight = (json) => {
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

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('mode-switch').click()
        })
    </script>
</body>

</html>
