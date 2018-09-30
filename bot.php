#!/usr/bin/php
<?php
# Copyright (C) 2018 Valerio Bozzolan
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as
# published by the Free Software Foundation, either version 3 of the
# License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program. If not, see <https://www.gnu.org/licenses/>.

// autoload boz-mw
include 'boz-mw/autoload.php';

// load configuration
include 'config.php';

// groups on Meta
$META_GROUPS = [
	'S'   => 'steward',
];

// groups on Italian Wikipedia
$WIT_GROUPS = [
	'B'   => 'bureaucrat',
	'CU'  => 'checkuser',
	'A'   => 'sysop',
	'AI'  => 'interface-admin',
];

// all groups
$GROUPS = array_merge( $META_GROUPS, $WIT_GROUPS );

// user list
$users = [];

// counter indexed by group name
$counts = [];
foreach( $GROUPS as $group ) {
	$counts[ $group ] = 0;
}

// choosen wikis
$wit  = \wm\WikipediaIt::getInstance();

$lists = [
	'allusers',
	'globalallusers',
];

// fetch all the sysops, checkusers, etc. from Italian wikipedia
// https://it.wikipedia.org/w/api.php?action=help&modules=query%2Ballusers
$results = $wit->createQuery( [
	'action'   => 'query',
	'list'     => $lists,

	// allusers
	'auprop'   => 'groups',
	'augroup'  => array_values( $WIT_GROUPS ),
	'aulimit'  => 400,

	// globalallusers
	'aguprop'  => 'groups',
	'agugroup' => array_values( $META_GROUPS ),
	'agulimit' => 200,
] );

// query continuation
foreach( $results->getGenerator() as $result ) {
	foreach( $lists as $list ) {
		foreach( $result->query->{ $list } as $user ) {
			$name = $user->name;
			if( ! isset( $users[ $name ] ) ) {
				$users[ $name ] = [];
			}
			if( isset( $user->groups ) ) {
				foreach( $user->groups as $group ) {
					$legend = array_search( $group, $GROUPS, true );
					if( $legend ) {
						$users[ $name ][] = $legend;
						$counts[ $group ]++;
					}
				}
			}
		}
	}
}

// stats message
$stats = [];
foreach( $counts as $group => $count ) {
	$stats[] = "$group: $count";
}
$stats = implode( ", ", $stats );

$wit->login();
$wit->edit( [
	'title'         => MARKADMINS_PAGE,
	'summary'       => "Bot: aggiornamento elenco utenti: $stats",
	'text'          => json_encode( [ 'legend' => $GROUPS, 'users' => $users ] ),
	'contentformat' => 'application/json',
	'bot'           => 1,
] );

\cli\Log::info( $stats );
