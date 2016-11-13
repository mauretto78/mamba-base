<?php

namespace Mamba\Services;

use Doctrine\ORM\EntityManager;
use Mamba\Base\BaseApplication as Application;
use Mamba\Lib\Stringy as S;
use Memio\Model\Argument;
use Memio\Memio\Config\Build;
use Memio\Model\File;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Method;
use Memio\Model\Object;
use Memio\Model\Phpdoc\Description;
use Memio\Model\Phpdoc\MethodPhpdoc;
use Memio\Model\Phpdoc\ParameterTag;
use Memio\Model\Phpdoc\ReturnTag;
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
        if ($this->_createRoutes() and $this->_createController() and $this->_createForm()) {
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
            $code = $this->_generateController($file, $controller);
            fwrite($newController, S::create($code)->deepHtmlDecode());
            fclose($newController);

            return 1;
        }

        return 0;
    }

    /**
     * @return int
     */
    private function _createForm()
    {
        $entity = $this->getEntity();
        $file = $this->app->getFormDir() . '/' . $entity . '.php';

        // Duplicate file
        if (file_exists($file)) {
            return 2;
        }

        // Create Form
        if ($newForm = fopen($file, 'w')) {
            $code = $this->_generateForm($file, $entity);
            fwrite($newForm, $code);
            fclose($newForm);

            return 1;
        }

        return 0;
    }

    /**
     * @param $file
     * @param $controller
     * @return string
     */
    private function _generateController($file, $controller)
    {
        $newController = File::make($file)
            ->addFullyQualifiedName(FullyQualifiedName::make('Mamba\Base\BaseController'))
            ->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\HttpFoundation\Request'))
            ->addFullyQualifiedName(FullyQualifiedName::make('Symfony\Component\HttpFoundation\JsonResponse'))
            ->setStructure(
                Object::make('Mamba\Controller\\'.$controller.'Controller')
                    ->extend(Object::make('Mamba\Base\BaseController'))
                    ->addMethod(
                        Method::make('listAction')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->setDescription(Description::make('showAction'))
                                ->addParameterTag(new ParameterTag('Request', 'request'))
                                ->setReturnTag(new ReturnTag('JsonResponse'))
                            )
                            ->addArgument(new Argument('Request', 'request'))
                            ->setBody("\t\t".'return new Response(\'dummy response\');')
                    )
                    ->addMethod(
                        Method::make('showAction')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->setDescription(Description::make('showAction'))
                                ->addParameterTag(new ParameterTag('Request', 'request'))
                                ->setReturnTag(new ReturnTag('JsonResponse'))
                            )
                            ->addArgument(new Argument('Request', 'request'))
                            ->setBody("\t\t".'return new Response(\'dummy response\');')
                    )
                    ->addMethod(
                        Method::make('createAction')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->setDescription(Description::make('createAction'))
                                ->addParameterTag(new ParameterTag('Request', 'request'))
                                ->setReturnTag(new ReturnTag('JsonResponse'))
                            )
                            ->addArgument(new Argument('Request', 'request'))
                            ->setBody("\t\t".'return new Response(\'dummy response\');')
                    )
                    ->addMethod(
                        Method::make('showAction')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->setDescription(Description::make('showAction'))
                                ->addParameterTag(new ParameterTag('Request', 'request'))
                                ->setReturnTag(new ReturnTag('JsonResponse'))
                            )
                            ->addArgument(new Argument('Request', 'request'))
                            ->setBody("\t\t".'return new Response(\'dummy response\');')
                    )
                    ->addMethod(
                        Method::make('updateAction')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->setDescription(Description::make('updateAction'))
                                ->addParameterTag(new ParameterTag('Request', 'request'))
                                ->setReturnTag(new ReturnTag('JsonResponse'))
                            )
                            ->addArgument(new Argument('Request', 'request'))
                            ->setBody("\t\t".'return new Response(\'dummy response\');')
                    )
                    ->addMethod(
                        Method::make('deleteAction')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->setDescription(Description::make('deleteAction'))
                                ->addParameterTag(new ParameterTag('Request', 'request'))
                                ->setReturnTag(new ReturnTag('JsonResponse'))
                            )
                            ->addArgument(new Argument('Request', 'request'))
                            ->setBody("\t\t".'return new Response(\'dummy response\');')
                    )
            )
        ;

        $prettyPrinter = Build::prettyPrinter();

        return $prettyPrinter->generateCode($newController);
    }

    /**
     * @param $file
     * @param $entity
     * @return string
     */
    private function _generateForm($file, $entity)
    {
        $fields = $this->em->getClassMetadata($this->app->getEntityNamespace().$entity)->getFieldNames();

        var_dump($fields);die();
    }
}
