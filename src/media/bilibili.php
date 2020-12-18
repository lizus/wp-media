<?php

if(defined('USE_ADVANCED_BILIBILI') && !USE_ADVANCED_BILIBILI) return;

/**
 * B站
 * 提供2个filter get_bilibili_cid和set_bilibili_cid
 */
wp_embed_register_handler( 'bilibili', '#https?://www.bilibili.com/video/av(\d+)(/?\?p=(\d*))?#i', function ( $matches, $attr, $url, $rawattr ) {
  $p=$matches[3] ?? 1;
  $embed_code='<div style="width:100%;">';
  
  $key='bilibili_cid_'.md5($url);
  $cid = apply_filters('get_bilibili_cid',0,$url);
  if (empty($cid)) {
    $aid=0;
    if (preg_match('/\/av(\d+)/',$url,$m)) {
      $aid=$m[1];
    }
    $request = new WP_Http();
    if ($aid>0) {
      $url2='https://api.bilibili.com/x/web-interface/view?aid='.$aid;
      $data2 = (array)$request->request($url2, array('timeout' => 3));
      if(isset($data2['body'])) {
        $dataJson=$data2['body'];
        $dataJson=json_decode($dataJson);
        if ($dataJson->code === 0) {
          $data=$dataJson->data;
          $cid=$data->cid;
        }
      }
    }
    if (empty($cid)) {
      $url=preg_replace('/https?/','https',$url);
      $data = (array)$request->request($url, array('timeout' => 3));
      if(!isset($data['body'])) $data['data'] = '';
      $cid=0;
      if(preg_match('/cid=(\d+)&aid=/i', (string)$data['body'], $match)){
        $cid = (int)$match[1];
      }
      if($cid<1 && preg_match('/"cid":(\d+)/i', (string)$data['body'], $match)) {
        $cid = (int)$match[1];
      }
      if(preg_match('/,"pages":\[\{([^\]]+)\}\]/i', (string)$data['body'], $match)) {
        $pages='[{'.$match[1].'}]';
        $pages=json_decode($pages,true);
        if (!empty($pages) && !empty($p)) {
          $page=$pages[$p-1];
          $cid=$page['cid'];
        }
      }
    }
    if ($cid > 0) {
      $set_bilibili_cid=apply_filters('set_bilibili_cid',$cid,$url,$cid);//存储一年
    }
  }
  if ($cid > 0) {
    //https://player.bilibili.com/player.html?aid=31260342&cid=54620906&page=1
    //https://www.bilibili.com/blackboard/html5player.html
    $embed_code.='<iframe class="video_pc" src="https://www.bilibili.com/blackboard/html5player.html?cid='.$cid.'&aid='.esc_attr($matches[1]).'&page='.@esc_attr($matches[3]).'&as_wide=1"  width="100%" frameborder="0" allowfullscreen="true"></iframe>
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
  }
  $embed_code.='</div>';
  $embed_code.='<div class="show-mobile show-mobile-mobile show-mobile-ios"><div class="video_info">当视频无法加载时请刷新页面，<br>或前往PC获得最佳体验。</div></div>';
  return apply_filters( 'embed_bilibili', $embed_code, $matches, $attr, $url, $rawattr );
  
});