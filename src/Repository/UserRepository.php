<?php

namespace App\Repository;

use App\Entity\User;
use PDO;

final class UserRepository extends AbstractRepository
{
    protected const TABLE = 'users';
    protected const ENTITY = User::class;

    public function save(User $user): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (`name`, firstName, username, `password`, email, telephone, 'role', createdAt, birthDate ) VALUES (:name, :firstName, :username, :password, :email, :telephone, :role, :createdAt, :birthDate )");

        return $stmt->execute([
            'name' => $user->getName(),
            'firstName' => $user->getFirstName(),
            'username' => $user->getUsername(),
            'password' => password_hash($user->getPassword(), PASSWORD_BCRYPT),
            'email' => $user->getEmail(),
            'telephone' => $user->getPhone(),
            'role' => $user->getRole(),
            'createdAt' => $user->getCreatedAt()->format('Y-m-d'),
            'birthDate' => $user->getBirthDate()->format('Y-m-d')
        ]);
    }

    public function find(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE id=:id");

        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();

        return ($result !== false) ? $this->hydrate($result) : null;
    }
}
