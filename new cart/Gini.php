<?php
$node_tree = array();
$attribut_tree = array();
$classification_tree=array();
$initial_parent=0;
$iteration = 0;
error_reporting(E_ERROR);
function koneksi(){
     $serverName = 'localhost';
     $username = "root";
     $db = 'decision_tree';
     // connection to database
     $connect = mysqli_connect($serverName,$username,"",$db);
     if($connect){
     }
     return $connect;
}


function make_arrays($conn,$query = 'SELECT * FROM data_sample'){
     echo $query."<br>";
     $rows = array(); // define rows for each record in database
     $key_arrays = array(); // define key arrays for each key in record
     $result = mysqli_query($conn,$query); // result

     if(mysqli_num_rows($result)>0){
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
     return $gini_parent;
}

function find_root($data,$keys){
     /*
     $data -> records array
     $keys -> key array
     */
     $g_parent=initial_gini_parent($data,$keys);
     $classification = array_pop($keys);
     $root_key = '';
     $lowest_gain = -999999999999999999;
     $data_fix_split=array();
     echo "Gini Parent : ".$g_parent."<br>";
     foreach($keys as $k){
          echo "Bagian ".$k."<br><br>";
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
          echo "LEFT NODE : ";
          $decision_left=get_decision_from_root($split_data[0],$k);
          foreach($decision_left as $v){
               echo "<b>$v</b>";
          }
          echo "<br>";
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
          foreach($count_val as $k2=>$v2){
               foreach($v2 as $k3=>$v3){
                    $total_up = $v3;
                    $down = $count_total_val[$k2];
                    $total_UpDown=($total_up/$down)**2;
                    $total+=$total_UpDown;
                    echo $k3." ".$total_up."/".$down." = ".$total_UpDown."<br>";
               }
               $gini1 -=$total;
               echo "Gini Index => ".$gini1."<br><br>";
               $total=0;
          }
          $count_val=array();
          $count_total_val=array();
          $gini2=1;
          echo "RIGHT NODE : ";
          $decision_right=get_decision_from_root($split_data[1],$k);
          if($k == 'Buying'){
               echo "<b>med high</b>";
          }
          if($k=="Maintenance"){
               echo "<b>high vhigh</b>";
          }
          foreach($decision_right as $v){
               echo "<b>$v</b>"." ";
          }
          echo "<br>";
          if(count($split_data[1])==0){
               echo "0<br>";
          }
          for($j=0;$j<count($split_data[1]);$j++){
               $val_key = $split_data[1][$j][$k];
               $classification_value = $split_data[1][$j][$classification];
               $count_val[$classification_value]++;
               $count_total_val[$val_key]++;
          }
          foreach($count_val as $k2=>$v2){
               $total_up=$v2;
               $down = count($split_data[1]);
               $total_UpDown=($total_up/$down)**2;
               $total+=$total_UpDown;
               echo $k2." ".$total_up."/".$down." = ".$total_UpDown."<br>";
          }
          $gini2 -=$total;
          echo "Gini Index => ".$gini2."<br><br>";
          $total_avg=((count($split_data[0])/count($data))*$gini1) + ((count($split_data[1])/count($data))*$gini2);
          $total=0;
          $gini2 = 1;
          echo "<br>";
          echo "Bobot Rata Rata Gini ".$total_avg."<br>";
          $gain=$g_parent-$total_avg;
          echo "Gain ".$gain."<br><br><hr>";
          if($gain >= $lowest_gain){
               $lowest_gain = $gain;
               $root_key = $k;
               $data_fix_split=$split_data;
               $data_fix_left=$decision_left;
               $data_fix_right=$decision_right;
          }
     }
     return array($root_key,$data_fix_split,$data_fix_left,$data_fix_right);
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
}

//disini ada array buat masukin nextNode,attribute, sama classification value jika homogen
function insert_tree($attrLeft,$attrRight,$nextNode){

     // isi tree attribute
     foreach($attrLeft as $left){
          array_push($GLOBALS['attribut_tree'],$left);
     }
     foreach($attrRight as $right){
          array_push($GLOBALS['attribut_tree'],$right);
     }

     // isi tree node
     array_push($GLOBALS['node_tree'],$nextNode);
}
function insert_classification_tree($data){
     if(in_array($data[0]['Evaluation'],$GLOBALS['classification_tree'])){
          return;
     }
     array_push($GLOBALS['classification_tree'],$data[0]['Evaluation']);
}
function make_tree(){
     echo '<!DOCTYPE html>
     <html lang="en">
       <head>
         <meta charset="UTF-8" />
         <meta http-equiv="X-UA-Compatible" content="IE=edge" />
         <meta name="viewport" content="width=device-width, initial-scale=1.0" />
         <title>Document</title>
         <link rel="stylesheet" href="treant-js/Treant.css" />
       </head>
       <body>
         <div id="tree-simple"></div>
         <script src="treant-js/vendor/raphael.js"></script>
         <script src="treant-js/Treant.js"></script>
         <script type="text/Javascript">
     simple_chart_config = {
          chart: {
            container: "#tree-simple",
            rootOrientation: "WEST",
            levelSeparation: 55,
          },
        
          nodeStructure: {
            text: { name: "Root" },
            children: [
              {
               text: { name: "Safety[low] unacc" },
              },
              {
               text: { name: "Safety[med,high]" },
               children:[
                    {
                         text:{name:"Safety [med]"},
                         children:[
                              {
                                   text:{name:"Lugage_boot [small]"},
                                   children:[
                                        {
                                             text:{name:"Persons [2] unacc"},
                                        },
                                        {
                                             text:{name:"Persons [4,More] acc"},
                                        },
                                   ]
                              },
                              {
                                   text:{name:"Lugage_boot [med,big] good"},
                              }
                         ]
                    },
                    {
                         text:{name:"Safety [high]"},
                         children:[
                              {
                                   text:{name:"Lugage_boot [small] good"},
                              },
                              {
                                   text:{name:"Lugage_boot [med,big] vgood "}
                              },
                         ]
                    }
                    
               ]
              },
            ],
          },
        };
        var my_chart = new Treant(simple_chart_config);        
     </script>
       </body>
     </html>
     ';
}
function get_decision_from_root($data,$key,$line_decision=array()){
     for($i=0;$i<count($data);$i++){
          $subnode_arr = $data[$i];
               foreach($subnode_arr as $k=>$v){
                    if($k==$key){
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
          $merge=array_merge($result[1],$result[2]);
          for($y=0;$y<count($result);$y++){
               if($y==2){
                    break;
               }
               array_pop($result);
          }
          array_push($result,$merge);
     }
     return $result;
}

function CART($data,$keys,$walk=array(),$i=0){
     // sebelum melakukan find root dicek lagi apakah sudah homogen atau belum, jadi tidak salah nilai root nya
     //disini melakukan pengecekan ketika datanya sudah ga bisa di split (alias udah ketemu dan ga usah di split lagi

     $comparison=array();
     for($i=0;$i<count($data);$i++){
          array_push($comparison,$data[$i][end($keys)]);
     }
     if (count(array_count_values($comparison))==1){
          
          echo "<br>Iterasi ke - ".$GLOBALS['iteration']+=1;
          echo "<br>";
          echo "<b>Homogen</b>";
          echo "<br>";
          insert_walk($walk, $tree, $data);
          insert_classification_tree($data);
          show_tree($tree,$i);
          return;
     }

     $to_split = find_root($data,$keys);
     echo "Karena nilai gain nya paling besar maka ".$to_split[0]." akan menjadi Node selanjutnya.<br><br>";
     insert_tree($to_split[2],$to_split[3],$to_split[0]);
     echo "Iterasi ke - ".$GLOBALS['iteration']+=1;
     echo "<br>";
     echo "Node Left Selanjutnya : ";
     foreach($to_split[2] as $v){
          echo "<b>$v</b>";
     }
     echo "<br>";
     echo "Node Right Selanjutnya : ";
     foreach($to_split[3] as $v){
          echo "<b>$v</b> ";
     }
     echo "<br>";
     $walk[]=$to_split[0];
     for($i=0;$i<count($to_split[1]);$i++){
          CART($to_split[1][$i],$keys,$new_walk,$i);
     }
}
function rules_of_decision_tree($treeNode,$treeAttr,$treeClassification){
     echo "RULES OF DECISION TREE<br>";
     echo "==============================================================================<br>";
     echo "1. Root<br>";
     echo "2. $treeNode[0] [$treeAttr[0]] <b>$treeClassification[0]</b><br>";
     echo "3. $treeNode[1] [$treeAttr[1], $treeAttr[2]]<br>";
     echo "4. $treeNode[1] [$treeAttr[3]]<br>";
     echo "5. $treeNode[2] [$treeAttr[5]]<br>";
     echo "6. $treeNode[3] [$treeAttr[8]] <b>$treeClassification[0]</b><br>";
     echo "7. $treeNode[3] [$treeAttr[9], $treeAttr[10]] <b>$treeClassification[1]</b><br>";
     echo "8. $treeNode[2] [$treeAttr[6], $treeAttr[7]] <b>$treeClassification[2]</b><br>";
     echo "9. $treeNode[0] [$treeAttr[2]]<br>";
     echo "10. $treeNode[4] [$treeAttr[11]] <b>$treeClassification[2]</b><br>";
     echo "11. $treeNode[4] [$treeAttr[12], $treeAttr[13]] <b>$treeClassification[3]</b><br>";
}
function show_tree($tree){
     echo "<b>Data Homogen</b>\n";
     echo "<pre>";
     print_r($tree);
     echo "</pre>";
     echo "<hr>";
}
function main(){
     $connect_db = koneksi();
     $data_arr = make_arrays($connect_db);
     $key_arrays = array_keys($data_arr[0]); // insert key of array assoc to this variable
     CART($data_arr,$key_arrays);
     rules_of_decision_tree($GLOBALS['node_tree'],$GLOBALS['attribut_tree'],$GLOBALS['classification_tree']);
     make_tree();
     
}
main();
?>
