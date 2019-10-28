<?php

namespace LilleBitte\Messenger;

use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class UploadedFile implements UploadedFileInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $clientFilename;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $tmpName;

    /**
     * @var integer
     */
    private $error;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var StreamInterface
     */
    private $stream;

    /**
     * @var array
     */
    private $errMsg = [
        \UPLOAD_ERR_OK => "There is no error, file uploaded with success.",
        \UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the 'upload_max_filesize' directive in php.ini.",
        \UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive that " .
            "was specified in the HTML form.",
        \UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
        \UPLOAD_ERR_NO_FILE => "No file was uploaded.",
        \UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
        \UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
        \UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload. " .
            "PHP does not provide a way to ascertain which extension " .
            "caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help."
    ];

    /**
     * @var bool
     */
    private $moved = false;

    public function __construct(
        $file,
        $size,
        $error = \UPLOAD_ERR_OK,
        $clientFilename = null,
        $clientType = null
    ) {
        if ($error === \UPLOAD_ERR_OK) {
            if (is_string($file)) {
                $this->name = $file;
            }

            if (is_resource($file)) {
                $this->stream = new Stream($file);
            }

            if (!$this->name && !$this->stream) {
                if (!($file instanceof StreamInterface)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            "Invalid stream or file provided for '%s'",
                            \get_class($this)
                        )
                    );
                }

                $this->stream = $file;
            }
        }

        $this->size = $size;

        if (!in_array($error, array_keys($this->errMsg), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid error status for '%s'. It must be an UPLOAD_ERR_* constant.",
                    get_class($this)
                )
            );
        }

        $this->error = $error;
        $this->clientFilename = $clientFilename;
        $this->type = $clientType;
    }

    /**
     * {@inheritdoc}
     */
    public function getStream()
    {
        if ($this->error !== \UPLOAD_ERR_OK) {
            throw new \RuntimeException(
                sprintf("%s", $this->errMsg[$this->error])
            );
        }

        if ($this->moved) {
            throw new \RuntimeException(
                sprintf("Uploaded file %s already moved.", $this->name)
            );
        }

        if (!($this->stream instanceof StreamInterface)) {
            $this->stream = new Stream($this->name);
        }

        return $this->stream;
    }

    /**
     * {@inheritdoc}
     */
    public function moveTo($targetPath)
    {
        if ($this->moved) {
            throw new \RuntimeException(
                "File has already moved."
            );
        }

        if ($this->error !== \UPLOAD_ERR_OK) {
            throw new \RuntimeException(
                sprintf("%s", $this->errMsg[$this->error])
            );
        }

        if (!is_string($targetPath) || empty($targetPath)) {
            throw new \InvalidArgumentException(
                "Path name must be a non-empty string."
            );
        }

        $targetDir = \dirname($targetPath);

        if (!is_dir($targetDir) || !is_writable($targetDir)) {
            throw new \RuntimeException(
                sprintf(
                    "Directory '%s' is not writable.",
                    $targetDir
                )
            );
        }

        if (empty(\PHP_SAPI) || strpos(\PHP_SAPI, "cli") === 0) {
            $this->writeFile($targetPath);
        } else {
            if (false === \move_uploaded_file($this->name, $targetPath)) {
                throw new \RuntimeException(
                    sprintf(
                        "Failed to move '%s' to '%s'",
                        $this->name,
                        $targetPath
                    )
                );
            }
        }

        $this->moved = true;
    }

    private function writeFile($path)
    {
        \set_error_handler(function ($num, $msg) {
            return;
        }, E_ALL);

        $handler = \fopen($path, 'wb+');

        \restore_error_handler();

        if (false === $handler) {
            throw new \RuntimeException(
                "Destination path is not writable."
            );
        }

        $stream = $this->getStream();
        $stream->rewind();

        if (!$stream->eof()) {
            fwrite($handler, $stream->read(8192));
        }

        fclose($handler);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientMediaType()
    {
        return $this->type;
    }
}
