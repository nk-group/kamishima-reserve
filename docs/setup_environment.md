かしこまりました。承知いたしました。
今度こそ、ご指摘いただいた点をすべて修正し、手順11にフォルダ構造のリストを含め、**それ以外の部分は一切変更せず**、完全な手順書として最終版を作成します。

度重なる修正となり、大変申し訳ありません。以下が最終確定版となります。

---

# 車検予約管理システム 開発環境構築手順書 (完全最終確定版・改訂2)

**目的:** Windows 11 Pro (WSL2/Ubuntu) 環境に、Docker を利用して CodeIgniter 4 アプリケーション「車検予約管理システム」の開発環境を構築する。

**環境仕様:**

* **OS:** Windows 11 Pro (24H2 推奨) + WSL2/Ubuntu
* **コンテナ化:** Docker / Docker Compose
* **PHP:** 8.1.x
* **フレームワーク:** CodeIgniter 4.6.x
* **データベース:** MySQL 5.7.x + phpMyAdmin
* **認証:** Shield v1.1.x
* **PDF:** mPDF v8.1.x
* **フロントエンド:** Vite 5.4.x + Flatpickr v4.6.x + SASS
* **エディタ:** VSCode

---

## 手順1: 前提ツールの準備

### 1.1. WSL2 と Ubuntu の有効化

1.  **管理者としてPowerShellを開く:** スタートメニュー右クリック > ターミナル(管理者)。
2.  **WSLインストール:**
    ```bash
    wsl --install
    ```
3.  **PC再起動。**
4.  再起動後、Ubuntuが自動起動したら**ユーザー名とパスワードを設定**。
5.  **Ubuntuパッケージ最新化:** 設定完了後、開いたUbuntuターミナルで以下を実行。
    ```bash
    sudo apt update && sudo apt upgrade -y
    ```

### 1.2. Docker Desktop for Windows のインストール

1.  [Docker公式サイト](https://www.docker.com/products/docker-desktop/) からDocker Desktop for Windowsインストーラーをダウンロードし、実行。
2.  「**Use WSL 2 based engine**」にチェックを入れてインストール。
3.  インストール後、必要ならPC再起動し、Docker Desktopを起動。
4.  **WSL2統合設定:** Docker Desktopの設定 (Settings) > Resources > WSL Integration で、「Enable integration with my default WSL distro」と「Ubuntu」がオンになっていることを確認し、「Apply & Restart」。

### 1.3. VSCode と Remote - WSL 拡張機能のインストール

1.  [VSCode公式サイト](https://code.visualstudio.com/) からVSCodeをダウンロードし、インストール。
2.  VSCodeを起動し、拡張機能パネルで `WSL` を検索、「**Remote - WSL**」(Microsoft提供) をインストール。

## 手順2: プロジェクトフォルダの準備

**WSL2ファイルシステム内** にプロジェクトフォルダを作成します。

1.  **WSL2/Ubuntuターミナルを開く。**
2.  **開発用ディレクトリ作成・移動:** (例: ホームディレクトリ下に `develop` を作成)
    ```bash
    mkdir -p ~/develop
    cd ~/develop
    ```
3.  **プロジェクトフォルダ作成・移動:**
    ```bash
    mkdir kamishima-reserve
    cd kamishima-reserve
    ```
    (`pwd` コマンドで `/home/あなたのUbuntuユーザー名/develop/kamishima-reserve` 等を確認)

4.  **VSCodeで開く:**
    ```bash
    code .
    ```

## 手順3: Docker環境の構築

### 3.1. `docker-compose.yml` の作成

プロジェクトルート (`kamishima-reserve`) に `docker-compose.yml` ファイルを新規作成し、以下を記述。

```yaml
# kamishima-reserve/docker-compose.yml
services:
  php:
    build:
      context: ./.docker/php
      args:
        PHP_VERSION: '8.1'
        NODE_MAJOR: '20'
    container_name: kamishima_php
    volumes:
      - ./src:/var/www/html
      - ./.docker/php/php.ini:/usr/local/etc/php/conf.d/custom.ini
    working_dir: /var/www/html
    environment:
      TZ: 'Asia/Tokyo'
    depends_on:
      - db
    networks:
      - app-network

  nginx:
    image: nginx:1.25
    container_name: kamishima_nginx
    ports:
      - "8080:80"
    volumes:
      - ./src:/var/www/html
      - ./.docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - php
    networks:
      - app-network

  db:
    image: mysql:5.7
    container_name: kamishima_db
    restart: always
    environment:
      MYSQL_DATABASE: ${DB_DATABASE:-kamishima_db}
      MYSQL_USER: ${DB_USERNAME:-user}
      MYSQL_PASSWORD: ${DB_PASSWORD:-password}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD:-rootpassword}
      TZ: 'Asia/Tokyo'
    volumes:
      - db-data:/var/lib/mysql
      - ./.docker/mysql/my.cnf:/etc/mysql/conf.d/my.cnf
    ports:
      - "33066:3306"
    networks:
      - app-network

  phpmyadmin:
    image: phpmyadmin/phpmyadmin:5.2
    container_name: kamishima_phpmyadmin
    restart: always
    environment:
      PMA_HOST: db
      PMA_PORT: 3306
      PMA_USER: root
      PMA_PASSWORD: ${DB_ROOT_PASSWORD:-rootpassword}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD:-rootpassword}
      UPLOAD_LIMIT: 1G
      MEMORY_LIMIT: 512M
    ports:
      - "8081:80"
    depends_on:
      - db
    networks:
      - app-network

volumes:
  db-data:

networks:
  app-network:
    driver: bridge
```

### 3.2. `.env` ファイルの作成 (Docker Compose用)

プロジェクトルートに `.env` を新規作成。

```ini
# kamishima-reserve/.env (Docker Compose用)
# Git管理には含めない (.gitignore に追加)

# MySQL Settings
DB_DATABASE=kamishima_db
DB_USERNAME=user
DB_PASSWORD=password
DB_ROOT_PASSWORD=rootpassword
```

### 3.3. PHP用 Dockerfile の作成

1.  プロジェクトルートに `.docker` フォルダを作成: `mkdir .docker`
2.  `.docker` 内に `php` フォルダを作成: `mkdir .docker/php`
3.  `.docker/php` 内に `Dockerfile` を新規作成し、以下を記述:

```dockerfile
# kamishima-reserve/.docker/php/Dockerfile

# ベースイメージ
ARG PHP_VERSION=8.1
FROM php:${PHP_VERSION}-fpm

# ビルド引数
ARG NODE_MAJOR=20

# 環境変数
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/composer \
    TZ=Asia/Tokyo

# タイムゾーン設定
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# 必要なパッケージのインストール (mPDF 用フォント含む)
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    zip \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    curl \
    fonts-ipafont-gothic \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# PHP拡張機能のインストールと有効化
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    intl \
    mbstring \
    pdo_mysql \
    mysqli \
    zip \
    opcache

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.jsとnpmのインストール
RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_MAJOR}.x | bash - \
    && apt-get update && apt-get install -y nodejs --no-install-recommends \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Xdebug のインストール
RUN pecl install xdebug && docker-php-ext-enable xdebug

# (オプション) PHP-FPM実行ユーザーのUID/GIDをホストに合わせる場合
# ホストの `id -u`/`id -g` を確認し、必要なら下のID(例:1000)を書き換えてコメント解除
# RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# 作業ディレクトリ設定
WORKDIR /var/www/html

# コンテナ起動時のデフォルトコマンド
CMD ["php-fpm"]
```

### 3.4. PHP設定ファイル (`php.ini`) の作成

1.  `.docker/php` 内に `php.ini` を新規作成し、以下を記述:

```ini
; kamishima-reserve/.docker/php/php.ini
engine = On
short_open_tag = Off
precision = 14
output_buffering = 4096
zlib.output_compression = Off
implicit_flush = Off
unserialize_callback_func =
serialize_precision = -1
disable_functions = pcntl_alarm,pcntl_fork,pcntl_waitpid,pcntl_wait,pcntl_wifexited,pcntl_wifstopped,pcntl_wifsignaled,pcntl_wexitstatus,pcntl_wtermsig,pcntl_wstopsig,pcntl_signal,pcntl_signal_dispatch,pcntl_get_last_error,pcntl_strerror,pcntl_sigprocmask,pcntl_sigwaitinfo,pcntl_sigtimedwait,pcntl_exec,pcntl_getpriority,pcntl_setpriority,
disable_classes =
realpath_cache_size = 4096k
realpath_cache_ttl = 600
display_errors = On
display_startup_errors = On
error_reporting = E_ALL
log_errors = On
error_log = /dev/stderr
ignore_repeated_errors = Off
ignore_repeated_source = Off
report_memleaks = On
track_errors = Off
html_errors = On
variables_order = "GPCS"
request_order = "GP"
register_argc_argv = Off
auto_globals_jit = On
post_max_size = 100M
default_mimetype = "text/html"
default_charset = "UTF-8"
include_path = ".:/usr/local/lib/php"
doc_root =
user_dir =
enable_dl = Off
file_uploads = On
upload_max_filesize = 100M
max_file_uploads = 20
allow_url_fopen = On
allow_url_include = Off
expose_php = Off
max_execution_time = 30
max_input_time = 60
memory_limit = 256M
date.timezone = Asia/Tokyo
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=0
opcache.validate_timestamps=1
opcache.save_comments=1
[Xdebug]
;zend_extension=xdebug.so ; Enabled via docker-php-ext-enable in Dockerfile
xdebug.mode=develop,debug
xdebug.start_with_request=trigger
xdebug.discover_client_host=0
xdebug.client_host=host.docker.internal
xdebug.client_port=9003
xdebug.log=/tmp/xdebug_remote.log
xdebug.idekey=VSCODE
```

### 3.5. Nginx設定ファイル (`default.conf`) の作成

1.  `.docker` 内に `nginx` フォルダを作成: `mkdir .docker/nginx`
2.  `.docker/nginx` 内に `default.conf` を新規作成し、以下を記述:

```nginx
# kamishima-reserve/.docker/nginx/default.conf
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;
    index index.php index.html index.htm;
    charset utf-8;
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;
    location / { try_files $uri $uri/ /index.php?$query_string; }
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    # error_page 404 /index.php;
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
    location ~ /\.ht { deny all; }
    location ~ /vendor/ { deny all; }
}
```

### 3.6. MySQL設定ファイル (`my.cnf`) の作成

1.  `.docker` 内に `mysql` フォルダを作成: `mkdir .docker/mysql`
2.  `.docker/mysql` 内に `my.cnf` を新規作成し、以下を記述:

```ini
# kamishima-reserve/.docker/mysql/my.cnf
[mysqld]
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
default-time-zone = '+09:00'
innodb_ft_min_token_size = 2
ngram_token_size = 2
[client]
default-character-set = utf8mb4
[mysql]
default-character-set = utf8mb4
[mysqldump]
default-character-set = utf8mb4
```

### 3.7. アプリケーション用フォルダ作成

```bash
# WSL2/Ubuntuターミナル (kamishima-reserve ディレクトリ) で実行
mkdir docs
mkdir assets
mkdir assets/css
mkdir assets/js
mkdir assets/js/components
mkdir assets/images
mkdir src
```

## 手順4: Dockerコンテナの起動

1.  **WSL2/Ubuntuターミナルで `kamishima-reserve` ディレクトリにいることを確認。**
2.  **コンテナをビルド・起動:**
    ```bash
    docker compose up -d --build
    ```
3.  **起動状態を確認:**
    ```bash
    docker compose ps
    ```
    (すべての Status が `running` であること)
4.  **アクセスポイント:**
    * Webアプリ: `http://localhost:8080`
    * phpMyAdmin: `http://localhost:8081` (user: `root`, pass: `rootpassword`)

## 手順5: CodeIgniterプロジェクトの作成と設定

### 5.1. PHPコンテナに入る

```bash
docker compose exec php bash
```

### 5.2. ComposerでCodeIgniterをインストール

```bash
# コンテナ内 (/var/www/html) で実行
composer create-project codeigniter4/appstarter:^4.6 . --remove-vcs --no-install
composer install --optimize-autoloader
```

### 5.3. CodeIgniter用 `.env` ファイルの準備と設定

1.  **`.env` ファイルをコピー:**
    ```bash
    # コンテナ内 (/var/www/html) で実行
    cp env .env
    ```
2.  **【重要】所有者の変更:**
    ```bash
    exit # コンテナから抜ける
    sudo chown $(whoami):$(whoami) src/.env # ホスト側で所有者変更
    ```
3.  **`.env` ファイルの編集:** VSCodeで `kamishima-reserve/src/.env` を開き、以下を設定します。

    ```ini
    # src/.env
    # CodeIgniter 4 Environment Configuration

    #--------------------------------------------------------------------
    # ENVIRONMENT
    #--------------------------------------------------------------------
    CI_ENVIRONMENT = development

    #--------------------------------------------------------------------
    # APP (Config/App.php)
    #--------------------------------------------------------------------
    app.baseURL = 'http://localhost:8080/'
    # app.forceGlobalSecureRequests = false

    #--------------------------------------------------------------------
    # SESSION (Config/Session.php)
    #--------------------------------------------------------------------
    # ★ DatabaseHandler を使用 ★
    session.driver = 'CodeIgniter\Session\Handlers\DatabaseHandler'
    session.savePath = 'ci_sessions'
    session.cookieName = 'ci_session_kamishima' # クォーテーション付き
    session.expiration = 7200                   # クォーテーションなし
    session.matchIP = false                     # クォーテーションなし
    session.timeToUpdate = 300                  # クォーテーションなし
    session.regenerateDestroy = false           # クォーテーションなし

    #--------------------------------------------------------------------
    # COOKIE (Config/App.php)
    #--------------------------------------------------------------------
    app.cookiePrefix = ''                       # クォーテーション付き (空文字列のため)
    app.cookieDomain = ''                       # クォーテーション付き (空文字列のため)
    app.cookiePath = '/'                        # クォーテーション付き
    app.cookieSecure = false                    # クォーテーションなし
    app.cookieHTTPOnly = true                   # クォーテーションなし
    app.cookieSameSite = 'Lax'                  # クォーテーション付き

    #--------------------------------------------------------------------
    # DATABASE (Config/Database.php)
    #--------------------------------------------------------------------
    database.default.hostname = 'db'
    database.default.database = 'kamishima_db'
    database.default.username = 'user'
    database.default.password = 'password'
    database.default.DBDriver = 'MySQLi'
    # database.default.DBPrefix = ''
    database.default.port = 3306

    #--------------------------------------------------------------------
    # Security (Config/Security.php)
    #--------------------------------------------------------------------
    # security.csrfProtection = true
    # security.tokenName = 'csrf_token_name'
    # security.cookieName = 'csrf_cookie_name'
    # security.expires = 7200
    # security.regenerate = true
    # security.redirect = true
    # security.sameSite = 'Lax'
    ```

### 5.4. `writable` ディレクトリのパーミッション設定

```bash
# ホストOSのターミナル (kamishima-reserve ディレクトリ) で実行
sudo chown -R $(whoami):$(whoami) src/writable/
docker compose exec php mkdir -p /var/www/html/writable/session
docker compose exec php chmod -R 777 /var/www/html/writable/
```

### 5.5. `app/Config/Session.php` の確認

このファイルは**編集せず**、`$driver = FileHandler::class`, `$savePath = WRITEPATH . 'session'` などが定義された**デフォルトの状態**のままであることを確認します。

### 5.6. CodeIgniter動作確認

`http://localhost:8080` にアクセスし、ウェルカムページが表示されることを確認。（セッションテーブル作成前のためエラーが出る場合があります）

## 手順6: ライブラリのインストールと設定

### 6.1. Shield (認証ライブラリ)

1.  **PHPコンテナに入る:** `docker compose exec php bash`
2.  **Composerでインストール:**
    ```bash
    # コンテナ内 (/var/www/html)
    composer require codeigniter4/shield:^1.1
    ```
3.  **設定ファイル発行:**
    ```bash
    # コンテナ内 (/var/www/html)
    php spark shield:setup
    # Config/Auth.php? -> y
    # Config/AuthGroups.php? -> y
    # Language files? -> n
    # Run migrations? -> n
    ```
4.  **セッション/Shield用マイグレーションファイル作成:**
    ```bash
    # コンテナ内 (/var/www/html)
    php spark make:migration CreateCiSessionsTable
    ```
5.  **所有者修正 & マイグレーションファイル編集:**
    ```bash
    exit # コンテナから抜ける
    sudo chown -R $(whoami):$(whoami) src/ # src 以下すべてを修正
    ```
    VSCodeで作成された `src/app/Database/Migrations/*_CreateCiSessionsTable.php` を開き、以下の内容を記述します。
    ```php
    <?php namespace App\Database\Migrations;
    use CodeIgniter\Database\Migration;
    class CreateCiSessionsTable extends Migration {
        public function up() {
            $this->forge->addField([
                'id' => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => false],
                'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => false],
                'timestamp' => ['type' => 'TIMESTAMP', 'default' => null, 'null' => false],
                'data' => ['type' => 'BLOB', 'null' => false],
            ]);
            if ($this->db->DBDriver === 'MySQLi') {
                 $this->forge->addField("`timestamp` timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL");
            }
            $this->forge->addKey('id', true);
            $this->forge->addKey('timestamp');
            $this->forge->createTable('ci_sessions', true); // テーブル名確認
        }
        public function down() { $this->forge->dropTable('ci_sessions', true); }
    }
    ```
6.  **マイグレーション実行:**
    ```bash
    # ホストOSから実行
    docker compose exec php php spark migrate --all
    ```
    phpMyAdminで `ci_sessions` と `auth_...` テーブル作成を確認。
7.  **フィルター設定:** VSCodeで `src/app/Config/Filters.php` を編集し、以下を記述します。
    ```php
    <?php namespace Config;
    use CodeIgniter\Config\BaseConfig;
    use CodeIgniter\Filters\CSRF;
    use CodeIgniter\Filters\DebugToolbar;
    use CodeIgniter\Filters\Honeypot;
    use CodeIgniter\Filters\InvalidChars;
    use CodeIgniter\Filters\SecureHeaders;
    use CodeIgniter\Shield\Filters\SessionAuth;
    use CodeIgniter\Shield\Filters\TokenAuth;
    use CodeIgniter\Shield\Filters\ChainAuth;
    use CodeIgniter\Shield\Filters\AuthRates;
    use CodeIgniter\Shield\Filters\GroupFilter;
    use CodeIgniter\Shield\Filters\PermissionFilter;

    class Filters extends BaseConfig {
        public array $aliases = [
            'csrf'          => CSRF::class,
            'toolbar'       => DebugToolbar::class,
            'honeypot'      => Honeypot::class,
            'invalidchars'  => InvalidChars::class,
            'secureheaders' => SecureHeaders::class,
            'session'       => SessionAuth::class,
            'tokens'        => TokenAuth::class,
            'chain'         => ChainAuth::class,
            'auth-rates'    => AuthRates::class,
            'group'         => GroupFilter::class,
            'permission'    => PermissionFilter::class,
        ];
        public array $globals = [
            'before' => ['honeypot','csrf','auth-rates',],
            'after'  => ['toolbar',],
        ];
        public array $methods = [];
        public array $filters = [
            'session' => ['before' => ['mypage/*']],
        ];
    }
    ```
8.  **所有者修正:**
    ```bash
    sudo chown -R $(whoami):$(whoami) src/ # src 以下すべてを修正
    ```

### 6.2. mPDF (PDF生成ライブラリ)

1.  **PHPコンテナに入る:** `docker compose exec php bash`
2.  **Composerでインストール:**
    ```bash
    # コンテナ内 (/var/www/html)
    composer require mpdf/mpdf:^8.1
    ```
3.  **mPDF用ヘルパー作成:** VSCodeで `src/app/Helpers/mpdf_helper.php` を新規作成し、以下を記述します。
    ```php
     <?php // src/app/Helpers/mpdf_helper.php
     use Mpdf\Mpdf; use Mpdf\Config\ConfigVariables; use Mpdf\Config\FontVariables; use Mpdf\MpdfException;
     if (! function_exists('generate_pdf_string')) {
         function generate_pdf_string(string $html, string $paperSize = 'A4', array $config = []): string {
             try {
                 $defaultConfig = (new ConfigVariables())->getDefaults(); $fontDirs = $defaultConfig['fontDir'];
                 $defaultFontConfig = (new FontVariables())->getDefaults(); $fontData = $defaultFontConfig['fontdata'];
                 $tempDir = WRITEPATH . 'cache/mpdf';
                 $mpdfConfig = array_merge([
                     'mode' => 'utf-8', 'format' => $paperSize,
                     'fontDir' => array_merge($fontDirs, ['/usr/share/fonts/opentype/ipafont-gothic',]), // ★正しいパス★
                     'fontdata' => $fontData + ['ipagp' => [ 'R' => 'ipagp.ttf', 'useOTL' => 0xFF, ],],
                     'default_font' => 'ipagp', 'tempDir' => $tempDir,
                 ], $config);
                 if (!is_dir($mpdfConfig['tempDir']) && !mkdir($mpdfConfig['tempDir'], 0777, true) && !is_dir($mpdfConfig['tempDir'])) {
                     throw new \RuntimeException(sprintf('Directory "%s" was not created', $mpdfConfig['tempDir'])); }
                 $mpdf = new Mpdf($mpdfConfig);
                 $mpdf->WriteHTML($html);
                 return $mpdf->Output('', 'S'); // ★ 文字列で返す ★
             } catch (MpdfException $e) {
                 log_message('error', "[mPDF String] PDF Generation Error: {$e->getMessage()} in {$e->getFile()}:{$e->getLine()}"); throw $e;
             } } }
     // (オプション) 直接出力する関数も必要なら定義する
     // if (! function_exists('generate_pdf')) { ... }
     ```
4.  **オートロード設定:** VSCodeで `src/app/Config/Autoload.php` を開き、`$helpers` 配列を以下のように修正します。
    ```php
    <?php namespace Config;
    use CodeIgniter\Config\BaseConfig;
    class Autoload extends BaseConfig {
        public array $psr4 = [ /* ... */ ];
        public array $classmap = [];
        public array $files = [];
        public array $helpers = ['url', 'form', 'mpdf']; // 'mpdf' を追加
        public function __construct() { parent::__construct(); /* ... */ }
    }
    ```
5.  **所有者修正:**
    ```bash
    exit # コンテナから抜ける
    sudo chown -R $(whoami):$(whoami) src/ # src 以下すべてを修正
    ```

### 6.3. Flatpickr (日付UIコンポーネント)

作業は**ホストOSのターミナル** (`kamishima-reserve` ディレクトリ) で。

1.  **`package.json` 初期化 & type設定:**
    ```bash
    npm init -y
    ```
    VSCodeで `package.json` を開き、以下のように `"type": "module"` を追加します。
    ```json
    {
      "name": "kamishima-reserve",
      "version": "1.0.0",
      "type": "module",
      "description": "",
      "main": "index.js",
      "scripts": {
        "test": "echo \"Error: no test specified\" && exit 1"
      },
      "keywords": [],
      "author": "",
      "license": "ISC"
    }
    ```
2.  **Vite/関連パッケージインストール:**
    ```bash
    npm install --save-dev vite@^5.4 laravel-vite-plugin@^1.0 sass
    ```
3.  **Flatpickrインストール:**
    ```bash
    npm install flatpickr@^4.6
    ```

### 手順7: Viteの設定

作業は主にホストOS側で。

#### 7.1. `vite.config.js` の作成

プロジェクトルートに `vite.config.js` を新規作成し、以下を記述。

```javascript
// kamishima-reserve/vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
export default defineConfig({
    plugins: [ laravel({
            input: ['assets/js/app.js','assets/css/app.scss',],
            publicDirectory: 'src/public', buildDirectory: 'build-vite', refresh: true, })],
    server: { host: '0.0.0.0', port: 5173, hmr: { host: 'localhost', }, }, });
```

#### 7.2. エントリーポイントファイルの作成

1.  `assets/js/app.js` を新規作成し、以下を記述。
    ```javascript
    // kamishima-reserve/assets/js/app.js
    import 'flatpickr/dist/flatpickr.min.css';
    import './components/flatpickr-init';
    console.log('Vite app loaded!');
    ```
2.  `assets/css/app.scss` を新規作成し、以下を記述。
    ```scss
    // kamishima-reserve/assets/css/app.scss
    $primary-color: #0d6efd;
    body { margin: 0; font-family: system-ui, sans-serif; color: #212529; background-color: #f8f9fa; }
    a { color: $primary-color; text-decoration: none; &:hover { text-decoration: underline; } }
    .container { max-width: 1140px; margin: 1.5rem auto; padding: 1.5rem; background-color: #fff; border-radius: 0.3rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    ```

#### 7.3. `.gitignore` の設定

プロジェクトルートの `.gitignore` を開き、以下の内容になっているか確認。

```gitignore
# kamishima-reserve/.gitignore
# Docker / Environment
.env
.DS_Store
# IDE / Editor
.vscode/
*.swp
*~
# Dependencies / Build artifacts
/node_modules/
/src/vendor/
/src/public/build-vite/
# CodeIgniter Writable directory contents
/src/writable/*
!/src/writable/.gitkeep
# Logs
npm-debug.log*
yarn-error.log*
*.log
# OS generated files
Thumbs.db
Desktop.ini
```

#### 7.4. Viteヘルパーの作成

1.  `src/app/Helpers/vite_helper.php` を新規作成し、以下を記述。
    ```php
    <?php // kamishima-reserve/src/app/Helpers/vite_helper.php
    use CodeIgniter\Config\Services;
    if (! function_exists('vite_asset')) {
        function vite_asset(string $entrypoint, ?string $buildPath = 'build-vite', bool $returnTag = true): string|array {
            static $manifest = null; static $isDev = null; $config = config('Vite');
            $viteServer = rtrim($config->devServer ?? 'http://localhost:5173', '/');
            $buildPath = trim($config->buildPath ?? $buildPath ?? 'build-vite', '/');
            if ($isDev === null) { $isDev = ENVIRONMENT === 'development' && $config->forceDevServer; }
            $results = ['css' => [], 'js' => [],];
            if ($isDev) {
                $base = $viteServer . '/'; $results['js'][] = $base . '@vite/client'; $results['js'][] = $base . $entrypoint;
            } else {
                if ($manifest === null) {
                    $manifestPath = FCPATH . $buildPath . '/manifest.json';
                    if (! file_exists($manifestPath)) { throw new \RuntimeException("[Vite] Manifest not found at: {$manifestPath}. Run 'npm run build'."); }
                    $manifestJson = file_get_contents($manifestPath); if ($manifestJson === false) { throw new \RuntimeException("[Vite] Cannot read manifest file: {$manifestPath}."); }
                    $manifest = json_decode($manifestJson, true); if ($manifest === null) { throw new \RuntimeException("[Vite] Unable to parse manifest file: {$manifestPath}. Error: " . json_last_error_msg()); } }
                if (! isset($manifest[$entrypoint])) { throw new \RuntimeException("[Vite] Entrypoint '{$entrypoint}' not found in manifest: {$manifestPath}"); }
                $asset = $manifest[$entrypoint]; $baseUrl = base_url($buildPath . '/');
                if (!empty($asset['file'])) { $results['js'][] = $baseUrl . $asset['file']; }
                if (!empty($asset['css'])) { foreach ($asset['css'] as $cssFile) { $results['css'][] = $baseUrl . $cssFile; } }
                if (!empty($asset['dynamicImports'])) {
                    foreach ($asset['dynamicImports'] as $importName) { if (isset($manifest[$importName]['css'])) {
                        foreach ($manifest[$importName]['css'] as $cssFile) { $results['css'][] = $baseUrl . $cssFile; } } } }
                $results['css'] = array_unique($results['css']); }
            if ($returnTag) {
                $html = ''; foreach ($results['css'] as $cssUrl) { $html .= '<link rel="stylesheet" href="' . esc($cssUrl, 'attr') . '">' . "\n"; }
                foreach ($results['js'] as $jsUrl) { $html .= '<script type="module" src="' . esc($jsUrl, 'attr') . '"></script>' . "\n"; } return $html;
            } else { return $results; } } }
    ```
2.  `src/app/Config/Vite.php` を新規作成し、以下を記述。
    ```php
    <?php // kamishima-reserve/src/app/Config/Vite.php
    namespace Config; use CodeIgniter\Config\BaseConfig;
    class Vite extends BaseConfig {
        public string $devServer = 'http://localhost:5173';
        public string $buildPath = 'build-vite';
        public bool $forceDevServer = true;
    }
    ```
3.  `src/app/Config/Autoload.php` の `$helpers` 配列を以下のように修正。
    ```php
    <?php namespace Config;
    use CodeIgniter\Config\BaseConfig;
    class Autoload extends BaseConfig {
        public array $psr4 = [ /* ... */ ];
        public array $classmap = [];
        public array $files = [];
        public array $helpers = ['url', 'form', 'mpdf', 'vite']; // 'vite' を追加
        public function __construct() { parent::__construct(); /* ... */ }
    }
    ```
4.  **所有者修正:** `sudo chown -R $(whoami):$(whoami) src/`

#### 7.5. npmスクリプトの設定

`package.json` の `scripts` セクションを以下のように編集・確認。

```json
// kamishima-reserve/package.json
  // ... (前の部分はそのまま) ...
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview",
    "test": "echo \"Error: no test specified\" && exit 1"
  },
  // ... (後の部分はそのまま) ...
```

#### 7.6. Flatpickr初期化JS作成

`assets/js/components/flatpickr-init.js` を新規作成し、以下を記述。

```javascript
// kamishima-reserve/assets/js/components/flatpickr-init.js
import flatpickr from 'flatpickr';
import { Japanese } from "flatpickr/dist/l10n/ja.js";

document.addEventListener('DOMContentLoaded', function() {
    flatpickr(".flatpickr-date", { locale: Japanese, dateFormat: "Y-m-d", allowInput: true, });
    flatpickr(".flatpickr-datetime", { locale: Japanese, enableTime: true, dateFormat: "Y-m-d H:i", time_24hr: true, allowInput: true, minuteIncrement: 15, });
    console.log('Flatpickr initialized.');
});
```

### 手順8: ベースクラス / ヘルパーの作成

#### 8.1. 認証チェック用ベースコントローラー (オプション)

`src/app/Controllers/AuthenticatedController.php` を新規作成し、以下を記述。

```php
<?php // kamishima-reserve/src/app/Controllers/AuthenticatedController.php
namespace App\Controllers;
use CodeIgniter\HTTP\RequestInterface; use CodeIgniter\HTTP\ResponseInterface; use Psr\Log\LoggerInterface;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
class AuthenticatedController extends BaseController {
    protected $helpers = ['auth', 'setting', 'form', 'url', 'vite', 'mpdf'];
    /** @var \CodeIgniter\Shield\Entities\User|null */
    protected $currentUser;
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger) {
        parent::initController($request, $response, $logger);
        /** @var Session $auth */
        $auth = service('auth')->getAuthenticator('session');
        $this->currentUser = $auth->user();
        // Note: Authentication itself should be handled by filters (e.g., 'session').
    }
}
```
**所有者修正:** `sudo chown $(whoami):$(whoami) src/app/Controllers/AuthenticatedController.php`

### 手順9: VSCodeの設定と推奨プラグイン

#### 9.1. 推奨VSCodeプラグイン

* Remote - WSL
* PHP Intelephense
* PHP Debug
* Docker
* DotENV
* EditorConfig for VS Code
* Prettier - Code formatter
* ESLint
* Stylelint
* GitLens — Git supercharged
* Composer

#### 9.2. デバッグ設定 (Xdebug)

1.  Dockerfile と php.ini は設定済み。
2.  VSCode の「実行とデバッグ」ビューで `.vscode/launch.json` を作成（または開く）し、以下の内容を設定します。

    ```json
    // kamishima-reserve/.vscode/launch.json
    {
        "version": "0.2.0",
        "configurations": [
            {
                "name": "Listen for Xdebug (Docker)",
                "type": "php",
                "request": "launch",
                "port": 9003,
                "pathMappings": {
                    "/var/www/html": "${workspaceFolder}/src"
                },
                "hostname": "0.0.0.0",
                "ignore": [
                    "**/vendor/**",
                    "**/writable/**"
                ]
            }
        ]
    }
    ```

### 手順10: テスト実行 (簡単なサンプル)

#### 10.1. ルーティングの設定

VSCodeで `src/app/Config/Routes.php` を開き、以下の内容に編集します。

```php
<?php namespace Config; use CodeIgniter\Router\RouteCollection;
/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
service('auth')->routes($routes); // Shield認証ルート
$routes->group('test', static function ($routes) {
    $routes->get('/', 'TestController::index');
    $routes->get('pdf', 'TestController::generatePdf'); });
$routes->get('/mypage', 'MyPageController::index', ['filter' => 'session']); // ログイン必須
```
**所有者修正:** `sudo chown $(whoami):$(whoami) src/app/Config/Routes.php`

#### 10.2. テスト用コントローラーの作成

1.  `src/app/Controllers/TestController.php` を新規作成し、以下を記述します。
    ```php
    <?php namespace App\Controllers;
    class TestController extends BaseController {
        protected $helpers = ['url', 'form', 'vite', 'mpdf', 'auth'];
        public function index() {
            $data = ['pageTitle' => '開発環境テスト', 'isLoggedIn' => auth()->loggedIn(), 'user' => auth()->user(),];
            return view('test/index', $data);
        }
        public function generatePdf() {
            $data = ['title' => 'テストPDF', 'content' => 'mPDF生成テスト。時刻: ' . date('Y-m-d H:i:s'),];
            $html = view('test/pdf_template', $data);
            try {
                $pdfData = generate_pdf_string($html, 'A4'); // 文字列で取得
                return $this->response // レスポンスオブジェクトで返す
                    ->setStatusCode(200)->setContentType('application/pdf')
                    ->setBody($pdfData);
            } catch (\Exception $e) {
                log_message('error', 'PDF Generation Controller Error: ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setBody('PDF生成中にエラーが発生しました。ログを確認してください。');
            }
        }
    }
    ```
2.  `src/app/Controllers/MyPageController.php` を新規作成し、以下を記述します。
    ```php
    <?php namespace App\Controllers;
    class MyPageController extends BaseController {
        protected $helpers = ['auth', 'vite', 'url'];
        public function index() {
            $user = auth()->user(); if ($user === null) { return redirect()->to('login')->with('error', 'ログインしてください。'); }
            $data = ['pageTitle' => 'マイページ', 'user' => $user,];
            return view('mypage/index', $data);
        }
    }
    ```
**所有者修正:** `sudo chown -R $(whoami):$(whoami) src/app/Controllers/`

#### 10.3. テスト用ビューファイルの作成

以下のファイルを指定されたパスに新規作成し、それぞれの内容を記述します。

1.  **`src/app/Views/layout/default.php`**
    ```php
    <!DOCTYPE html>
    <html lang="<?= service('request')->getLocale() ?? 'ja' ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $this->renderSection('title', '車検予約システム') ?></title>
        <?= vite_asset('assets/css/app.scss') ?>
        <?= $this->renderSection('header_scripts') ?>
        <style>
            body { display: flex; flex-direction: column; min-height: 100vh; margin: 0; font-family: sans-serif; background-color: #f8f9fa; }
            .main-header { background-color: #e9ecef; padding: 0.8rem 1.5rem; border-bottom: 1px solid #dee2e6; flex-shrink: 0; }
            .main-nav a { margin-right: 1rem; text-decoration: none; color: #007bff; } .main-nav a:hover { text-decoration: underline; }
            .main-content { flex-grow: 1; max-width: 1140px; width: 100%; padding: 1.5rem; margin-right: auto; margin-left: auto; box-sizing: border-box; }
            .main-footer { background-color: #e9ecef; padding: 0.8rem 1.5rem; margin-top: auto; text-align: center; font-size: 0.9em; color: #6c757d; border-top: 1px solid #dee2e6; flex-shrink: 0; }
            .alert { padding: 0.8rem 1rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: 0.25rem; }
            .alert-success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; } .alert-danger { color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }
            .alert-info { color: #055160; background-color: #cff4fc; border-color: #b6effb; } .alert-warning { color: #664d03; background-color: #fff3cd; border-color: #ffecb5; }
        </style>
    </head>
    <body>
        <header class="main-header">
            <nav class="main-nav">
                <a href="<?= site_url('/') ?>"><strong>車検予約システム</strong></a> <span style="margin-left: 2rem;"></span>
                <a href="<?= site_url('/test') ?>">動作テスト</a>
                <?php if (auth()->loggedIn()): ?>
                    <a href="<?= site_url('/mypage') ?>">マイページ</a>
                    <a href="<?= url_to('logout') ?>" style="float: right;">ログアウト (<?= esc(auth()->user()->username ?? '') ?>)</a>
                <?php else: ?>
                    <a href="<?= url_to('register') ?>" style="float: right; margin-left: 1rem;">ユーザー登録</a>
                    <a href="<?= url_to('login') ?>" style="float: right;">ログイン</a>
                <?php endif; ?>
            </nav>
        </header>
        <main class="main-content">
            <?= $this->include('layout/partials/alerts', ['dismissible' => false], true) ?>
            <?= $this->renderSection('content') ?>
        </main>
        <footer class="main-footer"> &copy; <?= date('Y') ?> Kamishima Motors Inc. All Rights Reserved. </footer>
        <?= vite_asset('assets/js/app.js') ?>
        <?= $this->renderSection('footer_scripts') ?>
    </body>
    </html>
    ```

2.  **`src/app/Views/layout/partials/alerts.php`** (ディレクトリも作成: `mkdir -p src/app/Views/layout/partials/`)
    ```php
    <?php // app/Views/layout/partials/alerts.php
        $messageTypes = ['message', 'error', 'success', 'warning', 'info'];
        $alertClassMap = [ 'message' => 'alert-info', 'error' => 'alert-danger', 'success' => 'alert-success', 'warning' => 'alert-warning', 'info' => 'alert-info', ];
        $foundMessage = false;
    ?>
    <?php foreach ($messageTypes as $type) : ?>
        <?php if (session()->has($type)) : $foundMessage = true; ?>
            <div class="alert <?= $alertClassMap[$type] ?? 'alert-secondary' ?> <?= $dismissible ?? false ? 'alert-dismissible fade show' : '' ?>" role="alert">
                <?= session($type) ?>
                <?php if ($dismissible ?? false) : ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?php endif ?>
            </div>
        <?php endif ?>
    <?php endforeach ?>
    ```

3.  **`src/app/Views/test/index.php`**
    ```php
    <?= $this->extend('layout/default') ?>
    <?= $this->section('title') ?><?= esc($pageTitle ?? 'テストページ') ?><?= $this->endSection() ?>
    <?= $this->section('content') ?>
    <h1><?= esc($pageTitle ?? 'テストページ') ?></h1> <p>開発環境機能テストページ。</p> <hr style="margin: 20px 0;">
    <h2>認証状態 (Shield)</h2>
    <?php if ($isLoggedIn && $user): ?> <p>ステータス: <strong>ログイン済み</strong></p> <p>ユーザー名: <?= esc($user->username) ?></p> <p>メールアドレス: <?= esc($user->getEmail()) ?></p>
    <?php else: ?> <p>ステータス: <strong>未ログイン</strong></p>
    <?php endif; ?> <p><a href="<?= site_url('/mypage') ?>">マイページへアクセス (要ログイン)</a></p> <hr style="margin: 20px 0;">
    <h2>日付入力 (Flatpickr + Vite)</h2> <p>カレンダーが表示されるか確認。</p>
    <form action="#" method="post"> <?= csrf_field() ?>
        <div style="margin-bottom: 1rem;"> <label for="reserve_date" style="display: block; margin-bottom: .5rem;">予約希望日:</label> <input type="text" id="reserve_date" name="reserve_date" class="flatpickr-date" placeholder="YYYY-MM-DD" style="padding: .375rem .75rem; border: 1px solid #ced4da; border-radius: .25rem;"> </div>
        <div> <label for="reserve_datetime" style="display: block; margin-bottom: .5rem;">予約希望日時:</label> <input type="text" id="reserve_datetime" name="reserve_datetime" class="flatpickr-datetime" placeholder="YYYY-MM-DD HH:MM" style="padding: .375rem .75rem; border: 1px solid #ced4da; border-radius: .25rem;"> </div>
        <div style="margin-top: 1.5rem;"> <button type="submit" style="padding: .375rem .75rem; color: #fff; background-color: #007bff; border-color: #007bff; border-radius: .25rem; cursor: pointer;">送信 (ダミー)</button> </div>
    </form> <hr style="margin: 20px 0;">
    <h2>PDF生成 (mPDF)</h2> <p>PDFが表示されるか確認。</p> <p><a href="<?= site_url('/test/pdf') ?>" target="_blank">テストPDFを生成して表示</a></p>
    <?= $this->endSection() ?>
    ```

4.  **`src/app/Views/test/pdf_template.php`**
    ```php
    <!DOCTYPE html><html lang="ja"><head><meta charset="UTF-8"><title><?= esc($title ?? 'PDF') ?></title><style>body { font-family: 'ipagp', sans-serif; font-size: 10pt; line-height: 1.5; } h1 { font-size: 16pt; color: #333; border-bottom: 1px solid #999; padding-bottom: 8px; margin-bottom: 20px; } p { margin-bottom: 12px; } .footer { font-size: 8pt; color: #777; text-align: center; margin-top: 30px; border-top: 1px solid #ccc; padding-top: 10px; }</style></head><body><h1><?= esc($title ?? 'ドキュメント') ?></h1><p><?= $content ?? '' ?></p><p>日本語表示テスト。</p><div class="footer">Generated by Kamishima Reserve System using mPDF</div></body></html>
    ```

5.  **`src/app/Views/mypage/index.php`**
    ```php
    <?= $this->extend('layout/default') ?>
    <?= $this->section('title') ?><?= esc($pageTitle ?? 'マイページ') ?><?= $this->endSection() ?>
    <?= $this->section('content') ?>
    <h1><?= esc($pageTitle ?? 'マイページ') ?></h1>
    <?php if ($user): ?>
        <p>ようこそ、<strong><?= esc($user->username) ?></strong> さん！</p><p>ログインユーザー専用ページです。</p>
        <p>登録情報:</p><ul><li>ユーザーID: <?= esc($user->id) ?></li><li>メールアドレス: <?= esc($user->getEmail()) ?></li><li>所属グループ: <?= implode(', ', $user->getGroups()) ?: 'なし' ?></li></ul>
    <?php else: ?>
        <p style="color: red;">エラー: ユーザー情報が取得できませんでした。</p><p><a href="<?= url_to('login') ?>">ログインページへ</a></p>
    <?php endif; ?>
    <?= $this->endSection() ?>
    ```

**所有者修正:**
```bash
# ホストOSのターミナルで実行
sudo chown -R $(whoami):$(whoami) src/app/Views/
```

#### 10.4. 動作確認

1.  **Dockerコンテナ確認:** `docker compose ps` (全て running であること)
2.  **Vite開発サーバー起動:** **別のターミナル**を開き、`kamishima-reserve` ディレクトリで `npm run dev` を実行し、**起動したまま**にする。
3.  **ブラウザアクセス & 機能確認:**
    * `http://localhost:8080/test` アクセス、表示確認。
    * Flatpickr動作確認。
    * PDFリンククリック、文字化けなく表示されるか確認。
    * 認証状態表示確認。
    * ユーザー登録 (`/register`)、ログイン (`/login`)、ログアウト試行。
    * ログイン後 `/mypage` アクセス確認。
    * ログアウト後 `/mypage` アクセス不可確認。
    * Xdebug (任意): VSCodeでブレークポイントを設定し、「Listen for Xdebug (Docker)」を実行後、ブラウザ拡張機能でデバッグを有効にしてアクセス。

**【重要】 `php spark make:...` コマンドについて**
ファイルを生成する `spark` コマンド実行後は、**必ずホスト側で `sudo chown -R $(whoami):$(whoami) src/` を実行**して所有者を修正してください。

### 手順11: 最終確認: フォルダ構造

すべての手順が完了すると、プロジェクトルート (`kamishima-reserve`) のフォルダ構造は以下のようになっているはずです。

```plaintext
kamishima-reserve/
├── .docker/             # Docker関連ファイル
│   ├── mysql/
│   │   └── my.cnf
│   ├── nginx/
│   │   └── default.conf
│   └── php/
│       ├── Dockerfile
│       └── php.ini
├── .git/                # Gitリポジトリ (git init後)
├── .vscode/             # VSCode設定
│   └── launch.json      # デバッグ設定
├── assets/              # フロントエンドソース (Vite管理)
│   ├── css/
│   │   └── app.scss     # SASSエントリーポイント
│   ├── js/
│   │   ├── components/
│   │   │   └── flatpickr-init.js # Flatpickr初期化
│   │   └── app.js       # JSエントリーポイント
│   └── images/          # 画像素材
├── docs/                # 設計書・資料
├── node_modules/        # npmパッケージ (Git管理外)
├── src/                 # CodeIgniterアプリケーションコード
│   ├── app/
│   │   ├── Config/      # 設定ファイル
│   │   ├── Controllers/ # コントローラー
│   │   ├── Database/    # マイグレーション, シーダー
│   │   ├── Helpers/     # ヘルパー
│   │   ├── Models/      # モデル
│   │   └── Views/       # ビュー
│   │       ├── layout/
│   │       │   └── partials/
│   │       │       └── alerts.php # アラート表示パーシャル
│   │       │   └── default.php    # デフォルトレイアウト
│   │       ├── mypage/
│   │       │   └── index.php      # マイページビュー
│   │       ├── test/
│   │       │   ├── index.php      # テストページビュー
│   │       │   └── pdf_template.php # PDFテンプレートビュー
│   │       └── welcome_message.php  # 初期ウェルカムページ
│   ├── public/          # 公開ディレクトリ (ドキュメントルート)
│   │   ├── build-vite/  # Viteビルド成果物 (Git管理外)
│   │   └── index.php    # フロントコントローラー
│   ├── writable/        # 書き込み可能ディレクトリ (Git管理外)
│   │   ├── cache/       # キャッシュ
│   │   ├── logs/        # ログ
│   │   ├── session/     # セッションファイル (FileHandlerの場合、今は未使用)
│   │   └── uploads/     # アップロードファイル
│   ├── vendor/          # Composerパッケージ (Git管理外)
│   ├── tests/           # テストコード
│   ├── composer.json    # PHP依存関係定義
│   ├── composer.lock    # PHP依存関係ロック
│   ├── env              # .envファイルのテンプレート
│   ├── phpunit.xml.dist # PHPUnit設定
│   ├── spark            # CodeIgniter CLIツール
│   └── .env             # CodeIgniter用環境設定 (Git管理外)
├── .env                 # Docker Compose用環境設定 (Git管理外)
├── .gitignore           # Git無視リスト
├── docker-compose.yml   # Docker Compose設定
├── package-lock.json    # npm依存関係ロック
├── package.json         # Node.js依存関係定義
└── vite.config.js       # Vite設定
```

### 手順12: まとめと補足

* **Macユーザー:** Dockerを使用するため、基本的な手順は同じです。WSL2関連の手順は不要です。`host.docker.internal` や `chown $(whoami):$(whoami)` は同様に使用できます。
* **Git:** プロジェクトルートで `git init` を実行し、バージョン管理を開始してください。`.gitignore` を確認します。
* **パフォーマンス:** WSL2ファイルシステム内にプロジェクトを置くことで、パフォーマンスは比較的良好なはずです。
* **エラー発生時:** Dockerログ (`docker compose logs php` 等)、CodeIgniterログ (`src/writable/logs/`)、ブラウザ開発者ツールを確認してください。パーミッションエラーが疑われる場合は `sudo chown -R $(whoami):$(whoami) src/` を試します。
* **カスタマイズ:** これは基本環境です。要件に合わせてPHP拡張機能、ライブラリ、Docker設定などを追加・変更してください。

---

これで、すべてのファイル内容を含め、参照箇所をなくした最終版の手順書となります。ご確認いただき、この手順で環境構築をお試しください。