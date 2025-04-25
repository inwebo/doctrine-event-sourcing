# CHANGELOG
## 1.0.1
* Fix MappingFactory mutator Exception
* Update documentation
* Fix Aggregator applyState: Will not set a null value with not nullable subject's setter
## 1.0.2
* Fix StoreListener : Multi import
## 1.0.3
* Rename HistoricResolver to DiffResolver
* Add a new method to Aggregator::historic(HasStatesInterface $subject): array
* Update dev back dependencies