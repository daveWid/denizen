var frisby = require('frisby'),
	URL = "http://denizen.dev";


frisby.create('OAuth2 Client Credentials Login')
	.post(URL+'/oauth/token', {
		client_id: "krAKQG20vByjJt40Xi50",
		client_secret: "LmjghywdeOUXCN9rsEgD7y7k7VfvGxWhbfxsgDLx",
		grant_type: "client_credentials"
	})
	.expectStatus(200)
	.afterJSON(function(response) {

/*
 Method | Route        | Grant Type          | Description
--------|--------------|---------------------|---
 GET    | /users       | client_credentials  | Get a list of all users
 GET    | /users/:id   | client_credentials  | Get 1 user by id
 POST   | /users       | client_credentials  | Create a new user
 PUT    | /users/:id   | client_credentials  | Update a user by id
 DELETE | /users/:id   | client_credentials  | Delete a user by id
 */

	/** Testing the for /user endpoints */

		frisby.create('Fetching all users')
			.get(URL+'/users')
			.addHeader('Authorization', 'Bearer ' + response.access_token)
			.expectStatus(200)
			.inspectJSON()
			/**\/
			.expectJSON({
				user: {
					user_id: Number,
					email: String,
					first_name: String,
					last_name: String
				}
			})
			/**/
		.toss();

	/** End /user **/
	})
.toss();
