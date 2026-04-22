start:
	cd docker && docker compose up -d

build:
	cd docker && docker compose build

stop:
	cd docker && docker compose down

logs:
	cd docker && docker compose logs -f

shell:
	docker exec -it symfony_app bash

db:
	docker exec -it mysql mysql -u api_user -papi_pass api_db

migrate:
	docker exec -it symfony_app php bin/console doctrine:migrations:migrate
