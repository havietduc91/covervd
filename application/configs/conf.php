<?php
 $config = array(
			
		array(
				'name' => 'hotness_cache_nr_pages',
				'type' => 'int',
				'meaning' => 'Limit pages number to cache data',
				'content' => 5
		),
 		array(
 				'name' => 'hotness_refresh_rate',
 				'type' => 'int',
 				'meaning' => 'Default time. This value can be specified & varied per region',
 				'content' => 300
 		),
 		array(
 				'name' => 'hotness_refresh_activites_count',
 				'type' => 'int',
 				'meaning' => 'Default price that customer must pay, in addition to the trip\'s cost, to start using the service. This value can be specified & varied per region',
 				'content' => 15
 		),
 		array(
 				'name' => 'fake_user',
 				'type' => 'string',
 				'meaning' => 'get config SITE for create Fake users',
 				'content' =>'hoibinet'
 		),
 		array(
 				'name' => 'crawler',
 				'type' => 'string',
 				'meaning' => 'get config EMAIL of Crawler for create Fake users post Story',
 				'content' =>'crawler@stuff.com'
 		),
 		array(
 				'name' => 'nrOfRandomUser',
 				'type' => 'int',
 				'meaning' => 'Number of user random range ( query to DB and get number users)</br>Default is "100" . if user is Cralwer ,when post new stores it will random user post',
 				'content' =>100
 		),
 		array(
 				'name' => 'ragemaker_language',
 				'type' => 'string',
 				'meaning' => 'rage maker language dropdown list',
 				'content' =>''
 		),
 		array(
 				'name' => 'fb:app_id ',
 				'type' => 'string',
 				'meaning' => 'Facebook app id',
 				'content' => ''
 		),
 		
 		
		/*
 			<li><b>hotness_cache_nr_pages</b> : <?php echo get_conf('hotness_cache_nr_pages', 5);?> (pages)
        <?php if (get_conf('hotness_cache_nr_pages', 0) ==0):?>( <a href="/conf/new?name=hotness_cache_nr_pages">Configure now</a> )<?php endif;?>
        <br/> </li>
        <li><b>hotness_refresh_rate</b> : <?php echo get_conf('hotness_refresh_rate', 300);?> (seconds)
        <?php if (get_conf('hotness_refresh_rate', 0) ==0):?>( <a href="/conf/new?name=hotness_refresh_rate">Configure now</a> )<?php endif;?>
        <br/>Default time.
        This value can be specified & varied per region</li>
        <li><b>hotness_refresh_activites_count</b> : <?php echo get_conf('hotness_refresh_activites_count',15);?> (activities number)
        <?php if (get_conf('hotness_refresh_activites_count', 0) ==0):?>( <a href="/conf/new?name=hotness_refresh_activites_count">Configure now</a> )<?php endif;?>
        <br/>Default price that customer must pay, in addition to the trip's cost, to start using the service.
        This value can be specified & varied per region</li>
        <li><b>get config SITE for create Fake users</b> : <?php echo get_conf('fake_user','hoibinet');?> (name of folder /data/hoibinet or etx)
        <?php if (get_conf('fake_user','hoibinet') ==0):?>( <a href="/conf/new?name=fake_user">Configure now</a> )<?php endif;?>
        <br/>Default is "hoibinet" . get all datafile in DIR "/data/hoibinet" parse and loop then insert to DB</li>
        <li><b>get config EMAIL of Crawler for create Fake users post Story</b> : Now config is :<?php echo get_conf('crawler','crawler@stuff.com');?> (Email of crawler | UNIQUE)
        <?php if (get_conf('crawler','crawler@stuff.com') ==0):?>( <a href="/conf/new?name=crawler">Configure now</a> )<?php endif;?>
        <br/>Default is "crawler@stuff.com" . if user is Cralwer ,when post new stores it will random user post</li>
        <li><b>get config "Number of Users you want random" </b> : Now config is :<?php echo get_conf('nrOfRandomUser','100')?> (number of user random range ( query to DB and get number users) )
        ( <a href="/conf/new?name=nrOfRandomUser">Configure now</a> )
        <br/>Default is "100" . if user is Cralwer ,when post new stores it will random user post</li>
        <li><b>get config "rage maker language dropdown list" </b> : Now config is :<?php echo get_conf('ragemaker_language','')?>
        ( <a href="/conf/new?name=ragemaker_language&type=string">Configure now</a> )
        <br/>Default is ""</li>
		 * fb:app_id
'cookie_salt' => array(
		'meaning' => 'Salt for hashing cookie' ,
		'value' => '123',
		'apikey' => array(
				'meaning' => 'API key for CL.crawler',
				'value' => ''
		)
)
*/
);