# eXolnet Laravel Module

This repository contains all the changes that we've applied to a laravel install to make it behave like we want it to behave.

Here's a non-exhaustive list of what it does:

* Cleanup Application in order to break dependency cycles during tests
* Add a model relation helper which enables us to sync has many relations easily
* Provides a ValidationTrait that can be used in models (similar to Ardent)
* Provides a routing/url generator that integrates localization
* Offers a SQLite based testing system which speeds up testing

## License

The code is licensed under the [MIT license](http://choosealicense.com/licenses/mit/). See LICENSE.