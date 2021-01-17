=== Datawiza Proxy Auth Plugin - SSO ===
Contributors: Datawiza
Tags: proxy,auth,SSO,OIDC,SAML,Oauth,Single Sign-On
Requires at least: 3.0.1
Tested up to: 5.5.6
Requires PHP: 5.6
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Datawiza Proxy Auth Plugin helps developers/DevOps/admins easily implement authentication and authorization for WordPress by using a JWT token provided by a reverse proxy.

== Description ==

The Datawiza Proxy Auth Plugin helps developers/DevOps/admins easily implement authentication and authorization for WordPress by using a JWT token provided by a reverse proxy.

This could be employed to achieve SSO (OAUTH/OIDC and SAML) to a Cloud Identity Provider (e.g., Azure Active Directory, Okta, Auth0) by using an Identity-Aware Proxy, e.g., [Datawiza Access Broker](https://www.datawiza.com/access-broker) and [Google IAP](https://cloud.google.com/iap).

Note that the plugin requires a reverse proxy sitting in front of the WordPress site. The reverse proxy performs authentication, and passes the user name and role in a JWT token to the plugin via a HTTP header.

## How it works

* The plugin retrieves the user id (email) from the JWT token and then checks if such a user exists. If not, the plugin creates a new user by using this email and signs him/her in.
* The plugin retrieves the user role from the JWT token and sets it as the user\'s role in WordPress.
* The plugin expects the JWT token including user id and role as a HTTP header `DW-Token`.

## Plugin Configuration in Wordpress

In `Setting` -> `Datawiza Proxy Auth`, you need to input a private secret which is used as a Cryptography Key. Such secret is shared among the plugin and the reverse proxy which is responsible for passing the JWT token to the plugin. The Signing Algorithm for the JWT token is `HS256`.

== Installation ==
1. Activate the plugin through the \"Plugins\" menu in WordPress.
2. Input private secret in Settings -> Datawiza Proxy Auth Plugin.

== Frequently Asked Questions ==

== Screenshots ==

== Changelog ==
= 1.1.1 =
* Retrieves user info from encrypted DW-Token instead of X-User.

= 1.1.0 =
* Initial release.

== Upgrade Notice ==

