install:
	@make clean
	@make build
	@make up
	docker compose exec app composer install
	docker compose exec app cp .env.example .env
	docker compose exec app php artisan key:generate
	docker compose exec app php artisan storage:link
	docker compose exec app chmod -R 777 storage bootstrap/cache
	@make fresh
clean:
	docker compose down --rmi all --volumes --remove-orphans
build:
	docker compose build --no-cache --force-rm
up:
	docker compose up -d
	sleep 5
	docker compose exec app chmod -R 777 storage bootstrap/cache
down:
	docker compose down
fresh:
	docker compose exec app php artisan migrate:fresh --seed
app:
	docker compose exec app bash
sql:
	docker compose exec db bash -c 'mysql -u $$MYSQL_USER -p$$MYSQL_PASSWORD $$MYSQL_DATABASE'
clear:
	docker compose exec app php artisan cache:clear 
	docker compose exec app php artisan config:clear 
	docker compose exec app php artisan route:clear
test:
	docker compose exec app php artisan config:clear
	docker compose exec app php artisan cache:clear
	docker compose exec app php artisan test --env=testing
copy:
	cp src/public/images/sample* src/storage/app/public/products
ide-helper:
	docker compose exec app php artisan ide-helper:generate
	docker compose exec app php artisan ide-helper:models -N
