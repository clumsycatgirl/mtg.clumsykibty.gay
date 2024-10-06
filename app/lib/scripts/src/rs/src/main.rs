use std::{
    fmt::Display,
    fs::{self, File},
    io::Write,
    path::{self, Path},
};

use anyhow::{anyhow, Result};
use clap::{Parser, Subcommand, ValueEnum};
use mysql::{prelude::Queryable, OptsBuilder};
use regex::Regex;

#[derive(Parser)]
#[command(version, about, long_about = None)]
struct Args {
    #[arg(short, long)]
    command: Command,

    #[arg(long, default_value_t = FileType::Unknown)]
    file_type: FileType,

    #[arg(long, default_value_t = String::from(""))]
    file_name: String,
}

#[derive(Subcommand, Debug, Clone, ValueEnum)]
enum Command {
    #[clap(name = "create")]
    Create,
    #[clap(name = "generate")]
    Generate,
}

#[derive(Subcommand, Debug, Clone, ValueEnum)]
enum FileType {
    #[clap(name = "controller")]
    Controller,

    #[clap(name = "model")]
    Model,

    #[clap(name = "unknown")]
    Unknown,
}

impl Display for FileType {
    fn fmt(&self, f: &mut std::fmt::Formatter<'_>) -> std::fmt::Result {
        write!(f, "{}", format!("{:?}", self).to_lowercase())
    }
}

fn main() -> Result<()> {
    let args = Args::parse();

    match args.command {
        Command::Create => create(args)?,
        Command::Generate => generate(args)?,
    }

    Ok(())
}

fn create(args: Args) -> Result<()> {
    if args.file_name.is_empty() {
        return Err(anyhow!("File name is required"));
    }

    match args.file_type {
        FileType::Controller => {
            create_controller(args.file_name.to_owned())?;
        }
        FileType::Model => create_model(args.file_name.to_owned(), String::new())?,
        _ => return Err(anyhow!("Unknown file type")),
    }

    Ok(())
}

fn write_to_file(file_name: String, content: String) -> Result<()> {
    let mut file = fs::File::create(file_name)?;
    file.write_all(content.as_bytes())?;
    Ok(())
}

fn create_controller(controller_name: String) -> Result<()> {
    let path = Path::new(controller_name.as_str());
    let filename = path.file_stem().unwrap().to_str().unwrap();
    let dirname = path.parent().unwrap().to_str().unwrap();

    let content = format!(
        r#"<?php

namespace App\Controllers{};
    
use Lib\Systems\Controllers\Controller;

class {}Controller extends Controller
{{
    public function index()
    {{
    }}
}}
    "#,
        if dirname.is_empty() {
            String::new()
        } else {
            format!("\\{}", dirname)
        },
        filename
    );

    let directory_path = format!("app/controllers/{}", dirname);
    fs::create_dir_all(directory_path)?;

    let fullpath = format!("app/controllers/{}Controller.php", controller_name);
    write_to_file(fullpath.to_owned(), content)?;

    println!(
        "-- created Controller as {}Controller at {} --",
        controller_name, fullpath
    );

    Ok(())
}

fn create_model(model_name: String, talbe_name: String) -> Result<()> {
    let path = Path::new(model_name.as_str());
    let filename = path.file_stem().unwrap().to_str().unwrap();
    let dirname = path.parent().unwrap().to_str().unwrap();

    let mut table_name = talbe_name.clone();
    if talbe_name.is_empty() {
        table_name = Regex::new(r"([A-Z])")
            .unwrap()
            .replace_all(model_name.as_str(), "_$1")
            .to_lowercase();
    }

    let content = format!(
        r#"<?php

namespace App\Models{};

include_once app_models_url . 'dataclasses' . DIRECTORY_SEPARATOR . '{}.php';
use App\Models\DataClasses\{};

use Lib\Systems\Models\Model;

class {}Model extends Model
{{
    /**
     * {} constructor.
     *
     * Initializes the model with the table name '{}'.
     */
    public function __construct() {{
        parent::__construct('{}');
    }}

    /**
     * Retrieve all {} entities from the database.
     *
     * @return array|false Array of {} objects, or false on failure.
     */
    public function all(): array|false|string {{
        $result = parent::all();
        if (is_array($result))
            $result = array_map(fn(array $row): {} => new {}($row), $result);
        return $result;
    }}

    /**
     * Find an {} entity by its ID.
     *
     * @param string|int|float|bool $id The ID of the {} entity to find.
     * @return object|array|false|null {} object if found, null if not found, false on failure.
     */
    public function find(string|int|float|bool $id): object|array|false|null {{
        $result = parent::find($id);
        if (is_array($result))
            $result = new {}($result);
        return $result;
    }}

    /**
     * Select {} entities based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false Array of {} objects matching conditions, or false on failure.
     */
    public function select_where(array $conditions): object|array|false {{
        $result = parent::select_where($conditions);
        if (is_array($result))
            $result = array_map(fn(array $row): {} => new {}($row), $result);
        return $result;
    }}

    /**
     * Select the first {} entity based on specified conditions.
     *
     * @param array $conditions Associative array of conditions (column => value).
     * @return object|array|false|null {} object if found, null if not found, false on failure.
     */
    public function select_where_first(array $conditions): object|array|false|null {{
        $result = parent::select_where_first($conditions);
        if (is_array($result))
            $result = new {}($result);
        return $result;
    }}
}}
    "#,
        if dirname.is_empty() {
            String::new()
        } else {
            format!("\\{}", dirname)
        },
        model_name,
        model_name,
        filename,
        model_name,
        model_name,
        table_name,
        model_name,
        model_name,
        model_name,
        model_name,
        model_name,
        model_name,
        model_name,
        model_name,
        model_name,
        model_name,
        model_name,
        model_name,
        model_name,
        model_name,
        model_name,
    );

    let directory_path = format!("app/models/{}", dirname);
    fs::create_dir_all(directory_path)?;

    let fullpath = format!("app/models/{}Model.php", model_name);
    write_to_file(fullpath.to_owned(), content)?;

    println!(
        "-- created Model as {}Model at {} for table {}--",
        model_name, fullpath, table_name
    );

    Ok(())
}

fn generate(_args: Args) -> Result<()> {
    dotenv::dotenv().ok();

    let mut database_config = vec![];
    let mut paths_config = vec![];
    let mut app_config = vec![];

    for (key, value) in dotenv::vars() {
        if key.starts_with("database.") {
            let db_key = key.trim_start_matches("database.");
            database_config.push((db_key.to_string(), value));
        } else if key.starts_with("app.") {
            let app_key = key.trim_start_matches("app.");
            app_config.push((app_key.to_string(), value));
        } else if key.starts_with("path.") {
            let path_key = key.trim_start_matches("path.");
            paths_config.push((path_key.to_string(), value));
        }
    }

    generate_database_config(&database_config)?;
    generate_paths_config(&paths_config)?;
    generate_app_config(&app_config)?;

    let mut database_database: String = String::new();
    let mut database_hostname: String = String::new();
    let mut database_password: String = String::new();
    let mut database_port: u16 = 0;
    let mut database_username: String = String::new();
    for (key, value) in database_config {
        if key == "database" {
            database_database = value;
        } else if key == "hostname" {
            database_hostname = value;
        } else if key == "password" {
            database_password = value;
        } else if key == "port" {
            database_port = value.parse()?;
        } else if key == "username" {
            database_username = value;
        }
    }

    println!(
        "Creating database '{}' as '{}' at {}:{}",
        database_database, database_username, database_hostname, database_port,
    );

    let create_query = r#"
    CREATE TABLE IF NOT EXISTS auth_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL
    );

    CREATE TABLE IF NOT EXISTS auth_groups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL
    );

    CREATE TABLE IF NOT EXISTS auth_permissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL
    );

    CREATE TABLE IF NOT EXISTS auth_group_permissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        group_id INT NOT NULL,
        permission_id INT NOT NULL,
        FOREIGN KEY (group_id) REFERENCES auth_groups(id),
        FOREIGN KEY (permission_id) REFERENCES auth_permissions(id)
    );

    CREATE TABLE IF NOT EXISTS auth_user_groups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        group_id INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES auth_users(id),
        FOREIGN KEY (group_id) REFERENCES auth_groups(id)
    );

    CREATE TABLE IF NOT EXISTS auth_user_permissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        permission_id INT NOT NULL,
        FOREIGN KEY (user_id) REFERENCES auth_users(id),
        FOREIGN KEY (permission_id) REFERENCES auth_permissions(id)
    );
"#
    .to_string();

    let relationships_query = r#"
        INSERT INTO auth_users (username, password) VALUES ('admin', '21232f297a57a5a743894a0e4a801fc3');
        
        INSERT INTO auth_groups (name) VALUES ('admin_group');
        
        INSERT INTO auth_group_permissions (group_id, permission_id) 
            SELECT auth_groups.id, auth_permissions.id
            FROM auth_groups, auth_permissions
            WHERE auth_groups.name = 'admin_group' AND auth_permissions.name = 'admin_permission';
        
        INSERT INTO auth_user_groups (user_id, group_id) 
            SELECT auth_users.id, auth_groups.id
            FROM auth_users, auth_groups
            WHERE auth_users.username = 'admin' AND auth_groups.name = 'admin_group';
        
        INSERT INTO auth_user_permissions (user_id, permission_id) 
            SELECT auth_users.id, auth_permissions.id
            FROM auth_users, auth_permissions
            WHERE auth_users.username = 'admin' AND auth_permissions.name = 'admin_permission';
        "#
    .to_string();

    let builder = OptsBuilder::new()
        .ip_or_hostname(Some(&database_hostname))
        .tcp_port(database_port)
        .user(Some(&database_username))
        .pass(Some(&database_password))
        .db_name(Some(&database_database));

    let pool = mysql::Pool::new(builder)
        .map_err(|err| anyhow::anyhow!("Failed to establish connection: {}", err))?;

    let mut conn = pool.get_conn()?;
    conn.query_drop(create_query)
        .map_err(|err| anyhow::anyhow!("Failed to execute query: {}", err))?;
    conn.query_drop(relationships_query)
        .map_err(|err| anyhow::anyhow!("Failed to execute query: {}", err))?;

    let table_names: Vec<String> = conn
        .query_map("SHOW TABLES", |table_name: String| table_name)
        .map_err(|err| anyhow::anyhow!("Failed to execute query: {}", err))?;
    table_names.into_iter().for_each(|table_name| {
        if table_name.starts_with("auth_") {
            return;
        }
        let re = Regex::new(r"_([a-z])").unwrap();
        let mut model_name = re
            .replace_all(table_name.as_str(), |caps: &regex::Captures| {
                caps[1].to_uppercase()
            })
            .to_string();

        if let Some(first_char) = model_name.get_mut(0..1) {
            first_char.make_ascii_uppercase();
        }

        let _ = create_model(model_name.clone(), table_name.clone());

        let columns_query = r"SELECT COLUMN_NAME, DATA_TYPE 
                      FROM INFORMATION_SCHEMA.COLUMNS 
                      WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?";

        let columns: Vec<(String, String)> = conn
            .exec_map(
                columns_query,
                (database_database.clone(), table_name.clone()),
                |(column_name, data_type)| (column_name, data_type),
            )
            .map_err(|err| anyhow::anyhow!("Failed to execute query: {}", err))
            .unwrap();

        let columns: Vec<(String, String)> = columns
            .iter()
            .map(|(name, dtype)| {
                let mut result = String::new();
                for (i, c) in name.chars().enumerate() {
                    if c.is_uppercase() {
                        if i != 0 {
                            result.push('_');
                        }
                        result.push(c.to_ascii_lowercase());
                    } else {
                        result.push(c);
                    }
                }
                (result, dtype.clone())
            })
            .collect();

        let php_class = generate_php_class(&model_name, &columns);
        let fullpath = format!("app/models/dataclasses/{}.php", model_name);
        _ = write_to_file(fullpath.clone(), php_class);
        println!(
            "-- Created DataClass for '{}' at '{}'",
            model_name, fullpath
        );
    });

    Ok(())
}

fn generate_database_config(config: &Vec<(String, String)>) -> Result<()> {
    let path = Path::new("app/config/database.php");
    let mut file = File::create(path)?;

    writeln!(file, "<?php\n")?;
    for (key, value) in config {
        writeln!(file, "const database_{} = '{}';", key, value)?;
    }

    println!("Database configuration file generated at {:?}", path);
    Ok(())
}

fn generate_paths_config(config: &Vec<(String, String)>) -> Result<()> {
    let path = Path::new("app/config/paths.php");
    let mut file = File::create(path)?;

    writeln!(file, "<?php\n")?;

    for (key, value) in config {
        if value.is_empty() {
            continue;
        }
        let const_name = key.replace('.', "_");
        writeln!(
            file,
            "const {} = base_url . '{}' . DIRECTORY_SEPARATOR;",
            const_name, value
        )?;
    }

    if !config.is_empty() {
        writeln!(file, "\n")?;
    }
    writeln!(
        file,
        "const app_url = base_url . 'app' . DIRECTORY_SEPARATOR;"
    )?;
    writeln!(
        file,
        "const app_cfg_url = app_url . 'config' . DIRECTORY_SEPARATOR;"
    )?;
    writeln!(
        file,
        "const app_controllers_url = app_url . 'controllers' . DIRECTORY_SEPARATOR;"
    )?;
    writeln!(
        file,
        "const app_models_url = app_url . 'models' . DIRECTORY_SEPARATOR;"
    )?;
    writeln!(
        file,
        "const app_views_url = app_url . 'views' . DIRECTORY_SEPARATOR;"
    )?;
    writeln!(
        file,
        "const app_assets_url = fc_url . 'assets' . DIRECTORY_SEPARATOR;"
    )?;

    println!("Paths configuration file generated at {:?}", path);
    Ok(())
}

fn generate_app_config(config: &Vec<(String, String)>) -> Result<()> {
    let path = Path::new("app/config/config.php");
    let mut file = File::create(path)?;

    writeln!(file, "<?php\n")?;
    for (key, value) in config {
        writeln!(file, "const app_{} = '{}';", key, value)?;
    }

    println!("App configuration file generated at {:?}", path);
    Ok(())
}

fn generate_php_class(class_name: &str, columns: &[(String, String)]) -> String {
    let mut class_content = format!(
        "<?php\n\nnamespace App\\Models\\DataClasses;\n\n/**\n * Class {}\n *\n * Represents a {} in the system.\n */\nclass {} {{\n",
        class_name, class_name, class_name
    );

    for (column_name, data_type) in columns {
        let php_type = match data_type.as_str() {
            "int" => "int|null",
            "varchar" | "text" | "char" => "string|null",
            "datetime" | "timestamp" => "\\DateTime|null",
            _ => "mixed",
        };
        class_content.push_str(&format!("    private {} ${};\n", php_type, column_name));
    }

    class_content.push_str("\n    /**\n     * Constructor.\n     *\n     * @param array $array The row from the database.\n     */\n");
    class_content.push_str("    public function __construct(array $array) {\n");

    for (column_name, _) in columns {
        let re = Regex::new(r"_(.)").unwrap();
        let mut camel = String::new();
        let mut last_end = 0;

        let name = column_name.as_str();
        for cap in re.captures_iter(name) {
            camel.push_str(&name[last_end..cap.get(0).unwrap().start()]);
            camel.push_str(&cap[1].to_uppercase());
            last_end = cap.get(0).unwrap().end();
        }
        camel.push_str(&name[last_end..]);
        class_content.push_str(&format!(
            "        $this->{} = $array['{}'] ?? null;\n",
            column_name, camel
        ));
    }

    class_content.push_str("    }\n\n");

    for (column_name, data_type) in columns {
        let php_type = match data_type.as_str() {
            "int" => "int|null",
            "varchar" | "text" | "char" => "string|null",
            "datetime" | "timestamp" => "\\DateTime|null",
            _ => "mixed",
        };
        class_content.push_str(&format!(
            "    /**\n     * Get the {}.\n     *\n     * @return {}\n     */\n",
            column_name, php_type
        ));
        class_content.push_str(&format!(
            "    public function {}(): {} {{\n",
            column_name, php_type
        ));
        class_content.push_str(&format!("        return $this->{};\n", column_name));
        class_content.push_str("    }\n\n");
    }

    class_content.push_str("}\n");

    class_content
}
