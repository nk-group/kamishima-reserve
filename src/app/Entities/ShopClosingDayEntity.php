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

        try {
            // DateTimeオブジェクトまたは文字列から日付を取得
            if ($this->closing_date instanceof \DateTime) {
                $date = $this->closing_date;
            } else {
                $date = new \DateTime($this->closing_date);
            }

            $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
            $weekday = $weekdays[(int)$date->format('w')];
            
            return $date->format('Y年n月j日') . '(' . $weekday . ')';
        } catch (\Exception $e) {
            return '';
        }
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

        try {
            // DateTimeオブジェクトまたは文字列から日付を取得
            if ($this->repeat_end_date instanceof \DateTime) {
                $date = $this->repeat_end_date;
            } else {
                $date = new \DateTime($this->repeat_end_date);
            }

            $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
            $weekday = $weekdays[(int)$date->format('w')];
            
            return $date->format('Y年n月j日') . '(' . $weekday . ')';
        } catch (\Exception $e) {
            return '';
        }
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

        try {
            // DateTimeオブジェクトまたは文字列から日付を取得
            if ($this->closing_date instanceof \DateTime) {
                $date = $this->closing_date;
            } else {
                $date = new \DateTime($this->closing_date);
            }

            return $date->format('Y/m/d');
        } catch (\Exception $e) {
            return '';
        }
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

        try {
            // DateTimeオブジェクトまたは文字列から日付を取得
            if ($this->closing_date instanceof \DateTime) {
                $date = $this->closing_date;
            } else {
                $date = new \DateTime($this->closing_date);
            }

            return $date->format('n月j日');
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * 曜日名を取得（毎週繰り返し用）
     *
     * @return string
     */
    public function getWeekdayName(): string
    {
        if (empty($this->closing_date)) {
            return '';
        }

        try {
            // DateTimeオブジェクトまたは文字列から日付を取得
            if ($this->closing_date instanceof \DateTime) {
                $date = $this->closing_date;
            } else {
                $date = new \DateTime($this->closing_date);
            }

            $weekdays = ['日', '月', '火', '水', '木', '金', '土'];
            
            return $weekdays[(int)$date->format('w')];
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * 詳細な説明を取得
     *
     * @return string
     */
    public function getDetailedDescription(): string
    {
        $description = $this->getClosingDateJapanese();
        
        switch ($this->repeat_type) {
            case self::REPEAT_TYPE_NONE:
                // 単発はそのまま
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
            'id' => $this->id ? (string)$this->id : '',
            'shop_id' => $this->shop_id ? (string)$this->shop_id : '',
            'holiday_name' => $this->holiday_name ?? '',
            'closing_date' => $this->getClosingDateForForm(),
            'repeat_type' => $this->repeat_type ? (string)$this->repeat_type : '0',
            'repeat_end_date' => $this->getRepeatEndDateForForm(),
            'is_active' => $this->is_active ? '1' : '0'
        ];
    }

    /**
     * フォーム用の休業日を取得
     *
     * @return string
     */
    private function getClosingDateForForm(): string
    {
        if (empty($this->closing_date)) {
            return '';
        }

        // closing_dateがDateTimeオブジェクトの場合とStringの場合に対応
        if ($this->closing_date instanceof \DateTime) {
            return $this->closing_date->format('Y-m-d');
        }

        // 文字列の場合、DateTime形式に変換してからフォーマット
        try {
            $date = new \DateTime($this->closing_date);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * フォーム用の繰り返し終了日を取得
     *
     * @return string
     */
    private function getRepeatEndDateForForm(): string
    {
        if (empty($this->repeat_end_date)) {
            return '';
        }

        // repeat_end_dateがDateTimeオブジェクトの場合とString の場合に対応
        if ($this->repeat_end_date instanceof \DateTime) {
            return $this->repeat_end_date->format('Y-m-d');
        }

        // 文字列の場合、DateTime形式に変換してからフォーマット
        try {
            $date = new \DateTime($this->repeat_end_date);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return '';
        }
    }
}