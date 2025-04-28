# PHP公式イメージ（Apache付き）をベースにする
FROM php:8.2-apache

# 必要に応じてパッケージをインストール
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql

# Composerをインストール
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# DocumentRootをpublicに変更する
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Apacheの設定を書き換え
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 作業ディレクトリ
WORKDIR /var/www/html

# 必要なポートを開放
EXPOSE 80