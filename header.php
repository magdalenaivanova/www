<?php

include("functions.php");

?>
<!DOCTYPE html>
<html><head>
<title>Backlog Management</title>
<meta charset="UTF-8">
<meta name="description" content="" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<!--<script src="https://code.jquery.com/ui/1.7.3/jquery-ui.min.js"></script> -->
<script type="text/javascript" src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>

<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<script type="text/javascript" src="js/prettify.js"></script>                                   
<script type="text/javascript" src="js/kickstart.js"></script>                                  
<link rel="stylesheet" type="text/css" href="css/kickstart.css" media="all" />                  
<link rel="stylesheet" type="text/css" href="style.css" media="all" />        
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css">                  
</head><body><a id="top-of-page"></a><div id="wrap" class="clearfix">
<div class="col_12">
<h1>Backlog management system</h1>
<script>
  $( function() {
    $( "#datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
  } );
</script>