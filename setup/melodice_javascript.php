<script type='text/javascript' src='https://www.youtube.com/iframe_api'></script>
<script type='text/javascript'>
  var player;
  var videoIndex;
  videoIndex=0;
<?php
$melodiceUrl='https://melodice.org/playlist/marvel-champions-the-card-game-2019/';
$meloDom = new DOMDocument;
$libxml_previous_state = libxml_use_internal_errors(true);
$meloDom->loadHTMLFile($melodiceUrl);
libxml_clear_errors();
libxml_use_internal_errors($libxml_previous_state);
$meloVids='';
foreach ($meloDom->getElementsByTagName('a') as $node) if (strpos(strtoupper($node->nodeValue),'YOUTUBE')) {
  $videoIds=explode(',',explode('=',$node->getAttribute('href'))[1]);
  foreach ($videoIds as $videoId) $meloVids.="'".$videoId."',";
  echo '  let meloVids=['.substr($meloVids,0,-1);}
?>
];
  function onYouTubeIframeAPIReady() {                      
    player=new YT.Player('ytPlayer', {videoId:'',events:{'onReady':onPlayerReady,'onStateChange':onPlayerStateChange}});}        
  function onPlayerReady(event) {
    event.target.loadVideoById(meloVids[0]);
    videoIndex++;                              
    event.target.playVideo();}    
  function onPlayerStateChange(event) {   
    if (event.data==YT.PlayerState.ENDED) {
      event.target.loadVideoById(meloVids[videoIndex]);
      videoIndex++;
      if (videoIndex>meloVids.length) {videoIndex=0;}
      }}
</script>

<iframe id='ytPlayer' type='text/html' width='640' height='390' src='https://www.youtube.com/embed/R52bof3tvZs?enablejsapi=1&rel=0&playsinline=1&autoplay=1&showinfo=0&autohide=1&controls=1&modestbranding=1&disablekb=1&origin=https://marvel.fouy.net' frameborder='0'>

    playlist
