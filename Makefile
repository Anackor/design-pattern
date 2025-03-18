start:
	cd docker && docker-compose up -d --build

stop:
	cd docker && docker-compose down

logs:
	cd docker && docker-compose logs -f

shell:
	docker exec -it symfony_app bash

db:
	docker exec -it symfony_mysql mysql -u symfony_user -p

migrate:
	docker exec -it symfony_app php bin/console doctrine:migrations:migrate
