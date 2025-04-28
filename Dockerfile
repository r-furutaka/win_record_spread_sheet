# PHP公式イメージ（Apache付き）をベースにする
FROM php:8.2-apache

# 必要に応じてパッケージをインストール
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql

# Composerをインストール
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 必要なポートを開放
EXPOSE 80

# 作業ディレクトリ
WORKDIR /var/www/html