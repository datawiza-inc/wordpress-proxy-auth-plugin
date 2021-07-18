# Wordpress Proxy Auth Plugin

## Introduction

The Wordpress Proxy Auth Plugin helps developers/DevOps/admins easily implement authentication and authorization for WordPress by using a [JWT (JSON Web Token)](https://en.wikipedia.org/wiki/JSON_Web_Token) provided by a reverse proxy.

This could be employed to achieve SSO (OAUTH/OIDC and SAML) to a Cloud Identity Provider (e.g., Azure Active Directory, Okta, Auth0) by using an Identity-Aware Proxy, e.g., [Datawiza Access Broker](https://www.datawiza.com/access-broker) and [Google IAP](https://cloud.google.com/iap).

Note that the plugin requires a reverse proxy sitting in front of the WordPress site. The reverse proxy performs authentication, and passes the user name and role in a JWT to the plugin via a HTTP header called `DW-TOKEN`.

By using [Datawiza Access Broker](https://console.datawiza.com/), you get a [configuration-based](https://docs.datawiza.com/step-by-step/step1.html) [no-code solution](https://docs.datawiza.com/), following the detail instruction [here](https://docs.datawiza.com/step-by-step/step1.html).

If you decide to use your own reverse proxy, please follow the instructions below.

## How it works

* The plugin retrieves the user id (email) from the JWT and then checks if such a user exists. If not, the plugin creates a new user by using this email and signs him/her in.
* The plugin retrieves the user role from the JWT and sets it as the user's role in WordPress.
* The plugin expects the JWT including user id and role as a HTTP header `DW-TOKEN`. For example, the payload of JWT may look like:  

```json
{
  "role": "administrator",
  "email": "admin@yourwebsite.com"
}
```

## Plugin config in Wordpress

In `Setting` -> `Datawiza Proxy Auth`, you need to input a private secret which is used as a Cryptography Key. Such secret is shared between the plugin and the reverse proxy which is responsible for passing the JWT to the plugin. The Signing Algorithm for the JWT is `HS256`.

**!!! NOTES !!!**

* **If the enabled Proxy Auth Plugin cannot retrieve the expected JWT in the HTTP header, the plugin will not work. The authentication will use the default authentication of wordpress and you will see an error banner on top of the wordpress pages.**
* **MAKE SURE that clients cannot bypass the reverse proxy. This is to prevent people from sending forged malicious requests with arbitrary JWTs directly to WordPress.**
* **It's recommended that the reverse proxy in front of the WordPress site erases the incoming http request’s `DW-TOKEN` header. The `DW-TOKEN` header should be generated by the reverse proxy only.**
* **If admin doesn’t assign role to the user, user’s role will be subscriber for WordPress by default.**
* **If user’s role has been updated in JWT, the plugin will update the role in WordPress accordingly.**

## Generate the JWT required by the plugin  

If you are using openresty/lua-nginx-module, here is the code sample to generate the JWT required by the plugin:

```lua
jwt = require("resty.jwt")
local jwt_token = jwt:sign(
    "jwt_secret",
    {
    header={typ="JWT", alg="HS256"},
    payload={email="admin@yourwebsite.com", role="administrator"}
    })
ngx.req.set_header('DW-TOKEN', jwt_token)
```

The `jwt_secret` above should be the same private secret input in `Setting` -> `Datawiza Proxy Auth`. The `role` in `payload` is optional. If it's not specified, the default role is `subscriber`. For more details about `lua-resty-jwt`, you can visit [here](https://github.com/SkyLothar/lua-resty-jwt).
