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
                $output->writeln('<error>Entity \Mamba\Entity\\'.$entity.' already exists</error>');
                break;

            case 3:
                $output->writeln('<error>File src/Entity/'.$entity.'.php already exists</error>');
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
     * @return string
     */
    private function _getEntityCodeStart($entity, $table = null)
    {
        $code =  '<?php';
        $code .= "\n\n";
        $code .= 'namespace Mamba\Entity;';
        $code .= "\n\n";
        $code .= 'use Doctrine\ORM\Mapping as ORM;';
        $code .= "\n";
        $code .= 'use Doctrine\ORM\Mapping\Column;';
        $code .= "\n\n";
        $code .='/**';
        $code .= "\n";
        $code .=' * Mamba\Entity\\'.$entity;
        $code .= "\n";
        $code .=' *';

        if($table){
            $code .= "\n";
            $code .= ' * @ORM\Table(name="'.$table.'")';
        }

        $code .= "\n";
        $code .= ' * @ORM\Entity(repositoryClass="Mamba\Repository\\'.$entity.'Repository")';
        $code .= "\n";
        $code .= ' */';
        $code .= "\n\n";
        $code .= 'class '.$entity;
        $code .= "\n";
        $code .= '{';

        return $code;
    }

    private function _getEntityCodeFields($fields)
    {
        $fields = explode('|', $fields);
        $code = '';

        if(!is_array($fields)) {
            return $code;
        }

        foreach ($fields as $field){
            $field = explode(':', $field);
            $code .= "\n\t";
            $code .='/**';
            $code .= "\n\t";
            $code .=' * @Column('.$field[1].')';
            $code .= "\n\t";
            $code .= ' */';
            $code .= "\n\t";
            $code .= 'protected $' . $field[0].';';
            $code .= "\n";
        }

        return $code;
    }

    /**
     * @return string
     */
    private function _getEntityCodeEnd()
    {
        $code = "\n";
        $code .= '}';

        return $code;
    }

    /**
     * @param $entity
     * @return strin
     */
    private function _getRepoCode($entity)
    {
        $code =  '<?php';
        $code .= "\n\n";
        $code .= 'namespace Mamba\Repository;';
        $code .= "\n\n";
        $code .= 'use Doctrine\ORM\EntityRepository;';
        $code .= "\n\n";
        $code .= 'class '.$entity.'Repository extends EntityRepository';
        $code .= "\n";
        $code .= '{';
        $code .= "\n\n";
        $code .= '}';

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
            return 3;
        }

        // Duplicate Class
        if (class_exists($class)) {
            return 2;
        }

        // Create Entity and Repository
        if ($newEntity = fopen($file, 'w') and $newRepo = fopen($repo, 'w')) {
            $txt = $this->_getEntityCodeStart($entity, $table);
            $txt .= $this->_getEntityCodeFields($fields);
            $txt .= $this->_getEntityCodeEnd();
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
