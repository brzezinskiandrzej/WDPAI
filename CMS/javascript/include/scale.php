<?php
$d = dir("./photo/".$_SESSION['records'][$id][1].""); 
while (false !== ($plik = $d->read())) {
if($plik=="."||$plik=="..")
continue;
$filename='photo/'.$_SESSION['records'][$id][1].'/'.$plik;
//echo $filename;
list($width,$height) = GetImageSize($filename);
$new_height=180;
$new_width=180;
$image_p=ImageCreateTrueColor($new_width,$new_height);
$image=ImageCreateFromPNG($filename);
ImageCopyResampled($image_p,$image,0,0,0,0,$new_width,$new_height,$width,$height);
ImageJPEG($image_p,"photo/".$_SESSION['records'][$id][1]."/2".$plik."");
//echo "<img src=photo/".$_SESSION['records'][$id][1]."/2".$plik.">";
echo '<img src=photo/'.$_SESSION['records'][$id][1].'/'.$plik.' width="180" height="180">';
break;
}
$d->close(); 
?>