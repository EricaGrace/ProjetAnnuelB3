<?php

namespace App\Utils;

use Exception;

/**
 * Usage:
 * $config->all();
 * $config->get('uploads');
 * $config->get('uploads')['location'];
 * $config('uploads');
 */
class Config
{
    /**
     * Configuration files directory
     */
    private const VARIABLES_DIR = __DIR__ . "/../Variables";
    /**
     * @var array contains all the configuration values
     */
    public array $config = [];

    public function __construct()
    {
        $this->getConfigurationFiles();
    }

    /**
     * Retrieves the files in the VARIABLES_DIR directory
     * @return void
     */
    private function getConfigurationFiles()
    {
        $files = Filesystem::getFilesList(self::VARIABLES_DIR);

        foreach ($files as $file) {
            $this->addConfig($file);
        }
    }

    /**
     * Add file content to Config::config property
     * @param $file
     * @return void
     */
    private function addConfig($file)
    {

        $fileInfo = pathinfo($file);
        $keyName = $fileInfo['filename'];

        $value = require(self::VARIABLES_DIR . '/' . $fileInfo['basename']);

        $this->config[$keyName] = $value;

    }

    public function __invoke(string $key): ?array
    {
        return $this->get($key);
    }

    /**
     * Checks if the parameter exists in the config property, then retrieves it
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    public function get(string $key): mixed
    {
        if (!$this->has($key)) {
            throw new Exception('Config file does not exist');
        }

        return $this->config[$key];
    }

    /**
     * Checks if the parameter exists in the config property
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        foreach ($this->config as $confkey => $config) {
            if ($key === $confkey) {
                return true;
            }
        }
        return false;
    }

    public function all(): array
    {
        return $this->config;
    }
}