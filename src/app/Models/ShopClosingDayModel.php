<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\ShopClosingDayEntity;

class ShopClosingDayModel extends Model
{
    protected $table            = 'shop_closing_days';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = ShopClosingDayEntity::class;
    protected $useSoftDeletes   = true;

    protected $allowedFields    = [
        'shop_id',
        'holiday_name',
        'closing_date',
        'repeat_type',
        'repeat_end_date',
        'is_active'
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    protected $dateFormat       = 'datetime';

    // 繰り返し種別の定数
    public const REPEAT_TYPE_NONE   = 0; // 繰り返しなし
    public const REPEAT_TYPE_WEEKLY = 1; // 毎週
    public const REPEAT_TYPE_YEARLY = 2; // 毎年

    // CodeIgniter標準のバリデーションルール
    protected $validationRules = [
        'shop_id' => [
            'label' => '店舗',
            'rules' => 'required|integer|is_not_unique[shops.id]'
        ],
        'holiday_name' => [
            'label' => '定休日名',
            'rules' => 'required|string|max_length[50]'
        ],
        'closing_date' => [
            'label' => '休業日',
            'rules' => 'required|valid_date[Y-m-d]'
        ],
        'repeat_type' => [
            'label' => '繰り返し種別',
            'rules' => 'required|in_list[0,1,2]'
        ],
        'repeat_end_date' => [
            'label' => '繰り返し終了日',
            'rules' => 'permit_empty|valid_date[Y-m-d]'
        ],
        'is_active' => [
            'label' => '有効フラグ',
            'rules' => 'required|in_list[0,1]'
        ],
    ];

    protected $validationMessages = [
        'shop_id' => [
            'required' => '店舗を選択してください。',
            'integer' => '店舗IDは数値で入力してください。',
            'is_not_unique' => '指定された店舗が見つかりません。'
        ],
        'holiday_name' => [
            'required' => '定休日名を入力してください。',
            'max_length' => '定休日名は50文字以内で入力してください。'
        ],
        'closing_date' => [
            'required' => '休業日を入力してください。',
            'valid_date' => '正しい日付形式で入力してください。'
        ],
        'repeat_type' => [
            'required' => '繰り返し種別を選択してください。',
            'in_list' => '繰り返し種別は指定された値から選択してください。'
        ],
        'repeat_end_date' => [
            'valid_date' => '正しい日付形式で入力してください。'
        ],
        'is_active' => [
            'required' => '有効フラグを設定してください。',
            'in_list' => '有効フラグは指定された値から選択してください。'
        ]
    ];

    protected $beforeInsert = ['validateDates'];
    protected $beforeUpdate = ['validateDates'];

    /**
     * 日付の整合性をチェック（既存機能を保持）
     */
    protected function validateDates(array $data): array
    {
        if (isset($data['data']['closing_date']) && isset($data['data']['repeat_end_date'])) {
            $closingDate = $data['data']['closing_date'];
            $endDate = $data['data']['repeat_end_date'];
            
            if (!empty($endDate) && $endDate < $closingDate) {
                throw new \RuntimeException('繰り返し終了日は休業日以降の日付を入力してください。');
            }
        }
        
        return $data;
    }

    /**
     * 繰り返し種別の選択肢を取得
     *
     * @return array
     */
    public static function getRepeatTypeOptions(): array
    {
        return [
            self::REPEAT_TYPE_NONE   => '繰り返しなし（単発）',
            self::REPEAT_TYPE_WEEKLY => '毎週',
            self::REPEAT_TYPE_YEARLY => '毎年'
        ];
    }

    /**
     * 店舗別の定休日を取得
     *
     * @param int $shopId 店舗ID
     * @param bool $activeOnly 有効なもののみ取得するか
     * @return array<ShopClosingDayEntity>
     */
    public function findByShopId(int $shopId, bool $activeOnly = true): array
    {
        $builder = $this->where('shop_id', $shopId);
        
        if ($activeOnly) {
            $builder->where('is_active', 1);
        }
        
        return $builder->orderBy('closing_date', 'ASC')->findAll();
    }

    /**
     * 指定した日付が休業日かどうかを判定
     *
     * @param int $shopId 店舗ID
     * @param string $targetDate 対象日付 (Y-m-d形式)
     * @return bool
     */
    public function isClosingDay(int $shopId, string $targetDate): bool
    {
        $closingDays = $this->findByShopId($shopId, true);
        $targetDateTime = new \DateTime($targetDate);
        
        foreach ($closingDays as $closingDay) {
            if ($this->isDateMatched($closingDay, $targetDateTime)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * 指定した期間の休業日一覧を取得
     *
     * @param int $shopId 店舗ID
     * @param string $startDate 期間開始日 (Y-m-d形式)
     * @param string $endDate 期間終了日 (Y-m-d形式)
     * @return array 休業日の配列 [['date' => 'Y-m-d', 'name' => '定休日名'], ...]
     */
    public function getClosingDaysInPeriod(int $shopId, string $startDate, string $endDate): array
    {
        $closingDays = $this->findByShopId($shopId, true);
        $result = [];
        
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        
        // 期間内の全日付をチェック
        $current = clone $start;
        while ($current <= $end) {
            foreach ($closingDays as $closingDay) {
                if ($this->isDateMatched($closingDay, $current)) {
                    $result[] = [
                        'date' => $current->format('Y-m-d'),
                        'name' => $closingDay->holiday_name,
                        'repeat_type' => $closingDay->repeat_type
                    ];
                    break; // 同じ日に複数の定休日設定があっても1つだけ
                }
            }
            $current->add(new \DateInterval('P1D'));
        }
        
        return $result;
    }

    /**
     * 定休日エンティティと対象日付がマッチするかを判定
     *
     * @param ShopClosingDayEntity $closingDay
     * @param \DateTime $targetDate
     * @return bool
     */
    private function isDateMatched(ShopClosingDayEntity $closingDay, \DateTime $targetDate): bool
    {
        $baseDate = new \DateTime($closingDay->closing_date);
        
        // 繰り返し終了日のチェック
        if (!empty($closingDay->repeat_end_date)) {
            $endDate = new \DateTime($closingDay->repeat_end_date);
            if ($targetDate > $endDate) {
                return false;
            }
        }
        
        switch ($closingDay->repeat_type) {
            case self::REPEAT_TYPE_NONE:
                // 単発：完全一致
                return $baseDate->format('Y-m-d') === $targetDate->format('Y-m-d');
                
            case self::REPEAT_TYPE_WEEKLY:
                // 毎週：曜日が一致し、基準日以降
                return $baseDate->format('w') === $targetDate->format('w') && 
                       $targetDate >= $baseDate;
                       
            case self::REPEAT_TYPE_YEARLY:
                // 毎年：月日が一致し、基準日の年以降
                return $baseDate->format('m-d') === $targetDate->format('m-d') && 
                       $targetDate->format('Y') >= $baseDate->format('Y');
                       
            default:
                return false;
        }
    }

    /**
     * 期間指定で定休日を検索
     *
     * @param array $filters 検索条件
     * @return array<ShopClosingDayEntity>
     */
    public function searchClosingDays(array $filters = []): array
    {
        $builder = $this;
        
        // 店舗ID指定
        if (!empty($filters['shop_id'])) {
            $builder = $builder->where('shop_id', $filters['shop_id']);
        }
        
        // 繰り返し種別指定
        if (isset($filters['repeat_type']) && $filters['repeat_type'] !== '') {
            $builder = $builder->where('repeat_type', $filters['repeat_type']);
        }
        
        // 有効/無効指定
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $builder = $builder->where('is_active', $filters['is_active']);
        }
        
        // 期間指定（休業日ベース）
        if (!empty($filters['date_from'])) {
            $builder = $builder->where('closing_date >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $builder = $builder->where('closing_date <=', $filters['date_to']);
        }
        
        // 定休日名検索
        if (!empty($filters['holiday_name'])) {
            $builder = $builder->like('holiday_name', $filters['holiday_name']);
        }
        
        return $builder->orderBy('shop_id', 'ASC')
                      ->orderBy('closing_date', 'ASC')
                      ->findAll();
    }

    /**
     * 一括作成処理
     *
     * @param int $shopId 店舗ID
     * @param string $holidayName 定休日名
     * @param string $startDate 開始日
     * @param string $endDate 終了日
     * @param int $repeatType 繰り返し種別
     * @param string|null $repeatEndDate 繰り返し終了日
     * @param int $isActive 有効フラグ
     * @return array ['success' => bool, 'message' => string]
     */
    public function createBatchClosingDays(
        int $shopId,
        string $holidayName,
        string $startDate,
        string $endDate,
        int $repeatType = self::REPEAT_TYPE_NONE,
        ?string $repeatEndDate = null,
        int $isActive = 1
    ): array {
        try {
            $start = new \DateTime($startDate);
            $end = new \DateTime($endDate);
            $createdCount = 0;
            
            // 期間内の全日付に対して定休日を作成
            $current = clone $start;
            while ($current <= $end) {
                $data = [
                    'shop_id' => $shopId,
                    'holiday_name' => $holidayName,
                    'closing_date' => $current->format('Y-m-d'),
                    'repeat_type' => $repeatType,
                    'repeat_end_date' => $repeatEndDate,
                    'is_active' => $isActive
                ];
                
                if ($this->save($data)) {
                    $createdCount++;
                }
                
                $current->add(new \DateInterval('P1D'));
            }
            
            return [
                'success' => true,
                'message' => "{$createdCount}件の定休日を一括作成しました。"
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => '一括作成に失敗しました: ' . $e->getMessage()
            ];
        }
    }
}