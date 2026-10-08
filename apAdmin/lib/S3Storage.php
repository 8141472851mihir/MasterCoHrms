<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AsyncAws\S3\S3Client;

class S3Storage
{
    private $client;
    private $bucket;
    private $region;

    public function __construct($accessKey, $secretKey, $region, $bucket)
    {
        $this->bucket = $bucket;
        $this->region = $region;
        $this->client = new S3Client([
            'region' => $region,
            'accessKeyId' => $accessKey,
            'accessKeySecret' => $secretKey,
        ]);
    }

    /**
     * Upload a file to S3
     * @param string $filePath   Local file path (e.g. $_FILES['file'])
     * @param string $keyPath    S3 file key (e.g. "folder/filename.jpg")
     * @return string|false      Returns the file URL on success, false on failure
     */
    public function uploadFile($filePath, $keyPath)
    {        
        try {
            // Detect MIME type from file content for accurate ContentType
            // This is especially important for PNG files to preserve transparency
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $contentType = finfo_file($finfo, $filePath['tmp_name']);
            finfo_close($finfo);

            if ($contentType === false) {
                // Fallback to browser-provided type or default
                $contentType = $filePath['type'] ?? 'application/octet-stream';
            }

            // Normalize PNG MIME types to ensure transparency is preserved
            // Some systems report 'image/x-png' instead of 'image/png'
            if ($contentType === 'image/x-png') {
                $contentType = 'image/png';
            }

            $this->client->putObject([
                'Bucket'      => $this->bucket,
                'Key'         => $keyPath,
                'Body'        => fopen($filePath['tmp_name'], 'rb'), // IMPORTANT
                'ContentType' => $contentType,
            ]);

            return "https://{$this->bucket}.s3.{$this->region}.amazonaws.com/{$keyPath}";
        } catch (\Throwable $e) {
            error_log('S3 Upload Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload a file to S3 from a local file path (for migrations/batch uploads)
     * @param string $filePath Local file path (e.g. "/path/to/file.jpg")
     * @param string $keyPath S3 file key (e.g. "folder/filename.jpg")
     * @param array  $options Optional settings:
     *   - 'max_width'   => max image width in pixels (only resizes if provided)
     *   - 'max_height'  => max image height in pixels (only resizes if provided)
     * @return string|false Returns the file URL on success, false on failure
     */
    public function uploadFileFromPath($filePath, $keyPath, $options = array())
    {
        try {
            if (!file_exists($filePath)) {
                error_log('S3 Upload Error: File does not exist - ' . $filePath);
                return false;
            }

            // Detect MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $contentType = finfo_file($finfo, $filePath);
            finfo_close($finfo);

            if ($contentType === false) {
                $contentType = 'application/octet-stream';
            }

            // Optional image resize for local file path uploads
            $maxWidth  = null;
            $maxHeight = null;
            if (isset($options['max_width']) && is_numeric($options['max_width']) && $options['max_width'] > 0) {
                $maxWidth = (int)$options['max_width'];
            }

            if (isset($options['max_height']) && is_numeric($options['max_height']) && $options['max_height'] > 0) {
                $maxHeight = (int)$options['max_height'];
            }

            // Only attempt resize if at least one dimension is provided and this is an image
            if (($maxWidth || $maxHeight) && is_string($contentType) && strpos($contentType, 'image/') === 0) {
                try {
                    $this->resizeImage($filePath, $contentType, $maxWidth, $maxHeight);
                    // After resize, re-detect MIME type in case format changed (rare)
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $contentType = finfo_file($finfo, $filePath) ?: $contentType;
                    finfo_close($finfo);
                } catch (\Throwable $e) {
                    // Silently skip resize on any error and continue upload as-is
                }
            }

            $this->client->putObject([
                'Bucket'      => $this->bucket,
                'Key'         => $keyPath,
                'Body'        => fopen($filePath, 'rb'),
                'ContentType' => $contentType,
            ]);

            return "https://{$this->bucket}.s3.{$this->region}.amazonaws.com/{$keyPath}";
        } catch (\Throwable $e) {
            error_log('S3 Upload Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a file from S3
     * @param string $keyPath    S3 file key (e.g. "folder/filename.jpg")
     * @return bool              True on success, false on failure
     */
    public function deleteFile($keyPath)
    {
        try {
            $this->client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $keyPath,
            ]);
            return true;
        } catch (\Throwable $e) {
            // echo ('S3 Delete Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if a file exists in S3
     * @param string $keyPath  e.g. "company/MYCO_1/Media.png"
     * @return bool
     */
    public function fileExists($keyPath)
    {
        try {
            $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $keyPath,
            ]);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Upload file to S3 with validation (size and extension check)
     * @param array $fileInput $_FILES array element (e.g., $_FILES['user_profile_pic'])
     * @param string $keyPath S3 file key (e.g. "folder/filename.jpg")
     * @param array $options Optional settings (only validates if provided):
     *   - 'allowed_extensions' => array of allowed extensions (only validates if provided)
     *   - 'max_size' => max file size in bytes (only validates if provided)
     *   - 'max_width' => max image width in pixels (only resizes if provided)
     *   - 'max_height' => max image height in pixels (only resizes if provided)
     * @return array Returns array with:
     *   - 'success' => bool
     *   - 'message' => string
     *   - 'url' => string (S3 URL if successful)
     */
    public function uploadFileWithValidation($fileInput, $keyPath, $options = array())
    {
        $result = array(
            'success' => false,
            'message' => '',
            'url' => ''
        );

        // Validate file input
        if (!isset($fileInput) || !isset($fileInput['tmp_name']) || !file_exists($fileInput['tmp_name'])) {
            $result['message'] = 'No file uploaded or file does not exist';
            return $result;
        }

        // Check for upload errors
        if (isset($fileInput['error']) && $fileInput['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = array(
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
            );
            $result['message'] = isset($errorMessages[$fileInput['error']]) 
                ? $errorMessages[$fileInput['error']] 
                : 'Unknown upload error';
            return $result;
        }

        // Validate file extension only if allowed_extensions is provided
        if (isset($options['allowed_extensions']) && is_array($options['allowed_extensions']) && !empty($options['allowed_extensions'])) {
            $ext = strtolower(pathinfo($fileInput['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $options['allowed_extensions'])) {
                $result['message'] = 'Invalid file extension. Allowed: ' . implode(', ', $options['allowed_extensions']);
                return $result;
            }
        }

        // Validate file size only if max_size is provided
        if (isset($options['max_size']) && is_numeric($options['max_size']) && $options['max_size'] > 0) {
            if (isset($fileInput['size']) && $fileInput['size'] > $options['max_size']) {
                $maxSizeMB = round($options['max_size'] / 1048576, 2);
                $result['message'] = "File size exceeds maximum allowed size of {$maxSizeMB} MB";
                return $result;
            }
        }

        // Optional image resize (only for image uploads and only if max dimensions are provided)
        $maxWidth  = null;
        $maxHeight = null;
        if (isset($options['max_width']) && is_numeric($options['max_width']) && $options['max_width'] > 0) {
            $maxWidth = (int)$options['max_width'];
        }

        if (isset($options['max_height']) && is_numeric($options['max_height']) && $options['max_height'] > 0) {
            $maxHeight = (int)$options['max_height'];
        }

        // Only attempt resize if at least one dimension is provided
        if (($maxWidth || $maxHeight) && isset($fileInput['tmp_name']) && file_exists($fileInput['tmp_name'])) {
            try {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime  = finfo_file($finfo, $fileInput['tmp_name']);
                finfo_close($finfo);

                if (is_string($mime) && strpos($mime, 'image/') === 0) {
                    // Resize in-place; ignore failures and continue with original file
                    $this->resizeImage($fileInput['tmp_name'], $mime, $maxWidth, $maxHeight);
                    // After resize, update size metadata if available
                    if (isset($fileInput['size'])) {
                        $fileInput['size'] = filesize($fileInput['tmp_name']);
                    }
                }
            } catch (\Throwable $e) {
                // Silently skip resize on any error and continue upload as-is
            }
        }

        // Upload to S3 using existing uploadFile method
        $s3Url = $this->uploadFile($fileInput, $keyPath);

        if ($s3Url === false) {
            $result['message'] = 'Failed to upload file to S3';
            return $result;
        }

        // Success
        $result['success'] = true;
        $result['message'] = 'File uploaded successfully';
        $result['url'] = $s3Url;

        return $result;
    }

    /**
     * Resize an image file in-place, preserving aspect ratio and avoiding upscaling.
     * This uses the GD extension and supports common formats (jpeg, png, gif, webp).
     *
     * @param string   $filePath  Local filesystem path to the image
     * @param string   $mime      MIME type (e.g. image/jpeg)
     * @param int|null $maxWidth  Max width in pixels (null to ignore)
     * @param int|null $maxHeight Max height in pixels (null to ignore)
     * @return void
     */
    private function resizeImage($filePath, $mime, $maxWidth = null, $maxHeight = null)
    {
        if (!$maxWidth && !$maxHeight) {
            return;
        }

        [$width, $height, $type] = getimagesize($filePath);
        if (!$width || !$height) {
            return;
        }

        // Determine scale while preserving aspect ratio
        $scaleX = $maxWidth  ? $maxWidth / $width  : 1;
        $scaleY = $maxHeight ? $maxHeight / $height : 1;
        $scale  = min($scaleX, $scaleY);

        // Do not upscale smaller images
        if ($scale >= 1) {
            return;
        }

        $newWidth  = (int)floor($width * $scale);
        $newHeight = (int)floor($height * $scale);

        // Create source image based on mime/type
        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImage = imagecreatefromjpeg($filePath);
                $saveFunc = 'imagejpeg';
                $quality  = 85;
                break;
            case IMAGETYPE_PNG:
                $srcImage = imagecreatefrompng($filePath);
                $saveFunc = 'imagepng';
                $quality  = 6; // compression level 0-9
                break;
            case IMAGETYPE_GIF:
                $srcImage = imagecreatefromgif($filePath);
                $saveFunc = 'imagegif';
                $quality  = null;
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    $srcImage = imagecreatefromwebp($filePath);
                    $saveFunc = 'imagewebp';
                    $quality  = 85;
                    break;
                }
                // fall-through if WEBP not supported
            default:
                return; // unsupported format
        }

        if (!$srcImage) {
            return;
        }

        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG and GIF
        if (in_array($type, [IMAGETYPE_PNG, IMAGETYPE_GIF], true)) {
            imagecolortransparent(
                $dstImage,
                imagecolorallocatealpha($dstImage, 0, 0, 0, 127)
            );
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
        }

        imagecopyresampled(
            $dstImage,
            $srcImage,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $width,
            $height
        );

        // Overwrite original file
        if ($saveFunc === 'imagejpeg') {
            $saveFunc($dstImage, $filePath, $quality);
        } elseif ($saveFunc === 'imagepng') {
            $saveFunc($dstImage, $filePath, $quality);
        } elseif ($saveFunc === 'imagewebp') {
            $saveFunc($dstImage, $filePath, $quality);
        } else {
            $saveFunc($dstImage, $filePath);
        }

        imagedestroy($srcImage);
        imagedestroy($dstImage);
    }
}
