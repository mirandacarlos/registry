# Registry

API rest to handle a list of elements stored in PHP session

## Install

Enter in the uncompressed directory and run:
docker compose up

## Usage

It is available at localhost/registry/{**action**}?element={**parameter**}
where action can be one of the following:
- **check**: check if parameter is in the registry
- **add**: add parameter to the registry
- **remove**: remove parameter from the registry
- **diff**: show diff between registry and parameter

and parameter can be a string or a coma separated string list (diff) containing only alphanumeric or white space characters

Examples:

localhost/registry/add?element=symfony

localhost/registry/check?element=php

localhost/registry/diff?element=car,plane,bike