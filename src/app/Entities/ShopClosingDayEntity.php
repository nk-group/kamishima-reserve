<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class ShopClosingDayEntity extends Entity
{
    protected $datamap = [];

    protected $dates = [
        'closing_date',
        'repeat_end_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'id'              => 'integer',
        'shop_id'         => 'integer',
        'repeat_type'     => 'integer',
        'is_active'       => 'boolean',
    ];

    // 繰り返し種別定数（モデルと同期）
    public const REPEAT_TYPE_NONE   = 0; // 繰り返しなし
    public const REPEAT_TYPE_WEEKLY = 1; // 毎週
    public const REPEAT_TYPE_YEARLY = 2; // 毎年

    /**
     * 繰り返し種別の表示名を取得
     *
     * @return string
     */
    public function getRepeatTypeName(): string
    {
        switch ($this->repeat_type) {
            case self::REPEAT_TYPE_NONE:
                return '単発';
            case self::REPEAT_TYPE_WEEKLY:
                return '毎週';
            case self::REPEAT_TYPE_YEARLY:
                return '毎年';
            default:
                return '不明';
        }
    }

    /**
     * 繰り返し種別のバッジクラスを取得
     *
     * @return string
     */
    public function getRepeatTypeBadgeClass(): string
    {
        switch ($this->repeat_type) {
            case self::REPEAT_TYPE_NONE:
                return 'badge bg-secondary'; // グレー
            case self::REPEAT_TYPE_WEEKLY:
                return 'badge bg-primary';   // 青
            case self::REPEAT_TYPE_YEARLY:
                return 'badge bg-success';   // 緑
            default:
                return 'badge bg-light text-dark';
        }
    }

    /**
     * 有効/無効の表示名を取得
     *
     * @return string
     */
    public function getActiveStatusName(): string
    {
        return $this->is_active ? '有効' : '無効';
    }

    /**
     * 有効/無効のバッジクラスを取得
     *
     * @return string
     */
    public function getActiveStatusBadgeClass(): string
    {
        return $this->is_active ? 'badge bg-success' : 'badge bg-danger';
    }

    /**
     * 休業日を日本語形式で取得
     *
     * @return string
     */
    public function getClosingDateJapanese(): string
    {
        if (empty($this->closing_date)) {
            return '';
        }

        $date = new \DateTime($this->closing_date);
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $weekday = $weekdays[(int)$date->format('w')];
        
        return $date->format('Y年n月j日') . '(' . $weekday . ')';
    }

    /**
     * 休業日を短縮形式で取得
     *
     * @return string
     */
    public function getClosingDateShort(): string
    {
        if (empty($this->closing_date)) {
            return '';
        }

        $date = new \DateTime($this->closing_date);
        return $date->format('m/d');
    }

    /**
     * 繰り返し終了日を日本語形式で取得
     *
     * @return string
     */
    public function getRepeatEndDateJapanese(): string
    {
        if (empty($this->repeat_end_date)) {
            return '';
        }

        $date = new \DateTime($this->repeat_end_date);
        $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
        $weekday = $weekdays[(int)$date->format('w')];
        
        return $date->format('Y年n月j日') . '(' . $weekday . ')';
    }

    /**
     * 曜日名を取得
     *
     * @return string
     */
    public function getWeekdayName(): string
    {
        if (empty($this->closing_date)) {
            return '';
        }

        $date = new \DateTime($this->closing_date);
        $weekdays = ['日曜日', '月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日'];
        
        return $weekdays[(int)$date->format('w')];
    }

    /**
     * 月日を取得（毎年繰り返し用）
     *
     * @return string
     */
    public function getMonthDay(): string
    {
        if (empty($this->closing_date)) {
            return '';
        }

        $date = new \DateTime($this->closing_date);
        return $date->format('n月j日');
    }

    /**
     * 詳細な説明文を取得
     *
     * @return string
     */
    public function getDetailDescription(): string
    {
        $description = $this->holiday_name;
        
        switch ($this->repeat_type) {
            case self::REPEAT_TYPE_NONE:
                $description .= ' (' . $this->getClosingDateJapanese() . ')';
                break;
                
            case self::REPEAT_TYPE_WEEKLY:
                $description .= ' (毎週' . $this->getWeekdayName() . ')';
                if (!empty($this->repeat_end_date)) {
                    $description .= ' ※' . $this->getRepeatEndDateJapanese() . 'まで';
                }
                break;
                
            case self::REPEAT_TYPE_YEARLY:
                $description .= ' (毎年' . $this->getMonthDay() . ')';
                if (!empty($this->repeat_end_date)) {
                    $description .= ' ※' . $this->getRepeatEndDateJapanese() . 'まで';
                }
                break;
        }
        
        return $description;
    }

    /**
     * 一覧表示用の簡潔な説明を取得
     *
     * @return string
     */
    public function getListDescription(): string
    {
        switch ($this->repeat_type) {
            case self::REPEAT_TYPE_NONE:
                return $this->getClosingDateShort();
                
            case self::REPEAT_TYPE_WEEKLY:
                $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
                $date = new \DateTime($this->closing_date);
                $weekday = $weekdays[(int)$date->format('w')];
                return '毎週' . $weekday . '曜日';
                
            case self::REPEAT_TYPE_YEARLY:
                return '毎年' . $this->getMonthDay();
                
            default:
                return '';
        }
    }

    /**
     * フォーム用の初期値を取得
     *
     * @return array
     */
    public function getFormData(): array
    {
        return [
            'id' => $this->id,
            'shop_id' => $this->shop_id,
            'holiday_name' => $this->holiday_name,
            'closing_date' => $this->closing_date,
            'repeat_type' => $this->repeat_type,
            'repeat_end_date' => $this->repeat_end_date,
            'is_active' => $this->is_active ? 1 : 0,
        ];
    }

    /**
     * 編集可能かどうかを判定
     * 
     * @return bool
     */
    public function isEditable(): bool
    {
        // 過去の単発定休日は編集不可
        if ($this->repeat_type === self::REPEAT_TYPE_NONE) {
            $closingDate = new \DateTime($this->closing_date);
            $today = new \DateTime();
            return $closingDate >= $today;
        }
        
        // 繰り返し定休日は編集可能
        return true;
    }

    /**
     * 削除可能かどうかを判定
     *
     * @return bool
     */
    public function isDeletable(): bool
    {
        // 基本的に削除は常に可能（論理削除）
        return true;
    }

    /**
     * 今日が対象の休業日かどうかを判定
     *
     * @return bool
     */
    public function isToday(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = new \DateTime();
        $baseDate = new \DateTime($this->closing_date);
        
        // 繰り返し終了日のチェック
        if (!empty($this->repeat_end_date)) {
            $endDate = new \DateTime($this->repeat_end_date);
            if ($today > $endDate) {
                return false;
            }
        }
        
        switch ($this->repeat_type) {
            case self::REPEAT_TYPE_NONE:
                return $baseDate->format('Y-m-d') === $today->format('Y-m-d');
                
            case self::REPEAT_TYPE_WEEKLY:
                return $baseDate->format('w') === $today->format('w') && 
                       $today >= $baseDate;
                       
            case self::REPEAT_TYPE_YEARLY:
                return $baseDate->format('m-d') === $today->format('m-d') && 
                       $today->format('Y') >= $baseDate->format('Y');
                       
            default:
                return false;
        }
    }

    /**
     * JSON形式でのシリアライズ用
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        
        // 追加の計算プロパティ
        $data['repeat_type_name'] = $this->getRepeatTypeName();
        $data['active_status_name'] = $this->getActiveStatusName();
        $data['closing_date_japanese'] = $this->getClosingDateJapanese();
        $data['detail_description'] = $this->getDetailDescription();
        $data['list_description'] = $this->getListDescription();
        $data['is_editable'] = $this->isEditable();
        $data['is_deletable'] = $this->isDeletable();
        $data['is_today'] = $this->isToday();
        
        return $data;
    }
}