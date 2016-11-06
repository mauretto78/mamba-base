<?php

namespace Mamba\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * Mamba\Entity\Api
 * @Table(name=api)
 * @Entity(repositoryClass=Mamba\Repository\ApiRepository)
 */
class Api
{
    /**
     * @var id
     * @Column(name=id, type=integer, nullable=false)
     * @Id
     * @GeneratedValue(strategy=IDENTITY)
     */
    private $id;

    /**
     * @var $first_name
     * @Column(name=first_name, type=string, nullable=true)
     */
    private $first_name;

    /**
     * @var $is_integer
     * @Column(name=is_integer, type=integer, nullable=false)
     */
    private $is_integer;

    /**
     * @var $is_boolean
     * @Column(name=is_boolean, type=boolean, nullable=false)
     */
    private $is_boolean;

    /**
     * @var $text
     * @Column(name=text, type=text, nullable=false)
     */
    private $text;

    /**
     * setFirstName
     */
    public function setFirstName($firstName)
    {
		$this->first_name = $firstName;
    }

    /**
     * getFirstName
     */
    public function getFirstName()
    {
		return $this->first_name;
    }

    /**
     * setIsInteger
     */
    public function setIsInteger($isInteger)
    {
		$this->is_integer = $isInteger;
    }

    /**
     * getIsInteger
     */
    public function getIsInteger()
    {
		return $this->is_integer;
    }

    /**
     * setIsBoolean
     */
    public function setIsBoolean($isBoolean)
    {
		$this->is_boolean = $isBoolean;
    }

    /**
     * getIsBoolean
     */
    public function getIsBoolean()
    {
		return $this->is_boolean;
    }

    /**
     * setText
     */
    public function setText($text)
    {
		$this->text = $text;
    }

    /**
     * getText
     */
    public function getText()
    {
		return $this->text;
    }
}
