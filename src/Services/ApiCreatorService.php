<?php

namespace Mamba\Services;

use gossi\codegen\generator\CodeGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;
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
     * @var object
     */
    private $controller;

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
        $this->entity = S::create($entity)->upperCamelize();
    }

    /**
     * @return object
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param object $controller
     */
    public function setController($controller)
    {
        $this->controller = S::create($controller)->upperCamelize();
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
        $yamlDir = $this->app->getConfigDir().'/routing/api';
        $yamlFile = $yamlDir.'/'.strtolower($this->getEntity()).'.yml';

        if(!@mkdir($yamlDir, 0755) && !is_dir($yamlDir)){
            throw new \RuntimeException('Directory '.$yamlDir.' could not be created');
        }

        //$routingFile = $this->app->getConfigDir().'/routing.yml';

        if ($newYamlFile = fopen($yamlFile, 'w')) {
            fwrite($newYamlFile, $yaml);
            fclose($newYamlFile);

            return true;
        }

        return false;
    }
    
    /**
     * @return int
     */
    private function _createController()
    {
        $controller = $this->getController();
        $file = $this->app->getControllerDir() . '/' . $controller . '.php';

        // Duplicate file
        if (file_exists($file)) {
            return 2;
        }

        // Create Controller
        if ($newController = fopen($file, 'w')) {
            $class = new PhpClass();
            $class
                ->setName($controller.' extends BaseController')
                ->setNamespace('Mamba\\Controller')
                ->setDescription($controller.' Class')
                ->setMethod(PhpMethod::create('listAction')
                    ->addParameter(PhpParameter::create('request'))
                    ->setDescription('listAction')
                    ->setType('JsonResponse')
                    ->setBody('return new JsonResponse(\'dummy response\');')
                )
                ->setMethod(PhpMethod::create('showAction')
                    ->addParameter(PhpParameter::create('request'))
                    ->setDescription('showAction')
                    ->setType('JsonResponse')
                    ->setBody('return new JsonResponse(\'dummy response\');')
                )
                ->setMethod(PhpMethod::create('createAction')
                    ->addParameter(PhpParameter::create('request'))
                    ->setDescription('createAction')
                    ->setType('JsonResponse')
                    ->setBody('return new JsonResponse(\'dummy response\');')
                )
                ->setMethod(PhpMethod::create('updateAction')
                    ->addParameter(PhpParameter::create('request'))
                    ->setDescription('updateAction')
                    ->setType('JsonResponse')
                    ->setBody('return new JsonResponse(\'dummy response\');')
                )
                ->setMethod(PhpMethod::create('deleteAction')
                    ->addParameter(PhpParameter::create('request'))
                    ->setDescription('deleteAction')
                    ->setType('JsonResponse')
                    ->setBody('return new JsonResponse(\'dummy response\');')
                )
                ->addUseStatement('Mamba\\Base\\BaseController')
                ->addUseStatement('Symfony\\Component\\HttpFoundation\\JsonResponse')
                ->addUseStatement('Symfony\\Component\\HttpFoundation\\Request')
            ;
            $generator = new CodeGenerator();

            $code = '<?php';
            $code .= "\n\n";
            $code .= $generator->generate($class);

            fwrite($newController, $code);
            fclose($newController);

            return 1;
        }

        return 0;
    }
}
