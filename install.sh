#!/usr/bin/env bash
set -euo pipefail

project_root="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${project_root}"

force_flag=""
if [[ "${1:-}" == "--force" ]]; then
    force_flag="--force"
fi

echo "[1/6] Checking Docker availability..."
if ! command -v docker >/dev/null 2>&1; then
    echo "Error: docker is not installed."
    exit 1
fi

if ! docker compose version >/dev/null 2>&1; then
    echo "Error: docker compose is not available."
    exit 1
fi

echo "[2/6] Preparing local configuration files..."
if [[ ! -f src/config/config.php ]]; then
    cp src/config/config-example.php src/config/config.php
    echo "Created src/config/config.php from template."
else
    echo "Using existing src/config/config.php."
fi

if [[ -f docker-compose.yml ]]; then
    echo "Using existing docker-compose.yml."
elif [[ -f docker-compose.yml.example ]]; then
    cp docker-compose.yml.example docker-compose.yml
    echo "Created docker-compose.yml from template."
else
    echo "Error: docker-compose.yml and docker-compose.yml.example are both missing."
    exit 1
fi

echo "[3/6] Starting containers..."
docker compose up -d --build

echo "[4/6] Waiting for services and running one-time database install..."
max_attempts=30
attempt_count=1

until docker compose exec -T php php src/scripts/install.php ${force_flag}; do
    if (( attempt_count >= max_attempts )); then
        echo "Error: installation failed after ${max_attempts} attempts."
        exit 1
    fi

    echo "Install attempt ${attempt_count}/${max_attempts} failed. Retrying in 2 seconds..."
    attempt_count=$((attempt_count + 1))
    sleep 2
done

echo "[5/6] Database installation completed successfully."

echo "[6/6] Install finished."
echo "Next step: review src/config/config.php and then open http://localhost"
