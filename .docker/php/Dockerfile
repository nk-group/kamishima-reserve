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