var frisby = require('frisby'),
	URL = "http://denizen.dev";

frisby.create('Valid Password Grant')
	.post(URL+'/oauth/token', {
		username: "api@denizen.com",
		password: "youllneverguess",
		client_id: "krAKQG20vByjJt40Xi50",
		client_secret: "LmjghywdeOUXCN9rsEgD7y7k7VfvGxWhbfxsgDLx",
		grant_type: "password"
	})
	.expectStatus(200)
	.afterJSON(function(response){

		var token = response.access_token;
		
		frisby.create('Update a users profile')
			.put(URL + '/me', {
				first_name: 'Changy',
				last_name: 'McChangerson'
			})
			.addHeader('Authorization', 'Bearer ' + token)
			.expectStatus(200)
			.afterJSON(function(response){

				frisby.create('Access a users own profile')
					.get(URL + '/me')
					.addHeader('Authorization', 'Bearer ' + token)
					.expectStatus(200)
					.expectJSON('user', {
						id: 1
					})
				.toss();

			})
		.toss();

	})
.toss();
