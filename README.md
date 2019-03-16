# sussex-ldap

Example implementation of authorisation using the Sussex LDAP server. 

## Install
Via a local composer repository
```bash
git clone https://github.com/guym4c/sussex-ldap.git sussex-ldap/
```

Add the following to your ```composer.json```:
```json
"repositories": [
    {
        "type": "path",
        "url":"sussex-ldap/"
    }
]
```

Then install
```
composer require guym4c/sussex-ldap
```

## Usage

Open a connection to the server:

```php
$ldap = new Connection();
```

To authorise a user, pass only the bare username without the domain (e.g. ```gm335```) and their password.

```php
$result = $ldap->authorise($user, $password);
if ($result->isBound()) {
    // user was logged in
}
```

If the user was not authorised, the ```BindResult``` that ```authorise()``` returns exposes the error using ```result->getError()```.

## Contributing
Bearing in mind this is intended to be an example implementation - feel free to open a PR.

