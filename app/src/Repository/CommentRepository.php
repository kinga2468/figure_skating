<?php
/**
 * Comment repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;

/**
 * Class CommentRepository.
 */
class CommentRepository
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
        $queryBuilder->where('c.id = :id')
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
            ->from('comments', 'c');
    }

    public function findVideoComments($videoId)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('*, u.login')
            ->from('comments', 'c')
            ->where('c.video_id = :video_id')
            ->setParameter(':video_id', $videoId, \PDO::PARAM_INT)
            ->orderBy('c.date_adding', 'DESC')
            ->innerJoin('c', 'users', 'u', 'c.users_id = u.id');
        $result = $queryBuilder->execute()->fetchAll();
        return $result;
    }

    /**
     * Delete record.
     *
     * @param array $comment Comment
     *
     * @return boolean Result
     */
//    public function delete($comment)
//    {
//        if (isset($comment['id']) && ctype_digit((string)$comment['id'])) {
//            //delete record
//            $id = $comment['id'];
//            return $this->db->delete('comments', ['id' => $id]);
//        } else {
//            throw new \InvalidArgumentException('Invalid parameter type');
//        }
//    }

    /**
     * Save record.
     *
     * @param array $comment Comment
     *
     * @return boolean Result
     */
    public function save($comment)
    {
        if (isset($comment['id']) && ctype_digit((string)$comment['id'])) {
            // update record
            $id = $comment['id'];
            unset($comment['id']);
            return $this->db->update('comments', $comment, ['id' => $id]);
        } else {
            // add new record
            $comment['date_adding'] = date('Y-m-d H:i:s');
            return $this->db->insert('comments', $comment);
        }
    }


//    public function findVideoIdForThisComment($id)
//    {
//        $queryBuilder = $this->db->createQueryBuilder();
//
//        $queryBuilder->select('c.video_id')
//            ->from('comments', 'c')
//            ->where('c.id = :id')
//            ->setParameter(':id', $id, \PDO::PARAM_INT);
//        $result = $queryBuilder->execute()->fetchAll();
//        return $result;
//    }



}