<?php
declare(strict_types=1);

namespace Croogo\Core\Utility;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Zastępnik usuniętych w CakePHP 5 klas Cake\Filesystem\Folder/File.
 *
 * Pokrywa wyłącznie operacje używane przez Croogo (read/delete/move/find).
 */
class FsUtils
{
    /**
     * Odpowiednik Folder::read() — zwraca [katalogi, pliki], posortowane.
     *
     * @param string $path Ścieżka katalogu
     * @return array{0: array<string>, 1: array<string>}
     */
    public static function read(string $path): array
    {
        $dirs = $files = [];
        if (!is_dir($path)) {
            return [$dirs, $files];
        }
        foreach ((array)scandir($path) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            if (is_dir($path . DIRECTORY_SEPARATOR . $entry)) {
                $dirs[] = $entry;
            } else {
                $files[] = $entry;
            }
        }
        sort($dirs);
        sort($files);

        return [$dirs, $files];
    }

    /**
     * Odpowiednik Folder::delete() — rekursywne usunięcie drzewa.
     */
    public static function deleteTree(string $dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $item) {
            $ok = $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
            if (!$ok) {
                return false;
            }
        }

        return rmdir($dir);
    }

    /**
     * Odpowiednik Folder::move() w wariancie "przenieś zawartość $from do $to
     * i usuń $from" (używane przy rozpakowywaniu zipów z katalogiem głównym).
     */
    public static function moveContents(string $from, string $to): bool
    {
        $from = rtrim($from, '/\\');
        $to = rtrim($to, '/\\');
        if (!is_dir($from)) {
            return false;
        }
        if (!is_dir($to) && !mkdir($to, 0777, true)) {
            return false;
        }
        foreach ((array)scandir($from) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            if (!rename($from . DIRECTORY_SEPARATOR . $entry, $to . DIRECTORY_SEPARATOR . $entry)) {
                return false;
            }
        }

        return rmdir($from);
    }

    /**
     * Odpowiednik Folder::find() — nazwy plików w $dir pasujące do $regex
     * (bez kropki i ukośników, pełny match jak w Folder).
     *
     * @return array<string>
     */
    public static function find(string $dir, string $regex): array
    {
        [, $files] = static::read($dir);

        return array_values(array_filter($files, function ($file) use ($regex) {
            return (bool)preg_match('/^' . $regex . '$/i', $file);
        }));
    }

    /**
     * Odpowiednik Folder::findRecursive() — pełne ścieżki plików pasujących
     * do $regex w całym drzewie $dir.
     *
     * @return array<string>
     */
    public static function findRecursive(string $dir, string $regex): array
    {
        $found = [];
        if (!is_dir($dir)) {
            return $found;
        }
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $item) {
            if ($item->isFile() && preg_match('/^' . $regex . '$/i', $item->getFilename())) {
                $found[] = $item->getPathname();
            }
        }
        sort($found);

        return $found;
    }
}
