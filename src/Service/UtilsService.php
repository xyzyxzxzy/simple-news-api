<?php

namespace App\Service;

class UtilsService
{
    public function convertString(
        string $string
    ): string
    {
        return htmlspecialchars(trim($string));
    }

    public function recursiveRemoveDir($dir): void
    {
        if (!file_exists($dir)) {
            return;
        }

        $includes = new \FilesystemIterator($dir);
        foreach ($includes as $include) {
            if (is_dir($include) && !is_link($include)) {
                $this->recursiveRemoveDir($include);
                return;
            }

            unlink($include);
        }
        
        rmdir($dir);
    }
}
