var frisby = require('frisby'),
	URL = "http://denizen.dev";

/**
 * ----- Client Credentials ----
 * id: krAKQG20vByjJt40Xi50
 * secret: LmjghywdeOUXCN9rsEgD7y7k7VfvGxWhbfxsgDLx
 * ----- API User -------
 * email: api@denizen.com
 * password: youllneverguess
 */

frisby.create('Valid Password Grant')
	.post(URL+'/oauth/token', {
		username: "api@denizen.com",
		password: "youllneverguess",
		client_id: "krAKQG20vByjJt40Xi50",
		client_secret: "LmjghywdeOUXCN9rsEgD7y7k7VfvGxWhbfxsgDLx",
		grant_type: "password"
	})
	.expectStatus(200)
	.expectHeaderContains('Content-Type', 'application/json')
	.expectJSONTypes({
		access_token: String,
		token_type: String,
		expires: Number,
		expires_in: Number
	})
.toss();

frisby.create('Valid Client Credentials Grant')
	.post(URL+'/oauth/token', {
		client_id: "krAKQG20vByjJt40Xi50",
		client_secret: "LmjghywdeOUXCN9rsEgD7y7k7VfvGxWhbfxsgDLx",
		grant_type: "client_credentials"
	})
	.expectStatus(200)
	.expectHeaderContains('Content-Type', 'application/json')
	.expectJSONTypes({
		access_token: String,
		token_type: String,
		expires: Number,
		expires_in: Number
	})
.toss();