<?php

namespace Mamba\Services;

use Mamba\Base\BaseApplication as Application;
use Doctrine\ORM\EntityManager;
use Stringy\Stringy as S;
use Symfony\Component\Yaml\Yaml;

class ApiCreatorService
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var object
     */
    private $entity;

    /**
     * @var string
     */
    private $version;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->em = $this->app->key('orm.em');
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return bool
     */
    public function create()
    {
        if ($this->_createRoutes() and $this->_createController()) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function _createRoutes()
    {
        $routes = [];
        $underscoredEntity = S::create($this->getEntity())->underscored()->toLowerCase()->toAscii();
        $baseUrl = 'api/'.$this->getVersion().'/'.$underscoredEntity;
        $controller = 'Mamba\Controller\\'.$this->getEntity().'Controller';
        $baseName = 'api.'.$underscoredEntity;

        // build the routing array
        $routes['routings'] = [
            $baseName.'.list' => [
                'method' => 'get',
                'url' => $baseUrl,
                'action' => $controller.'@list',
            ],
            $baseName.'.show' => [
                'method' => 'get',
                'url' => $baseUrl.'/{id}',
                'action' => $controller.'@show',
            ],
            $baseName.'.create' => [
                'method' => 'post',
                'url' => $baseUrl,
                'action' => $controller.'@create',
            ],
            $baseName.'.update' => [
                'method' => 'put',
                'url' => $baseUrl.'/{id}',
                'action' => $controller.'@update',
            ],
            $baseName.'.delete' => [
                'method' => 'delete',
                'url' => $baseUrl.'/{id}',
                'action' => $controller.'@delete',
            ],
        ];

        $yaml = Yaml::dump($routes);
        $file = $this->app->getRootDir().'/config/routing/api/'.$this->getEntity().'.yml';

        if ($newYamlFile = fopen($file, 'w')) {
            fwrite($newYamlFile, $yaml);
            fclose($newYamlFile);

            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    private function _createController()
    {
        return true;
    }
}
