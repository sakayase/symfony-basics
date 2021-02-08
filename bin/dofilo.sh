#!/bin/bash

# dofilo = doctrine:fixtures:load

# suppression du schema
php bin/console doctrine:schema:drop --force
# vide la table où est stockée toutes les versions des migrations 
php bin/console doctrine:query:sql -q "TRUNCATE doctrine_migration_versions"
# reconstruit le schema
php bin/console doctrine:migrations:migrate --no-interaction
# injection des fixtures
php bin/console doctrine:fixtures:load --no-interaction

# rendre ce script executable :
# chmod +x bin/dofilo.sh 