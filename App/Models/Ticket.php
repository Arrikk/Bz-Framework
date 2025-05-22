<?php
namespace App\Models;

use Core\Model\Model;

class Ticket extends Model
{
    // protected static $table = 'tickets';
    
    /**
     * Get count of tickets by status for a given user
     * 
     * @param string $userId User ID
     * @param string $status Status to count
     * @return int Count of tickets with specified status
     */
    public static function countByStatus($userId, $status)
    {
        $result = self::find([
            '$.where' => "user_id = '$userId' AND status = '$status'"
        ], 'COUNT(*) as count');
        
        return $result[0]->count ?? 0;
    }
    
    /**
     * Get tickets with their response count
     * 
     * @param array $criteria Search criteria
     * @return array Tickets with response count
     */
    public static function getWithResponseCount($criteria = [])
    {
        $whereClause = '';
        
        if (!empty($criteria)) {
            $conditions = [];
            foreach ($criteria as $key => $value) {
                if (is_string($value)) {
                    $conditions[] = "t.$key = '$value'";
                } else {
                    $conditions[] = "t.$key = $value";
                }
            }
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }
        
        $query = "
            SELECT 
                t.*,
                COUNT(r._id) as response_count
            FROM 
                " . self::$table . " t
            LEFT JOIN 
                ticket_responses r ON t._id = r.ticket_id
            $whereClause
            GROUP BY 
                t._id
            ORDER BY 
                t.created_at DESC
        ";
        
        return Model::select($query)->obj()->exec();
    }
    
    /**
     * Get unresolved tickets
     * 
     * @param int $limit Number of records to return
     * @return array Unresolved tickets
     */
    public static function getUnresolved($limit = 10)
    {
        return self::find([
            '$.where' => "status != 'closed' AND status != 'resolved'",
            '$.order' => 'created_at ASC',
            '$.limit' => $limit
        ]);
    }
    
    /**
     * Get tickets requiring admin attention (new or awaiting reply)
     * 
     * @param int $limit Number of records to return
     * @return array Tickets requiring attention
     */
    public static function getRequiringAttention($limit = 10)
    {
        return self::find([
            'where.status' => OPEN,
            'or.status' => AWAITING_REPLY,
            '$.order' => 'updated_at ASC',
            '$.limit' => $limit
        ]);
    }
    
    /**
     * Search tickets by keyword
     * 
     * @param string $keyword Search keyword
     * @param int $limit Number of records to return
     * @return array Matching tickets
     */
    public static function search($keyword, $limit = 20)
    {
        return self::find([
            '$.where' => Model::like('subject', $keyword),
            '$.or' => Model::like('message', $keyword),
            '$.limit' => $limit
        ]);
    }

    public static function findTicketBy($id, $user = null):Ticket|bool {
        $submitted = User::findOne(['$.where' => "id = tickets.user_id"], 'email', false)->query;
        return Ticket::findOne([
            'where.id' => $id,
            'and.user_id' => $user
        ], "*, ($submitted) as submittedBy");
    }
}
