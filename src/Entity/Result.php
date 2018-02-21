<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResultRepository")
 */

class Result
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Many Features have One Product.
     * @ORM\ManyToOne(targetEntity="User", inversedBy="results")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user_id;

    /**
     * Many Features have One Product.
     * @ORM\ManyToOne(targetEntity="Quiz", inversedBy="results")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     */
    private $quiz_id;

    /**
     * Many Features have One Product.
     * @ORM\ManyToOne(targetEntity="Question", inversedBy="results")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    private $question_id;

    /**
     * Many Features have One Product.
     * @ORM\ManyToOne(targetEntity="Answer", inversedBy="results")
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id")
     */
    private $answer_id;

    /**
     * @ORM\Column(type="date")
     */
    private $time;
}
