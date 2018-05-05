<?php
/**
 * Skater repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class SkaterRepository.
 */
class SkaterRepository
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
        $queryBuilder->where('s.id = :id')
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
            ->from('skaters', 's');
    }
    /**
    * findSkaterVideo($id)
    */
    public function findSkaterVideo($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('*')
            ->from('video', 'v')
            ->where('v.skaters_id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetchAll();
        return $result;
    }
    /**
     * findSkaterVideoPaginated($id)
     */
    public function findSkaterVideoPaginated($id, $page=1)
    {
        $countQueryBuilder = $this->findSkaterVideo($id)
            ->select('COUNT(DISTINCT v.id) AS total_results')
            ->setMaxResults(1);

        $paginator = new Paginator($this->findSkaterVideo($id), $countQueryBuilder);
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage(self::NUM_ITEMS);

        return $paginator->getCurrentPageResults();
    }

    /**
     * Save record.
     *
     * @param array $tag Tag
     *
     * @return boolean Result
     */
    public function save($skater)
    {
        $serializer = new Serializer(array(new DateTimeNormalizer('Y-m-d')));

        $skater['date_of_birth'] = $serializer->normalize($skater['date_of_birth']);

        if (isset($skater['id']) && ctype_digit((string) $skater['id'])) {
            // update record
            $id = $skater['id'];
            unset($skater['id']);

            return $this->db->update('skaters', $skater, ['id' => $id]);
        } else {
            // add new record
            return $this->db->insert('skaters', $skater);
        }
    }

}