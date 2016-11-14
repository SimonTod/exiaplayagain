exiaplayagain
=============

A Symfony project created on October 21, 2016, 3:01 pm.

####TODO

- [x] games database
    - name
    - type
    - pay / free / crack
    - [ ] last time played
    - info
    - url
    - image
- [x] admin possibility to add games
- [x] esthetic game info view
- [x] admin possibility to modify / remove game
- [ ] user / games relation
- [x] votes database
    - game 1
    - game 2
    - game 3
    - game 4
    - limited date
- [x] admin possibility to create / delete a vote
- [x] users possibility to see a vote
- [x] users possibility vote for a vote
- [x] display other informations like stats and others

####GENERATE ENTITIES FROM EXISTING DATABASE

```bash
sudo php app/console doctrine:mapping:import --force ExiaplayagainBundle xml
sudo php app/console doctrine:mapping:convert --force annotation ./src
sudo php app/console doctrine:generate:entities ExiaplayagainBundle
```

####Deploy update on server

```bash
sudo git pull origin master
sudo php app/console cache:clear --env=prod
sudo rm -rf app/cache app/logs
sudo mkdir app/cache app/logs
sudo chmod 777 app/cache app/logs
```

####SQL FOR DATABASE USERS_VOTES

```sql
CREATE TABLE `exiaplayagain`.`users_votes` 
( 
    `id` INT NOT NULL AUTO_INCREMENT , 
    `user` INT NOT NULL , 
    `vote` INT NOT NULL , 
    `game` INT NOT NULL , 
    PRIMARY KEY (`id`),
    FOREIGN KEY (user) REFERENCES users(id),
    FOREIGN KEY (vote) REFERENCES votes(id),
    FOREIGN KEY (game) REFERENCES games(id)
) ENGINE = InnoDB;
```

#####ADD IN ENTITY VOTES.PHP

```php
private $userHasVoted;

public function setUserHasVoted($userHasVoted)
{
    $this->userHasVoted = $userHasVoted;

    return $this;
}

public function getUserHasVoted()
{
    return $this->userHasVoted;
}

private $votedGame;

public function setVotedGame($votedGame)
{
    $this->votedGame = $votedGame;

    return $this;
}

public function getVotedGame()
{
    return $this->votedGame;
}

private $numVotesGame1;

public function getNumVotesGame1()
{
    return $this->numVotesGame1;
}

public function setNumVotesGame1($numVotesGame1)
{
    $this->numVotesGame1 = $numVotesGame1;

    return $this;
}

private $percentVotesGame1;

public function getPercentVotesGame1()
{
    return $this->percentVotesGame1;
}

public function setPercentVotesGame1($percentVotesGame1)
{
    $this->percentVotesGame1 = $percentVotesGame1;

    return $this;
}

private $numVotesGame2;

public function getNumVotesGame2()
{
    return $this->numVotesGame2;
}

public function setNumVotesGame2($numVotesGame2)
{
    $this->numVotesGame2 = $numVotesGame2;

    return $this;
}

private $percentVotesGame2;

public function getPercentVotesGame2()
{
    return $this->percentVotesGame2;
}

public function setPercentVotesGame2($percentVotesGame2)
{
    $this->percentVotesGame2 = $percentVotesGame2;

    return $this;
}

private $numVotesGame3;

public function getNumVotesGame3()
{
    return $this->numVotesGame3;
}

public function setNumVotesGame3($numVotesGame3)
{
    $this->numVotesGame3 = $numVotesGame3;

    return $this;
}

private $percentVotesGame3;

public function getPercentVotesGame3()
{
    return $this->percentVotesGame3;
}

public function setPercentVotesGame3($percentVotesGame3)
{
    $this->percentVotesGame3 = $percentVotesGame3;

    return $this;
}

private $numVotesGame4;

public function getNumVotesGame4()
{
    return $this->numVotesGame4;
}

public function setNumVotesGame4($numVotesGame4)
{
    $this->numVotesGame4 = $numVotesGame4;

    return $this;
}

private $percentVotesGame4;

public function getPercentVotesGame4()
{
    return $this->percentVotesGame4;
}

public function setPercentVotesGame4($percentVotesGame4)
{
    $this->percentVotesGame4 = $percentVotesGame4;

    return $this;
}
```
