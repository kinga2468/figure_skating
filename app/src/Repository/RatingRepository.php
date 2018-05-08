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
     *
     * Find average records for photo.
     *
     * @param string $photoId id of photo
     *
     * @return array|mixed Result
     */
    public function AverageRaringForPhoto($photoId)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('r.photoId = :photoId')
            ->select("avg(r.value)")
            ->setParameter(':photoId', $photoId);
        $result = $queryBuilder->execute()->fetch();
        return !$result ? [] : current($result);
    }
    /**
     *
     * Check if user rated photo.
     *
     * @param int $photoId id of photo
     * @param int $userId id of user
     * @return array|mixed Result
     */
    public function CheckIfUserRatedPhoto($photoId, $userId)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->where('r.photoId = :photoId')
            ->setParameter(':photoId', $photoId);
        $results = $queryBuilder->execute()->fetchAll();
        foreach ($results as $result){
            if($result['userId']===$userId){
                return true;
            }
        }
        return false;
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

//        dump($rating['average_rating_for_video']);
//        dump($this->averageRating($videoId));
//
//        $rating['average_rating_for_video'] = $this->averageRating($videoId);


        if (isset($rating['id']) && ctype_digit((string) $rating['id'])) {
            // update record
//            $id = $rating['id'];
//            unset($rating['id']);
            return 0;

//            return $this->db->update('rating', $rating, ['id' => $id]);
        } else {
            // add new record
            $this->db->insert('rating', $rating);

            $rate = $this->averageRating($videoId);
            $video['average_rating'] = $rate['average_rate'];
//            dump($video['average_rating']);
            $this->saveAverageRating($video);

            return 1;
        }
    }

    public function saveAverageRating($video)
    {
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

//    public function saveAverageRating($video, $videoId)
//    {
//        $video['average_rating'] = $this->averageRating($videoId);
//
//        if (isset($video['id']) && ctype_digit((string) $video['id'])) {
//            // update record
//            $id = $video['id'];
//            unset($video['id']);
////            return 0;
//
//            return $this->db->update('video', $video, ['id' => $id]);
//        } else {
//            // add new record
//            return $this->db->insert('video', $video);
//        }
//    }

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