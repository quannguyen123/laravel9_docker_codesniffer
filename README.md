#run terminal
setup: composer global require "squizlabs/php_codesniffer=*"
fix code: composer cs-fix
check error code: composer test