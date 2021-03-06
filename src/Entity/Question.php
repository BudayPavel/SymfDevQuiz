<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * One Question has Many Answers.
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="question", cascade={"persist", "remove"} )
     */
    private $answers;

    /**
     * One Product has Many Features.
     * @ORM\OneToMany(targetEntity="Result", mappedBy="question_id", cascade={"persist", "remove"})
     */
    private $results;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
        $this->results = new ArrayCollection();
    }

    public function getAnswers()
    {
        return $this->answers;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}
