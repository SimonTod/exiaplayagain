exiaplayagain
=============

A Symfony project created on October 21, 2016, 3:01 pm.

yoyoyo test commit

####TODO

- [x] games database
    - name
    - type
    - pay / free / crack
    - info
    - url
    - image
    - last time played
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
- [x] index page
- [x] SSL Let's Encrypt Certificate (HTTPS)
- [ ] better user management using FOSUserBundle

https://openclassrooms.com/courses/developpez-votre-site-web-avec-le-framework-symfony/securite-et-gestion-des-utilisateurs-1#/id/r-3624667

####DEV INFO
#####Discord Tokens Type :
- 0 -> verify
- 1 -> login

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
sudo chmod -R 777 app/cache app/logs
```

####SQL CREATE ALL TABLES

```sql
CREATE TABLE `exiaplayagain`.`games` 
( 
    `id` INT NOT NULL AUTO_INCREMENT , 
    `name` VARCHAR(255) NOT NULL ,
    `type` VARCHAR(255) NOT NULL ,
    `availability` VARCHAR(255) NOT NULL ,
    `price` FLOAT ,
    `info` TEXT ,
    `url` VARCHAR(255) ,
    `image` VARCHAR(255) ,
    `last_played` DATE ,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE `exiaplayagain`.`users` 
( 
    `id` INT NOT NULL AUTO_INCREMENT , 
    `username` VARCHAR(255) NOT NULL ,
    `name` VARCHAR(255) NOT NULL ,
    `password` VARCHAR(255) NOT NULL ,
    `is_admin` tinyint(1) NOT NULL DEFAULT 0,
    `discord_username` VARCHAR(255) NULL DEFAULT NULL,
    `discord_id` INT NULL DEFAULT NULL,
    `discord_is_verified` tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE `exiaplayagain`.`votes` 
( 
    `id` INT NOT NULL AUTO_INCREMENT , 
    `limited_date` DATETIME NOT NULL ,
    `game_1` INT NOT NULL ,
    `game_2` INT NOT NULL ,
    `game_3` INT NOT NULL ,
    `game_4` INT NOT NULL ,
    PRIMARY KEY (`id`) ,
    FOREIGN KEY (game_1) REFERENCES games(id),
    FOREIGN KEY (game_2) REFERENCES games(id),
    FOREIGN KEY (game_3) REFERENCES games(id),
    FOREIGN KEY (game_4) REFERENCES games(id)
) ENGINE = InnoDB;

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

CREATE TABLE `exiaplayagain`.`discord_tokens`
(
    `id` INT NOT NULL AUTO_INCREMENT , 
    `user` INT NOT NULL , 
    `token` INT NOT NULL , 
    `type` INT NOT NULL , 
    `validity` DATETIME DEFAULT NULL ,
    `ip` VARCHAR(255) NOT NULL ,
    PRIMARY KEY (`id`),
    FOREIGN KEY (user) REFERENCES users(id)
) ENGINE = InnoDB;

DELIMITER $$
CREATE TRIGGER `settime` BEFORE INSERT ON `discord_tokens` FOR EACH ROW SET NEW.`validity` = TIMESTAMPADD(MINUTE,5,NOW())
$$
DELIMITER ;
```

#####ADD IN ENTITY DISCORDTOKENS.PHP

```php
public function __construct()
{
    $this->validity = null;
    $this->token = rand(0, 2000000000);
}
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

private $totalUsersVotes;

public function getTotalUsersVotes()
{
    return $this->totalUsersVotes;
}

public function setTotalUsersVotes($totalUsersVotes)
{
    $this->totalUsersVotes = $totalUsersVotes;

    return $this;
}
```
