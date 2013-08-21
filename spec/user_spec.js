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

		var token = response.access_token;

	// Testing the for /user endpoints

		frisby.create('Fetching all users')
			.get(URL+'/users')
			.addHeader('Authorization', 'Bearer ' + token)
			.expectStatus(200)
			.expectJSONTypes('users.*', {
				id: Number,
				email: String,
				first_name: String,
				last_name: String
			})
		.toss();

		frisby.create('Fetching 1 user')
			.get(URL+'/users/1')
			.addHeader('Authorization', 'Bearer ' + token)
			.expectStatus(200)
			.expectJSONTypes('user', {
				id: Number,
				email: String,
				first_name: String,
				last_name: String
			})
		.toss();

		frisby.create('Updating user information')
			.put(URL+'/users/1', {
				first_name: 'Just',
				last_name: 'Changed',
				bunch: 'ofjunk',
				will: 'getcutout...'
			})
			.addHeader('Authorization', 'Bearer ' + token)
			.expectStatus(200)
			.expectJSON('user', {
				first_name: 'Just',
				last_name: 'Changed'
			})
		.toss();

		frisby.create("Create user with invalid data returns errors")
			.post(URL + '/users', {})
			.addHeader('Authorization', 'Bearer ' + token)
			.expectStatus(400)
			.expectJSONTypes({
				errors: Array
			})
		.toss();

		frisby.create('Create a user')
			.post(URL+'/users', {
				first_name: "New",
				last_name: "User",
				email: "just@atest.com",
				password: 'test1234',
				confirm_password: 'test1234'
			})
			.addHeader('Authorization', 'Bearer ' + token)
			.expectStatus(201)
			.expectJSONTypes('user', {
				id: Number,
				email: String,
				first_name: String,
				last_name: String
			})
			.afterJSON(function(response){

				var id = response.user.id;

				frisby.create('Duplicate email returns an error')
					.post(URL + '/users', {
						first_name: response.user.first_name,
						last_name: response.user.last_name,
						email: response.user.email,
						password: 'iforgotmyoldpassword',
						confirm_password: 'iforgotmyoldpassword'
					})
					.addHeader('Authorization', 'Bearer ' + token)
					.expectStatus(400)
					.expectJSON({
						errors: ['email|unique']
					})
					.afterJSON(function(response){

						frisby.create('Delete a user')
							.delete(URL + '/users/' + id)
							.addHeader('Authorization', 'Bearer ' + token)
							.expectStatus(200)
						.toss();

					})
				.toss();

			})
		.toss();

	// /user
	})
.toss();
