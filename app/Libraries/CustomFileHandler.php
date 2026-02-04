<?php

namespace App\Libraries;

use CodeIgniter\Cache\Handlers\FileHandler;
use Config\Cache;

/**
 * Custom File Handler untuk mengatasi masalah permission di Windows
 */
class CustomFileHandler extends FileHandler
{
    public function __construct(Cache $config)
    {
        $options = [
            'storePath' => $config->file['storePath'] ?? '',
            'mode'      => $config->file['mode'] ?? 0640,
            ...$config->file,
        ];

        $this->path = $options['storePath'] !== '' ? $options['storePath'] : WRITEPATH . 'cache';
        $this->path = rtrim($this->path, '\\/') . '/';

        // Buat folder jika belum ada
        if (!is_dir($this->path)) {
            @mkdir($this->path, 0777, true);
            @chmod($this->path, 0777);
        }

        // Skip checking untuk Windows - langsung assume writable
        // Jika tidak writable, akan error saat save nanti
        if (DIRECTORY_SEPARATOR !== '\\') {
            // Untuk Unix/Linux, tetap check
            if (!is_really_writable($this->path)) {
                throw \CodeIgniter\Cache\Exceptions\CacheException::forUnableToWrite($this->path);
            }
        }

        $this->mode   = $options['mode'];
        $this->prefix = $config->prefix;

        helper('filesystem');
    }
}
