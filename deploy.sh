#!/bin/bash
cd /var/www/OnlySchool || exit
git fetch origin main
git reset --hard origin/main  # Replace 'main' with your branch name

# Stop and restart Sail containers
./vendor/bin/sail down
./vendor/bin/sail up -d

# Run npm build
./vendor/bin/sail npm run build

