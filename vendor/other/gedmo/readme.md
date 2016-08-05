Rixxi/Gedmo
===========================


Requirements
------------

Rixxi/Gedmo requires [Kdyby/Doctrine](https://github.com/kdyby/doctrine)


Installation
------------

The best way to install Rixxi/Gedmo is using  [Composer](http://getcomposer.org/):

```sh
$ composer require rixxi/gedmo
```

Configuration
------------

The best way to install Rixxi/Gedmo is using  [Composer](http://getcomposer.org/):

```yml
extensions:
	gedmo: Rixxi\Gedmo\DI\OrmExtension
	# you should probably register Kdyby/Doctrine too
	doctrine: Kdyby\Doctrine\DI\OrmExtension

gedmo:
	translatableLocale: cs_CZ
	defaultLocale: cs_CZ

	# enable all annotations
	all: on
	# enable per annotation
	loggable: on
	sluggable: on
	softDeleteable: on
	sortable: on
	timestampable: on
	translatable: on
	treeable: on
	uploadable: on

# you must add bit type to your doctrine connection and soft-deleteable to filters
doctrine:
	types:
		bit: Doctrine\DBAL\Types\BooleanType

	filters:
		# without this softDeleteable won't work...            ...probably
		soft-deleteable: Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter
```


-----

Repository [http://github.com/rixxi/gedmo](http://github.com/rixxi/gedmo).
