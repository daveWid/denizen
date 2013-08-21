# Denizen

User management with OAuth2 should be fun (and required)!

This project is powered by the [Slim framework](http://www.slimframework.com/)
paired with [OAuth2 Server](https://github.com/php-loep/oauth2-server).

## OAuth2?

Ya, it's pretty sweet and if you aren't using it to secure your API you should be.

If you want to learn more about it please visit the [offical site](http://oauth.net/2/).

## Routes

Below is a list of all of the routes that are provided and the grant type

### OAuth Token

 Method | Route        | Grant Type                     | Description
--------|--------------|--------------------------------|---
 POST   | /oauth/token | password or client_credentials | Creates a new access token

The /oauth/token route will generate an access token for a single user `password`
grant or an application `client_credentials` grant. All of the user resources
below will require an access token with the specified grant type.

Currently this project only supports the `password` and `client_credentials` grant types.
_The OAuth2 server library used by this project is spec compliant though, so please
help enhance this library!_

### User Resources

 Method | Route        | Grant Type          | Description
--------|--------------|---------------------|---
 GET    | /users       | client_credentials  | Get a list of all users
 GET    | /users/:id   | client_credentials  | Get 1 user by id
 POST   | /users       | client_credentials  | Create a new user
 PUT    | /users/:id   | client_credentials  | Update a user by id
 DELETE | /users/:id   | client_credentials  | Delete a user by id

These user interactions are all specified for the `client_credentials` grant type.
They will give applications access to user operations.

### Profile

 Method | Route | Grant Type   | Description
--------|-------|--------------|----
 GET    | /me   | password     | Get the users profile
 PUT    | /me   | password     | Update the users profile

The profile operations will allow users to get and update their own profiles.
This doesn't include changing passwords as that process is a little bit more involved.

Which leads us to...

### Password Management

 Method | Route           | Grant Type         | Description
--------|-----------------|--------------------|----
 PUT    | /password       | client_credentials | Change the password
 POST   | /password/token | client_credentials | Generate an access token used to update the password


## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request
