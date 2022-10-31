<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Document</title>
</head>
<body>
<?php

function koneksi($server,$user,$dbname){
     // connection to database
     $connect = mysqli_connect($server,$user,"",$dbname);
     if($connect){
          echo "Berhasil";
     }
     return $connect;
}

function make_arrays($conn){
     $rows = array(); // define rows for each record in databse
     $key_arrays = array(); // define key arrays for each key in record
     $query = 'SELECT * FROM data_sample'; //query 
     $result = mysqli_query($conn,$query); // result
     if(mysqli_num_rows($result)>0){ 
          echo mysqli_num_rows($result);
          echo "<br>";
          while($row=mysqli_fetch_assoc($result)){
               $rows[] = $row;
          }
     }
     
     return $rows;

}

function find_root($data,$keys){
     /*
     $data -> records array
     $keys -> key array
     */
     $g_root = 1; // define initial value gini
     $total_data_class_root=array_count_values(
          array_column($data,end($keys))
     ); // 
     foreach($total_data_class_root as $keys => $values){
          $nodes_arr[] = $values; // count and insert each value of data in total_data_class to nodes arr
     }
     for($i=0;$i<sizeof($nodes_arr);$i++){
          $g_root -=(($nodes_arr[$i]/array_sum($nodes_arr))**2);
     }
     echo sizeof($nodes_arr);
     echo '<br>Gini Parent'.$g_root."<br>";
}

function split($target,$data){    
     /*
     target - key
     $data - records
     */ 
     $initial_value_gini = 1;
     echo "<br><br>".$target."<br><br>";
     $total_data_target = array_count_values(
          array_column($data,$target)
     );
     echo "Total data ".$target.var_dump($total_data_target);
     for($i=0;$i<sizeof($data);$i++){
          // echo var_dump($data[$i][$target]);
          // $initial_value_gini -= ($data[$i][$target])/array_sum($data[$i][$target]);
     }
     echo "<br><br>";
}


function cart(){
     $serverName = 'localhost';
     $username = "root";
     $db = 'decision_tree';
     $connect_db = koneksi($serverName,$username,$db);
     $data_arr = make_arrays($connect_db);
     $key_arrays = array_keys($data_arr[0]); // insert key of array assoc to this variable
     $find_value_root = find_root($data_arr,$key_arrays); // run gini function

     foreach($key_arrays as $key){
          // echo $key;
          $data_split = split($key,$data_arr);//split each node
     }
     
     // $calculate_gini = gini($data_arr);
}
cart();
?>
</body>
</html>
