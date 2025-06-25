<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;
use App\Validation\CustomRules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var string[]
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        CustomRules::class, // カスタムルールを追加
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // 定休日マスタ用のバリデーションルールセット
    // --------------------------------------------------------------------

    /**
     * 定休日マスタ - 新規作成用
     */
    public array $shop_closing_day_create = [
        'shop_id' => [
            'label' => '店舗',
            'rules' => 'required|integer|shop_exists'
        ],
        'holiday_name' => [
            'label' => '定休日名',
            'rules' => 'required|string|max_length[50]|unique_holiday_name_per_shop'
        ],
        'closing_date' => [
            'label' => '休業日',
            'rules' => 'required|valid_date[Y-m-d]|future_date_for_single'
        ],
        'repeat_type' => [
            'label' => '繰り返し種別',
            'rules' => 'required|in_list[0,1,2]'
        ],
        'repeat_end_date' => [
            'label' => '繰り返し終了日',
            'rules' => 'permit_empty|valid_date[Y-m-d]|check_end_date_after_start'
        ],
        'is_active' => [
            'label' => '有効フラグ',
            'rules' => 'required|in_list[0,1]'
        ],
    ];

    /**
     * 定休日マスタ - 更新用
     */
    public array $shop_closing_day_update = [
        'id' => [
            'label' => 'ID',
            'rules' => 'required|integer'
        ],
        'shop_id' => [
            'label' => '店舗',
            'rules' => 'required|integer|shop_exists'
        ],
        'holiday_name' => [
            'label' => '定休日名',
            'rules' => 'required|string|max_length[50]|unique_holiday_name_per_shop'
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
            'rules' => 'permit_empty|valid_date[Y-m-d]|check_end_date_after_start'
        ],
        'is_active' => [
            'label' => '有効フラグ',
            'rules' => 'required|in_list[0,1]'
        ],
    ];

    /**
     * 定休日マスタ - 一括作成用
     */
    public array $shop_closing_day_batch = [
        'shop_id' => [
            'label' => '店舗',
            'rules' => 'required|integer|shop_exists'
        ],
        'holiday_name' => [
            'label' => '定休日名',
            'rules' => 'required|string|max_length[50]'
        ],
        'start_date' => [
            'label' => '開始日',
            'rules' => 'required|valid_date[Y-m-d]'
        ],
        'end_date' => [
            'label' => '終了日',
            'rules' => 'required|valid_date[Y-m-d]|check_end_date_after_start'
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

    // --------------------------------------------------------------------
    // エラーメッセージ
    // --------------------------------------------------------------------

    /**
     * 定休日マスタ用のカスタムエラーメッセージ
     */
    public array $shop_closing_day_create_errors = [
        'shop_id' => [
            'shop_exists' => '指定された店舗が見つからないか、無効な店舗です。'
        ],
        'holiday_name' => [
            'unique_holiday_name_per_shop' => 'この店舗では既に同じ名前の定休日が登録されています。'
        ],
        'closing_date' => [
            'future_date_for_single' => '単発の定休日は今日以降の日付を入力してください。'
        ],
        'repeat_end_date' => [
            'check_end_date_after_start' => '繰り返し終了日は休業日以降の日付を入力してください。'
        ]
    ];

    public array $shop_closing_day_update_errors = [
        'shop_id' => [
            'shop_exists' => '指定された店舗が見つからないか、無効な店舗です。'
        ],
        'holiday_name' => [
            'unique_holiday_name_per_shop' => 'この店舗では既に同じ名前の定休日が登録されています。'
        ],
        'repeat_end_date' => [
            'check_end_date_after_start' => '繰り返し終了日は休業日以降の日付を入力してください。'
        ]
    ];

    public array $shop_closing_day_batch_errors = [
        'shop_id' => [
            'shop_exists' => '指定された店舗が見つからないか、無効な店舗です。'
        ],
        'end_date' => [
            'check_end_date_after_start' => '終了日は開始日以降の日付を入力してください。'
        ]
    ];
}