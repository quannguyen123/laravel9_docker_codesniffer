#run terminal
setup: composer require squizlabs/php_codesniffer --dev
fix code: composer cs-fix
check error code: composer test