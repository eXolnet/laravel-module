#!/bin/bash

git subsplit init git@github.com:exolnet/laravelmodule.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Cache:git@github.com:exolnet/laravel-cache.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Core:git@github.com:exolnet/laravel-core.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Database:git@github.com:exolnet/laravel-database.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Extension:git@github.com:exolnet/laravel-extension.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Formatter:git@github.com:exolnet/laravel-formatter.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Foundation:git@github.com:exolnet/laravel-foundation.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Html:git@github.com:exolnet/laravel-html.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Log:git@github.com:exolnet/laravel-log.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Menu:git@github.com:exolnet/laravel-menu.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Routing:git@github.com:exolnet/laravel-routing.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Session:git@github.com:exolnet/laravel-session.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Test:git@github.com:exolnet/laravel-test.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/User:git@github.com:exolnet/laravel-user.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Validation:git@github.com:exolnet/laravel-validation.git
rm -Rf .subsplit/