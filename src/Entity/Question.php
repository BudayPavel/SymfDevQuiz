<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
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
     * @ORM\Column(type="string")
     */
    private $text;

    /**
     * One Question has Many Answers.
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="question")
     */
    private $answers;

    /**
     * One Product has Many Features.
     * @ORM\OneToMany(targetEntity="Result", mappedBy="question")
     */
    private $results;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getQuestions()
    {
        return $this->answers;
    }

    public function getId()
    {
        return $this->id;
    }

    public function settext(string $text)
    {
        $this->text = $text;
    }

    public function gettext()
    {
        return $this->text;
    }



}
