<?php
/**
 * Video repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;

/**
 * Class SkaterRepository.
 */
class VideoRepository
{
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

    public function getMatching($match, $table)
    {
        $queryBuilder = $this->db->createQueryBuilder()
            ->select('*')
            ->where('championship = :championship', 'type = :type',
                'skater = :skater', 'year_championship = :year_championship')
            ->setParameter(':championship', $match['championship'])
            ->setParameter(':type', $match['type'])
            ->setParameter(':skater', $match['skater'])
            ->setParameter(':year_championship', $match['year_championship'])
//            ->orderBy('created_at', 'ASC')
            ->from($table);
        $result = $queryBuilder->execute()->fetchAll();
        $result = isset($result) ? $result : [];
        var_dump($result);
        return $result;
    }

//    public function videoRecord()
//    {
//        $queryBuilder = $this->db->createQueryBuilder()
//            ->select('count(*)')
//            ->from('video', 'v');
//        $query1 = $queryBuilder->getQuery()->getResult();
//
//        $result = count($query1);
//
//        return $result;
//    }

}