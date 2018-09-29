#!/usr/bin/php
<?php
include 'boz-mw/autoload.php';
include 'config.php';

defined( 'MARKADMINS_PAGE' ) or exit( "fill config" );

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
]

// fetch all the sysops, checkusers, etc. from Italian wikipedia
// https://it.wikipedia.org/w/api.php?action=help&modules=query%2Ballusers
$results = $wit->createQuery( [
	'action'  => 'query',
	'list'    => $lists,

	// allusers
	'auprop'  => 'groups',
	'augroup' => array_values( $WIT_GROUPS ),
	'aulimit' => 400,

	// globalallusers
	'agugroup' => 'steward',
	'agulimit' => 200,
] );

// query continuation
foreach( $results->getGenerator() as $result ) {
	foreach( $lists as $list ) {
		foreach( $result->query->{ $list } as $user ) {
			$name = $user->name;
		    	$users[ $name ] = isset( $users[ $name ] ) ? $users[ $name ] : [];
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
