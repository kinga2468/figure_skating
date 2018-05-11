<?php
/**
 * Video repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;
use Utils\Paginator;

/**
 * Class SkaterRepository.
 */
class VideoRepository
{
    /**
     * Number of items per page.
     *
     * const int NUM_ITEMS
     */
    const NUM_ITEMS = 9;
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;

    /**
     * VideoRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Fetch all records.
     *
     * @return array Result
     */
    public function findAll()
    {
        $queryBuilder = $this->queryAll();

        return $queryBuilder->execute()->fetchAll();
    }
    /**
     * Get records paginated.
     *
     * @param int $page Current page number
     *
     * @return array Result
     */
    public function findAllPaginated($page = 1)
    {
        $countQueryBuilder = $this->queryAll()
            ->select('COUNT(DISTINCT v.id) AS total_results')
            ->setMaxResults(1);

        $paginator = new Paginator($this->queryAll()->orderBy('date_add', 'desc'), $countQueryBuilder);
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage(self::NUM_ITEMS);

        return $paginator->getCurrentPageResults();
    }

    /**
     * Find one record.
     *
     * @param string $id Element id
     *
     * @return array|mixed Result
     */
    public function findOneById($id)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('v.id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetch();

        return !$result ? [] : $result;
    }

    /**
     * Query all records.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder Result
     */
    protected function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select('*')
            ->from('video', 'v');
    }
    /**
     * Find championship
     */
    public function findChampionship()
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('v.championship')
            ->from('video', 'v')
            ->groupBy('v.championship');
        $result = $queryBuilder->execute()->fetchAll();
        return $result;
    }
    /**
 * Find year
 */
    public function findYear()
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('v.year_championship')
            ->from('video', 'v')
            ->groupBy('v.year_championship');
        $result = $queryBuilder->execute()->fetchAll();
        return $result;
    }
    /**
     * Find skater
     */
    public function findSkater()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('s.name')
            ->from('skaters', 's')
            ->innerJoin('s', 'video', 'v', 'v.skaters_id = s.id')
            ->groupBy('s.name');
        $result = $queryBuilder->execute()->fetchAll();
        return $result;
    }
    /**
     * Find type
     */
    public function findType()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('v.type')
            ->from('video', 'v')
            ->groupBy('v.type');
        $result = $queryBuilder->execute()->fetchAll();
        return $result;
    }

    /**
     * znajdz łyżwiarza z tego video
     * używane przy video view
     *
     * @param $videoId
     * @return array
     */
    public function getVideoSkater($videoId)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('s.name')
            ->from('skaters', 's')
            ->innerJoin('s', 'video', 'v', 'v.skaters_id = s.id')
            ->where('v.id = :id')
            ->setParameter(':id', $videoId, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetchAll();
        return $result;
    }

    public function findNewestVideo()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('*')
            ->from('video', 'v')
            ->orderBy('v.date_add', 'DESC');
        $result = $queryBuilder->execute()->fetchAll();
        return $result;
    }

    public function findPopularVideo()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('*')
            ->from('video', 'v')
            ->orderBy('v.average_rating', 'DESC');
        $result = $queryBuilder->execute()->fetchAll();
        return $result;
    }

    public function howManyVideo()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('COUNT(*) as videoAmount')
            ->from('video', 'v');
        $result = $queryBuilder->execute()->fetchAll();

        foreach ($result as $first) {
            foreach ($first as $second){
                return (int) $second;
            }
        }
//        return $result;
    }

    public function getMatching($match)
    {
        $queryBuilder = $this->db->createQueryBuilder()
            ->select('*')
            ->from('video');

        if ($match['championship']) {
            $queryBuilder->andWhere('championship = :championship')
                ->setParameter(':championship', $match['championship']);
        }
        if ($match['type']) {
            $queryBuilder->andWhere('type = :type')
                ->setParameter(':type', $match['type']);
        }
        if ($match['year_championship']) {
            $queryBuilder->andWhere('year_championship = :year_championship')
                ->setParameter(':year_championship', $match['year_championship']);
        }
        
        $result = $queryBuilder->execute()->fetchAll();
        $result = isset($result) ? $result : [];

        return $result;
    }

    /**
     * znajduje id użytkownika po jego loginie
     * używane przy save, saveOperation i saveForLimit
     *
     * @param $userLogin
     * @return mixed
     */
    protected function findUserIdByLogin($userLogin)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('u.id')
            ->from('users', 'u')
            ->where('u.login = :login')
            ->setParameter(':login', $userLogin, \PDO::PARAM_INT);
        $user_id = current($queryBuilder->execute()->fetch());
        return $user_id;
    }

    /**
     * Save record.
     */
    public function save($video, $userLogin)
    {
        $user_id = $this -> findUserIdByLogin($userLogin);
        $video['users_id'] = $user_id;
        $video['average_rating'] = 0.00;
        $currentDateTime = new \DateTime();
        $video['date_add'] = $currentDateTime->format('Y-m-d H:i:s');

        if (isset($video['id']) && ctype_digit((string) $video['id'])) {
            // update record
            $id = $video['id'];
            unset($video['id']);

            return $this->db->update('video', $video, ['id' => $id]);
        } else {
            // add new record
            return $this->db->insert('video', $video);
        }
    }


    /**
     * SaveForEdit record.
     */
    public function saveForEdit($video, $userLogin)
    {
        $user_id = $this -> findUserIdByLogin($userLogin);
        $video['users_id'] = $user_id;
        $currentDateTime = new \DateTime();
        $video['date_add'] = $currentDateTime->format('Y-m-d H:i:s');

        if (isset($video['id']) && ctype_digit((string) $video['id'])) {
            // update record
            $id = $video['id'];
            unset($video['id']);

            return $this->db->update('video', $video, ['id' => $id]);
        } else {
            // add new record
            return $this->db->insert('video', $video);
        }
    }

    /**
     * Remove record.
     */
    public function delete($video)
    {
        return $this->db->delete('video', ['id' => $video['id']]);
    }

}