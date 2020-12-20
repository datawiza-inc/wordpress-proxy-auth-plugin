# wordpress-proxy-auth-plugin
## Introduction
The [proxy-auth plugin](https://wordpress.org/plugins/reverse-proxy-auth-widget/) helps developers/DevOps/admins easily implement authentication and authorization for WordPress by using the HTTP header fields provided by a reverse proxy. 

This could be employed to achieve SSO (OAUTH/OIDC and SAML) to a Cloud Identity Provider (e.g., Azure Active Directory, Okta, Auth0) by using an Identity-Aware Proxy, e.g., [Datawiza Access Broker](https://www.datawiza.com/access-broker) and [Google IAP](https://cloud.google.com/iap).

## How it works
* The plugin retrieves the user id (email) from the HTTP header and then checks if such a user exists. If not, the plugin creates a new user by using this email and signs him/her in. 
* The plugin retrieves the user role from the HTTP header and sets it as the user's role in WordPress. 
