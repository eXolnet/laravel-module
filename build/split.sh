#!/bin/bash

git subsplit init git@github.com:exolnet/laravelmodule.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Cache:git@github.com:exolnet/cache.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Core:git@github.com:exolnet/core.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Database:git@github.com:exolnet/database.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Extension:git@github.com:exolnet/extension.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Formatter:git@github.com:exolnet/formatter.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Foundation:git@github.com:exolnet/foundation.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Html:git@github.com:exolnet/html.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Log:git@github.com:exolnet/log.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Menu:git@github.com:exolnet/menu.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Routing:git@github.com:exolnet/routing.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Session:git@github.com:exolnet/session.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Test:git@github.com:exolnet/test.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/User:git@github.com:exolnet/user.git
git subsplit publish --heads="master 1.0" $FLAGS src/Exolnet/Validation:git@github.com:exolnet/validation.git
rm -Rf .subsplit/