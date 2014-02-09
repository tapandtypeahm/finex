<?php
//$wordnum = numberToWord(10);
//echo $wordnum."<BR>";

function singledigit($number)
{
        switch($number){
            case 0:$word = " zero";break;
            case 1:$word = " One";break;
            case 2:$word = " two";break;
            case 3:$word = " three";break;
            case 4:$word = " Four";break;
            case 5:$word = " Five";break;
            case 6:$word = " Six";break;
            case 7:$word = " Seven";break;
            case 8:$word = " Eight";break;
            case 9:$word = " Nine";break;
        }
        return $word;
}

    function doubledigitnumber($number){
        if($number == 0){
            $word = "";
        }
        else{
            $word = singledigit($number);
        }       
        return $word;
    }

    function doubledigit($number){
		
        switch($number[0]){
            case 0:$word = doubledigitnumber($number[1]);break;
            case 1:
                switch($number[1]){
                    case 0:$word = "Ten";break;
                    case 1:$word = "Eleven";break;
                    case 2:$word = "Twelve";break;
                    case 3:$word = "Thirteen";break;
                    case 4:$word = "Fourteen";break;
                    case 5:$word = "Fifteen";break;
                    case 6:$word = "Sixteen";break;
                    case 7:$word = "Seventeen";break;
                    case 8:$word = "Eighteen";break;
                    case 9:$word = "Ninteen";break;
                }break;
            case 2:$word = "Twenty".doubledigitnumber($number[1]);break;                
            case 3:$word = "Thirty".doubledigitnumber($number[1]);break;
            case 4:$word = "Forty".doubledigitnumber($number[1]);break;
            case 5:$word = "Fifty".doubledigitnumber($number[1]);break;
            case 6:$word = "Sixty".doubledigitnumber($number[1]);break;
            case 7:$word = "Seventy".doubledigitnumber($number[1]);break;
            case 8:$word = "Eighty".doubledigitnumber($number[1]);break;
            case 9:$word = "Ninety".doubledigitnumber($number[1]);break;

        }
        return $word;
    }

    function unitdigit($numberlen,$number){
        switch($numberlen){         
            case 3:$word = "Hundred";break;
            case 4:$word = "Thousand";break;
            case 5:$word = "Thousand";break;
            case 6:$word = "Lakh";break;
            case 7:$word = "Lakh";break;
            case 8:$word = "Crore";break;
            case 9:$word = "Crore";break;

        }
        return $word;
    }

    function numberToWord($number){
		
		$number="$number";
		
        $numberlength = strlen($number);
        if ($numberlength == 1) { 
            return singledigit($number);
        }elseif ($numberlength == 2) {
            return doubledigit($number);
        }
        else {

            $word = "";
            $wordin = "";

            if($numberlength == 9){
                if($number[0] >0){
                    $unitdigit = unitdigit($numberlength,$number[0]);
                    $word = doubledigit($number[0].$number[1]) ." ".$unitdigit." ";
                    return $word." ".numberToWord(substr($number,2));
                }
                else{
                    return $word." ".numberToWord(substr($number,1));
                }
            }

            if($numberlength == 7){
                if($number[0] >0){
                    $unitdigit = unitdigit($numberlength,$number[0]);
                    $word = doubledigit($number[0].$number[1]) ." ".$unitdigit." ";
                    return $word." ".numberToWord(substr($number,2));
                }
                else{
                    return $word." ".numberToWord(substr($number,1));
                }

            }

            if($numberlength == 5){
                if($number[0] >0){
                    $unitdigit = unitdigit($numberlength,$number[0]);
                    $word = doubledigit($number[0].$number[1]) ." ".$unitdigit." ";
                    return $word." ".numberToWord(substr($number,2));
                }
                else{
                    return $word." ".numberToWord(substr($number,1));
                }


            }
            else{
                if($number[0] >0){
                    $unitdigit = unitdigit($numberlength,$number[0]);
                    $word = singledigit($number[0]) ." ".$unitdigit." ";
                }               
                return $word." ".numberToWord(substr($number,1));
            }
        }
    }