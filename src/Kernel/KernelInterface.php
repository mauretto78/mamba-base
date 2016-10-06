<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Base\Kernel;

/**
 * Interface KernelInterface.
 *
 * @author Mauro Cassani
 */
interface KernelInterface
{
    /**
     * @param string $env
     *
     * @return mixed
     */
    public function setEnv($env);

    /**
     * @param string $root
     *
     * @return mixed
     */
    public function setRootDir($rootDir);

    /**
     * @param string $configDir
     *
     * @return mixed
     */
    public function setConfigDir($configDir);

    /**
     * @param string $cacheDir
     *
     * @return mixed
     */
    public function setCacheDir($cacheDir);

    /**
     * @param string $logsDir
     *
     * @return mixed
     */
    public function setLogsDir($logsDir);

    /**
     * @param string $viewDir
     *
     * @return mixed
     */
    public function setViewDir($viewDir);

    /**
     * @param string $serverName
     *
     * @return mixed
     */
    public function setServerName($serverName);

    /**
     * @param $provider
     *
     * @return mixed
     */
    public function addProvider($provider);

    /**
     * @param $command
     *
     * @return mixed
     */
    public function addCommand($command);
}
