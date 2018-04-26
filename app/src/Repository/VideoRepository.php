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
//           ->setParameter(':v.championship', $championship, \PDO::PARAM_INT);
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
//            ->setParameter(':v.year_championship', $year, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetchAll();
        return $result;
    }
    /**
     * Find skater
     */
    public function findSkater()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('s.name', 's.surname')
            ->from('skaters', 's')
            ->innerJoin('s', 'video', 'v', 'v.skaters_id = s.id')
            ->groupBy('s.surname');
//            ->setParameter(':s.id', $skater, \PDO::PARAM_INT);
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
//            ->setParameter(':v.type', $type, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetchAll();
        return $result;
    }
//
//    public function getMatching($match, $table)
//    {
//        $championship = $this->findChampionship($match['championship']);
//        $year = $this->findYear($match['year_championship']);
//        $skater = $this->findSkater($match['cities_id']);
//        $type = $this->findType($match['cities_id']);
//
//        $match['cities_id'] = $cityId;
//        $queryBuilder = $this->db->createQueryBuilder()
//            ->select('*')
//            ->where('offer_types_id = :offer_types_id',
//                'property_types_id = :property_types_id', 'cities_id = :cities_id')
//            ->setParameter(':offer_types_id', $match['offer_types_id'])
//            ->setParameter(':property_types_id', $match['property_types_id'])
//            ->setParameter(':cities_id', $match['cities_id'])
//            ->orderBy('created_at', 'ASC')
//            ->from($table);
//        $result = $queryBuilder->execute()->fetchAll();
//        $result = isset($result) ? $result : [];
//        return $result;
//    }

    public function getVideoSkater($videoId)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('s.name', 's.surname')
            ->from('skaters', 's')
            ->innerJoin('s', 'video', 'v', 'v.skaters_id = s.id')
            ->where('v.id = :id')
//            ->groupBy('s.surname')
            ->setParameter(':id', $videoId, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetchAll();
        return $result;
    }
}