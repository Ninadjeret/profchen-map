<?php

class POGO_phptoshop {

    function __construct( $image_path = null ) {   
        $this->image_path = $image_path;
    
        // get the input file extension and create a GD resource from it
        $ext = pathinfo($image_path, PATHINFO_EXTENSION);
        
        if($ext == "jpg" || $ext == "jpeg")
            $this->image = imagecreatefromjpeg($image_path);
        elseif($ext == "png")
            $this->image = imagecreatefrompng($image_path);
        elseif($ext == "gif")
            $this->image = imagecreatefromgif($image_path);
        else
            echo 'Unsupported file extension';
                
    }

    function pixelate( $output, $pixelate_x = 20, $pixelate_y = 20 ) {

    
        // now we have the image loaded up and ready for the effect to be applied
        // get the image size
        $size = getimagesize($this->image_path);
        $height = $size[1];
        $width = $size[0];

        $this->image_header = imagecrop($this->image, ['x' => 0, 'y' => 0, 'width' => $width, 'height' => 250]);
        
        // start from the top-left pixel and keep looping until we have the desired effect
        for($y = 0;$y < $height;$y += 1)
        {
    
            for($x = 0;$x < $width;$x += 1)
            {
                // get the color for current pixel
                $rgb = imagecolorsforindex($this->image_header, imagecolorat($this->image, $x, $y));
                if( $x ===540 && $y === 570 ) {
                  //var_dump($rgb);
                } 
                // get the closest color from palette
                if( $rgb['red'] > 220 && $rgb['blue'] > 220 && $rgb['green'] > 220 ) {
                  $color = imagecolorallocate ( $this->image_header , $rgb['red'] , $rgb['green'] , $rgb['blue'] );
                  imagefilledrectangle($this->image_header, $x-1, $y-1, $x, $y, $color); 
                } else {
                  $color = imagecolorallocate ( $this->image_header , 0 , 0 , 0 );
                  imagefilledrectangle($this->image_header, $x-1, $y-1, $x, $y, $color);                   
                }
                  
            }       
        }
        
        ob_clean();
        header('Content-Type: image/png');
        header("Content-Disposition: inline; filename=\"pixel.png\"");
        imagepng($this->image_header);
        imagedestroy($this->image_header);
        die(); 
                
    }

}