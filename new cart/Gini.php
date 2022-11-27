<?php

$tree = array();
$initial_parent=0;
error_reporting(E_ERROR);
function koneksi(){
     $serverName = 'localhost';
     $username = "root";
     $db = 'decision_tree';
     // connection to database
     $connect = mysqli_connect($serverName,$username,"",$db);
     if($connect){
          // echo "Berhasil";
     }
     return $connect;
}


function make_arrays($conn,$query = 'SELECT * FROM data_sample'){
     echo $query."<br>";
     $rows = array(); // define rows for each record in database
     $key_arrays = array(); // define key arrays for each key in record
     $result = mysqli_query($conn,$query); // result

     if(mysqli_num_rows($result)>0){
          // echo mysqli_num_rows($result);
          echo "<br>";
          while($row=mysqli_fetch_assoc($result)){
               $rows[] = $row;
          }
     }
     return $rows;

}
function initial_gini_parent($data,$keys){
     $classification = array_pop($keys);
     $gini_parent = 1;
     $total_up=0;
     $total_down=count($data);
     $count_val= array();
     for($i=0;$i<count($data);$i++){
          $classification_value=$data[$i][$classification];
          $count_val[$classification_value]++;
     }
     foreach($count_val as $v){
          $total_up=$v;
          $total_UpDown+=($total_up/$total_down)**2;
     }
     $gini_parent-=$total_UpDown;
     // $GLOBALS['initial_parent']=$gini_parent;
     return $gini_parent;
}

function find_root($data,$keys){
     /*
     $data -> records array
     $keys -> key array
     */
     // echo var_dump($keys);
     $g_parent=initial_gini_parent($data,$keys);
     $classification = array_pop($keys);
     $root_key = '';
     $lowest_gain = -999999999999999999;
     $data_fix_split=array();
     echo "Gini Parent : ".$g_parent."<br>";
     foreach($keys as $k){
          echo "Bagian ".$k."<br>";
          // echo "Total Data ".count($data)."<br>";
          // $gini_weight_arr=array();
          // $total_down_arr=array();
          // count_val = (val_atribut) {(classification value) (jumlahnya)
          //                            (classification value) (jumlahnya)}
          //
          // count_total_val = (val_atribut) (total_jumlahnya)

          //count val berisi jumlah classification disetiap [record][kolom]  contoh: array(3) { [3]=> array(2) { ["good"]=> int(1) ["vgood"]=> int(1) } [4]=> array(2) { ["unacc"]=> int(2) ["vgood"]=> int(1) } ["more"]=> array(4) { ["unacc"]=> int(4) ["acc"]=> int(2) ["good"]=> int(3) ["vgood"]=> int(2) } }

          //count_total_val berisi jumlah total classification di setiap record contoh:array(3) { [3]=> int(2) [4]=> int(3) ["more"]=> int(11) }

          
          $count_val = array();
          $count_total_val = array();
          $j = 0;
          $split_data=split($data,$k);
          // echo "Ini split data :\n";
          // echo "<pre>";
          // print_r($split_data);
          // echo "</pre>";
          echo "LEFT NODE<br>";
         // echo "<pre>";
          // print_r($split_data);
          // echo "</pre>"; 
          for($j=0;$j<count($split_data[0]);$j++){
               $val_key = $split_data[0][$j][$k];
               $classification_value = $split_data[0][$j][$classification];
               $count_val[$val_key][$classification_value]++;
               $count_total_val[$val_key]++;
          }
          $gini1 = 1;
          $gini_avg=0;
          $total_up=0;
          $total_down=0;
          $down = 0;
          $total = 0;
          $gain=0;
          // print_r($count_val);
          // print_r($count_total_val);
          foreach($count_val as $k2=>$v2){
               foreach($v2 as $k3=>$v3){
                    $total_up = $v3;
                    $down = $count_total_val[$k2];
                    $total_UpDown=($total_up/$down)**2;
                    $total+=$total_UpDown;
                    echo $k3." ".$total_up."/".$down." = ".$total_UpDown."<br>";
               }
               $gini1 -=$total;
               // $gini_avg1+=$gini*($down/count($data));
               echo "Gini ".$k2." => ".$gini1."<br><br>";
               $total=0;
          }
          $count_val=array();
          $count_total_val=array();
          $gini2=1;
          echo "RIGHT NODE<br>";
          if(count($split_data[1])==0){
               echo "0<br>";
          }
          for($j=0;$j<count($split_data[1]);$j++){
               $val_key = $split_data[1][$j][$k];
               $classification_value = $split_data[1][$j][$classification];
               $count_val[$classification_value]++;
               $count_total_val[$val_key]++;
          }
          // var_dump($count_val);
          // var_dump($count_total_val)."<br>";
          foreach($count_val as $k2=>$v2){
               $total_up=$v2;
               $down = count($split_data[1]);
               $total_UpDown=($total_up/$down)**2;
               $total+=$total_UpDown;
               echo $k2." ".$total_up."/".$down." = ".$total_UpDown."<br>";
          }
          $gini2 -=$total;
          echo "Gini ".$val_key." => ".$gini2."<br><br>";
          $total_avg=((count($split_data[0])/count($data))*$gini1) + ((count($split_data[1])/count($data))*$gini2);
          $total=0;
          $gini2 = 1;
          // print_r($count_val);
          // print_r($count_total_val);
          echo "<br>";
          echo "Bobot Rata Rata Gini ".$total_avg."<br>";
          $gain=$g_parent-$total_avg;
          echo "Gain ".$gain."<br><br><hr>";
          if($gain > $lowest_gain){
               $lowest_gain = $gain;
               $root_key = $k;
               $data_fix_split=$split_data;
               // echo "<pre>";
               // print_r($data_fix_split);
               // echo "hehe";
               // echo "</pre>";
          }
     }
     echo "<pre>";
     echo "Ini data fix split :\n";
     print_r($data_fix_split);
     echo "</pre>";
     return array($root_key,$data_fix_split);


     // var_dump($data);
}
function insert_walk($walk, &$t, $leaf){
     // walk = jalur tree nya
     // tree = adalah representasi treenya (array kosong pada iterasi pertama)
     // leaf = adalah datany
     foreach($walk as $w){
          $next=$w;
          //cek jika key belum ada pada array tree
          if($t == NULL or !array_key_exists($next,$t)){
               $t[$next]=array();
          }
          $t=&$t[$next];
     }
     $t[]=$leaf;
     // echo var_dump($t);
}
function get_decision_from_root($data,$to_split,$line_decision=array()){
     for($i=0;$i<count($data);$i++){
          $subnode_arr = $data[$i];
               foreach($subnode_arr as $k=>$v){
                    if($k==$to_split){
                         if(in_array($v,$line_decision)){
                              break;
                         }
                         array_push($line_decision,$v);
                    }
               }
     }
     return $line_decision;
}

function split($data,$key,$result=array()){
     $conn = koneksi();
     $data_arr=array();
     for($i=0;$i<count($data);$i++){
          if(in_array($data[$i][$key],$data_arr)){
               continue;
          }
          else{
               array_push($data_arr,$data[$i][$key]);
          }
     }
     if($data_arr[0]=="med" and $data_arr[1]=="high" and $data_arr[2]=="low"){
          $data_arr[0]="low";
          $data_arr[1]="med";
          $data_arr[2]="high";
     }
     if($data_arr[0]=="big" and $data_arr[1]=="small" and $data_arr[2]=='med'){
          $data_arr[0]="small";
          $data_arr[1]="med";
          $data_arr[2]="big";
     }
     if($data_arr[0]=='More' and $data_arr[1]==2 and $data_arr[2]==4){
          $data_arr[0]=2;
          $data_arr[1]=4;
          $data_arr[2]="More";
     }
     // var_dump($data_arr);
     if(count($data_arr)<=1){
          $len=count($data_arr)+1;
          for($j=0;$j<$len;$j++){
               $second=array();
               for($k=0;$k<count($data);$k++){
                    if($data[$k][$key]==$data_arr[$j]){
                         array_push($second,$data[$k]);
                    }
               }
               array_push($result,$second);
          }
     }else{
          for($j=0;$j<count($data_arr);$j++){
               $second=array();
               for($k=0;$k<count($data);$k++){
                    if($data[$k][$key]==$data_arr[$j]){
                         array_push($second,$data[$k]);
                    }
               }
               array_push($result,$second);
          }
     }
     if(count($result)>2){
          // echo "<pre>";
          $merge=array_merge($result[1],$result[2]);
          // echo "</pre>";
          for($y=0;$y<count($result);$y++){
               if($y==2){
                    break;
               }
               array_pop($result);
          }
          array_push($result,$merge);
     }
     echo "count result\n";
     echo "ini result \n";
     echo "<pre>";
     print_r($result);
     echo "</pre>";
     // echo count($result);
     return $result;
}

function CART($data,$keys,$walk=array()){
     // sebelum melakukan find root dicek lagi apakah sudah homogen atau belum, jadi tidak salah nilai root nya
     //disini melakukan pengecekan ketika datanya sudah ga bisa di split (alias udah ketemu dan ga usah di split lagi
     // echo var_dump($walk);


     // data tidak berubah
     echo "<pre>";
     print_r($data);
     echo "</pre>";

     // echo count($data);
     $comparison=array();
     for($i=0;$i<count($data);$i++){
          array_push($comparison,$data[$i][end($keys)]);
     }
     // echo var_dump($comparison);
     if (count(array_count_values($comparison))==1){
          echo "<b>Homogen</b>";
          echo "<br>";
          // echo "hore";
          insert_walk($walk, $tree, $data);
          show_tree($tree);
          return;
     }

     $to_split = find_root($data,$keys);
     echo "Karena nilai gain nya paling besar maka ".$to_split[0]." akan menjadi Node selanjutnya.<br><br>";
     // decisions digunakan untuk menentukan garis atau alur sebuah node
     // $decisions=get_decision_from_root($data,$to_split[0]);
     // echo var_dump($decisions)."<br>";
     // echo "oke";
     // walk digunakan untuk menaruh setiap node hingga leaf
     $walk[]=$to_split[0];
     // echo "<pre>";
     // print_r($to_split[1][0]);
     // echo "</pre>";
     // echo var_dump($walk);
     // $split_data=split($data,$to_split,$decisions);
     // echo "INi adalah coyut".count($decisions);
     // echo count($to_split[1]);
     
     for($i=0;$i<count($to_split[1]);$i++){
          // echo "Kenapa";
          // $new_walk=$walk;
          // $new_walk[]=$decisions[$i];
          // echo "<br>".var_dump($new_walk),"<br>";
          // echo $new_walk[];
          // echo var_dump($result);
          echo "ini i".$i;
          CART($to_split[1][$i],$keys,$new_walk);
          // echo count($to_split[1][$i]);
          // if($i>=1){
          //      for($k=0;$k<count($keys);$k++){
          //           if($keys[$k]==$to_split[0]){
          //                unset($keys[$k]);
          //                echo "hore";
          //                break;
          //           }
          //      }
          // }
     }
}

function show_tree($tree){
     echo "<pre>";
     print_r($tree);
     echo "</pre>";
     echo "<hr>";
     // echo var_dump($tree);
}
function main(){
     $connect_db = koneksi();
     $data_arr = make_arrays($connect_db);
     $key_arrays = array_keys($data_arr[0]); // insert key of array assoc to this variable
     // initial_gini_parent($data_arr,$key_arrays);
     // echo "Gini Parent : ".$GLOBALS['initial_parent']."<br>";
     CART($data_arr,$key_arrays);
}
main();
?>