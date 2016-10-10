<?php

namespace Mamba\Base\Tests;

use Mamba\Base\App\BaseApplication as Application;
use Mamba\Base\Type\BaseType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Silex\Provider\FormServiceProvider;

class BaseTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var BaseType
     */
    protected $type;

    public function setUp()
    {
        $this->app = new Application('dev');
        $this->app->register(new FormServiceProvider());
        $this->type = new SampleType($this->app);
    }

    public function testHasAnApplicationInstance()
    {
        $this->assertInstanceOf('Mamba\Base\App\BaseApplication', $this->type->getApp());
    }

    public function testImplementsBaseCommandInterface()
    {
        $this->assertInstanceOf('Mamba\Base\Type\BaseType', $this->type);
    }

    public function testHasAFormFactoryInstance()
    {
        $this->assertInstanceOf('Symfony\Component\Form\FormFactory', $this->type->getFactory());
    }

    public function testFormIsAnInstanceOfSymfonyForm()
    {
        $this->assertInstanceOf('Symfony\Component\Form\Form', $this->type->getForm());
    }

    public function testFormViewIsAnInstanceOfSymfonyFormView()
    {
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $this->type->createView());
    }
}

class SampleType extends BaseType
{
    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->factory->createBuilder(FormType::class)
            ->add('sample', TextType::class, [
                'label' => 'sample',
            ])
            ->getForm();
    }

    /**
     * @return FormView
     */
    public function createView()
    {
        return $this->getForm()->createView();
    }
}
