<?php
namespace App\Utility;

class Utility{

    
    public function readJsonData($dataFilePath){
        if (!file_exists($dataFilePath)) {
            return false;
        }
        $data = json_decode(file_get_contents($dataFilePath), true);
        if(json_last_error() !== JSON_ERROR_NONE){
            return false;
        }
        return $data;
    }
    

    public function writeJsonData($dataFilePath, $data){
        if (!file_exists($dataFilePath)) {
            return false;
        }
        $existingData = json_decode(file_get_contents($dataFilePath), true);
        if(json_last_error() !== JSON_ERROR_NONE){
            return false;
        }

        if(!$data === false){
            if(!$existingData===false){
                $existingData[]=$data;
                file_put_contents($dataFilePath, json_encode($existingData, JSON_PRETTY_PRINT));
                return true;
            }else{
                file_put_contents($dataFilePath, json_encode([$data], JSON_PRETTY_PRINT));
                return true;
            }
            return false;
        }
        return false;
    }


    public function getSavings($array):float{
        if(!$array===true){
            return 0;
        }
        $totalIncomeAmount = 0;
        $totalExpenseAmount = 0;
        foreach($array as $item){
            if($item['status']==='income'){
                $totalIncomeAmount+= (float)$item['amount'];
            }
            if($item['status']==='expense'){
                $totalExpenseAmount+= (float)$item['amount'];
            }
        }
        if ($totalIncomeAmount >= $totalExpenseAmount) {
            return $totalIncomeAmount - $totalExpenseAmount;
        }else{
            return 0;
        }
        
        
    }

    public function getListByStatus($array,$status){
        if(!$array===true){
            return false;
        }
        $incomeArray=[];
        foreach($array as $item){
            if($item['status']===$status){
                
                $incomeArray[]= $item;
            }
        }
        if(!$incomeArray===false){
            return $incomeArray;
        }else{
            return false;
        }
    }
    
    public function getCategory($array){
        if(!$array===true){
            return false;
        }
        $categoryArray=[];
        foreach($array as $item){
            $categoryArray[$item['category']]= $item['status'];
        }
        return $categoryArray;
    }

    public function getTotalByStatus($array, $status):float{
        if(!$array===true){
            return 0;
        }
        $totalAmount=0;
        foreach ($array as $item) {
            if($item['status']=== $status){
                $totalAmount+=(float)$item['amount'];
            }
        }
        return $totalAmount;

    }


}
