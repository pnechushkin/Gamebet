<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Результаты</title>
    <script type="text/javascript">
        function showOrHidenot_sel(radio,select,nam) {
            radio = document.getElementById('razdel_'+nam+'');
            select = document.getElementById('select_'+nam+'');
            if (radio.checked) select.style.display = "block";
            else select.style.display = "none";
        }
    </script>
</head>
<?php
/**
 * Created by PhpStorm.
 * User: Павел
 * Date: 11.04.2017
 * Time: 15:08
 */
error_reporting(E_ALL);
$curlInit = curl_init('https://affiliates.gamebet.com/global/feed/json/');
curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
$response = curl_exec($curlInit);
curl_close($curlInit);
$tabl=json_decode($response, true);?>
<body>
<form method="post">
    <?php
    $id_s=1;
    foreach ($tabl['sport'] as $key => $id) :?>
        <div style="width: auto;">
            <div style="float: left; text-align: left;  margin: 5px; width: 20% ">
                <input type="radio" id="razdel_<?php echo $id_s?>" name="razdel" value="<?php echo $key?>" onclick="showOrHidenot_sel(razdel_<?php echo $id_s?>,select_<?php echo $id_s ?>,<?php echo $id_s?>)"><?php echo $tabl['sport'][$key]['name']?> <?php echo $key?>
            </div>
            <div style="float: left; text-align: center; margin: 5px; display:none;" id="select_<?php echo $id_s?>" >
                <select name="podrazdel[<?php echo $key?>]">
                    <?php
                    foreach ($tabl['sport'][$key]['competition'] as $key1 => $name ){
                        $podrazdelarr =[];
                        if (!in_array($tabl['sport'][$key]['competition']['name'], $podrazdelarr)) :
                            $podrazdelarr[]=$tabl['sport'][$key1]['competition']['name'];
                            ?>
                            <option value="<?php echo $tabl['sport'][$key]['competition'][$key1]['id']?>"><?php echo $tabl['sport'][$key]['competition'][$key1]['name']?></option>
                        <?php endif ;
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="clear: both;"></div>
        <?php
        $id_s++;
    endforeach ;?>
    <div style="text-align: center; padding: 20px 20px 20px 20px;">
        <input type="submit" value="Получить">
    </div>
</form>
<div>
    <?php
    if (!empty($_POST))  :
        $razdel=$_POST['razdel'].'</br>';
        $podrazdel=$_POST['podrazdel'][(int)$razdel];
        $games=$tabl['sport'][(int)$razdel]['competition'][(int)$podrazdel]['game'];
        foreach ($games as $id => $inf ):
            $url='https://affiliates.gamebet.com/global/feed/json/?gameId='.$id;
            $curl = curl_init($url);
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
            $resp = curl_exec($curl);
            curl_close($curl);
            /** @var array $tablrez */
            $tablrez=json_decode($resp, true);
            $team1_name=$inf['team1_name'];
            $team2_name=$inf['team2_name'];
            $rez=@$tablrez['all_events']['P1XP2'][0]['events'];
            $w1=null;
            $X=null;
            $W2=null;
            for ($i=0; $i<count($rez);$i++){
                if ($rez[$i]['name']=='W1'){
                    $w1=$rez[$i]['price'];
                }
                if ($rez[$i]['name']=='X'){
                    $X=$rez[$i]['price'];
                }
                if ($rez[$i]['name']=='W2'){
                    $W2=$rez[$i]['price'];
                }
            }
            ?>
            <div style="text-align: center; width: auto"><?php echo $team1_name?> Vs <?php echo $team2_name?></div>
            <div>
                <div style="float: left; text-align: left; margin: 5px; width: 30%"><?php echo $team1_name?> - <?php echo $w1?></div>
                <div style="float: left; text-align: center; margin: 5px; width: 30%">X - <?php echo $X?></div>
                <div style="float: left; text-align: right; margin: 5px; width: 30%"><?php echo $team2_name?> - <?php echo $W2?></div>
            </div>
            <div style="clear: both;"></div>
        <?php endforeach ;
    endif;?>
</div>
</body>
</html>