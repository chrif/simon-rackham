# Simon Rackham on Spotify

## Description

This is a parser to prepare a CSV import into a [Google Spreadsheet](https://docs.google.com/spreadsheets/d/1CRpVbfeCu_HMWR1s7nSp7tVopaSbpVbv5uNrOaKivd4/edit#gid=1936319743)

The spreadsheet is about composer Simon Rackham and the availability of his recordings on Spotify.

## Usage

- [Install Docker](https://www.docker.com/community-edition#/download)
- On the terminal, run: `docker-compose run --rm php ash -c "composer install && bin/console a:e"`
- Result is written to `parsed.csv`
