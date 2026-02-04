<?php

namespace App\Models;

use CodeIgniter\Model;

class ChangeLogModel extends Model
{
    protected $table            = 'change_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'user_name',
        'action',
        'entity',
        'entity_id',
        'summary',
        'old_value',
        'new_value',
        'created_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'user_id' => 'int',
        'entity_id' => '?int'
    ];

    // Dates
    protected $useTimestamps = false; // Manual control
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'action' => 'required|in_list[create,update,delete]',
        'entity' => 'required|max_length[50]',
        'summary' => 'required'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID harus diisi'
        ],
        'action' => [
            'required' => 'Action harus diisi',
            'in_list' => 'Action harus create, update, atau delete'
        ],
        'entity' => [
            'required' => 'Entity harus diisi'
        ],
        'summary' => [
            'required' => 'Summary harus diisi'
        ]
    ];

    /**
     * Insert log entry
     * 
     * @param array $data ['user_id', 'user_name', 'action', 'entity', 'entity_id', 'summary', 'old_value', 'new_value']
     * @return bool|int Insert ID or false
     */
    public function insertLog(array $data)
    {
        try {
            // Set created_at if not provided
            if (!isset($data['created_at'])) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }

            // Validate required fields
            if (empty($data['user_id']) || empty($data['action']) || empty($data['entity']) || empty($data['summary'])) {
                log_message('error', 'ChangeLog insertLog: Missing required fields');
                return false;
            }

            $result = $this->insert($data);
            
            if ($result) {
                log_message('info', "ChangeLog inserted: {$data['action']} {$data['entity']} by user {$data['user_id']}");
                return $this->getInsertID();
            }

            log_message('error', 'ChangeLog insertLog failed: ' . json_encode($this->errors()));
            return false;

        } catch (\Exception $e) {
            log_message('error', 'ChangeLog insertLog exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recent changes with user info
     * 
     * @param int $limit Number of records to retrieve
     * @return array
     */
    public function getRecent(int $limit = 10): array
    {
        try {
            return $this->select('change_logs.*, users.nama_lengkap as user_full_name, users.username')
                ->join('users', 'users.id = change_logs.user_id', 'left')
                ->orderBy('change_logs.created_at', 'DESC')
                ->limit($limit)
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'ChangeLog getRecent error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get changes by entity
     * 
     * @param string $entity Entity type (harga, jenis_sampah, etc)
     * @param int|null $entityId Specific entity ID
     * @param int $limit Number of records
     * @return array
     */
    public function getByEntity(string $entity, ?int $entityId = null, int $limit = 50): array
    {
        try {
            $builder = $this->select('change_logs.*, users.nama_lengkap as user_full_name, users.username')
                ->join('users', 'users.id = change_logs.user_id', 'left')
                ->where('change_logs.entity', $entity);

            if ($entityId !== null) {
                $builder->where('change_logs.entity_id', $entityId);
            }

            return $builder->orderBy('change_logs.created_at', 'DESC')
                ->limit($limit)
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'ChangeLog getByEntity error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get changes by user
     * 
     * @param int $userId User ID
     * @param int $limit Number of records
     * @return array
     */
    public function getByUser(int $userId, int $limit = 50): array
    {
        try {
            return $this->select('change_logs.*, users.nama_lengkap as user_full_name, users.username')
                ->join('users', 'users.id = change_logs.user_id', 'left')
                ->where('change_logs.user_id', $userId)
                ->orderBy('change_logs.created_at', 'DESC')
                ->limit($limit)
                ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'ChangeLog getByUser error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get statistics
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        try {
            $today = date('Y-m-d');
            $thisWeek = date('Y-m-d', strtotime('-7 days'));
            $thisMonth = date('Y-m');

            return [
                'total' => $this->countAllResults(false),
                'today' => $this->where('DATE(created_at)', $today)->countAllResults(false),
                'this_week' => $this->where('DATE(created_at) >=', $thisWeek)->countAllResults(false),
                'this_month' => $this->where('DATE_FORMAT(created_at, "%Y-%m")', $thisMonth)->countAllResults(false),
                'by_action' => $this->getCountByAction(),
                'by_entity' => $this->getCountByEntity()
            ];
        } catch (\Exception $e) {
            log_message('error', 'ChangeLog getStatistics error: ' . $e->getMessage());
            return [
                'total' => 0,
                'today' => 0,
                'this_week' => 0,
                'this_month' => 0,
                'by_action' => [],
                'by_entity' => []
            ];
        }
    }

    /**
     * Get count by action
     * 
     * @return array
     */
    private function getCountByAction(): array
    {
        try {
            $results = $this->select('action, COUNT(*) as count')
                ->groupBy('action')
                ->findAll();

            $counts = ['create' => 0, 'update' => 0, 'delete' => 0];
            foreach ($results as $result) {
                $counts[$result['action']] = (int)$result['count'];
            }

            return $counts;
        } catch (\Exception $e) {
            log_message('error', 'ChangeLog getCountByAction error: ' . $e->getMessage());
            return ['create' => 0, 'update' => 0, 'delete' => 0];
        }
    }

    /**
     * Get count by entity
     * 
     * @return array
     */
    private function getCountByEntity(): array
    {
        try {
            $results = $this->select('entity, COUNT(*) as count')
                ->groupBy('entity')
                ->findAll();

            $counts = [];
            foreach ($results as $result) {
                $counts[$result['entity']] = (int)$result['count'];
            }

            return $counts;
        } catch (\Exception $e) {
            log_message('error', 'ChangeLog getCountByEntity error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Clean old logs (optional maintenance)
     * 
     * @param int $daysToKeep Number of days to keep
     * @return int Number of deleted records
     */
    public function cleanOldLogs(int $daysToKeep = 90): int
    {
        try {
            $cutoffDate = date('Y-m-d', strtotime("-{$daysToKeep} days"));
            
            $count = $this->where('DATE(created_at) <', $cutoffDate)->countAllResults(false);
            $this->where('DATE(created_at) <', $cutoffDate)->delete();
            
            log_message('info', "ChangeLog: Cleaned {$count} old logs older than {$cutoffDate}");
            return $count;
        } catch (\Exception $e) {
            log_message('error', 'ChangeLog cleanOldLogs error: ' . $e->getMessage());
            return 0;
        }
    }
}
