<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnswerRepository")
 */
class Answer
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
     * @ORM\Column(type="boolean")
     */
    private $right;

    /**
     * Many Features have One Product.
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="answer")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question;

    /**
     * One Product has Many Features.
     * @ORM\OneToMany(targetEntity="Result", mappedBy="answer")
     */
    private $results;

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

    public function setright(boolean $right)
    {
        $this->right = $right;
    }

    public function getright()
    {
        return $this->right;
    }
}
