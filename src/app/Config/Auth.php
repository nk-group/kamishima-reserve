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

use CodeIgniter\Shield\Config\Auth as ShieldAuth;
use CodeIgniter\Shield\Authentication\Actions\ActionInterface;
use CodeIgniter\Shield\Authentication\AuthenticatorInterface;
use CodeIgniter\Shield\Authentication\Authenticators\AccessTokens;
use CodeIgniter\Shield\Authentication\Authenticators\HmacSha256;
use CodeIgniter\Shield\Authentication\Authenticators\JWT;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Authentication\Passwords\CompositionValidator;
use CodeIgniter\Shield\Authentication\Passwords\DictionaryValidator;
use CodeIgniter\Shield\Authentication\Passwords\NothingPersonalValidator;
use CodeIgniter\Shield\Authentication\Passwords\PwnedValidator;
use CodeIgniter\Shield\Authentication\Passwords\ValidatorInterface;
use CodeIgniter\Shield\Models\UserModel;

class Auth extends ShieldAuth
{
    /**
     * ////////////////////////////////////////////////////////////////////
     * AUTHENTICATION
     * ////////////////////////////////////////////////////////////////////
     */

    // Constants for Record Login Attempts. Do not change.
    public const RECORD_LOGIN_ATTEMPT_NONE    = 0; // Do not record at all
    public const RECORD_LOGIN_ATTEMPT_FAILURE = 1; // Record only failures
    public const RECORD_LOGIN_ATTEMPT_ALL     = 2; // Record all login attempts

    /**
     * --------------------------------------------------------------------
     * View files
     * --------------------------------------------------------------------
     */
    public array $views = [
        'login'                       => '\App\Views\Auth\login', // ★カスタムログインビューを指定
        'register'                    => '\CodeIgniter\Shield\Views\register',
        'layout'                      => '\CodeIgniter\Shield\Views\layout',
        'action_email_2fa'            => '\CodeIgniter\Shield\Views\email_2fa_show',
        'action_email_2fa_verify'     => '\CodeIgniter\Shield\Views\email_2fa_verify',
        'action_email_2fa_email'      => '\CodeIgniter\Shield\Views\Email\email_2fa_email',
        'action_email_activate_show'  => '\CodeIgniter\Shield\Views\email_activate_show',
        'action_email_activate_email' => '\CodeIgniter\Shield\Views\Email\email_activate_email',
        'magic-link-login'            => '\CodeIgniter\Shield\Views\magic_link_form',
        'magic-link-message'          => '\CodeIgniter\Shield\Views\magic_link_message',
        'magic-link-email'            => '\CodeIgniter\Shield\Views\Email\magic_link_email',
    ];

    /**
     * --------------------------------------------------------------------
     * Redirect URLs
     * --------------------------------------------------------------------
     */
    public array $redirects = [
        'register'          => '/',                // 新規登録後のリダイレクト先 (今回は登録無効なので影響は少ない)
        'login'             => '/admin/dashboard', // ★ログイン成功後のリダイレクト先
        'logout'            => '/login',           // ★ログアウト後のリダイレクト先
        'force_reset'       => '/',
        'permission_denied' => '/',
        'group_denied'      => '/',
    ];

    /**
     * --------------------------------------------------------------------
     * Authentication Actions
     * --------------------------------------------------------------------
     * Specifies the class that represents an action to take after
     * the user logs in or registers a new account at the site.
     */
    public array $actions = [
        'register' => null, // メールアクティベーションなどを無効化
        'login'    => null, // 2FAなどを無効化 (必要に応じて設定)
    ];

    /**
     * --------------------------------------------------------------------
     * Authenticators
     * --------------------------------------------------------------------
     */
    public array $authenticators = [
        // 'tokens'  => AccessTokens::class, // トークン認証は今回は使用しないためコメントアウト
        'session' => Session::class,      // ★セッションベース認証を主に使用
        // 'hmac'    => HmacSha256::class,   // HMAC認証は今回は使用しないためコメントアウト
        // 'jwt'     => JWT::class,          // JWT認証は今回は使用しないためコメントアウト
    ];

    /**
     * --------------------------------------------------------------------
     * Default Authenticator
     * --------------------------------------------------------------------
     */
    public string $defaultAuthenticator = 'session'; // ★デフォルト認証をセッションに

    /**
     * --------------------------------------------------------------------
     * Authentication Chain
     * --------------------------------------------------------------------
     */
    public array $authenticationChain = [
        'session', // ★認証チェーンもセッションのみ
        // 'tokens',
        // 'hmac',
        // 'jwt',
    ];

    /**
     * --------------------------------------------------------------------
     * Allow Registration
     * --------------------------------------------------------------------
     */
    public bool $allowRegistration = true; // ★ユーザー自身による新規登録を無効化

    /**
     * --------------------------------------------------------------------
     * Record Last Active Date
     * --------------------------------------------------------------------
     */
    public bool $recordActiveDate = true; // ユーザーの最終アクティブ日時を記録 (セキュリティ上推奨)

    /**
     * --------------------------------------------------------------------
     * Allow Magic Link Logins
     * --------------------------------------------------------------------
     */
    public bool $allowMagicLinkLogins = false; // ★マジックリンクログインを無効化 (パスワード認証のみのため)

    /**
     * --------------------------------------------------------------------
     * Magic Link Lifetime
     * --------------------------------------------------------------------
     */
    public int $magicLinkLifetime = HOUR; // 上記が無効なら影響なし

    /**
     * --------------------------------------------------------------------
     * Session Authenticator Configuration
     * --------------------------------------------------------------------
     */
    public array $sessionConfig = [
        'field'              => 'user',
        'allowRemembering'   => true,   // 「ログイン状態を記憶する」を許可
        'rememberCookieName' => 'remember',
        'rememberLength'     => 30 * DAY, // 記憶期間30日
    ];

    /**
     * --------------------------------------------------------------------
     * The validation rules for username
     * --------------------------------------------------------------------
     */
    public array $usernameValidationRules = [
        'label' => 'Auth.username',
        'rules' => [
            'required',
            'max_length[30]',
            'min_length[3]',
            'regex_match[/\A[a-zA-Z0-9\.]+\z/]',
        ],
    ]; // ユーザー名は使用しないが、デフォルトのまま残しておいても影響は少ない

    /**
     * --------------------------------------------------------------------
     * The validation rules for email
     * --------------------------------------------------------------------
     */
    public array $emailValidationRules = [
        'label' => 'Auth.email',
        'rules' => [
            'required',
            'max_length[254]',
            'valid_email',
        ],
    ]; // メールアドレスのバリデーションルール (デフォルトのまま)

    /**
     * --------------------------------------------------------------------
     * Minimum Password Length
     * --------------------------------------------------------------------
     */
    public int $minimumPasswordLength = 8; // 最小パスワード長 (デフォルトのまま)

    /**
     * --------------------------------------------------------------------
     * Password Check Helpers
     * --------------------------------------------------------------------
     */
    public array $passwordValidators = [
        CompositionValidator::class,
        NothingPersonalValidator::class,
        DictionaryValidator::class,
        // PwnedValidator::class, // 必要であればインストールして有効化
    ]; // パスワード検証ルール (デフォルトのまま)

    /**
     * --------------------------------------------------------------------
     * Valid login fields
     * --------------------------------------------------------------------
     */
    public array $validFields = [
        'email',      // ★メールアドレスのみでログイン
        // 'username', // usernameでのログインは無効化
    ];

    /**
     * --------------------------------------------------------------------
     * Additional Fields for "Nothing Personal"
     * --------------------------------------------------------------------
     */
    public array $personalFields = []; // (デフォルトのまま)

    /**
     * --------------------------------------------------------------------
     * Password / Username Similarity
     * --------------------------------------------------------------------
     */
    public int $maxSimilarity = 50; // (デフォルトのまま)

    /**
     * --------------------------------------------------------------------
     * Hashing Algorithm to use
     * --------------------------------------------------------------------
     */
    public string $hashAlgorithm = PASSWORD_DEFAULT; // (デフォルトのまま)

    /**
     * --------------------------------------------------------------------
     * ARGON2I/ARGON2ID Algorithm options
     * --------------------------------------------------------------------
     */
    public int $hashMemoryCost = 65536; // PASSWORD_ARGON2_DEFAULT_MEMORY_COST; (デフォルトのまま)
    public int $hashTimeCost = 4;     // PASSWORD_ARGON2_DEFAULT_TIME_COST; (デフォルトのまま)
    public int $hashThreads  = 1;     // PASSWORD_ARGON2_DEFAULT_THREADS; (デフォルトのまま)

    /**
     * --------------------------------------------------------------------
     * BCRYPT Algorithm options
     * --------------------------------------------------------------------
     */
    public int $hashCost = 12; // (デフォルトのまま)

    /**
     * ////////////////////////////////////////////////////////////////////
     * OTHER SETTINGS
     * ////////////////////////////////////////////////////////////////////
     */

    /**
     * --------------------------------------------------------------------
     * Customize the DB group used for each model
     * --------------------------------------------------------------------
     */
    public ?string $DBGroup = null; // (デフォルトのまま)

    /**
     * --------------------------------------------------------------------
     * Customize Name of Shield Tables
     * --------------------------------------------------------------------
     */
    public array $tables = [
        'users'             => 'users',
        'identities'        => 'auth_identities',
        'logins'            => 'auth_logins',
        'token_logins'      => 'auth_token_logins',
        'remember_tokens'   => 'auth_remember_tokens',
        'groups_users'      => 'auth_groups_users',
        'permissions_users' => 'auth_permissions_users',
    ]; // (デフォルトのまま)

    /**
     * --------------------------------------------------------------------
     * User Provider
     * --------------------------------------------------------------------
     */
    //public string $userProvider = UserModel::class;
    public string $userProvider = \App\Models\UserModel::class; // ★ カスタムUserModelを指定

    /**
     * Returns the URL that a user should be redirected
     * to after a successful login.
     */
    public function loginRedirect(): string
    {
        // $redirects 配列の設定が使用されるため、通常はこのメソッドの変更は不要
        $session = session();
        $url     = $session->getTempdata('beforeLoginUrl') ?? setting('Auth.redirects')['login'];

        return $this->getUrl($url);
    }

    /**
     * Returns the URL that a user should be redirected
     * to after they are logged out.
     */
    public function logoutRedirect(): string
    {
        // $redirects 配列の設定が使用されるため、通常はこのメソッドの変更は不要
        $url = setting('Auth.redirects')['logout'];

        return $this->getUrl($url);
    }

    /**
     * Returns the URL the user should be redirected to
     * after a successful registration.
     */
    public function registerRedirect(): string
    {
        // $redirects 配列の設定が使用されるため、通常はこのメソッドの変更は不要
        $url = setting('Auth.redirects')['register'];

        return $this->getUrl($url);
    }

    /**
     * Returns the URL the user should be redirected to
     * if force_reset identity is set to true.
     */
    public function forcePasswordResetRedirect(): string
    {
        // $redirects 配列の設定が使用されるため、通常はこのメソッドの変更は不要
        $url = setting('Auth.redirects')['force_reset'];

        return $this->getUrl($url);
    }

    /**
     * Returns the URL the user should be redirected to
     * if permission denied.
     */
    public function permissionDeniedRedirect(): string
    {
        // $redirects 配列の設定が使用されるため、通常はこのメソッドの変更は不要
        $url = setting('Auth.redirects')['permission_denied'];

        return $this->getUrl($url);
    }

    /**
     * Returns the URL the user should be redirected to
     * if group denied.
     */
    public function groupDeniedRedirect(): string
    {
        // $redirects 配列の設定が使用されるため、通常はこのメソッドの変更は不要
        $url = setting('Auth.redirects')['group_denied'];

        return $this->getUrl($url);
    }

    /**
     * Accepts a string which can be an absolute URL or
     * a named route or just a URI path, and returns the
     * full path.
     *
     * @param string $url an absolute URL or a named route or just URI path
     */
    protected function getUrl(string $url): string
    {
        // To accommodate all url patterns
        $final_url = '';

        switch (true) {
            case strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0: // URL begins with 'http' or 'https'. E.g. http://example.com
                $final_url = $url;
                break;

            case route_to($url) !== false: // URL is a named-route
                $final_url = rtrim(url_to($url), '/ ');
                break;

            default: // URL is a route (URI path)
                $final_url = rtrim(site_url($url), '/ ');
                break;
        }

        return $final_url;
    }
}