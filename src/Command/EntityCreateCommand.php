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

use gossi\codegen\generator\CodeGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;
use gossi\codegen\model\PhpProperty;
use Mamba\Base\BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Stringy\Stringy as S;

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
     * @param null $fields
     *
     * @return string
     */
    private function _getEntityCode($entity, $table = null, $fields = null)
    {
        $class = new PhpClass();
        $class
            ->setName($entity)
            ->setNamespace('Mamba\\Entity')
            ->setDescription($this->_getEntityHeadBlockCode($entity, $table))
            ->addUseStatement('Doctrine\\ORM\\Mapping', 'ORM')
        ;
        $class
            ->setProperty(PhpProperty::create('id')
                ->setVisibility('private')
                ->setDescription($this->_getEntityIdHeadBlockCode())
            );
        foreach ($fields as $key => $value) {
            $underscoredKey = S::create($key)->underscored()->toAscii();
            $camelizedKey = S::create($key)->camelize()->toAscii();
            $ucamelizedKey = S::create($key)->upperCamelize()->toAscii();

            $class
                ->setProperty(PhpProperty::create((string) $underscoredKey)
                    ->setVisibility('private')
                    ->setDescription('@ORM\Column(name="'.$underscoredKey.'", type="'.$value['type'].'", nullable='.$value['nullable'].')')
                )
            ;
            $class
                ->setMethod(PhpMethod::create('set'.$ucamelizedKey)
                    ->setDescription('set'.$ucamelizedKey)
                    ->addParameter(PhpParameter::create($camelizedKey))
                    ->setBody('$this->'.$underscoredKey.' = $'.$camelizedKey.';')
                )
            ;
            $class
                ->setMethod(PhpMethod::create('get'.$ucamelizedKey)
                    ->setDescription('get'.$ucamelizedKey)
                    ->setBody('return $this->'.$underscoredKey.';')
                )
            ;
        }
        $generator = new CodeGenerator();

        $code = '<?php';
        $code .= "\n\n";
        $code .= $generator->generate($class);

        return $code;
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
            $headBlock .= '@ORM\Table(name="'.$table.'")';
            $headBlock .= "\n";
        }
        $headBlock .= '@ORM\Entity(repositoryClass="Mamba\Repository\\'.$entity.'Repository")';

        return $headBlock;
    }

    public function _getEntityIdHeadBlockCode()
    {
        $idCode = '@ORM\Column(name="id", type="integer", nullable=false)';
        $idCode .= "\n";
        $idCode .= '@ORM\Id';
        $idCode .= "\n";
        $idCode .= '@ORM\GeneratedValue(strategy="IDENTITY")';

        return $idCode;
    }

    /**
     * @param $entity
     *
     * @return string
     */
    private function _getRepoCode($entity)
    {
        $class = new PhpClass();
        $class
            ->setName($entity.'Repository extends EntityRepository')
            ->setNamespace('Mamba\\Repository')
            ->setDescription($entity.'Repository Class')
            ->setMethod(PhpMethod::create('dummyMethod')
                ->setDescription('dummyMethod')
                ->setType('mixed')
                ->setBody('//')
            )
            ->addUseStatement('Doctrine\\ORM\\EntityRepository')
        ;
        $generator = new CodeGenerator();

        $code = '<?php';
        $code .= "\n\n";
        $code .= $generator->generate($class);

        return $code;
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
            $txt = $this->_getEntityCode($entity, $table, $fields);
            fwrite($newEntity, $txt);
            fclose($newEntity);

            $txt = $this->_getRepoCode($entity);
            fwrite($newRepo, $txt);
            fclose($newRepo);

            return 1;
        }

        return 0;
    }
}
