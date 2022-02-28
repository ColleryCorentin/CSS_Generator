<?php

$new_height = 0;
$new_width = 0;
$r = false;
$arr_dir = [];
$newimg = "sprite.png";
$newcss = "style.css";
$data = 0;
//***************** Creer un SCAN *****************//

    function my_scandir($dir)
    {
        global $r,$arr_dir;

        $real_path = realpath($dir);
            if (!is_dir($dir))
            {
                echo "$dir n'est pas un dossier correct, veuillez rentrer un dossier existant.\n";
                exit();
            }
            else
            {
                if ($handle = opendir($dir))
            {
                while (false !== ($entry = readdir($handle)))
                {
                    if ($entry != '.' && $entry != '..')
                    {
                        $entry_path = $real_path . '/' . $entry;
                
                    if(substr($entry, -4) == '.png')
                {
                    array_push($arr_dir, $entry_path);
                }
                elseif(is_dir($entry_path) && $r == true)
                {    
                    my_scandir($entry_path);    
                }
            }

            }
                closedir($handle);
        }
    
    }
}

//******************** Calcul des images *******************//

    function imgcalc($arr_dir)
    {
        $arr_w = [];
        $arr_h = [];
        global $new_height,$new_width,$newimg,$data;


        foreach($arr_dir as $i)
        {

            list($width, $height) = getimagesize($i);
            array_push($arr_w, $width);
            array_push($arr_h, $height);

        }
        $somme_w = array_sum($arr_w);
        $somme_h = array_sum($arr_h);
        $max_width = max($arr_w);
        $max_height = max($arr_h);

        

//********************* Creation de la sprite ******************//

        echo "Comment afficher les images :\n\n";
        echo "Horizontal : 1\n" . "Vertical : 2\n";
        $input = readline();

        if($input == '1')
        {
            $data = 1;
            $background = imagecreatetruecolor($somme_w, $max_height);

            foreach ($arr_dir as $i)
            {
                list($width, $height) = getimagesize($i);
                $tmp = imagecreatefrompng($i);
                imagecopy($background, $tmp, $new_width, 0, 0, 0, $width, $height);
                $new_width += $width;
            }
            imagedestroy($tmp);
            imagepng($background, $newimg);
            echo "La sprite horizontale a été créée\n";
        }

        elseif($input == '2')
        {
            $data = 2;
            $background = imagecreatetruecolor($max_width, $somme_h);

            foreach ($arr_dir as $i)
            {
                list($width, $height) = getimagesize($i);
                $tmp = imagecreatefrompng($i);
                imagecopy($background, $tmp, 0, $new_height, 0, 0, $width, $height);
                $new_height += $height;
            }
            imagedestroy($tmp);
            imagepng($background, $newimg);
            echo "La sprite verticale a été créée\n";
        }
        else{
            echo"\nErreur, Veuillez rentrer les bonnes valeurs.\n\n";
            sleep(1);
            imgcalc($arr_dir);
        }
    }

//************************ Le CSS *********************//

    function My_css($newimg)
    {
        global $arr_dir,$newcss,$data;
        $nbrimg = 1;
        $new_h = 0;
        $new_v = 0;

        $style = fopen("$newcss", 'w+'); // creer un fichier css.  
        fwrite($style, ".sprite { 
            background-image: url($newimg);
            background-repeat: no-repeat;
        }
        ");

        if ($data === 1){
            foreach ($arr_dir as $i){
                list($width, $height) = getimagesize($i);
                fwrite($style, "#img$nbrimg{
                    width: " . $width . "px; 
                    height: " . $height . "px;
                    background-position:" . $new_v . "px," . $new_h . "px; 
                }
                ");
                $new_v += $width;
                $nbrimg += 1;
            }
        }
else{
        foreach ($arr_dir as $i){
            list($width, $height) = getimagesize($i);
            fwrite($style, "#img$nbrimg{
                width: " . $width . "px; 
                height: " . $height . "px;
                background-position:" . $new_v . "px," . $new_h . "px; 
            }
            ");
            $new_h += $height;
            $nbrimg += 1;
        }
    }
}
//************************ Le Man *********************//

    function man()
    {
echo "\n    \e[34m[NAME]\e[0m\n
css_generator - sprite generator for HTML use\n
    \e[33m[SYNOPSIS]\e[0m\n
css_generator [OPTIONS]. . . assets_folder\n
    \e[32m[DESCRIPTION]\e[0m\n
Concatenate all images inside a folder in one sprite and write a style sheet ready to use.
Mandatory arguments to long options are mandatory for short options too.\n
-r, -- recursive
Look for images into the assets_folder passed as arguement and all of its subdirectories.\n
-i, -- output-image=IMAGE
Name of the generated image. If blank, the default name is « sprite.png ».\n
-s, -- output-style=STYLE
Name of the generated stylesheet. If blank, the default name is « style.css ».\n\n";
exit();
        }

//**************** ARG ********************//

        function arg(){
            global $argv, $argc, $r, $newimg,$newcss;

            if($argc < 2){
                echo "Veuillez rentrer un champ correct : --help / -h / -? / --usage\n";
                exit();
            }

            if($argv[1]=="-h" || $argv[1]== "--help" || $argv[1]== "-?" || $argv[1]== "--usage"){
                man();
            }

            elseif($argv[1] == "-r" || $argv[1] == "--recursive"){
                $r = true;
            }

            elseif($argv[1] == "-i" || $argv[1] == "-s" || $argv[1] == "--image" || $argv[1] == "--style"){
                if($argv[1] == "-i"|| $argv[1] == "--image"){ 
                    echo "Comment voulez vous appeler votre sprite ?";
                    $input = readline();
                if($input !== false)
                {
                    $newimg = $input;
                }
                }
                elseif ($argv[1] == "-s" || $argv[1] == "--style"){
                    echo "Comment voulez vous appeler votre css ?";{
                        $input = readline();
                    if($input !== false){
                        $newcss = $input;
                    }
                }
            }
            else{
                echo"Veuillez rentrer un nom de fichier valide";
            }
        }
    }

//**************** Fonction ****************//

        arg();
        my_scandir($argv[$argc -1]);
        imgcalc($arr_dir);
        My_css($newimg);