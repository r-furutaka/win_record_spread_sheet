# Makefile

# デフォルトターゲット
.DEFAULT_GOAL := help

# サービス名
SERVICE = app

up: ## コンテナをビルドして起動
	docker-compose up -d --build

down: ## コンテナを停止
	docker-compose down

restart: ## コンテナを再起動（ビルド付き）
	docker-compose down && docker-compose up -d --build

logs: ## コンテナのログを見る
	docker-compose logs -f $(SERVICE)

sh: ## コンテナにbashで入る
	docker-compose exec $(SERVICE) bash

composer-install: ## composer install を実行
	docker-compose exec $(SERVICE) sh -c "cd /var/www/html && composer install"

composer-update: ## composer update を実行
	docker-compose exec $(SERVICE) sh -c "cd /var/www/html && composer update"

rebuild: ## イメージ・ボリュームを全削除して再ビルド
	docker-compose down --rmi all --volumes --remove-orphans
	docker-compose up -d --build

help: ## このヘルプを表示
	@echo ""
	@echo "Usage:"
	@echo "  make [command]"
	@echo ""
	@echo "Commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'
	@echo ""
