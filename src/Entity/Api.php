<?php

namespace Mamba\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mamba\Entity\Api
 * @ORM\Table(name="api")
 * @ORM\Entity(repositoryClass="Mamba\Repository\ApiRepository")
 */
class Api {

	/**
	 * @ORM\Column(name="first_name", type="string", nullable=true)
	 */
	private $first_name;

	/**
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @ORM\Column(name="is_boolean", type="boolean", nullable=false)
	 */
	private $is_boolean;

	/**
	 * @ORM\Column(name="is_integer", type="integer", nullable=false)
	 */
	private $is_integer;

	/**
	 * @ORM\Column(name="text", type="text", nullable=false)
	 */
	private $text;

	/**
	 * getFirstName
	 */
	public function getFirstName() {
		return $this->first_name;
	}

	/**
	 * getIsBoolean
	 */
	public function getIsBoolean() {
		return $this->is_boolean;
	}

	/**
	 * getIsInteger
	 */
	public function getIsInteger() {
		return $this->is_integer;
	}

	/**
	 * getText
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * setFirstName
	 * 
	 * @param mixed $firstName
	 */
	public function setFirstName($firstName) {
		$this->first_name = $firstName;
	}

	/**
	 * setIsBoolean
	 * 
	 * @param mixed $isBoolean
	 */
	public function setIsBoolean($isBoolean) {
		$this->is_boolean = $isBoolean;
	}

	/**
	 * setIsInteger
	 * 
	 * @param mixed $isInteger
	 */
	public function setIsInteger($isInteger) {
		$this->is_integer = $isInteger;
	}

	/**
	 * setText
	 * 
	 * @param mixed $text
	 */
	public function setText($text) {
		$this->text = $text;
	}
}