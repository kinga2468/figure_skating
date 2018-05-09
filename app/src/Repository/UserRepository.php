<?php
/**
 * User repository
 */

namespace Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Silex\Application;

/**
 * Class UserRepository.
 */
class UserRepository
{
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * TagRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Loads user by login.
     *
     * @param string $login User login
     * @throws UsernameNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array Result
     */
    public function loadUserByLogin($login)
    {
        try {
            $user = $this->getUserByLogin($login);

            if (!$user || !count($user)) {
                throw new UsernameNotFoundException(
                    sprintf('Username "%s" does not exist.', $login)
                );
            }

            $roles = $this->getUserRoles($user['id']);

            if (!$roles || !count($roles)) {
                throw new UsernameNotFoundException(
                    sprintf('Username "%s" does not exist.', $login)
                );
            }

            return [
                'login' => $user['login'],
                'password' => $user['password'],
                'roles' => $roles,
            ];
        } catch (DBALException $exception) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $login)
            );
        } catch (UsernameNotFoundException $exception) {
            throw $exception;
        }
    }

    /**
     * usuwanie użytkownika
     * usuwanie dzieci odbywa się poprzez baze
     *
     * @param $user
     * @return int
     */
    public function delete($user)
    {
        return $this->db->delete('users'/*tabela user*/, ['id' => $user['id']]);
    }

    /**
     * Gets user data by login.
     *
     * @param string $login User login
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array Result
     */
    public function getUserByLogin($login)
    {
        try {
            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('u.id', 'u.login', 'u.password')
                ->from('users', 'u')
                ->where('u.login = :login')
                ->setParameter(':login', $login, \PDO::PARAM_STR);

            return $queryBuilder->execute()->fetch();
        } catch (DBALException $exception) {
            return [];
        }
    }

    /**
     * Gets user roles by User ID.
     *
     * @param integer $userId User ID
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array Result
     */
    public function getUserRoles($userId)
    {
        $roles = [];

        try {
            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('r.name')
                ->from('users', 'u')
                ->innerJoin('u', 'roles', 'r', 'u.role_id = r.id')
                ->where('u.id = :id')
                ->setParameter(':id', $userId, \PDO::PARAM_INT);
            $result = $queryBuilder->execute()->fetchAll();

            if ($result) {
                $roles = array_column($result, 'name');
            }

            return $roles;
        } catch (DBALException $exception) {
            return $roles;
        }
    }

    /**
     * znajduje id użytkownika po loginie
     *
     * @param $login
     * @return mixed
     */
    public function findUserId($login)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('id')
            ->from('users')
            ->where('login = :login')
            ->setParameter(':login', $login);
        $some = $queryBuilder->execute()->fetch();
        if($some != NULL){
            $userId = current($queryBuilder->execute()->fetch());
            return $userId;
        }
        else{
            return 0;
        }

//        $userId = $queryBuilder->execute()->fetch();
//        var_dump("userid",$userId);

    }

    /**
     * znajduje id zalogowanie obecnie użytkownika
     *  używane przy nie pozwoleniu użytkownikowi wchodzić na nie swoje strony
     *
     * @param Application $app
     * @return array|mixed
     */
    public function getLoggedUserId(Application $app)
    {
        $token = $app['security.token_storage']->getToken();

        if (null !== $token) {
            $user = $token->getUser();
            $userId = $this ->findUserId($user);
        }

        return $userId;
    }


}
