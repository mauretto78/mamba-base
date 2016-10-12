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
        $entity = $helper->ask($input, $output, $question);

        $question2 = new Question('<question>Please enter the name of the SQL table:</question> ', null);
        $table = $helper->ask($input, $output, $question2);

        $createEntity = $this->_createEntity($entity, $table);

        switch ($createEntity){
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
    
    private function _createEntity($entity, $table = null)
    {
        $class = '\Mamba\Entity\\'.$entity;
        $file = $this->app->getRootDir().'/src/Entity/'.$entity.'.php';
        $repo = $this->app->getRootDir().'/src/Repository/'.$entity.'Repository.php';

        // Duplicate file
        if(file_exists($file)){
            return 3;
        }

        // Duplicate Class
        if(class_exists($class)){
            return 2;
        }

        // Create Entity and Repository
        if($newEntity = fopen($file, 'w') and $newRepo = fopen($repo, 'w')){
            $txt = '<?php

namespace Mamba\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mamba\Entity\\'.$entity.'
 *
 * @ORM\Table(name="'.$table.'")
 * @ORM\Entity(repositoryClass="Mamba\Repository\\'.$entity.'Repository")
 */
class '.$entity.'
{

}';
            fwrite($newEntity, $txt);
            fclose($newEntity);

            $txt = '<?php

namespace Mamba\Repository;

use Doctrine\ORM\EntityRepository;

class '.$entity.'Repository extends EntityRepository
{
    public function yourCustomMethod()
    {
        return \'silence is golden.\';
    }
}';
            fwrite($newRepo, $txt);
            fclose($newRepo);

            return 1;
        }

        return 0;
    }
}
