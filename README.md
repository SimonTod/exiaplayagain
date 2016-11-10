exiaplayagain
=============

A Symfony project created on October 21, 2016, 3:01 pm.

####TODO

- [x] games database
    - name
    - type
    - pay / free / crack
    - last time played
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
- [ ] users possibility to see and vote for a vote
- [ ] display other informations like stats and others

####SQL FOR DATABASE VOTES

```sql
CREATE TABLE `exiaplayagain`.`votes` (
    `id` INT NOT NULL AUTO_INCREMENT , 
    `limited_date` DATETIME NOT NULL , 
    `game_1` INT NOT NULL , 
    `game_2` INT NOT NULL , 
    `game_3` INT NOT NULL , 
    `game_4` INT NOT NULL , 
    PRIMARY KEY (`id`),
    FOREIGN KEY (game_1) REFERENCES games(id),
    FOREIGN KEY (game_2) REFERENCES games(id),
    FOREIGN KEY (game_3) REFERENCES games(id),
    FOREIGN KEY (game_4) REFERENCES games(id)
) ENGINE = InnoDB;
```

####GENERATE ENTITIES FROM EXISTING DATABASE

```bash
php app/console doctrine:mapping:import --force ExiaplayagainBundle xml
sudo php app/console doctrine:mapping:convert --force annotation ./src
sudo php app/console doctrine:generate:entities ExiaplayagainBundle
```