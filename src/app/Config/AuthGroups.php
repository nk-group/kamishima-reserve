<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * デフォルトグループ
     * --------------------------------------------------------------------
     * 新規登録ユーザーが自動的に追加されるグループ。
     * 注意: Config\Auth.php の $allowRegistration が false の場合、
     * この設定はユーザーの自動登録時には使用されませんが、
     * Shieldのいくつかの内部処理で参照される可能性があります。
     * 一般利用者を想定して 'user' を指定しておくのが無難です。
     */
    public string $defaultGroup = 'user';

    /**
     * --------------------------------------------------------------------
     * グループ定義
     * --------------------------------------------------------------------
     * システム内で利用可能なグループの連想配列。
     * キーがグループ名（半角英数字）、値がグループ情報（表示名、説明）の配列となります。
     *
     * 例: $user->addGroup('admin'); のようにキー名でグループを指定して使用します。
     *
     * @var array<string, array<string, string>>
     */
    public array $groups = [
        'admin' => [
            'title'       => 'システム管理者',
            'description' => 'サイトの全機能にアクセスでき、システム設定やユーザー管理を行う最上位の権限グループ。',
        ],
        'staff' => [
            'title'       => '店舗スタッフ',
            'description' => '予約の管理、顧客対応など、店舗の日常業務を行うための権限グループ。',
        ],
        'user' => [
            'title'       => '一般利用者',
            'description' => '車検の予約や自身の予約情報の確認ができる顧客向けの権限グループ。',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * パーミッション定義
     * --------------------------------------------------------------------
     * システム内で利用可能なパーミッション（操作権限）の一覧。
     * ここに定義されていないパーミッションは、グループに割り当てることはできません。
     * 命名規則の例: [対象領域].[操作].[範囲] (例: 'reservations.create.own', 'users.edit.all')
     *
     * @var array<string, string>
     */
    public array $permissions = [
        // 管理者専用機能
        'admin.access'                 => '管理系機能への基本アクセス権限',
        // 'admin.users.view'             => 'ユーザー一覧の表示',
        // 'admin.users.create'           => '新規ユーザーの作成',
        // 'admin.users.edit'             => 'ユーザー情報の編集',
        // 'admin.users.delete'           => 'ユーザーの削除',
        // 'admin.users.groups'           => 'ユーザーのグループ割り当て変更',
        // 'admin.settings.system'        => 'システム動作環境設定の変更',
        // 'admin.settings.reminders'     => 'リマインドメール設定の変更',
        // 'admin.data.deleteall'         => 'データの一括削除',

        // スタッフ機能（管理者も基本的にこれらの権限を持つ）
        'staff.access'                 => 'スタッフ向け機能への基本アクセス権限',
        // 'staff.dashboard'              => 'スタッフ用フロントページの表示',
        // 'staff.reservations.calendar'  => '予約状況カレンダー（全体）の表示',
        // 'staff.reservations.list'      => '予約検索／一覧の表示',
        // 'staff.reservations.detail'    => '予約詳細の表示',
        // 'staff.reservations.edit'      => '予約情報の編集',
        // 'staff.reservations.confirm'   => '予約の確定処理',
        // 'staff.reservations.sendmail'  => '予約確定メールの送信',
        // 'staff.reservations.print.schedule' => '入庫予定表の印刷',
        // 'staff.reservations.print.tag' => '予約タグの印刷',
        // 'staff.reservations.exportcsv' => '予約データのCSV出力',
        // 'staff.holidays.manage'        => '定休日の管理',

        // 一般利用者機能
        'user.access'                  => '利用者向け機能への基本アクセス権限',
        // 'user.reservations.calendar'   => '予約状況確認カレンダーの表示（利用者向け）',
        // 'user.reservations.create'     => '新規予約の作成（自身の予約）',
        // 'user.reservations.view'       => '自身の予約状況の確認（一覧・詳細）',
        // 'user.reservations.cancel'  => '自身の予約のキャンセル（もし機能があれば）',
    ];

    /**
     * --------------------------------------------------------------------
     * パーミッションマトリックス（権限割り当て）
     * --------------------------------------------------------------------
     * 各グループにどのパーミッションを割り当てるかを定義します。
     * グループ名をキーとし、そのグループが持つパーミッション名の配列を値とします。
     * パーミッション名にはワイルドカード '*' を使用できます。
     * 例: 'admin.*' は 'admin.' で始まる全てのパーミッションを意味します。
     *
     * @var array<string, list<string>>
     */
    public array $matrix = [
        'admin' => [
            'admin.*',  // admin.で始まる全ての権限
            'staff.*',  // staff.で始まる全ての権限も基本的に付与
            'user.*',   // user.で始まる全ての権限も基本的に付与
        ],
        'staff' => [
            'staff.*',  // staff.で始まる全ての権限も基本的に付与
            'user.*',   // user.で始まる全ての権限も基本的に付与
        ],
        'user' => [
            'user.*',   // user.で始まる全ての権限も基本的に付与
            // 'user.access',
            // 'user.reservations.calendar',
            // 'user.reservations.create',
            // 'user.reservations.view',
            // 'user.reservations.cancel',
        ],
    ];
}