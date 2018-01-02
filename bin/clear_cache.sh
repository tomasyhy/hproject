#!/usr/bin/env bash

# special case for demo server
PHP=$(which php)

$PHP bin/console c:c --no-warmup --env=dev
$PHP bin/console c:c --no-warmup --env=test
$PHP bin/console c:c --no-warmup --env=prod
$PHP bin/console c:w --env=dev
$PHP bin/console c:w --env=test
$PHP bin/console c:w --env=prod
$PHP console doctrine:cache:clear-metadata

#doctrine
$PHP bin/console doctrine:cache:clear-metadata
$PHP bin/console doctrine:cache:clear-query
$PHP bin/console doctrine:cache:clear-result