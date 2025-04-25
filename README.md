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

## Core Concepts

* **[#[AggregateRoot]](./src/Mapping/AggregateRoot.php)**: A Doctrine entity whose state changes are tracked. Marked with the #[AggregateRoot] attribute. It must implement HasStatesInterface.
* **[#[AggregateSource]](./src/Mapping/AggregateSource.php)**: A property within an Aggregate Root whose changes should be tracked. Marked with the #[AggregateSource] attribute. Requires getter and setter methods in both the Aggregate Root and the associated State entity.
* **[State](./src/Model/Interface/StateInterface.php)**: A separate Doctrine entity designed to store a snapshot of the tracked properties of an Aggregate Root at a specific point in time. It must implement StateInterface and have a ManyToOne relationship back to its Aggregate Root.
* **[StoreListener](./src/Listener/StoreListener.php)**: A Doctrine Entity Listener (e.g., StoreListener) that hooks into prePersist and preUpdate events. It uses the Aggregator to create and persist new State entities whenever an Aggregate Root is created or modified.
* **[MappingFactory](./src/Model/MappingFactory.php)**: Responsible for parsing the #[AggregateRoot] and #[AggregateSource] attributes on a subject class, validating the configuration, and creating Mapping objects that define how properties are transferred between the subject and its state.
* **[MappingFactory](./src/Model/Aggregator.php)**: Uses the mapping information provided by MappingFactory to create new State entities from an Aggregate Root or apply a specific State back onto an Aggregate Root.
* **[HistoricResolver](src/Resolver/DiffResolver.php)**: Enables querying the history of changes for an Aggregate Root by comparing consecutive State entities.

## Setup and Usage

### 1. Installation:

```shell
  composer req inwebo/doctrine-event-sourcing
```

### 2. Define the Aggregate Root (Subject):

* This is your main Doctrine entity.
* Annotate the class with #[ORM\EntityListeners([StoreListener::class])] (or your custom listener).
* Annotate the class with #[AggregateRoot], providing the stateClass (FQCN of your State entity) and subjectSetter (the method name in the State entity used to set the relationship back to this Aggregate Root).
* Implement the HasStatesInterface. Typically, you'll need a OneToMany relationship to store the states.
* Annotate the properties you want to track with #[AggregateSource], specifying the getter and setter method names. These methods must exist in both the Aggregate Root and the State entity.

```php
<?php
// src/Entity/Product.php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Inwebo\DoctrineEventSourcing\Listener\StoreListener; // Default listener
use Inwebo\DoctrineEventSourcing\Mapping\AggregateRoot;
use Inwebo\DoctrineEventSourcing\Mapping\AggregateSource;
use Inwebo\DoctrineEventSourcing\Model\Interface\HasStatesInterface;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

#[AggregateRoot(stateClass: ProductState::class, subjectSetter: 'setProduct')]
#[ORM\Entity]
#[ORM\EntityListeners([StoreListener::class])]
class Product implements HasStatesInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[AggregateSource(getter: 'getName', setter: 'setName')]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    #[AggregateSource(getter: 'getPrice', setter: 'setPrice')]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $price;

    #[ORM\OneToMany(targetEntity: ProductState::class, mappedBy: 'product', cascade: ['persist'])]
    private Collection $states;

    public function __construct(string $name, string $price) {
        $this->name = $name;
        $this->price = $price;
        $this->states = new ArrayCollection();
    }

    // --- Getters and Setters for tracked properties ---
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getPrice(): string { return $this->price; }
    public function setPrice(string $price): void { $this->price = $price; }

    // --- Interface Implementation ---
    /** @return Collection<int, StateInterface> */
    public function getEventSourcingStates(): Collection { return $this->states; }

    public function getId(): ?int { return $this->id; }
}
```

### 3. Define the State Entity:

* Create a new Doctrine entity to store the state snapshots.
* It **must** implement `StateInterface`.
* It **must** have a `ManyToOne` relationship back to the Aggregate Root. The `inversedBy` side of this relationship corresponds to the `states` collection in the Aggregate Root, and the setter for this property (e.g., `setProduct`) is specified in the `#[AggregateRoot]` attribute's `subjectSetter` argument.
* Include properties corresponding to the `#[AggregateSource]` properties in the Aggregate Root. These properties should generally be `nullable` as they represent a snapshot at a particular time.
* Include getter and setter methods for these properties matching the names specified in the `#[AggregateSource]` attributes.

```php
<?php
// src/Entity/ProductState.php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Inwebo\DoctrineEventSourcing\Model\Interface\StateInterface;

#[ORM\Entity]
class ProductState implements StateInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null; // State entities usually need their own ID

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'states')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false)]
    private Product $product; // Relationship back to the Aggregate Root

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $price = null;

    // No constructor needed usually, states are created by the listener

    // --- Getters and Setters for tracked properties (matching AggregateSource) ---
    public function getName(): ?string { return $this->name; }
    public function setName(?string $name): void { $this->name = $name; }
    public function getPrice(): ?string { return $this->price; }
    public function setPrice(?string $price): void { $this->price = $price; }

    // --- Setter for the relationship (matches subjectSetter in AggregateRoot) ---
    public function setProduct(Product $product): void { $this->product = $product; }
    public function getProduct(): Product { return $this->product; }

    public function getId(): ?int { return $this->id; }
}
```

### 4. Doctrine Configuration:

Ensure Doctrine is configured to recognize your entities and that Entity Listeners are enabled. No special library configuration is typically required beyond standard Doctrine setup.

### 5. Usage:

Whenever you persist or update an instance of your Aggregate Root (`Product` in the example) using Doctrine's `EntityManager`, the `StoreListener` will automatically:

* On `prePersist`: Create an initial `ProductState` capturing the initial values of tracked properties (`name`, `price`) and associate it with the `Product`.
* On `preUpdate`: If any tracked properties have changed, create a new `ProductState` capturing the *new* values and associate it with the `Product`. The listener persists and flushes this new state entity.

You can then access the history via the $product->getEventSourcingStates() collection.

## Component Breakdown

### `src/Mapping/` - Attributes for Configuration

* **[AggregateRoot.php](./src/Mapping/AggregateRoot.php)**: Class attribute `(#[Attribute(\Attribute::TARGET_CLASS)])` used to mark an entity as the root of an aggregate for event sourcing.
* `stateClass`: (Required) The fully qualified class name (FQCN) of the corresponding State entity.
* `subjectSetter`: (Required) The name of the method within the State entity used to set the relationship back to the Aggregate Root (e.g., `setProduct` in the `ProductState` example).
* **[AggregateSource](./src/Mapping/AggregateSource.php).php**: Property attribute (`#[Attribute(\Attribute::TARGET_PROPERTY)]`) used to mark specific properties within an Aggregate Root whose changes should be tracked.
* `getter`: (Required) The name of the getter method for this property. Must exist in both the Aggregate Root and the State entity.
* `setter`: (Required) The name of the setter method for this property. Must exist in both the Aggregate Root and the State entity.


### `src/Model/` - Core Logic and Interfaces

* `Interface/`: Defines the contracts for entities involved in event sourcing.
  * `HasStatesInterface.php`: Must be implemented by Aggregate Root entities. Requires the getEventSourcingStates(): Collection method to return the collection of associated State entities.
  * `StateInterface.php`: Marker interface that must be implemented by State entities.
  * `StoreListenerInterface.php`: Defines the methods (prePersist, preUpdate) expected from a Doctrine Entity Listener used by this library.
* `Dto/`: Data Transfer Objects.
  * ChangeDto.php: Represents a single change to a field, holding the field name, old value, and new value. Used by HistoricResolver.
  * ChangeSetDto.php: A collection of ChangeDto arrays, representing the history of changes across multiple states. Used by HistoricResolver.
* `MappingFactory.php`:
  * Parses `#[AggregateRoot]` and `#[AggregateSource]` attributes on a given subject class.
  * Validates the configuration (checks if classes/methods exist, arguments are provided).
  * Builds a collection of `Mapping` objects representing the valid configuration.
  * Throws specific exceptions from `src/Exception/` if the mapping is invalid.
* `Aggregator.php`:
  * Uses a `MappingFactory` instance to understand the relationship between an Aggregate Root and its State.
  * `createState(HasStatesInterface $subject)`: Creates a new State entity instance populated with the current values from the Aggregate Root's tracked properties.
  * `createStateFromChange(PreUpdateEventArgs $args)`: Creates a new State entity based on the changes detected in a Doctrine preUpdate event. It first updates the subject entity with the new values from the change set before creating the state snapshot.
  * `applyState(HasStatesInterface $subject, StateInterface $state)`: Applies the values from a given State entity back onto the corresponding Aggregate Root entity (useful for projections or reverting state).
  * `Mapping.php`: Represents the validated mapping for a single `#[AggregateSource]` property. Holds `ReflectionMethod` instances for the getters and setters in both the Subject (Aggregate Root) and the State, allowing efficient invocation.

### `src/Listener/` - Doctrine Integration

* `AbstractStoreListener.php`: Provides the base implementation for the Doctrine Entity Listener logic.
* Implements `StoreListenerInterface`.
* Handles the `prePersist` and `preUpdate` events.
* Uses Aggregator to create and associate State entities.
* Includes a simple flag (`$hasBeenUpdated`) to prevent duplicate state creation if multiple updates occur within the same transaction/flush cycle before the listener logic fully completes (though Doctrine's event management usually prevents this).
* `StoreListener.php`: The default, concrete implementation extending `AbstractStoreListener`. This is the listener you typically register in your Aggregate Root's `#[ORM\EntityListeners]` attribute.

### `src/Resolver/` - History Analysis

* `HistoricResolver.php`:
* Takes an `Aggregator` to understand the entity mapping.
* `resolve(HasStatesInterface $subject)`: Iterates through the StateInterface collection associated with the subject. It compares each state with the previous one (and the final state with the current subject state) to generate a `ChangeSetDto` detailing the history of modifications to the tracked properties.

### `src/Exception/` - Error Handling

Contains specific exception classes thrown during mapping validation (MappingFactory) or if interfaces are missing. This allows for more granular error handling compared to generic exceptions.

* `Mapping/AggregateRoot/`: Exceptions related to the `#[AggregateRoot]` attribute (missing, invalid arguments, class not found).
* `Mapping/AggregateSource/`: Exceptions related to the `#[AggregateSource]` attribute (missing arguments, invalid getter/setter methods).
* `MissingHasStatesInterfaceException.php`: Thrown if the subject class provided to MappingFactory does not implement `HasStatesInterface`.

## Configuration and Extension

* **Configuration**: Primarily done via the `#[AggregateRoot]` and `#[AggregateSource]` attributes on your entities. Ensure the arguments (`stateClass`, `subjectSetter`, `getter`, `setter`) correctly reference existing classes and methods.
* **Custom Listener**: You can create your own listener by extending `AbstractStoreListener` or implementing `StoreListenerInterface` directly if you need custom logic before or after state creation (e.g., adding metadata to the state, custom logging). Remember to register your custom listener in the `#[ORM\EntityListeners]` attribute.
* **State Entity Customization**: Add non-tracked properties to your State entity if needed (e.g., timestamp, user ID causing the change), but you'll need a custom listener to populate them.

🧰 [GitSummarize](https://gitsummarize.com/inwebo/doctrine-event-sourcing?doc=core-concepts)