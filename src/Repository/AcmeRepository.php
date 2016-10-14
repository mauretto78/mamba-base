<?php

namespace Mamba\Repository;

use Doctrine\ORM\EntityRepository;

class AcmeRepository extends EntityRepository
{
    public function yourCustomMethod()
    {
        return 'silence is golden.';
    }
}