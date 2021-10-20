<?php

namespace Andrijaj\DemoProject\Framework;

use Andrijaj\DemoProject\Framework\Exceptions\FileNonExistentException;

class FileService
{
    /**
     * Copies the file from one place to the other.
     * @param string $source
     * @param string $destination
     * @throws FileNonExistentException
     */
    public function copy(string $source, string $destination)
    {
        if (!file_exists($source)) {
            throw new FileNonExistentException();
        }

        $data = file_get_contents($source);

        file_put_contents($destination . '/' . basename($source), $data);
    }

    /**
     * Renames the specified file with the new name.
     * @param string $source
     * @param string $newFileName
     * @return bool
     */
    public function rename(string $source, string $newFileName): bool
    {
        $path = pathinfo($source);
        $newFilePath = $path['dirname'] . '/' . $newFileName;

        return rename($source, $newFilePath);
    }

    /**
     * Deletes the file with the specified path $filePath
     * @param $filePath
     * @throws FileNonExistentException
     */
    public function delete($filePath)
    {
        if (!file_exists($filePath)) {
            throw new FileNonExistentException();
        }
        unlink($filePath);
    }
}