<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;


/**
 * User
 */
class User extends BaseUser
{
    public const ENABLED = 1;
    public const DISABLED = 0;

    /**
     * @var int
     */
    protected $id;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;


    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $problems;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $solutions;


    /**
     * Add problem
     *
     * @param \AppBundle\Entity\Problem $problem
     *
     * @return User
     */
    public function addProblem(\AppBundle\Entity\Problem $problem)
    {
        $this->problems[] = $problem;

        return $this;
    }

    /**
     * Remove problem
     *
     * @param \AppBundle\Entity\Problem $problem
     */
    public function removeProblem(\AppBundle\Entity\Problem $problem)
    {
        $this->problems->removeElement($problem);
    }

    /**
     * Get problems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProblems()
    {
        return $this->problems;
    }

    /**
     * Add solution
     *
     * @param \AppBundle\Entity\Solution $solution
     *
     * @return User
     */
    public function addSolution(\AppBundle\Entity\Solution $solution)
    {
        $this->solutions[] = $solution;

        return $this;
    }

    /**
     * Remove solution
     *
     * @param \AppBundle\Entity\Solution $solution
     */
    public function removeSolution(\AppBundle\Entity\Solution $solution)
    {
        $this->solutions->removeElement($solution);
    }

    /**
     * Get solutions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSolutions()
    {
        return $this->solutions;
    }
}
