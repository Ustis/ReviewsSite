<?php


namespace App\dto;


use Symfony\Component\Form\FormTypeInterface;

class PasswordDto
{
    private $password;

    private $repeatPassword;

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getRepeatPassword()
    {
        return $this->repeatPassword;
    }

    /**
     * @param mixed $repeatPassword
     */
    public function setRepeatPassword($repeatPassword): void
    {
        $this->repeatPassword = $repeatPassword;
    }
}