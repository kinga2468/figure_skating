<?php
/**
 * Skater repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;
use Utils\Paginator;

/**
 * Class SkaterRepository.
 */
class SkaterRepository
{
    /**
     * Number of items per page.
     *
     * const int NUM_ITEMS
     */
    const NUM_ITEMS_FOR_MAIN_PAGE = 6;
    /**
     * Number of items per page.
     *
     * const int NUM_ITEMS
     */
    const NUM_ITEMS = 10;
    /**
     * Number of items per page.
     *
     * const int NUM_ITEMS
     */
    const NUM_ITEMS_VIDEO_OF_SKATER = 4;
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
     * Get records paginated.
     *
     * @param int $page Current page number
     *
     * @return array Result
     */
    public function findAllPaginated($page = 1)
    {
        $countQueryBuilder = $this->queryAll()
            ->select('COUNT(DISTINCT s.id) AS total_results')
            ->setMaxResults(1);

        $paginator = new Paginator($this->queryAll(), $countQueryBuilder);
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage(self::NUM_ITEMS);

        return $paginator->getCurrentPageResults();
    }

    /**
     * Get records paginated.
     *
     * @param int $page Current page number
     *
     * @return array Result
     */
    public function findAllPaginatedForMainPage($page = 1)
    {
        $countQueryBuilder = $this->queryAll()
            ->select('COUNT(DISTINCT s.id) AS total_results')
            ->setMaxResults(1);

        $paginator = new Paginator($this->queryAll(), $countQueryBuilder);
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage(self::NUM_ITEMS_FOR_MAIN_PAGE);

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

        return $queryBuilder->select('*')
            ->from('video', 'v')
            ->where('v.skaters_id = :id')
            ->setParameter(':id', $id, \PDO::PARAM_INT);
//        $result = $queryBuilder->execute()->fetchAll();
//         $result;
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
        $paginator->setMaxPerPage(self::NUM_ITEMS_VIDEO_OF_SKATER);

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

    /**
     * Remove record.
     *
     * @param array $tag Tag
     *
     * @return boolean Result
     */
    public function delete($tag)
    {
        return $this->db->delete('skaters', ['id' => $tag['id']]);
    }

}