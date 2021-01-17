# Datawiza Proxy Auth Plugin

## Introduction

The Datawiza Proxy Auth Plugin helps developers/DevOps/admins easily implement authentication and authorization for WordPress by using a JWT token provided by a reverse proxy.

This could be employed to achieve SSO (OAUTH/OIDC and SAML) to a Cloud Identity Provider (e.g., Azure Active Directory, Okta, Auth0) by using an Identity-Aware Proxy, e.g., [Datawiza Access Broker](https://www.datawiza.com/access-broker) and [Google IAP](https://cloud.google.com/iap).

Note that the plugin requires a reverse proxy sitting in front of the WordPress site. The reverse proxy performs authentication, and passes the user name and role in a JWT token to the plugin via HTTP headers.

## How it works

* The plugin retrieves the user id (email) from the JWT token and then checks if such a user exists. If not, the plugin creates a new user by using this email and signs him/her in.
* The plugin retrieves the user role from the JWT token and sets it as the user\'s role in WordPress.
* The plugin expects the JWT token including user id and role as a HTTP header `DW-Token`.

## Plugin Configuraiton in Wordpress

In `Setting` -> `Datawiza Proxy Auth`, you need to input a private secret which is used as a Cryptography Key. Such secret is shared among the plugin and the reverse proxy which is responsible for passing the JWT token to the plugin. The Signing Algorithm for the JWT token is `HS256`.

**!!! NOTES !!!**
* **If the enabled Proxy Auth Plugin cannot retrieve the expected JWT token in the HTTP header, the plugin will not work. The authenticaion will use the default authenticaion of wordpress and you will see an error banner on top of the wordpress pages.**

## Step by step instruction to use the plugin with the Datawiza Access-Broker

The plugin works with any reverse proxy as long as the proxy can pass the correct JWT token in the HTTP header to the plugin. Here is the step by step instruction if you are using Datawiza Access Broker.

Signup or login in [Datawiza Management Console](https://console.datawiza.com)

Create a deployment and a application and set the public domain as https://localhost:9772 and upstream servers as http://wordpress

Create a pair of provisioning key and keep a note of it.

Under Attributes tab and create a new attribute: For Okta, `Field` is "email", `Expected` is "email", `Type` is "Header".

Create a IdP and set up the configuration. you can follow [Okta Configuration](https://docs.datawiza.com/idp/okta.html)

Copy the example.docker-compose.yml and rename it as docker-compose.yml. Replace the provisioning key and secret.

Run the following command to start Access Broker and Wordpress

```sh
docker-compose up -d
```

After installing the WordPress, you need to input private secret in `Setting` -> `Proxy Auth Plugin`.

