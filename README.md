# trips
Trips api

# Authentication JWT 
```
$ mkdir -p var/jwt
$ openssl genpkey -out var/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
$ openssl pkey -in var/jwt/private.pem -out config/jwt/public.pem -pubout

Don't forget to change the JWT_PASSPHRASE in the .env file
```

# API docs: 
/api/docs

# Relations 
Api uses `IRI` strings instead of ids for relations for e.g. 
```json
{
  "name": "name",
  "startDate": "2020-01-01",
  "endDate": "2020-02-01",
  "country": "/api/countries/2",
  "users": "/api/users/1"
}
```
# Supported formats 
`application/json`, `application/ld+json` https://json-ld.org/ , `text/html`

json ld specification is with hydra http://www.hydra-cg.com/ if you want to avoid it negotiate for `application/json`

# Countries sync
There is a command for sync countries from `https://restcountries.eu` which can be executed with `php bin/console sync:countries`
