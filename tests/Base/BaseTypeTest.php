<?php

namespace Mamba\Base\Tests;

use Mamba\Base\BaseType;
use Mamba\Tests\MambaTest;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormView;
use Silex\Provider\FormServiceProvider;
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

    public function testAddingErrorToForm()
    {
        $this->type->addError(new FormError('error'));
        $this->type->addError(new FormError('error2'));
        $this->type->addError(new FormError('error3'));

        $formData = [
            'sample' => '',
        ];

        $form = $this->type->getForm()->submit($formData);

        var_dump( $this->type->getForm()->getErrors());
    }

    public function testSubmittingForm()
    {
        $formData = [
            'sample' => 'test',
        ];

        $form = $this->type->getForm()->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
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
                'constraints' => [
                    new Assert\NotBlank(),
                ]
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
