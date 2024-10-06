use std::fs::{self, File};
use std::io::{self, BufRead};
use std::path::{Path, PathBuf};

use anyhow::{anyhow, Result};
use clap::Parser;

#[derive(Parser)]
#[command(version, about, long_about = None)]
struct Args {
    #[arg(short, long, default_value_t = String::from("."))]
    directory: String,
}

fn main() -> Result<()> {
    let args = Args::parse();

    if args.directory.is_empty() {
        return Err(anyhow!("No starting directory provided"));
    }

    let mut files_with_lines: Vec<(PathBuf, usize)> = Vec::new();

    if let Err(err) = list_files_by_lines(Path::new(args.directory.as_str()), &mut files_with_lines)
    {
        eprintln!("Error: {}", err);
    }

    files_with_lines.sort_by(|a, b| b.1.cmp(&a.1));

    let total_loc: usize = files_with_lines.iter().map(|(_, lines)| lines).sum();

    println!("total loc: {}", total_loc);
    for (file_path, lines) in files_with_lines {
        println!("{}: {} lines", file_path.display(), lines);
    }

    Ok(())
}

fn list_files_by_lines(
    directory: &Path,
    files_with_lines: &mut Vec<(PathBuf, usize)>,
) -> io::Result<()> {
    for entry in fs::read_dir(directory)? {
        let entry = entry?;
        let file_path = entry.path();

        if file_path.is_dir() {
            if let Err(err) = list_files_by_lines(&file_path, files_with_lines) {
                eprintln!("Error in directory {:?}: {}", file_path, err);
            }
        } else {
            let file_path_str = file_path.to_string_lossy().to_string();

            if file_path_str.contains(".git")
                || file_path_str.contains("node_modules")
                || file_path_str.contains("target")
            {
                continue;
            }

            if let Some(extension) = file_path.extension() {
                if extension == "exe" {
                    continue;
                }
            }

            let lines = count_lines(&file_path)?;
            files_with_lines.push((file_path.clone(), lines));
        }
    }

    Ok(())
}

fn count_lines(file_path: &Path) -> io::Result<usize> {
    let file = File::open(file_path)?;
    let reader = io::BufReader::new(file);
    Ok(reader.lines().count())
}
