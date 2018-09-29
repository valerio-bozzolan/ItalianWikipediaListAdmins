# Mark admins, check users, stewards from Italian Wikipedia

This is an Italian Wikipedia bot. This bot is suitable to be called once a day and populate a JSON with users in special groups.

Look at the involved page:

https://it.wikipedia.org/wiki/Utente:ItwikiBot/AdminList

To see an example of client-side script:

https://it.wikipedia.org/wiki/Utente:Valerio_Bozzolan/MarkAdminsLocalStorage.js

## Configuration

Copy `config-example.php` and save it as `config.php`. Fill it.

## Execution

    ./bot.php

## Hacking

Look at [./bot.php](bot.php)

## License

Copyright (C) 2018 [Valerio Bozzolan](https://it.wikipedia.org/wiki/Utente:Valerio_Bozzolan)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program. If not, see <https://www.gnu.org/licenses/>.
