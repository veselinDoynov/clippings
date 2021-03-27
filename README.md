# Steps to create dev environment

### 1. Preliminary requirements

#### 1.1 Windows
- [Docker for Windows (stable)](https://docs.docker.com/docker-for-windows/install/#download-docker-for-windows)


#### 1.2 Linux
- git
- docker (more info [here](https://docs.docker.com/engine/installation/))
- docker-compose (more info [here](https://docs.docker.com/compose/install/#install-as-a-container))

#### 1.3 
- [Docker for Mac](https://docs.docker.com/docker-for-mac/install/)

### 2. Setup traefik if you don't already have it
Create the `osinet` network first:
`docker network create osinet`

```sh
git clone git@gitlab.objectsystems.com:web/traefik.git

cd /path/to/traefik && docker-compose up -d
```

### 3. Start containers
```bash
cd /path/to/calm-be && docker-compose up -d
```

### 4. Install backend requirements (change the name of the Docker container if you have changed the COMPOSE_PROJECT_NAME in the .env file)
```bash
docker exec -u 1000 clippings-api-fpm bash -c "cd /app && composer install --no-ansi -o -n"
```
### 5. Edit your hosts file to include the following row
`127.0.0.1 clippings.local.osi clippings-api.local.osi`

### 6. Run tests (when you need to, not needed for the initial setup of the project)
```bash
docker exec -u 1000 clippings-api-fpm bash -c "cd /app && vendor/bin/codecept run --coverage --coverage-xml --coverage-html"
```
### 7. Go to https://clippings-api.local.osi
