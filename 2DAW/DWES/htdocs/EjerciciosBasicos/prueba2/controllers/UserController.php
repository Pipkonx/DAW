<?php
require_once "models/User.php";

class UserController
{
    private $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function list()
    {
        $users = $this->model->getAll();
        require "views/users/list.php";
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create($_POST['nombre'], $_POST['email'], $_POST['password'], $_POST['rol']);
            header("Location: index.php?controller=User&action=list");
        }
        require "views/users/create.php";
    }

    public function edit()
    {
        $user = $this->model->getById($_GET['id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($_GET['id'], $_POST['nombre'], $_POST['email'], $_POST['rol']);
            header("Location: index.php?controller=User&action=list");
        }
        require "views/users/edit.php";
    }

    public function delete()
    {
        $this->model->delete($_GET['id']);
        header("Location: index.php?controller=User&action=list");
    }
}
