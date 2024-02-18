<?php

namespace Plat4m\Utilities;

use Exception;

class Uploader
{
    /**
     * Key used in the form for file.
     */
    private $formKey = NULL;

    /**
     * File temp path.
     */
    private $fileTmpPath = NULL;

    /**
     * Original file name.
     */
    private $fileName = NULL;

    /**
     * Original file extension.
     */
    private $fileExtension = NULL;

    /**
     * Original file size.
     */
    private $fileSize = NULL;

    /**
     * Original file type.
     */
    private $fileType = NULL;

    /**
     * New file name.
     */
    private $newFileName = NULL;

    /**
     * Extract necessary information for uploading.
     * @param string $formKey Key used in the form for file.
     */
    public function __construct($formKey)
    {
        if (empty($formKey)) {
            throw new Exception("File form key is required", 400);
        }

        if (!isset($_FILES[$formKey])) {
            throw new Exception("File form key ({$formKey}) does not exist", 400);
        }

        if ($_FILES[$formKey]['error'] !== UPLOAD_ERR_OK) {
            throw new Exception($_FILES['uploadedFile']['error'], 500);
        }

        $this->formKey = $formKey;
        $this->fileTmpPath = $_FILES[$formKey]['tmp_name'];
        $this->fileName = $_FILES[$formKey]['name'];
        $this->fileSize = $_FILES[$formKey]['size'];
        $this->fileType = $_FILES[$formKey]['type'];
        $fileNameParts = explode(".", $this->fileName);
        $this->fileExtension = strtolower(end($fileNameParts));

        if (!in_array($this->fileExtension, ALLOWED_FILE_EXT_FOR_UPLOAD)) {
            throw new Exception("Invalid file extension: {$this->fileExtension}", 400);
        }

        $this->generateNewName();
    }

    /**
     * Generates new name for file.
     * E.g. 7bba4732ce4a0297579a5643e4475d04.jpg
     */
    private function generateNewName()
    {
        $curTime = time();
        $hash = md5("{$curTime}{$this->fileName}");
        $this->newFileName = "{$hash}.{$this->fileExtension}";
    }

    /**
     * Uploads file.
     * @param string $destinationDir Destination directory.
     * @return string New file name if successfully uploaded.
     * @throws Exception
     */
    public function upload($destinationDir)
    {
        if (empty($destinationDir)) {
            throw new Exception("Destination directory is required", 500);
        }

        $destination = $destinationDir . "/" . $this->newFileName;

        if (!move_uploaded_file($this->fileTmpPath, $destination)) {
            throw new Exception("Failed to upload", 500);
        }

        return $this->newFileName;
    }
}
