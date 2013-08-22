var frisby = require('frisby'),
	URL = "http://denizen.dev";

frisby.create('Valid Client Credentials Grant')
	.post(URL+'/oauth/token', {
		client_id: "krAKQG20vByjJt40Xi50",
		client_secret: "LmjghywdeOUXCN9rsEgD7y7k7VfvGxWhbfxsgDLx",
		grant_type: "client_credentials"
	}, {json: true})
	.expectStatus(200)
	.afterJSON(function(response){

		var token = response.access_token;

		/** Run a post and put request with json data */

		frisby.create('Generate a password token')
			.post(URL + '/password/token', {
				email: 'api@denizen.com'
			}, {json: true})
			.addHeader('Authorization', 'Bearer ' + token)
			.expectStatus(200)
			.expectJSONTypes({
				id: Number,
				password_token: String,
				password_token_expires: Number,
				password_token_expires_in: Number
			})
			.afterJSON(function(response){

				var passwordToken = response.password_token;

				frisby.create('Change the password')
					.put(URL + '/password', {
						password_token: passwordToken,
						password: 'youllneverguess',
						confirm_password: 'youllneverguess'
					})
					.addHeader('Authorization', 'Bearer ' + token)
					.expectStatus(200)
				.toss();

			})
		.toss();

	})
.toss();

