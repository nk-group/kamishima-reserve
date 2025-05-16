<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

// Shield フィルタークラスの use 宣言を追加
use CodeIgniter\Shield\Filters\SessionAuth;
use CodeIgniter\Shield\Filters\ChainAuth;
use CodeIgniter\Shield\Filters\TokenAuth;
use CodeIgniter\Shield\Filters\GroupFilter;
use CodeIgniter\Shield\Filters\PermissionFilter;

class Filters extends BaseFilters
{
    /**
     * フィルタークラスのエイリアス（別名）を設定します。
     * これにより、ルーティングファイルやコントローラでフィルターを短い名前で指定できるようになります。
     * 例: 'csrf' => \CodeIgniter\Filters\CSRF::class
     *
     * @var array<string, class-string|list<class-string>>
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        // === Shield フィルターのエイリアス定義 ===
        'sessionauth'   => SessionAuth::class,    // セッションを利用した認証状態のチェックを行います。未認証の場合はログインページへリダイレクトします。
        'chainauth'     => ChainAuth::class,      // 複数の認証方法（例: セッション、トークン）を順番に試行します。
        'tokenauth'     => TokenAuth::class,      // APIなどで使用されるアクセストークンによる認証を行います。
        'group'         => GroupFilter::class,    // ユーザーが指定したグループに所属しているかチェックします。例: 'group:admin,staff'
        'permission'    => PermissionFilter::class, // ユーザーが指定したパーミッション（権限）を持っているかチェックします。例: 'permission:users.create'
    ];

    /**
     * 特別な必須フィルターのリストです。
     * ここにリストされたフィルターは、他の種類のフィルターの前後に適用され、
     * ルートが存在しない場合でも常に適用されます。
     * フレームワークの機能を提供するものがデフォルトで設定されています。
     *
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            // 'forcehttps', // 本番環境ではHTTPSを強制するために有効化を推奨します。開発中はコメントアウト可。
            // 'pagecache',  // Webページのキャッシング。動的な内容が多い場合は慎重に設定が必要です。
        ],
        'after' => [
            // 'pagecache',  // キャッシュの後処理。
            'performance', // パフォーマンスメトリクス。開発時に役立ちます。
            //'toolbar',     // デバッグツールバー。開発時に画面下部に表示されます。
        ],
    ];

    /**
     * 全てのリクエストの前処理・後処理として常に適用されるフィルターエイリアスのリストです。
     * アプリケーション全体で共通して必要なセキュリティ対策などを設定します。
     *
     * @var array<string, array<string, array<string, string>>>|array<string, list<string>>
     */
    public array $globals = [
        'before' => [
            // 'honeypot', // フォームへのスパム投稿対策。必要に応じて有効化してください。
            'csrf',     // ★CSRF (クロスサイトリクエストフォージェリ) 保護を有効化します。フォーム送信時にはCSRFトークンが必要になります。
            // 'invalidchars', // 不正な文字が含まれるリクエストを拒否します。
        ],
        'after' => [
            // 'honeypot',
            // 'secureheaders', // HTTPレスポンスヘッダにセキュリティ関連のヘッダを追加します。設定を適切に行う必要があります。
        ],
    ];

    /**
     * 特定のHTTPメソッド（GET, POSTなど）に対してのみ動作するフィルターエイリアスのリストです。
     *
     * @var array<string, list<string>>
     */
    public array $methods = [
        // 例: 'post' => ['CSRF保護だけでなく、追加のバリデーションフィルターなど']
    ];

    /**
     * URIパターンに基づいて、そのパターンの前処理・後処理として実行されるフィルターエイリアスのリストです。
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [
        // ★スタッフ/管理者向けエリア (例: /admin/ で始まるパス) へのアクセスに認証を要求します。
        // 'sessionauth' フィルターは、ユーザーがログインしていなければログインページへリダイレクトします。
        'sessionauth' => [
            'before' => [
                'admin/*', // '/admin/' で始まる全てのURIパスに適用
                // 特定のパスを除外したい場合: 'admin/*except:admin/public/*' のように記述できますが、
                // 除外設定が複雑になる場合は、Routes.phpでのフィルタリングの方が管理しやすいことがあります。
                // 今回は、Shieldの認証ルート (/login, /logout など) はこのフィルタの対象外になる想定です
                // (Routes.php の service('auth')->routes($routes); で適切に処理されるため)。
            ],
        ],

        // 以下は、より詳細なアクセス制御の例です (通常は Routes.php でのルート定義時に指定推奨)。
        // 特定のグループのみアクセスを許可する例:
        // 'group:admin' => [ // 'admin' グループのユーザーのみ許可
        //     'before' => [
        //         'admin/settings/*', // 例: 管理設定ページ
        //         'admin/users/*',    // 例: ユーザー管理ページ
        //     ]
        // ],
        // 特定のパーミッションを持つユーザーのみアクセスを許可する例:
        // 'permission:users.manage' => [
        //     'before' => [
        //         'admin/users/manageRoles', // 例: ロール管理ページ
        //     ]
        // ]
    ];
}