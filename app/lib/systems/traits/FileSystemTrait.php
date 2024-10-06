<?php

namespace Lib\Systems\Traits;

trait FileSystemTrait {
    function read_file(string $path): string {
        if (file_exists($path)) {
            return file_get_contents($path);
        } else {
            throw new \Exception("File not found: $path");
        }
    }

    function write_file(string $path, string $content): void {
        file_put_contents($path, $content);
    }

    function delete_file(string $path): void {
        if (file_exists($path)) {
            unlink($path);
        } else {
            throw new \Exception("File not found: $path");
        }
    }
}
