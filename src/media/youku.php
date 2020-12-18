<?php

//
//优酷
wp_embed_register_handler( 'youku', '#https?://v.youku.com/v_show/id_(.*?).html#i', function ($matches,$attr,$url,$rawattr){
	return '<div clss="media-wrap media-youku"><iframe height="100%" width="100%" src="//player.youku.com/embed/'.esc_attr($matches[1]).'" frameborder=0 allowfullscreen></iframe></div>';
} );
