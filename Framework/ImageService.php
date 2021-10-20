<?php

namespace Andrijaj\DemoProject\Framework;

use Andrijaj\DemoProject\Framework\Exceptions\FileNonExistentException;
use Andrijaj\DemoProject\Framework\Exceptions\ImagePathNotPartOfDocumentRootException;

class ImageService extends FileService
{
    private const EXTENSION = ['image/jpeg' => '.jpg', 'image/png' => '.png'];

    /**
     * Copies image temporary image file to the specified destination and renames it to the specified newName.
     * Returns the new image path.
     * @param array $image
     * @param string $destination
     * @param string $newName
     * @return string
     * @throws FileNonExistentException
     */
    public function moveTemporaryFile(array $image, string $destination, string $newName): string
    {
        $this->copy($image['tmp_name'], $destination);
        $this->rename($destination . '/' . basename($image['tmp_name']), $newName . $this->getImageExtension($image));

        return $destination . '/' . $newName . self::EXTENSION[$image['type']];
    }

    /**
     * Returns the relative path of the image from the DOCUMENT_ROOT.
     * If the image is not contained in the DOCUMENT_ROOT, throws ImagePathNotPartOfDocumentRootException.
     * @param $image
     * @return string
     * @throws ImagePathNotPartOfDocumentRootException
     */
    public function getRelativePathFromRoot($image): string
    {
        if (strpos($image, $_SERVER['DOCUMENT_ROOT'])) {
            throw new ImagePathNotPartOfDocumentRootException();
        }

        return substr($image, strlen($_SERVER['DOCUMENT_ROOT']));
    }

    /**
     * Returns the extension of the image base on the $image array.
     * @param array $image
     * @return string
     */
    public function getImageExtension(array $image): string{
        return self::EXTENSION[$image['type']];
    }
}