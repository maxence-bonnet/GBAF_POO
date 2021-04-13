<?php

namespace Models;

class Connecion extends Model
{
    public function testConnectionRequest($username,$password) // traite une demande de connexion
    {
        $username = htmlspecialchars($username);
        $password = htmlspecialchars($password);
        $result = $this->db->prepare('SELECT username, password, prenom, nom, photo FROM account WHERE username = :username');
        $result->execute(array('username' => $username));
        $content = $result->fetch();
        if($content)
        {
            $testpass = password_verify($password,$content['password']);
            if($testpass)
            {
                $connection = true;
                $_SESSION['username'] = $username;
                $_SESSION['prenom'] = htmlspecialchars($content['prenom']);
                $_SESSION['nom'] = htmlspecialchars($content['nom']);
                $_SESSION['photo'] = htmlspecialchars($content['photo']);
            }
            else
            {
                $connection = false;
            }
        }
        else
        {
            $connection = false;
        }
        return $connection;
    }
}
