<?php
// conver .csv file to .geojson
// geocoding by lat, lon from yandex

$strings = file('../gps_refstation.csv');
foreach ($strings as $string){
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
               $array[2],
               $array[1]
                )
            ),
         'properties'=>array(
            'Station'=>$array[0],
                'Location'=>$address,
                'Lat'=>$array[1],
                'Lon'=>$array[2],
                'web'=>$array[3],
         )
   );
}
$data['type']='FeatureCollection';
$data[features]=$rows;

$json = json_encode($data);
file_put_contents('../gps_refstation.geojson',$json);
