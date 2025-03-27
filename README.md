# Doctrine-event-sourcing
![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/inwebo/doctrine-event-sourcing/.github%2Fworkflows%2Fsymfony.yml?branch=master&style=flat-square)
![Packagist Version](https://img.shields.io/packagist/v/inwebo/doctrine-event-sourcing?style=flat-square)
![Packagist Downloads](https://img.shields.io/packagist/dd/inwebo/doctrine-event-sourcing?style=flat-square)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/inwebo/doctrine-event-sourcing/php?style=flat-square)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/inwebo/doctrine-event-sourcing/doctrine%2Form?style=flat-square)

Simple Event sourcing pattern implementation with DoctrineListener

## Installation

```shell
  composer req inwebo/doctrine-event-sourcing
```

## Tests

```shell
  composer phpunit
```

## PhpStan

```shell
  composer phpstan
```
> Level 10

## Outils


## Comment

Je veux sauvegarder les changements d'attributs de classe d'une entité doctrine automatiquement afin de parcourir l'historique des modifications ou alors de faire une projection dans le futur.

Un exemple d'implémentation est disponible avec l'entité [Foo](./tests/src/Entity/Foo/Foo.php) ainsi que [FooState](./tests/src/Entity/Foo/FooState.php)

Étapes :

* Je définis l'entité (sujet) dont je dois observer les modifications de ses propriétés.
* Je crée une nouvelle entité (state) avec les mêmes propriétés ainsi que leurs types que le sujet et ceux-ci sont maintenant `nullable`
  * Cette nouvelle entité DOIT posséder une relation ManyToOne avec le sujet. Voir exemple [FooState](./tests/src/Entity/Foo/FooState.php)
* J'annote mon sujet avec l'attribut de classe [#[AggregateRoot]](./src/Mapping/AggregateRoot.php)
  * L'attribut possède deux arguments :
    * `stateClass` : L'argument `targetEntity` présent dans l'attribut #[OneToMany] cette class DOIT exister
    * `subjectSetter` : Dans mon état quelle est la méthode permettant de définir la relation avec le sujet. Càd comment définir la valeur `inversedBy`
* J'annote les propriétés de classe de mon sujet avec l'annotation [#[AggregateSource]](./src/Mapping/AggregateSource.php)
  * L'attribut possède deux arguments :
    * `getter` : Une méthode retournant la valeur de la propriété à observer. Ce `getter` DOIT être disponible dans le sujet et l'état
    * `setter` : Une méthode définissant la valeur de la propriété à observer. Ce `setter` DOIT être disponible dans le sujet et l'état
* J'ajoute un #[ORM\EntityListeners]
  * Il existe un [StoreListener](./src/Listener/StoreListener.php) par défaut