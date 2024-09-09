#!/usr/bin/env sh

IMAGE_TAG='tl-coding-test/php:8.3'

docker build -t ${IMAGE_TAG} . --pull
