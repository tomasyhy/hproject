<?php

namespace AppBundle\Entity;

/**
 * Solution
 */
class Solution
{
    /**
     * @var int
     */
    private $id;


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
    private $content;

    /**
     * @var integer
     */
    private $rating;


    /**
     * Set content
     *
     * @param string $content
     *
     * @return Solution
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return Solution
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }
    /**
     * @var \AppBundle\Entity\User
     */
    private $user;


    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Solution
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * @var \AppBundle\Entity\Problem
     */
    private $problem;


    /**
     * Set problem
     *
     * @param \AppBundle\Entity\Problem $problem
     *
     * @return Solution
     */
    public function setProblem(\AppBundle\Entity\Problem $problem)
    {
        $this->problem = $problem;

        return $this;
    }

    /**
     * Get problem
     *
     * @return \AppBundle\Entity\Problem
     */
    public function getProblem()
    {
        return $this->problem;
    }
}
