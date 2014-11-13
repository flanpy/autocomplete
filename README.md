Plugin autocomplete
============
Autocomplete plugin for Roundcube webmail using jQueryUI (*BETA*)

## Installation :

1. Download plugin in plugins folder of your roundcube installation.
2. Add "autocomplete" (+ "jqueryui" if not already defined) to $config['plugins'] in cfg file.


## Features :
In adressbook view, the plugin add the autocomplete feature for no-LDAP users via search method defined in rcube_contacts class.<br/>
In mail folders, the plugin add the autocomplete feature for both emails & names, contacting the IMAP server to find a result.
####How it works:
While you press a key in a input identified by `id="quicksearchbox"` while `task="adressbook"` or `"mail"`, an AJAX request is sent to the server to ask contacts matching your query string.
The server send back an array of maximum 4 answers to update the `source` option of autocomplete feature defined by jQueryUI.

## ToDo :
- [ ] Caching previous requests
- [ ] Take into consideration filters in mail folders search
