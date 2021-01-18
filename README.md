# Proxy Auth Plugin

## Introduction

The Proxy Auth Plugin helps developers/DevOps/admins easily implement authentication and authorization for WordPress by using a [JWT(JSON Web Token)](https://en.wikipedia.org/wiki/JSON_Web_Token) provided by a reverse proxy.

This could be employed to achieve SSO (OAUTH/OIDC and SAML) to a Cloud Identity Provider (e.g., Azure Active Directory, Okta, Auth0) by using an Identity-Aware Proxy, e.g., [Datawiza Access Broker](https://www.datawiza.com/access-broker) and [Google IAP](https://cloud.google.com/iap).

Note that the plugin requires a reverse proxy sitting in front of the WordPress site. The reverse proxy performs authentication, and passes the user name and role in a JWT to the plugin via a HTTP header.

## How it works

* The plugin retrieves the user id (email) from the JWT and then checks if such a user exists. If not, the plugin creates a new user by using this email and signs him/her in.
* The plugin retrieves the user role from the JWT and sets it as the user\'s role in WordPress.
* The plugin expects the JWT including user id and role as a HTTP header `DW-Token`. For example, the payload of JWT may look like:  

```json
{
  "role": "administrator",
  "email": "admin@yourwebsite.com"
}
```

## Plugin config in Wordpress

In `Setting` -> `Datawiza Proxy Auth`, you need to input a private secret which is used as a Cryptography Key. Such secret is shared among the plugin and the reverse proxy which is responsible for passing the JWT to the plugin. The Signing Algorithm for the JWT is `HS256`.

**!!! NOTES !!!**

* **If the enabled Proxy Auth Plugin cannot retrieve the expected JWT in the HTTP header, the plugin will not work. The authentication will use the default authentication of wordpress and you will see an error banner on top of the wordpress pages.**

## JWT

If you are using openresty/lua-nginx-module, here is the code sample to generate the JWT required by the plugin:  

```
# nginx.conf:

lua_package_path "/path/to/lua-resty-jwt/lib/?.lua;;";

server {
        default_type text/plain;
        location = /sign {
            content_by_lua '
                local cjson = require "cjson"
                local jwt = require "resty.jwt"

                local jwt_token = jwt:sign(
                    "jwt_secret",
                    {
                        header={typ="JWT", alg="HS256"},
                        payload={foo="bar"}
                    }
                )
                ngx.req.set_header('DW-TOKEN', jwt_token)
            ';
        }
    }
```

The `jwt_secret` above should be the private secret inputed in `Setting` -> `Datawiza Proxy Auth`.  
For more details about `lua-resty-jwt`, you can visit [here](https://github.com/SkyLothar/lua-resty-jwt).  
And [here](https://en.wikipedia.org/wiki/JSON_Web_Token#Implementations) is about other languages and frameworks' implementations.  

## Step by step instruction to use the plugin with the Datawiza Access Broker

The plugin works with any reverse proxy as long as the proxy can pass the correct JWT in the HTTP header to the plugin. Here is the step by step instruction if you are using Datawiza Access Broker.

Signup or login in [Datawiza Management Console](https://console.datawiza.com). Follow the [docs](https://docs.datawiza.com) to create a deployment, IdP, and an application.

Step 1. When creating the deployment, take a note of the provisioning key and secret.

Step 2. Under Attributes tab and add add a new attribute: For Okta, `Field` is "email", `Expected` is "email", `Type` is "Header".

After installing the Proxy Auth Plugin, you need to input provisioning secrect in Step 1 as the private secret in `Setting` -> `Proxy Auth Plugin`.
