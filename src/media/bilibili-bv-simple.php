<?php

// B站
wp_embed_register_handler( 'bilibilibv', '#https?://www.bilibili.com/video/(BV\w+)#i', function ( $matches, $attr, $url, $rawattr ) {
	$embed_code='<div style="width:100%;">';
    $embed_code.='<iframe class="video_pc" src="//player.bilibili.com/player.html?bvid='.esc_attr($matches[1]).'&as_wide=1"  width="100%" frameborder="0" allowfullscreen="true"></iframe>
    <style type="text/css">
      .video_pc {
        width: 100%;
        height: 585px;
      }
      @media (max-width:767px) {
        .video_pc {
          height: 50vw;
        }
      }
		</style>';
	$embed_code.='</div>';
	$embed_code.='<div class="show-mobile show-mobile-mobile show-mobile-ios"><div class="video_info">当视频无法加载时请刷新页面，<br>或前往PC获得最佳体验。</div></div>';
	return apply_filters( 'embed_bilibilibv', $embed_code, $matches, $attr, $url, $rawattr );

});