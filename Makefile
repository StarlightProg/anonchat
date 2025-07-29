init: docker-down-clear docker-pull docker-build docker-up 

up: docker-up
down: docker-down
restart: down up

docker-up:
	docker compose up -d
docker-down:
	docker compose down --remove-orphans
docker-down-clear:
	docker compose down -v --remove-orphans
docker-pull:
	docker compose pull
docker-build:
	docker compose build

build: build-nginx build-laravel build-queue

build-nginx:
	docker build --pull --file=docker/production/nginx/Dockerfile --tag=${REGISTRY}:nginx-${IMAGE_TAG} ./
build-laravel:
	docker build --pull --file=docker/production/php-fpm/Dockerfile --tag=${REGISTRY}:laravel-${IMAGE_TAG} ./
build-queue:
	docker build --pull --file=docker/production/php-fpm/Dockerfile --tag=${REGISTRY}:queue-${IMAGE_TAG} ./

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

push:
	docker push ${REGISTRY}:nginx-${IMAGE_TAG}
	docker push ${REGISTRY}:laravel-${IMAGE_TAG}
	docker push ${REGISTRY}:queue-${IMAGE_TAG}