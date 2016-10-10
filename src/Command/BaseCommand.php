<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Base\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mamba\Base\App\BaseApplication as Container;
use Knp\Command\Command;
use Mamba\Base\Contracts\BaseCommandInterface;

/**
 * Class BaseCommand
 * @package Mamba\Base\Command
 */
class BaseCommand extends Command implements BaseCommandInterface
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * BaseCommand constructor.
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        parent::__construct('BaseCommand');

        $this->app = $app;
    }

    /**
     * @return Container
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return mixed
     */
    public function configure()
    {}

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {}
}
