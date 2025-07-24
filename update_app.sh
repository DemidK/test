#!/bin/bash

# Определяем путь к лог-файлу относительно расположения скрипта
# Это предполагает, что скрипт лежит в корне проекта Laravel
LOG_FILE="$(dirname "$0")/storage/logs/deploy.log"

# Функция для логирования сообщений с временной меткой
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] - $1" >> "$LOG_FILE"
}

log_message "====== Deployment Started ======"

# 1. Получение последних изменений из Git
log_message "Running 'git pull'..."
git pull origin main >> "$LOG_FILE" 2>&1
log_message "'git pull' finished."

# 2. Установка зависимостей Composer
log_message "Running 'composer install'..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev >> "$LOG_FILE" 2>&1
log_message "'composer install' finished."

# 3. Очистка кэша Laravel
log_message "Clearing caches..."
php artisan cache:clear >> "$LOG_FILE" 2>&1
php artisan config:clear >> "$LOG_FILE" 2>&1
php artisan route:clear >> "$LOG_FILE" 2>&1
php artisan view:clear >> "$LOG_FILE" 2>&1
log_message "Caches cleared."

# 4. Применение миграций базы данных
log_message "Running 'php artisan migrate'..."
php artisan migrate --force >> "$LOG_FILE" 2>&1
log_message "'php artisan migrate' finished."

log_message "====== Deployment Finished Successfully ======"

# Выход с кодом 0, означающим успех
exit 0