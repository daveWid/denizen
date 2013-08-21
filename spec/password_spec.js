var frisby = require('frisby'),
	URL = "http://denizen.dev";

frisby.create('Valid Password Grant')
	.post(URL+'/oauth/token', {
		client_id: "krAKQG20vByjJt40Xi50",
		client_secret: "LmjghywdeOUXCN9rsEgD7y7k7VfvGxWhbfxsgDLx",
		grant_type: "client_credentials"
	})
	.expectStatus(200)
	.afterJSON(function(response){

		var token = response.access_token;

/*
 Method | Route           | Grant Type         | Description
--------|-----------------|--------------------|----
 PUT    | /password       | client_credentials | Change the password
 POST   | /password/token | client_credentials | Generate an access token used to update the password
 */

		frisby.create('Generate a password token')
			.post(URL + '/password/token', {
				email: 'api@denizen.com'
			})
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
						password: 'afinepassword',
						confirm_password: 'afinepassword'
					})
					.addHeader('Authorization', 'Bearer ' + token)
					.expectStatus(200)
					.afterJSON(function(response){

						frisby.create('Token expires after used')
							.put(URL + '/password', {
								password_token: passwordToken,
								password: 'adifferentpassword',
								confirm_password: 'adifferentpassword'
							})
							.addHeader('Authorization', 'Bearer ' + token)
							.expectStatus(400)
							.afterJSON(function(response){

								// Reset back to normal
								frisby.create('Generate reset token')
									.addHeader('Authorization', 'Bearer ' + token)
									.post(URL + "/password/token", {
										email: 'api@denizen.com'
									})
									.afterJSON(function(response){
										frisby.create('Reset password')
											.addHeader('Authorization', 'Bearer ' + token)
											.put(URL + '/password', {
												password_token: response.password_token,
												password: 'youllneverguess',
												confirm_password: 'youllneverguess'
											})
										.toss();
									})
								.toss();

							})
						.toss();

					})
				.toss();

			})
		.toss();

	})
.toss();
