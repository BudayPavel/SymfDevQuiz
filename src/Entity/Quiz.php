<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\QuizRepository")
 */
class Quiz
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */

    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="string")
     */
    private $date;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="Question")
     * @ORM\JoinTable(name="quiz_questions",
     *      joinColumns={@ORM\JoinColumn(name="quiz_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="question_id", referencedColumnName="id")}
     *      )
     */
    private $questions;

    /**
     * One Product has Many Features.
     * @ORM\OneToMany(targetEntity="Result", mappedBy="quiz_id", cascade={"persist", "remove"})
     */
    private $results;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->results = new ArrayCollection();
    }

    public function getResults()
    {
        return $this->results;
    }

    public function getQuestions()
    {
        return $this->questions;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setname(string $name)
    {
        $this->name = $name;
    }

    public function getname()
    {
        return $this->name;
    }

    public function setactive($active)
    {
        $this->active = $active;
    }

    public function getactive()
    {
        return $this->active;
    }

    public function setdate($date)
    {
        $this->date = $date;
    }

    public function getdate()
    {
        return $this->date;
    }
}
