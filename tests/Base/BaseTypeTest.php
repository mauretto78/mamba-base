<?php

namespace Mamba\Base\Tests;

use Mamba\Base\BaseType;
use Mamba\Tests\MambaTest;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints as Assert;

class BaseTypeTest extends MambaTest
{
    /**
     * @var BaseType
     */
    protected $type;

    public function setUp()
    {
        parent::setUp();
        $this->app->register(new FormServiceProvider());
        $this->type = new SampleType($this->app);
        $this->type->setName('Sample Form');
    }

    public function testHasCorrectName()
    {
        $this->assertEquals('Sample Form', $this->type->getName());
    }

    public function testHasAnApplicationInstance()
    {
        $this->assertInstanceOf('Mamba\Base\BaseApplication', $this->type->getApp());
    }

    public function testImplementsBaseCommandInterface()
    {
        $this->assertInstanceOf('Mamba\Base\BaseType', $this->type);
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
    
    public function testSubmittingForm()
    {
        $formData = [
            'sample' => 'test',
        ];

        $form = $this->type->getForm()->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isSubmitted());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }

        foreach ($form->getData() as $key => $value) {
            $this->assertEquals($key, 'sample');
            $this->assertEquals($value, 'test');
        }
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
