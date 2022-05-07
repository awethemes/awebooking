<?php

namespace AweBooking\PMS\Traits;

trait AssetsTrait
{
    /**
     * @param string $path
     * @return string
     */
    public function getAssetUrl(string $path): string
    {
        return untrailingslashit(AWEBOOKING_PMS_ASSETS_URL) . '/' . ltrim($path, '/');
    }

    /**
     * Get asset info from extracted asset files.
     *
     * @param string $name Asset name as defined in build/webpack configuration.
     * @param string|null $attribute Optional attribute to get. Can be "version" or "dependencies".
     * @return string|array{version:string, dependencies:array}
     */
    public function getAssetInfo(string $name, string $attribute = null)
    {
        static $assets = [];

        if (!array_key_exists($name, $assets)) {
            $asset_path = untrailingslashit(AWEBOOKING_PMS_ASSETS_PATH) . sprintf('/%s.asset.php', $name);

            if (file_exists($asset_path) && is_readable($asset_path)) {
                $info = $assets[$name] = include $asset_path;
            } else {
                $info = ['version' => AWEBOOKING_PMS_VERSION, 'dependencies' => []];
            }
        } else {
            $info = $assets[$name];
        }

        if (!empty($attribute) && isset($info[$attribute])) {
            return $info[$attribute];
        }

        return $info;
    }

    /**
     * Get the asset version from mix-manifest.json.
     *
     * @param string $entryName
     * @param string $key
     * @return array
     */
    public function getAssetEntrypoint(string $entryName, string $key): array
    {
        $entryData = $this->getAssetEntrypoints()[$entryName] ?? [];

        // If we don't find the file type then just send back nothing.
        if (!isset($entryData[$key])) {
            return [];
        }

        return array_values($entryData[$key]);
    }

    /**
     * @param string $entryName
     * @return bool
     */
    public function registerAssets(string $entryName): bool
    {
        $styles = $this->getAssetEntrypoint($entryName, 'css');
        $scripts = $this->getAssetEntrypoint($entryName, 'js');

        if (empty($scripts) && empty($styles)) {
            return false;
        }

        $version = $this->getAssetInfo($entryName, 'version');
        $dependencies = $this->getAssetInfo($entryName, 'dependencies');

        foreach (array_reverse($scripts) as $i => $script) {
            wp_register_script(
                sprintf('awebooking-pms-%s%s', $entryName, $i > 0 ? $i : ''),
                $this->getAssetUrl($script),
                $dependencies,
                $version,
                true
            );
        }

        foreach (array_reverse($styles) as $i => $style) {
            wp_register_style(
                sprintf('awebooking-pms-%s%s', $entryName, $i > 0 ? $i : ''),
                $this->getAssetUrl($style),
                [],
                $version
            );
        }

        return true;
    }

    /**
     * @return array
     */
    protected function getAssetEntrypoints(): array
    {
        static $entrypoints;

        if ($entrypoints === null) {
            $data = json_decode(file_get_contents(AWEBOOKING_PMS_ASSETS_PATH . '/entrypoints.json'), true);

            $entrypoints = $data['entrypoints'] ?? [];
        }

        return $entrypoints;
    }
}
