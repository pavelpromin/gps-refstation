<?php
// conver .csv file to .geojson
// geocoding by lat, lon from yandex
$counter=0;
$preprogress=false;
$strings = file('../gps_refstation.csv');
foreach ($strings as $string){
   $counter++;
   $progress=round($counter/count($strings)*100);
   if ($progress!==$preprogress){
      echo "> ".round($counter/count($strings)*100)."%\r\n";
      $preprogress=$progress;
   }
   
   $array = explode(",", $string);
   $yandex = file_get_contents("http://geocode-maps.yandex.ru/1.x/?geocode=$array[2],$array[1]");
   $address = new SimpleXMLElement($yandex);
   $address = (string)$address->GeoObjectCollection[0]->featureMember[1]->GeoObject[0]->metaDataProperty[0]->GeocoderMetaData[0]->AddressDetails[0]->Country[0]->AddressLine[0];
   #echo "$array[0];$address;$array[1];$array[2];\r\n";
 
   @$i++;
   $rows[] = array(
         'type'=>'Feature',
         'geometry'=>array(
            'type'=>'Point',
            'coordinates'=>array(
               floatval($array[2]),
               floatval($array[1])
                )
            ),
         'properties'=>array(
            'Station'=>$array[0],
                'Location'=>strtr($address,'"','\''),
                'Lat'=>$array[1],
                'Lon'=>$array[2],
                'web'=>$array[3],
         )
   );
}
$data['type']='FeatureCollection';
$data['features']=$rows;

$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents('../gps_refstation.geojson',$json);
