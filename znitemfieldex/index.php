<?php

$DIR_LIBS = '';
$strRel = '../../../'; 
require($strRel . 'config.php');
if (!$member->isLoggedIn()) doError("You\'re not logged in.");
include_libs('PLUGINADMIN.php');
include_libs('MEDIA.php');

include_once('znitemfieldex.class.inc');

$myAdmin = new znItemFieldEX_ADMIN();
$myAdmin->action( (requestVar('action')) ? requestVar('action') : 't_overview_bnc' ); //overview

