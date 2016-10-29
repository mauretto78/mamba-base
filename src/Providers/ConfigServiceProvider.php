<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Config\FileLocator;
use Yosymfony\ConfigLoader\Config;
use Yosymfony\ConfigLoader\Loaders\TomlLoader;
use Yosymfony\ConfigLoader\Loaders\YamlLoader;
use Yosymfony\ConfigLoader\Loaders\JsonLoader;

/**
 * Class ConfigServiceProvider.
 */
class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * Register this provider.
     *
     * @param Container $app
     *
     * @throws \Exception if config cache file is generated but is malformed or not readable
     */
    public function register(Container $app)
    {
        $app['config'] = function ($app) {
            if (isset($app['config.CacheFilePath'])) {
                $cacheFile = $app['config.CacheFilePath'];
            }
            if (isset($app['config.baseDir'])) {
                $baseDir = $app['config.baseDir'];
            }
            if (isset($app['config.configFiles'])) {
                $configFiles = $app['config.configFiles'];
            }

            if ($app['debug'] || !file_exists($cacheFile)) {
                $configArray = [];
                foreach ($configFiles as $filename) {
                    $pathFile = $baseDir.'/'.$filename;
                    if (!file_exists($pathFile)) {
                        throw new \RuntimeException(sprintf('The file "%s" is not found', $pathFile));
                    } elseif (!is_readable($pathFile)) {
                        throw new \RuntimeException(sprintf('The file "%s" is not readable', $pathFile));
                    }

                    $locator = new FileLocator([
                        $baseDir,
                    ]);

                    $config = new Config([
                            new TomlLoader($locator),
                            new YamlLoader($locator),
                            new JsonLoader($locator),
                    ]);

                    $configArray[] = $config->load($pathFile)->getArray();
                }

                $config = call_user_func_array('array_replace_recursive', $configArray);

                file_put_contents($cacheFile, serialize($config));
            } else {
                $config = unserialize(file_get_contents($cacheFile));
                if ($config === false) {
                    throw new \Exception(sprintf('The config cache file "%s" is malformed or not readable', $cacheFile));
                }
            }

            return $config;
        };
    }
}
