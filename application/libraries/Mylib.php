<?php  
class Mylib { 
    function __construct()
    {
        $this->ci =& get_instance();    // get a reference to CodeIgniter.
    }
     
    /****** FORM LIB ******/
 
    function textbox($var_name="",$value="", $readonly="", $placeholder="", $inlinehelp="", $class = "")
    { 
        $output = '';
        $output .= "<input name=\"".$var_name."\" id=\"".$var_name."\" type=\"text\" class=\"form-control input-inline input-medium  $class \" placeholder=\"".$placeholder."\" value=\"".$value."\" ".$readonly.">
                    <span class=\"help-inline\">".$inlinehelp."</span>"; 
        echo $output;
    } 
    function textbox_special($var_name="",$value="", $readonly="", $placeholder="", $inlinehelp="")
    { 
        $output = '';
        $output .= "<input size=\"4\" name=\"".$var_name."\" id=\"".$var_name."\" type=\"text\" class=\"form-control \" placeholder=\"".$placeholder."\" value=\"".$value."\" \"".$readonly."\">
                    "; 
        echo $output;
    } 
    function inputdate($var_name="",$value="", $readonly="", $placeholder="", $inlinehelp="")
    { 
        $output = '';
        $output .= "<input name=\"".$var_name."\" id=\"".$var_name."\" type=\"text\" class=\"form-control input-inline input-medium date-picker\" placeholder=\"".$placeholder."\" value=\"".$value."\" \"".$readonly."\">
                    <span class=\"help-inline\">".$inlinehelp."</span>"; 
        echo $output;
    } 
/*
    function inputfile($var_name="",$value="", $readonly="false", $placeholder="", $inlinehelp="")
    { 
        $output = '';
        $output .= '<div class="fileinput fileinput-new" data-provides="fileinput">
                        <div class="input-group input-large"> 
                                <input type="file" name="'.$var_name.'" id="'.$var_name.'"> </span> 
                        </div>
                    </div>'; 
        echo $output;
    } 
*/
    function inputfile($var_name="",$value="", $readonly="false", $placeholder="", $inlinehelp="")
    { 
        $output = '';
        $output .= '<div style="position:relative;">
							<a class="btn btn-sm blue" href="javascript:;">
								Choose File...
								<input type="file" name="'.$var_name.'" id="'.$var_name.'" style="position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:\'progid:DXImageTransform.Microsoft.Alpha(Opacity=0)\';opacity:0;background-color:transparent;color:transparent;" size="40"  onchange="$(\'#upload-file-info\').html(this.files[0].name);">
							</a>
							&nbsp;
							<span class="label label-info" id="upload-file-info"></span>
					</div>'; 
        echo $output;
    } 

    function inputfile_image($var_name="",$value="", $readonly="false", $placeholder="", $inlinehelp="")
    { 
        $output = '';
        $output .= '<div class="fileinput fileinput-new" data-provides="fileinput">
                        <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                            <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt="" /> </div>
                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                        <div>
                            <span class="btn default btn-file">
                                <span class="fileinput-new"> Select image </span>
                                <span class="fileinput-exists"> Change </span>
                                <input type="file" name="'.$var_name.'"> </span>
                            <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                        </div>
                    </div>'; 
        echo $output;
    } 
    function textarea($var_name="",$value="", $rows="3")
    { 
        $output = '';
        $output .= "<textarea name=\"".$var_name."\" id=\"".$var_name."\" class=\"form-control\" rows=\"".$rows."\">".$value."</textarea>"; 
        echo $output;
    } 
    function radiobox($var_name="",$value="", $array_data="")
    { 
        $output = '<div class="mt-radio-list">';

        FOREACH($array_data AS $key=>$val) {  
            $checked = "";
            if ($key == $value) $checked = " checked";
            $output .= '<label class="mt-radio">
                            <input type="radio" name="'.$var_name.'" id="'.$var_name.'" value="'.$key.'" '.$checked.'> &nbsp;&nbsp;'.$val.'
                            <span></span>
                        </label>&nbsp;&nbsp;&nbsp;&nbsp;';
        }

        $output .= "</div>";                                              
 
        echo $output;
    }  
    function checkbox($var_name="",$value="", $array_data="")
    {   
        # $array_data ::  arr[key] = val
        # $value ::  arr[key] = key

        $output = '';
        $output = '<div class="mt-checkbox-list">';

        if (is_array($array_data) && count($array_data) > 0) {
            FOREACH ($array_data AS $key => $val) { 
                $checked = ""; 
                IF (isset($value[$key]) && $value[$key] != "") {
                    $checked = " checked"; 
                }

                $output .= '<label class="mt-checkbox mt-checkbox-outline">
                                <input type="checkbox" name="'.$var_name.'[]" id="'.$var_name.'[]" value="'.$key.'" '.$checked.'>&nbsp;&nbsp;'.$val.'
                                <span></span>
                            </label>';
            }                                                                            
        }   

        $output .= "</div>";                                              
 
        echo $output;
    }  
    function selectbox($var_name="",$value="", $array_data="", $multiple = "", $class_name = "chosen-select", $placeholder="", $inlinehelp="")
    { 
        # $array_data ::  arr[key] = val
        $output = '';
        $output .= "<select class=\"".$class_name."\" name=\"".$var_name."\" id=\"".$var_name."\" ".$multiple.">";

        if (is_array($array_data) && count($array_data) > 0) {
            FOREACH ($array_data AS $key => $val) {
                $selected = "";
                if ($value == $key) $selected = " selected";
                $output .= "<option  value=\"".$key."\" ".$selected.">".$val."</option>";
            }                                                                            
        } 

        $output .= "</select>";                                                 
 
        echo $output;
    }  
    function selectbox_multiple($var_name="",$value="", $array_data="", $multiple = "multiple", $class_name = "form-control select2_sample1", $placeholder="", $inlinehelp="")
    { 
        # $array_data ::  arr[key] = val
        # $value ::  arr[key] = key

        $output = '';
        $output .= "<select class=\"".$class_name."\" name=\"".$var_name."\" id=\"".$var_name."\" ".$multiple.">";

        if (is_array($array_data) && count($array_data) > 0) {
            FOREACH ($array_data AS $key => $val) { 
                    $selected = "";
                IF (isset($value[$key]) && $value[$key] != "") {
                    $selected = " selected"; 
                } 
                $output .= "<option  value=\"".$key."\" ".$selected.">".$val."</option>";
            }                                                                            
        } 

        $output .= "</select>";                                                 
 
        echo $output;
    }  

    /****** END FORM LIB ******/
    
    function generate2darray($table, $key, $val, $where = "",$val2 = "")
    { 
        $output = '';
        $sql    = 'select * from '.$table. " ";
        if ($where <> "") $sql .= " WHERE ". $where;
        $sql .= "ORDER BY $val";
        #echo "A : ". $sql;
        $query = $this->ci->db->query($sql);
   
        foreach ($query->result() as $row) {
            $id             = $row->$key; 
            if ($val2 == "") {
                $output[$id]    = $row->$val;
            } else {
                $output[$id]    = $row->$val.' - '. $row->$val2;
            }
        }  
        return $output;
    } 
    function generateparent($table, $key, $val, $arr_where)
    {  
        $data = ""; $id = "";
        $sql    = "select * from ".$table." WHERE parent_id = '0' AND is_topik='0' ORDER BY no_urut"; 
        #echo $sql."<br>\n";
        $query = $this->ci->db->query($sql);

        $data["0"]      = "-";
   
        foreach ($query->result() as $rs) {
            $id         = $rs->$key;
            $data[$id]  = $rs->$val; 
            $label      = $data[$id];
            $data       = $this->getchild($data, $table, $key, $val, $id, $label);
        }  
        #print_r($data);exit;
        return $data;
    } 
    function getchild($data, $table, $key, $val, $id, $label) { 
        $sql    = "select * from ".$table." WHERE parent_id = $id AND is_topik='0' ORDER BY no_urut"; 
        #echo $sql."<br>\n";
        $query = $this->ci->db->query($sql);
   
        foreach ($query->result() as $rs) {
            $id         = $rs->$key;
            $data[$id]  = $label ." - ". $rs->$val; 
            $label      = $data[$id];
            $data       = $this->getchild($data, $table, $key,$val, $id, $label);
        }  
        return $data;
    }
    //////
    
    function inputfile_ace($var_name="",$value="", $readonly="false", $placeholder="", $inlinehelp="")
    { 
        $output = '';
        $output .= '<label class="ace-file-input">
                        <input id="'.$var_name.'" type="file">
                        <span class="ace-file-container selected" data-title="Change">
                        <span class="ace-file-name" data-title="">
                        </span>
                        <a class="remove" href="#">
                    </label>'; 
        echo $output;
    } 
    
    function selisih_jam($jam_awal="",$jam_akhir="")
    {  
        $l = explode(":", $jam_awal);
        $dtawal = mktime($l[0],$l[1], $l[2],"1","1","1");
        $la = explode(":", $jam_akhir);
        $dtakhir = mktime($la[0],$la[1], $la[2],"1","1","1");

        $dtselisih = $dtakhir - $dtawal;

        $totalmenit = $dtselisih / 60;
        echo "Total Menit : ". $totalmenit."<br>";
        echo "menit / 60 : ". ($totalmenit / 60) ."<br>";
        $jam = explode(".", $totalmenit / 60);
        echo "Jam : ". $jam[0]."<br>"; 
        $sisamenit = (($totalmenit / 60) - $jam[0]) * 60;
        echo "menit : ". number_format($sisamenit,2)."<br>"; 
        
    } 
    function get_jam($jam_awal)
    {  
        $l = explode(":", $jam_awal);
        return $l[0]; 
    } 
    function get_prosentase_lama_tidur($jam_awal)
    {  
        $l = explode(":", $jam_awal);
        if ($l[0]>=6) return 20; else return 0; 
    } 
    function get_point_fatique($persentase)
    {   
        if ($persentase >= 81) {
            return 3;
        } else if ($persentase >=65 && $persentase <= 80) {
            return 2;
        } else {
            return 1;
        }
    } 
    function get_point_spo($persentase)
    {  
        return 3;exit;
        if ($persentase >= 95) {
            return 3;
        } else if ($persentase >=90 && $persentase <= 94) {
            return 2;
        } else {
            return 1;
        }
    } 
    function get_point_bpm($persentase)
    {  
        return 3;exit;
        if ($persentase > 100) {
            return 2;
        } else if ($persentase >=50 && $persentase <= 100) {
            return 3;
        } else {
            return 1;
        }
    } 
    function get_nilai_pengawasan($var_1, $var_2, $var_3)
    {  
        $nilai_terendah = min($var_1, $var_2, $var_3);
        return $nilai_terendah;
    } 
    function get_status_pengawasan($var_1, $var_2, $var_3)
    {  
        $nilai_terendah = min($var_1, $var_2, $var_3);
        if ($nilai_terendah == 3) {
            return "DISETUJUI";
        } else if ($nilai_terendah == 2) {
            return "BUTUH PENGAWASAN";
        } else {
            return "TIDAK DISETUJUI";
        }
    } 
    function tgl_kemarin($var,$mundur="1")
    {  
        $tmp = explode("-", $var);
        $tgl = date("Y-m-d",mktime(0, 0, 0, $tmp["1"], $tmp["2"] - $mundur, $tmp["0"] ));

        return $tgl;
    } 
    function tgl_besok($var,$mundur="1")
    {  
        $tmp = explode("-", $var);
        $tgl = date("Y-m-d",mktime(0, 0, 0, $tmp["1"], $tmp["2"] + $mundur, $tmp["0"] ));

        return $tgl;
    } 

    function get_lama_tidur($jam_awal)
    {  
        $l = explode(":", $jam_awal);
        return $l[0]; 
    } 

    function get_persen_mf($var) { 
        $l = explode(":", $var["lama_tdr_kemarin"]);
        $lama_tdr_kemarin   = $l[0]; 

        $l = explode(":", $var["lama_tdr_sekarang"]);
        $lama_tdr_hari_ini  = $l[0]; 
 
        $persen_fatique = 0;

        if ($lama_tdr_hari_ini >= 4  && $lama_tdr_hari_ini < 5 && $lama_tdr_kemarin < 6) {
            $persen_fatique = 0;
        }
        if ($lama_tdr_hari_ini >= 4  && $lama_tdr_hari_ini < 5 && $lama_tdr_kemarin >= 6) {
            $persen_fatique = 15;
        }
        if ($lama_tdr_hari_ini >= 5 && $lama_tdr_hari_ini < 6 && $lama_tdr_kemarin < 6) {
            $persen_fatique = 15;
        }
        if ($lama_tdr_hari_ini >= 5 && $lama_tdr_hari_ini < 6 && $lama_tdr_kemarin >= 6) {
            $persen_fatique = 20;
        }
        if ($lama_tdr_hari_ini >= 6 && $lama_tdr_hari_ini < 7 && $lama_tdr_kemarin < 6) {
            $persen_fatique = 45;
        }
        if ($lama_tdr_hari_ini >= 6 && $lama_tdr_hari_ini < 7 && $lama_tdr_kemarin >= 6) {
            $persen_fatique = 50;
        }
        if ($lama_tdr_hari_ini >= 7 && $lama_tdr_kemarin < 6) {
            $persen_fatique = 50;
        }
        if ($lama_tdr_hari_ini >= 7 && $lama_tdr_kemarin >= 6) {
            $persen_fatique = 50;
        } 

        if ($var["apakah_sedang_minum_obat"]  == "T") $persen_pertanyaan_1 = 20; else $persen_pertanyaan_1 = 0;
        if ($var["apakah_sedang_ada_masalah"]  == "T") $persen_pertanyaan_2 = 15; else $persen_pertanyaan_2 = 0;
        if ($var["apakah_siap_bekerja"]  == "Y") $persen_pertanyaan_3 = 15; else $persen_pertanyaan_3 = 0;

        $persentase = $persen_fatique + $persen_pertanyaan_1 + $persen_pertanyaan_2 + $persen_pertanyaan_3;
        return $persentase;
    }
    
    function rekomendasi_operator($var)
    {  
        $persen_mf  = $this->get_persen_mf($var);
        $point_mf   = $this->get_point_fatique($persen_mf);
        $point_spo = $this->get_point_spo($var["spo"]);
        $point_bpm = $this->get_point_bpm($var["bpm"]);

        $nilai_rekomendasi = $this->get_nilai_pengawasan($point_mf , $point_spo , $point_bpm );
        # echo "nilai_rekomendasi : ". $nilai_rekomendasi ;exit;

        return $nilai_rekomendasi; 
    } 

    function prediksi_butuh_pengawasan($var) { 
        $l = explode(":", $var["lama_tdr_kemarin"]);
        $lama_tdr_kemarin   = $l[0]; 

        $l = explode(":", $var["lama_tdr_sekarang"]);
        $lama_tdr_hari_ini  = $l[0]; 
 
        $prediksi_butuh_pengawasan = 1;

        if ($lama_tdr_hari_ini >= 4 && $lama_tdr_hari_ini < 5 && $lama_tdr_kemarin < 6) {
            $prediksi_butuh_pengawasan = 1;
        }
        if ($lama_tdr_hari_ini >= 4 && $lama_tdr_hari_ini < 5 && $lama_tdr_kemarin >= 6) {
            $prediksi_butuh_pengawasan = 2;
        }
        if ($lama_tdr_hari_ini >= 5 && $lama_tdr_hari_ini < 6 && $lama_tdr_kemarin < 5) {
            $prediksi_butuh_pengawasan = 2;
        }
        if ($lama_tdr_hari_ini >= 5 && $lama_tdr_hari_ini < 6 && $lama_tdr_kemarin >= 5 && $lama_tdr_kemarin < 6) {
            $prediksi_butuh_pengawasan = 3;
        }
        if ($lama_tdr_hari_ini >= 5 && $lama_tdr_hari_ini < 6 && $lama_tdr_kemarin >= 6) {
            $prediksi_butuh_pengawasan = 4;
        }
        if ($lama_tdr_hari_ini >= 6 && $lama_tdr_hari_ini < 7 && $lama_tdr_kemarin < 5) {
            $prediksi_butuh_pengawasan = 3;
        }
        if ($lama_tdr_hari_ini >= 6 && $lama_tdr_hari_ini < 7 && $lama_tdr_kemarin >= 5 && $lama_tdr_kemarin < 6) {
            $prediksi_butuh_pengawasan = 4;
        }
        if ($lama_tdr_hari_ini >= 6 && $lama_tdr_hari_ini < 7 && $lama_tdr_kemarin >= 6) {
            $prediksi_butuh_pengawasan = 5;
        }
        if ($lama_tdr_hari_ini >= 7 && $lama_tdr_kemarin < 5) {
            $prediksi_butuh_pengawasan = 4;
        }
        if ($lama_tdr_hari_ini >= 7 && $lama_tdr_kemarin >= 5 && $lama_tdr_kemarin < 6) {
            $prediksi_butuh_pengawasan = 5;
        }
        if ($lama_tdr_hari_ini >= 7 && $lama_tdr_kemarin >= 6) {
            $prediksi_butuh_pengawasan = 6;
        } 
        # echo " a : ". $lama_tdr_hari_ini. ' - '.$lama_tdr_kemarin." = ". $prediksi_butuh_pengawasan;exit;
        return $prediksi_butuh_pengawasan;
    }


    function prediksi_stop_bekerja($var) { 
        $l = explode(":", $var["lama_tdr_kemarin"]);
        $lama_tdr_kemarin   = $l[0]; 

        $l = explode(":", $var["lama_tdr_sekarang"]);
        $lama_tdr_hari_ini  = $l[0]; 
 
        $prediksi_stop_bekerja = 1;

        if ($lama_tdr_hari_ini >= 4 && $lama_tdr_hari_ini < 5 && $lama_tdr_kemarin < 6) {
            $prediksi_stop_bekerja = 1;
        }
        if ($lama_tdr_hari_ini >= 4 && $lama_tdr_hari_ini < 5 && $lama_tdr_kemarin >= 6 && $lama_tdr_kemarin < 7) {
            $prediksi_stop_bekerja = 2;
        }
        if ($lama_tdr_hari_ini >= 4 && $lama_tdr_hari_ini < 5 && $lama_tdr_kemarin >= 7) {
            $prediksi_stop_bekerja = 4;
        } 
        if ($lama_tdr_hari_ini >= 5 && $lama_tdr_hari_ini < 6 && $lama_tdr_kemarin < 5) {
            $prediksi_stop_bekerja = 3;
        }
        if ($lama_tdr_hari_ini >= 5 && $lama_tdr_hari_ini < 6 && $lama_tdr_kemarin >= 5 && $lama_tdr_kemarin < 6) {
            $prediksi_stop_bekerja = 5;
        }
        if ($lama_tdr_hari_ini >= 5 && $lama_tdr_hari_ini < 6 && $lama_tdr_kemarin >= 6) {
            $prediksi_stop_bekerja = 6;
        } 
        if ($lama_tdr_hari_ini >= 6 && $lama_tdr_hari_ini < 7 && $lama_tdr_kemarin < 5) {
            $prediksi_stop_bekerja = 5;
        }
        if ($lama_tdr_hari_ini >= 6 && $lama_tdr_hari_ini < 7 && $lama_tdr_kemarin >= 5 && $lama_tdr_kemarin < 6) {
            $prediksi_stop_bekerja = 6;
        }
        if ($lama_tdr_hari_ini >= 6 && $lama_tdr_hari_ini < 7 && $lama_tdr_kemarin >= 6) {
            $prediksi_stop_bekerja = 7;
        } 
        if ($lama_tdr_hari_ini >= 7 && $lama_tdr_kemarin < 5) {
            $prediksi_stop_bekerja = 6;
        }
        if ($lama_tdr_hari_ini >= 7 && $lama_tdr_kemarin >= 5 && $lama_tdr_kemarin < 6) {
            $prediksi_stop_bekerja = 7;
        }
        if ($lama_tdr_hari_ini >= 7 && $lama_tdr_kemarin >= 6) {
            $prediksi_stop_bekerja = 8;
        }
 
        return $prediksi_stop_bekerja;
    }
    function selisih_hari($start, $end) {

        $start_date = new DateTime("2012-02-10 11:26:00");
        $end_date = new DateTime("2012-04-25 01:50:00");
        $interval = $start_date->diff($end_date);

        echo "Result " . $interval->y . " years, " . $interval->m." months, ".$interval->d." days "." , ".$interval->h." hour "." , ".$interval->i." minutes "." , ".$interval->s." sec \n";
    
    }
} 
?>