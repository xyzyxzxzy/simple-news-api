<?php

namespace App\Service;

class UtilsService
{
    /**
     * Преобразовать строку
     * @param string $string
     * @return string
     */
    public function convertString(
        string $string
    ): string
    {
        return htmlspecialchars(trim($string));
    }

    /**
     * Удалить директорию и её содержимое
     * @param string $dir
     * @return void
     */
    public function recursiveRemoveDir($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        $includes = new \FilesystemIterator($dir);
        foreach ($includes as $include) {
            if (is_dir($include) && !is_link($include)) {
                return $this->recursiveRemoveDir($include);
            }

            unlink($include);
        }
        
        rmdir($dir);
    }
}
