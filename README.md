# Eventoj: Events management system

Eventoj (pronounce "eventoy") means ̀"events" in espéranto.

This application have been built to propulse the events management website of AlterCampagne.

## Getting started

Clone this repository and run `bin/doctor` to ensure you have everything installed (basically `make` & `docker`).

Once it's OK, run `make install` to start the docker stack and prepare the application.
Go to https://eventoj.local to use the application.

## Development

### Database schema

You can find more information regarding the database schema here: https://altercampagne.github.io/eventoj/
This site is autmatically generated with SchemaSpy when needed.

### Paheko

All transactions are sync'ed with our accounting software (Paheko) which is available at the following address: http://127.0.0.1:8081
