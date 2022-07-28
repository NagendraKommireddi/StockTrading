<?php
$target_dir ="uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

if(isset($_POST["submit"])){
    $extension = pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION);

    if($extension != "csv"){
        echo "File Format Not Supported, retry again with csv file";
        exit;
    }
    $start_date = date("d-m-Y", strtotime($_POST['datepicker']));  
    $end_date = date("d-m-Y", strtotime($_POST['datepicker2']));  
    if($start_date > $end_date){
        echo "Start Date cannot be greater than end Date";
        exit;
    }

    move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
    $contents = file($target_file);
    $all_data = [];
    foreach($contents as $key => $line){
        $value = explode(',',$line);
        if($key == 0){
            if(trim($value[0]) != 'id_no'){
                echo "id_no header missing at row".$key;
                exit;
            }
            else if (trim($value[1]) != 'date'){
                echo "date header missing at row".$key;
                exit;
            }
            else if(trim($value[2]) != 'stock_name'){
                echo "stock_name header missing at row".$key;
                exit;
            }
            else if(trim($value[3]) != 'price'){
                echo $value[3];
                echo "price header missing at row".$key;
                exit;
            }
            continue;
        }
        else{
            if(!validateDate($value[1])){
                echo "Invalid Date at row".$key." Use dd-mm-yyyy format";
                exit;
            }
            if(is_nan(trim($value[3])) && trim($value[3]) >0){
                echo "Invalid Price at row ".$key."";
                exit;
            }
        }
        $all_data[$value[2]][$value[1]] = array(
                                    'date' => $value[1],
                                    'stock_name' => $value[2],
                                    'price' => $value[3]
                                     );
    }

    foreach ($all_data as $key => $value) {
        ksort($value);
        $all_data[$key] = $value;
    }

    $search_input = $_POST['input_box'];

    if(!array_key_exists($search_input, $all_data)){
        echo "Not a Valid Stock Name please cross check the input";
        exit;
    }
    $all_data = $all_data[$search_input];

    $filtered_data = [];
    foreach($all_data as $key => $value){
        if($key >= $start_date && $key <= $end_date){
            $filtered_data[] = $value;
        }
    }
    if(empty($filtered_data)){
        echo "No Data found for Given Details";
        exit;
    }
    $processed_data = stockBuySell($filtered_data,count($filtered_data));
    echo "<pre>";
    echo "<table border='2' style='width: 100%; height: 50%' >";
    echo "<tbody>";
    echo "<tr>";
    if(isset($processed_data['min_loss'])){
        echo "<td>Minimum Loss for 200 shares</td>";
        echo "<td>".abs($processed_data['min_loss'] * 200)."</td>";
    }
    else{
        echo "<td>Maximum Profit for 200 shares</td>";
        echo "<td>".($processed_data['max_profit'] * 200) ."</td>";
    }

    echo "</tr>";
    echo "<tr>";
    echo "<td>Mean Stock Price for single share</td>";
    echo "<td>".$processed_data['mean_stock_price']."</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Standard Deviation(Population) for single share</td>";
    echo "<td>".$processed_data['standard_deviation_population']."</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Standard Deviation(Sample) for single share</td>";
    echo "<td>".$processed_data['standard_deviation_sample']."</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>BuyDate</td>";
    echo "<td>".$processed_data['buy_date']."</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>SellDate</td>";
    echo "<td>".$processed_data['sell_date']."</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
    exit;
}
?>

<?php
 function stockBuySell($price,$n)
 {
        error_reporting(E_ALL ^ E_NOTICE);  
        $max_profit = 0;
        $count = 0;
        $day_counter = 0;

        //max_profit calculator
        $returner = sell_on_profit($price);
        if($returner['max_profit'] == 0){
            $returner = sell_on_loss($price);
        }

        foreach($price as $key=>$value){
            if($value['date'] >= $returner['buy_date'] && $value['date'] <= $returner['sell_date']){
                $returner['mean_stock_price'] += $value['price'];
                $day_counter++;
            }
        }

        $returner['mean_stock_price'] = $returner['mean_stock_price'] / $day_counter;

        $variance = 0;
        foreach($price as $key => $value){
            if($value['date'] >= $returner['buy_date'] && $value['date'] <= $returner['sell_date']){
                $varaince_of_item = $value['price'] - $returner['mean_stock_price'];
                $varaince_of_item *= $varaince_of_item;
                $variance += $varaince_of_item;
            }
        }

        $standard_deviation_population = ($variance/($day_counter));
        $standard_deviation_population = sqrt($standard_deviation_population);

        $standard_deviation_sample = ($variance/($day_counter-1));
        $standard_deviation_sample = sqrt($standard_deviation_sample);

        $returner['standard_deviation_population'] = $standard_deviation_population;
        $returner['standard_deviation_sample'] = $standard_deviation_sample;
        return $returner;
 }

 function sell_on_profit($price){

    $returner = array('max_profit' => 0,'mean_stock_price' =>0,'standard_deviation_population'=>0,'standard_deviation_sample' =>0, 'buy_date' => '','sell_date' => '');
    $count =0;
    foreach($price as $key=>$value){
        if($count == 0){
            $count++;
            $prev = $value;
            continue;
        }

        if($prev['price'] > $value['price']){
            $prev = $value; 
        }

        if ($value['price'] -  $prev['price'] > $returner['max_profit']){
            $returner['max_profit'] = $value['price'] -  $prev['price'];
            $returner['buy_date'] = $prev['date'];
            $returner['sell_date'] = $value['date'];
        }
    }
    return $returner;
 }

 function sell_on_loss($price){
    
    $returner = array('min_loss' => PHP_INT_MIN,'mean_stock_price' =>0,'standard_deviation_population'=>0,'standard_deviation_sample' =>0, 'buy_date' => '','sell_date' => '');
    $count =0;

    foreach($price as $key=>$value){

        if($count == 0){
            $count++;
            $prev = $value;
            continue;
        }
        if($value['price'] == $prev['price']){
            $returner['min_loss'] = 0;
            $returner['buy_date'] = $prev['date'];
            $returner['sell_date'] = $value['date'];
            break;
        }
        if ($value['price'] -  $prev['price'] > $returner['min_loss']){
            $returner['min_loss'] = $value['price'] -  $prev['price'];
            $returner['buy_date'] = $prev['date'];
            $returner['sell_date'] = $value['date'];
        }
    }

    return $returner;
 }
 function validateDate($date, $format = 'd-m-Y')
    {   
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
?>
