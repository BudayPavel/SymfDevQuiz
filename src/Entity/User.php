<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
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

    private $login;

    /**
     * @ORM\Column(type="string")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string")
     */
    private $role;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * One Product has Many Features.
     * @ORM\OneToMany(targetEntity="Result", mappedBy="user")
     */
    private $results;

    public function getId()
    {
        return $this->id;
    }

    public function setlogin(string $login)
    {
        $this->login = $login;
    }

    public function getlogin()
    {
        return $this->login;
    }

    public function setfname(string $firstname)
    {
        $this->firstname = $firstname;
    }
    public function getfname()
    {
        return $this->firstname;
    }

    public function setlname(string $lastname)
    {
        $this->lastname = $lastname;
    }

    public function getlname()
    {
        return $this->lastname;
    }

    public function setpass(string $pass)
    {
        $this->password = $pass;
    }

    public function getpass()
    {
        return $this->password;
    }

    public function setrole(string $role)
    {
        $this->role = $role;
    }

    public function getrole()
    {
        return $this->role;
    }

    public function setactive(boolean $active)
    {
        $this->active = $active;
    }

    public function getactive()
    {
        return $this->active;
    }
}
