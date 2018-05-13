<?php
/**
 * SignUp repository
 */

namespace Repository;

use Doctrine\DBAL\Connection;
use Silex\Application;
use Utils\Paginator;

/**
 * Class SignUpRepository.
 *
 * @package Repository
 */
class SignUpRepository
{
    /**
     * Number of items per page.
     *
     * const int NUM_ITEMS
     */
    const NUM_ITEMS = 5;
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
     * Fetch all records.
     */
    public function findAll()
    {
        $queryBuilder = $this->queryAll();

        return $queryBuilder->execute()->fetchAll();
    }

    /**
     * znajduje wszystkich użytkowników, wraz z paginacją
     *
     * @param int $page
     * @return array
     */
    public function findAllPaginated($page = 1)
    {
        $countQueryBuilder = $this->queryAll()
            ->select('COUNT(DISTINCT u.id) AS total_results')
            ->setMaxResults(1);

        $paginator = new Paginator($this->queryAll(), $countQueryBuilder);
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage(self::NUM_ITEMS);

        return $paginator->getCurrentPageResults();
    }

    /**
     * Find one record.
     *
     * @param $id
     * @return array|mixed
     */
    public function findOneById($id)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('u.id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetch();

        return !$result ? [] : $result;
    }

    /**
     * Query all records.
     */
    protected function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select('*')
            ->from('users', 'u');
    }

    /**
     * zapisywanie użytkownika przy rejestracji
     *
     * @param $user
     * @param Application $app
     * @return int
     */
    public function save($user, Application $app)
    {
        $user['role_id'] = 2;
        $user['password'] = $app['security.encoder.bcrypt']->encodePassword($user['password'], '');

        if (isset($user['id']) && ctype_digit((string) $user['id'])) {
            // update record
            $id = $user['id'];
            unset($user['id']);

            return $this->db->update('users', $user, ['id' => $id]);
        } else {
            // add new record
            return $this->db->insert('users', $user); // pierwsze user to nazwa tabeli
        }
    }

    /**
     * edycja hasła użytkownika
     *
     * @param $user
     * @param Application $app
     * @return int
     */
    public function save2($user, Application $app)
    {
        $user['password'] = $app['security.encoder.bcrypt']->encodePassword($user['password'], '');

        if (isset($user['id']) && ctype_digit((string) $user['id'])) {
            // update record
            $id = $user['id'];
            unset($user['id']);

            return $this->db->update('users', $user, ['id' => $id]);
        } else {
            // add new record
            return $this->db->insert('users', $user); // pierwsze user to nazwa tabeli
        }
    }

    /**
     * służy do zmiany roli użytkownika
     *
     * @param $user
     * @param Application $app
     * @return int
     */
    public function save3($user, Application $app)
    {
        if (isset($user['id']) && ctype_digit((string) $user['id'])) {
            // update record
            $id = $user['id'];
            unset($user['id']);

            return $this->db->update('users', $user, ['id' => $id]);
        } else {
            // add new record
            return $this->db->insert('users', $user); // pierwsze user to nazwa tabeli
        }
    }

    /**
     * Remove record.
     *
     * @param $user
     * @return int
     */
    public function delete($user)
    {
        return $this->db->delete('users', ['id' => $user['id']]);
    }

    /**
     * Find for uniqueness. Login
     *
     * @param $login
     * @param null $id
     * @return array
     */
    public function findForUniqueness($login, $id = null)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('u.login = :login')
            ->setParameter(':login', $login, \PDO::PARAM_STR);
        if ($id) {
            $queryBuilder->andWhere('u.id <> :id')
                ->setParameter(':id', $id, \PDO::PARAM_INT);
        }

        return $queryBuilder->execute()->fetchAll();
    }
}
