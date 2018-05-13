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
     * zapisywanie oceny
     *
     * @param array $rating Rating
     *
     * @return boolean Result
     */
    public function save($rating, $videoId, $video)
    {
        if (isset($rating['id']) && ctype_digit((string) $rating['id'])) {
            // jeśli już ocenione przez użytkownika to nie można ocenić jeszcze raz
            return 0;
        } else {
                $this->db->insert('rating', $rating);

                $rate = $this->averageRating($videoId);
                $video['average_rating'] = $rate['average_rate'];
                $this->saveAverageRating($video);

                return 1;

        }
    }

    /*
     *  zapisywanie średniej ocen dla danego video do tabeli video
     */
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

    /**
     * Sprawdzanie czy dany użytkownik ocenił już video
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

    /**
     * obliczanie ilu użytkoników oceniło video
     */
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