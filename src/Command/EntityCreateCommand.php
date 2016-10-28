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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
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

        $question3 = new Question('<question>Please enter fields:</question> ', null);
        $fields = $helper->ask($input, $output, $question3);

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
    }

    /**
     * @param $entity
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
        foreach ($this->_getEntityFieldsArray($fields) as $key => $value) {
            $class
                ->setProperty(PhpProperty::create($key)
                    ->setVisibility('private')
                    ->setDescription('@ORM\Column("'.$value.'")')
                )
            ;
            $class
                ->setMethod(PhpMethod::create('set'.ucfirst(S::create($key)->camelize()->toAscii()))
                    ->setDescription('set'.ucfirst(S::create($key)->camelize()->toAscii()))
                    ->addParameter(PhpParameter::create($key))
                    ->setBody('$this->'.$key.' = $'.$key.';')
                )
            ;
            $class
                ->setMethod(PhpMethod::create('get'.ucfirst(S::create($key)->camelize()->toAscii()))
                    ->setDescription('get'.ucfirst(S::create($key)->camelize()->toAscii()))
                    ->setBody('return $this->'.$key.';')
                )
            ;
        }
        $generator = new CodeGenerator();

        $code =  '<?php';
        $code .= "\n\n";
        $code .= $generator->generate($class);

        return $code;
    }

    /**
     * @param $entity
     * @param null $table
     * @return string
     */
    private function _getEntityHeadBlockCode($entity, $table = null)
    {
        $headBlock = 'Mamba\Entity\\'.$entity;
        $headBlock .= "\n";
        if($table){
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
     * @param $fields
     * @return array
     */
    private function _getEntityFieldsArray($fields)
    {
        $fieldsArray = [];
        $fields = explode('|', $fields);

        if(!is_array($fields)) {
            return array();
        }

        foreach ($fields as $field){
            $field = explode(':', $field);
            if(!is_array($field)) {
                return array();
            } else {
                $fieldsArray[$field[0]] = S::create($field[1])->camelize()->toAscii();
            }
        }

        return $fieldsArray;
    }

    /**
     * @param $entity
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

        $code =  '<?php';
        $code .= "\n\n";
        $code .= $generator->generate($class);

        return $code;
    }

    /**
     * @param $entity
     * @param null $table
     * @return int
     */
    private function _createEntity($entity, $table = null, $fields = null)
    {
        $entity = $this->_getEntityName($entity);
        $class = '\Mamba\Entity\\'.$entity;
        $file = $this->app->getRootDir().'/src/Entity/'.$entity.'.php';
        $repo = $this->app->getRootDir().'/src/Repository/'.$entity.'Repository.php';

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
