<?php
namespace app\components;

class Env
{
    private static $vars = [];

    public static function load(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException(".env file not found: {$filePath}");
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = self::parseLine($line);
            self::$vars[$name] = $value;
        }
    }

    public static function get(string $key, $default = null)
    {
        return self::$vars[$key] ?? $default;
    }

    private static function parseLine(string $line): array
    {
        if (strpos($line, '=') === false) {
            return [trim($line), ''];
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (preg_match('/^["\'](.*)["\']$/', $value, $matches)) {
            $value = $matches[1];
        }

        return [$name, $value];
    }
}