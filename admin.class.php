<?php

namespace Modea\Agenda;

class Admin extends User
{

    private $id_admin;
    private $login;
    private $password;

    public function getId_admin() {
        return $this->id_admin;
    }

    public function setId_admin($id_admin) {
        $this->id_admin = $id_admin;
    }

    public function getLogin() {
        return $this->login;
    }

    public function setLogin($login) {
        $this->login = $login;
    }

    public function getpassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }
}