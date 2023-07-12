#run terminal
setup: composer require squizlabs/php_codesniffer --dev
fix code: composer cs-fix
check error code: composer test



docker-compose build --no-cache
docker-compose up -d
docker exec -it container_name bash
composer install
php artisan key:generate

api
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

php artisan config:cache
php artisan config:clear

cài đặt passport
php artisan passport:install


https://github.com/kimtrien/vietnam-zone
import dữ liệu provinces và districts
php artisan vietnamzone:import

