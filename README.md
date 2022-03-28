## Playground BDD
It is the playground for learning how to use bdd with php

### Init Project
```shell
make init
```

### Run Project
```shell
make up
```

### Run Tests
#### Behat
```shell
docker-compose exec app vendor/bin/behat
```

#### PHPUnit
```shell
docker-compose exec app bin/phpunit
```