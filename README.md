# Doctrine-event-sourcing

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

## How can I help you ?

If you want to save a state of an entity easily, you want to extract all changes of an entity over the time or see an exact version
of an entity with ease, this library can help you.

## Entity cycle of life

Each time prePersist or preUpdate doctrine's event is invoked the entity's state is saved.

## Example

We want to save the state of a product over the time. A customer told us that he paid 35€ a product during Xmas sales.
Is he wrong or not ?

You can check an implementation with a [Product](./tests/src/Entity/Product.php) entity and its state [ProductState](./tests/src/Entity/ProductState.php)