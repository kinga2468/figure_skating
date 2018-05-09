<?php
/**
 * Rating repository.
 */
namespace Repository;
use Doctrine\DBAL\Connection;
/**
 * Class RatingRepository.
 *
 * @package Repository
 */
class RatingRepository
{
    /**
     * Database
     * @var Connection
     */
    protected $db;
    /**
     * RatingRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Save record.
     *
     * @param array $rating Rating
     *
     * @return boolean Result
     */
    public function save($rating, $videoId, $video)
    {
//        $videoIsRatedByUser = $this->CheckIfUserRatedVideo($videoId, $userId);
//        var_dump($videoIsRatedByUser);

        if (isset($rating['id']) && ctype_digit((string) $rating['id'])) {
            // update record
//            $id = $rating['id'];
//            unset($rating['id']);
            return 0;

//            return $this->db->update('rating', $rating, ['id' => $id]);
        } else {
                $this->db->insert('rating', $rating);

                $rate = $this->averageRating($videoId);
//                var_dump($rate['average_rate']);
//                var_dump($video['average_rating']);
                $video['average_rating'] = $rate['average_rate'];

                $this->saveAverageRating($video);

                return 1;

        }
    }

    public function saveAverageRating($video)
    {
        if (isset($video['id']) && ctype_digit((string) $video['id'])) {
            // update record

//            return 0;
            $id = $video['id'];
            unset($video['id']);
//
            return $this->db->update('video', $video, ['id' => $id]);
        } else {
            // add new record
            return $this->db->insert('video', $video);
        }
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
            ->from('rating', 'r');
    }

    public function averageRating($videoId)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('round(avg(r.rate),2) as average_rate')
            ->from('rating', 'r')
            ->innerJoin('r', 'video', 'v', 'r.video_id = v.id')
            ->where('v.id = :id')
            ->setParameter(':id', $videoId, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetchAll();

        return !$result ? [] : current($result);
    }

    /**
     * Check if user rated video.
     */
    public function CheckIfUserRatedVideo($video_id, $userId)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('*')
            ->from('rating', 'r')
            ->where('r.video_id = :video_id')
            ->setParameter(':video_id', $video_id);
        $results = $queryBuilder->execute()->fetchAll();

        foreach ($results as $result){
            if($result['users_id'] ===$userId){
                return true;
            }
        }
        return false;
    }

    public function howManyUsersRatedThisVideo($videoId)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        $queryBuilder->select('count(*) as howMany')
            ->from('rating', 'r')
            ->innerJoin('r', 'video', 'v', 'r.video_id = v.id')
            ->where('v.id = :id')
            ->setParameter(':id', $videoId, \PDO::PARAM_INT);
        $result = $queryBuilder->execute()->fetchAll();

        return !$result ? [] : current($result);


    }
}