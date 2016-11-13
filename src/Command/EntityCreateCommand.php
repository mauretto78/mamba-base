<?php

/*
 * This file is part of the Mamba microframework.
 *
 * (c) Mauro Cassani <assistenza@easy-grafica.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamba\Command;

use Mamba\Base\BaseCommand;
use Mamba\Lib\Stringy as S;
use Memio\Model\Argument;
use Memio\Memio\Config\Build;
use Memio\Model\File;
use Memio\Model\FullyQualifiedName;
use Memio\Model\Method;
use Memio\Model\Object;
use Memio\Model\Phpdoc\Description;
use Memio\Model\Phpdoc\MethodPhpdoc;
use Memio\Model\Phpdoc\PropertyPhpdoc;
use Memio\Model\Phpdoc\StructurePhpdoc;
use Memio\Model\Phpdoc\VariableTag;
use Memio\Model\Property;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;

class EntityCreateCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('app:entity:create')
            ->setDescription('Create an Entity.')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question = new Question('<question>Please enter the name of the Entity:</question> ', 'Acme');
        $question->setValidator(function ($value) {
            if (trim($value) == '') {
                throw new \Exception('The Entity name can not be empty');
            }

            return $value;
        });
        $entity = $helper->ask($input, $output, $question);

        $question2 = new Question('<question>Please enter the name of the SQL table:</question> ', null);
        $table = $helper->ask($input, $output, $question2);

        // Infinite loop
        while (1 === 1) {

            // Ask for fields and types
            $field = new Question('<question>Field name:</question> ', null);
            $type = new ChoiceQuestion(
                'Please select field type:',
                [
                    'string',
                    'integer',
                    'smallint',
                    'bigint',
                    'boolean',
                    'decimal',
                    'date',
                    'time',
                    'datetime',
                    'datetimetz',
                    'text',
                    'float',
                    'blob',
                ],
                0
            );
            $nullable = new ChoiceQuestion(
                'Is nullable?',
                [
                    'true',
                    'false',
                ],
                0
            );
            $type->setErrorMessage('Type %s is invalid.');
            $fields[$helper->ask($input, $output, $field)] = [
                'type' => $helper->ask($input, $output, $type),
                'nullable' => $helper->ask($input, $output, $nullable),
            ];

            // confirmation question
            $confirmationQuestion = new ConfirmationQuestion('Another fields?', false);

            if (!$confirm = $helper->ask($input, $output, $confirmationQuestion)) {
                $createEntity = $this->_createEntity($entity, $table, $fields);

                switch ($createEntity) {
                    case 0:
                        $output->writeln('<error>Error creating entity '.$entity.'.</error>');
                        break;

                    case 1:
                        $output->writeln('<info>Entity '.$entity.' was successfully created.</info>');
                        break;

                    case 2:
                        $output->writeln('<error>File src/Entity/'.$entity.'.php already exists.</error>');
                        break;
                }

                return;
            }
        }
    }

    /**
     * @param $entity
     *
     * @return S
     */
    private function _getEntityName($entity)
    {
        return  S::create($entity)->upperCamelize();
    }

    /**
     * @param $entity
     * @param null $table
     *
     * @return int
     */
    private function _createEntity($entity, $table = null, $fields = null)
    {
        $entity = $this->_getEntityName($entity);
        $class = '\Mamba\Entity\\'.$entity;
        $file = $this->app->getEntityDir().'/'.$entity.'.php';
        $repo = $this->app->getRepoDir().'/'.$entity.'Repository.php';

        // Duplicate file
        if (file_exists($file)) {
            return 2;
        }

        // Create Entity and Repository
        if ($newEntity = fopen($file, 'w') and $newRepo = fopen($repo, 'w')) {
            $code = $this->_generateEntity($file, $entity, $table, $fields);
            fwrite($newEntity, S::create($code)->deepHtmlDecode());
            fclose($newEntity);

            $code = $this->_generateRepo($repo, $entity);
            fwrite($newRepo, S::create($code)->deepHtmlDecode());
            fclose($newRepo);

            return 1;
        }

        return 0;
    }

    /**
     * @param $file
     * @param $entity
     * @param null $table
     * @param null $fields
     *
     * @return string
     */
    private function _generateEntity($file, $entity, $table = null, $fields = null)
    {

        $object = Object::make('Mamba\Entity\\'.$entity);
        $object->setPhpdoc(StructurePhpdoc::make()
            ->setDescription(Description::make($this->_getEntityHeadBlockCode($entity, $table)))
        );
        $object->addProperty(Property::make('id')
            ->setPhpdoc(PropertyPhpdoc::make()
                ->setVariableTag(VariableTag::make('id'."\n".$this->_getEntityIdHeadBlockCode())
                )
            )
            ->makePrivate()
        );

        foreach ($fields as $key => $value) {
            $underscoredKey = S::create($key)->underscored()->toAscii();
            $camelizedKey = S::create($key)->camelize()->toAscii();
            $ucamelizedKey = S::create($key)->upperCamelize()->toAscii();

            $object->addProperty(
                Property::make($underscoredKey)
                    ->setPhpdoc(PropertyPhpdoc::make()
                        ->setVariableTag(VariableTag::make('$'.$underscoredKey."\n".'@Column(name="'.$underscoredKey.'", type="'.$value['type'].'", nullable="'.$value['nullable'].'")')
                        )
                    )
            );

            $object->addMethod(
                Method::make('set'.$ucamelizedKey)
                    ->setPhpdoc(MethodPhpdoc::make()
                        ->setDescription(Description::make('set'.$ucamelizedKey))
                    )
                    ->addArgument(new Argument('mixed', $camelizedKey))
                    ->setBody("\t\t".'$this->'.$underscoredKey.' = $'.$camelizedKey.';')
            );

            $object->addMethod(
                Method::make('get'.$ucamelizedKey)
                    ->setPhpdoc(MethodPhpdoc::make()
                        ->setDescription(Description::make('get'.$ucamelizedKey))
                    )
                    ->setBody("\t\t".'return $this->'.$underscoredKey.';')
            );
        }
        
        $newEntity = File::make($file)
            ->addFullyQualifiedName(new FullyQualifiedName('Doctrine\ORM\Mapping\Column'))
            ->addFullyQualifiedName(new FullyQualifiedName('Doctrine\ORM\Mapping\Entity'))
            ->addFullyQualifiedName(new FullyQualifiedName('Doctrine\ORM\Mapping\GeneratedValue'))
            ->addFullyQualifiedName(new FullyQualifiedName('Doctrine\ORM\Mapping\Id'))
            ->addFullyQualifiedName(new FullyQualifiedName('Doctrine\ORM\Mapping\Table'))
            ->setStructure($object)
        ;

        $prettyPrinter = Build::prettyPrinter();

        return $prettyPrinter->generateCode($newEntity);
    }

    /**
     * @param $entity
     * @param null $table
     *
     * @return string
     */
    private function _getEntityHeadBlockCode($entity, $table = null)
    {
        $headBlock = 'Mamba\Entity\\'.$entity;
        $headBlock .= "\n";
        if ($table) {
            $headBlock .= '@Table(name="'.$table.'")';
            $headBlock .= "\n";
        }
        $headBlock .= '@Entity(repositoryClass="Mamba\Repository\\'.$entity.'Repository")';

        return $headBlock;
    }

    /**
     * @return string
     */
    public function _getEntityIdHeadBlockCode()
    {
        $idCode = '@Column(name="id", type="integer", nullable="false")';
        $idCode .= "\n";
        $idCode .= '@Id';
        $idCode .= "\n";
        $idCode .= '@GeneratedValue(strategy="IDENTITY")';

        return $idCode;
    }


    /**
     * @param $file
     * @param $entity
     * @return string
     */
    private function _generateRepo($file, $entity)
    {
        $newRepository = File::make($file)
            ->addFullyQualifiedName(FullyQualifiedName::make('Doctrine\ORM\EntityRepository'))
            ->setStructure(
                Object::make('Mamba\Repository\\'.$entity.'Controller')
                    ->extend(Object::make('Doctrine\ORM\EntityRepository'))
                    ->addMethod(
                        Method::make('dummyMethod')
                            ->setPhpdoc(MethodPhpdoc::make()
                                ->setDescription(Description::make('Your awesome code here'))
                            )
                            ->setBody("\t\t".'// your awesome code here.')
                    )
            )
        ;

        $prettyPrinter = Build::prettyPrinter();

        return $prettyPrinter->generateCode($newRepository);
    }
}
