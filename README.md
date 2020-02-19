Robot opinions: [![Maintainability](https://api.codeclimate.com/v1/badges/8bdffcbe593df3a79495/maintainability)](https://codeclimate.com/github/ImperialAdepts/WebGame/maintainability)

How to run on linux
=========
- install `docker` and `docker-compose`: `sudo apt-get install docker docker-compose`
- checkout this project `git clone https://github.com/ImperialAdepts/WebGame`
- run docker container: `sh run.sh`
- create databases schema and fill initial data `sh recreate_database.sh`
- go to `http://localhost:2000/`
