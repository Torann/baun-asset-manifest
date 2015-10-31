<?php

namespace Torann\AssetManifest;

use Baun\Plugin;

class Manifest extends Plugin
{
    /**
     * Debug enabled.
     *
     * @var bool
     */
    public $debug = false;

    /**
     * Asset direcory name.
     *
     * @var string
     */
    public $assets_dir = 'assets';

    /**
     * Asset manifest.
     *
     * @var array
     */
    public $manifest = array();

    /**
     * Initialize plugin.
     */
    public function init()
    {
        $this->debug = $this->config->get('app.debug', $this->debug);
        $this->assets_dir = $this->config->get('app.assets_dir', $this->assets_dir);

        $this->load();
        $this->theme->addFunction('asset', $this);
    }

    public function custom_asset($key)
    {
        if ($this->debug === false && isset($this->manifest[$key])) {
            return $this->asset($this->manifest[$key]);
        }

        return $this->asset($key);
    }

    /**
     * Cleanup asset path.
     *
     * @param  string $asset
     * @return string
     */
    private function asset($asset)
    {
        return str_replace('//', '/', trim("/{$this->assets_dir}/{$asset}", '/'));
    }

    /**
     * Load manifest file
     */
    private function load()
    {
        $file = BASE_PATH.DIRECTORY_SEPARATOR.'rev-manifest.json';

        if (file_exists($file)) {
            $this->manifest = json_decode(file_get_contents($file), true);
        }
    }
}