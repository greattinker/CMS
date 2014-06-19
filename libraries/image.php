<?php
/* (C) 2006 - 2008 by Julian von Mendel (prog@derjulian.net)
 * License: LGPL3
 * $LastChangedBy: jvm $
 * $LastChangedDate: 2008-04-03 14:04:24 +0000 (Thu, 03 Apr 2008) $
 * $Revision: 5 $
 */

//error_reporting(E_ALL | E_STRICT);

class ImageException extends Exception {}
class AlgorithmException extends Exception {}

/** implement this interface to create an image processing algorithm which
 * can be called by image::algorithm and used via
 *   $img = new Image;
 *   $img->createFromFile(...);
 *   $img->algorithm(new yourclass());
 *   $img->save();
 */
interface imagealgorithm {
	public function setoriginalimage(image $image);
	public function option($get); /* return true or false */
	public function algorithm($x, $y);
	/* optional:
	 *   public function setnewimage(image $image);
	 *   public function return();
	 * possible options are:
	 *   needspixel (you need to know the colors of each pixel)
	 *   newimg (you want to replace the image by your own one but need
	 *           the old one for information purposes)
	 *   return (for own return value)
	*/
}

class imagealgorithmmask implements imagealgorithm {
	protected $image = false;

    protected $mask;
    protected $shrink;
    protected $offsetx;
    protected $offsety;
    protected $threshold = 0;

	public function option($get) {
		if ($get == "newimg")
			return true;
		return false;
	}

	public function setoriginalimage(image $image) {
        $image->initpixelcache();
		$this->image = $image;
	}

    public function setparameter($mask, $shrink) {
        $this->mask = $mask;
        $this->shrink = $shrink;
        $this->offsetx = (count($mask) - 1) / 2;
        $this->offsety = (count($mask[0]) - 1) / 2;
    }

    public function setthreshold($threshold) {
        $this->threshold = $threshold;
    }

	public function algorithm($x, $y) {
		if (!($this->image instanceof image))
			throw new AlgorithmException("No valid image set.");

		if ($y < $this->offsety ||
                $y >= $this->image->height() - $this->offsety ||
				$x < $this->offsetx ||
                $x >= $this->image->width() - $this->offsetx)
			return 0;

		$sum = array("red" => 0, "green" => 0, "blue" => 0);

		for($i = -1 * $this->offsetx; $i <= $this->offsetx; $i++) {
        for($j = -1 * $this->offsety; $j <= $this->offsety; $j++) {
            foreach ($this->image->getpixel($x + $i, $y + $j, false) as
                    $key => $val) {
                if ($key == "alpha")
                    continue;

                $sum[$key] += $val * $this->mask[$i +
                        $this->offsetx][$j + $this->offsety];
            }
		}
        }

        foreach ($sum as $key => $val) {
            $sum[$key] = 1 / $this->shrink * $val;

            if ($sum[$key] < $this->threshold) {
                $sum = 0;
                break;
            }
        }

        return $sum;
	}
}

/** if you want to use Image::algorithm to iterate over every pixel of
 * your image, but you don't want to write a whole class for this purpose,
 * you can use this class as an interface for your function. example:
 *   function makeeverythingblue($image, $pixel, $new, $additionalparam) {
 *   	# pixel and additionalparam will be false, because we deactivate both
 *      return array("red" => 0, "green" => 0, "blue" => 255)
 *   }
 *
 *   $img = new Image;
 *   $img->createFromFile(...)
 *       ->algorithm(new imagealgorithm_function("makeeverythingblue",
 *   		false, true, false))
 *       ->save();
 *
 * the array you return defines the new color for this pixel. ("newimg"
 * option neccessary)
 * if you return only one number (0..255), it will be used
 * as value for red, green and blue (so 255 is white, 0 is black and
 * 128 is gray)
 */
class imagealgorithm_function implements imagealgorithm {
	protected $image = false;
	protected $callback;
	protected $newimg;
	protected $additionalparam;
	protected $needspixel;

	function __construct($callback, $additionalparam = false,
			$newimg = true) {
		if (!is_callable($callback))
			throw new ImageException("No valid callback given.");

		$this->callback = $callback;
		$this->additionalparam = $additionalparam;
		$this->newimg = $newimg;
	}

	public function option($get) {
		if ($get == "newimg")
			return $this->newimg;
		return false;
	}

	public function setoriginalimage(image $image) {
        $image->initpixelcache();
		$this->image = $image;
	}

	public function setnewimage(image $image) {
		$this->newimg = $image;
	}

	public function algorithm($x, $y) {
		if (!($this->image instanceof image))
			throw new AlgorithmException("No valid image set.");

		return call_user_func($this->callback, $x, $y, $this->image,
				$this->newimg, $this->additionalparam);
	}
}

class image {
    protected $type; /* file type; "png"/"jpeg"/"gif" */
    protected $height; /* height in px */
    protected $width; /* height in px */
    protected $handle; /* image handle */
    protected $file = "output.png";
    protected $channel = self::black;
    protected $autohandledestroy = true;

    public $colornames = array("red", "green", "blue");

    protected $pixel = array();
    const undefined = 256;

	const all = 2;
	const maxval = 3;
	const red = 4;
	const green = 5;
	const blue = 6;
	const black = 7;

	/* redirect function calls */
    function __call($method, $args) {
    	/* convert everything to lower case,
    	 * so people can use what they prefer */
        $method = strtolower($method);

    	/* aliases */
    	switch ($method) {
    		case "sethandler":
    			$method = "sethandle";
    			break;
    		case "handler":
    			$method = "handle";
    			break;
    	}

    	/* check if maybe a method of this class was meant
    	 * (and a not lowercase method was called, or an alias) */
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $args);
        }

		/* check if php gd function exists */
        if (!function_exists("image".$method)) {
            throw new ImageException("Function 'image".$method.
					"' doesn't exist.");
        }

		/* php gd functions want an image handle as first parameter */
        if ($this->handle())
            $args = array_merge(array($this->handle()), $args);

        $ret = call_user_func_array("image".$method, $args);

		/* some php functions return an image handle; we always change
		 * our own image, because it is not common to edit multiple images
         * at the same time, so most times this is wanted; if not, user has
         * to create a copy of this image before */
        if (is_resource($ret))
            $this->sethandle($ret);

        return $ret;
    }

    public function createfromfile($path) {
	    if (!file_exists($path))
	        throw new ImageException("File '".$path."' not found.");

	    $info = self::info($path);
        switch($info[2]) {
	        case 1:
		        $this->sethandler($this->createfromgif($path));
		        $this->type = "gif";
	        	break;
	        case 2:
		        $this->sethandler($this->createfromjpeg($path));
		        $this->type = "jpeg";
	        	break;
	        case 3:
		        $this->sethandler($this->createfrompng($path));
		        $this->type = "png";
	        	break;
	        default:
		        throw new ImageException("Unknown file type '".$info[2]."'.");
	    }

	    $this->file = $path;
        $this->width  = $this->sx();
        $this->height = $this->sy();

        return $this;
    }

    public function sethandle($handle) {
        if (!is_resource($handle))
            throw new ImageException(
					"Only valid image identifiers can be set as handle.");

        if ($this->autohandledestroy)
            $this->close();

        $this->type   = false;
        $this->handle = $handle;
        $this->width  = $this->sx();
        $this->height = $this->sy();

        return $this;
    }

    public function autohandledestroy($yes) {
        $this->autohandledestroy = $yes;
        return $this;
    }

    public function setchannel($channel) {
    	$this->channel = $channel;
        return $this;
    }

    public function channel() {
    	return $channel;
    }

    public function handle() {
        return $this->handle;
    }

    public function width() {
        return $this->width;
    }

    public function height() {
        return $this->height;
    }

    public function filetype() {
        return $this->type;
    }

    public function mimetype() {
        return "image/".$this->type;
    }

	/** save file;
	 * type is determined by extension and can be "gif", "jpg" or "png" */
    public function save($destination = false, $quality = 25) {
    	if (!$destination)
    		$destination = $this->file;

        $type = explode(".", $destination);
        $type = strtolower(end($type));

    	switch($type) {
            case "gif":
                return $this->gif($destination);
            	break;
            case "jpg":
            case "jpeg":
                return $this->jpeg($destination, $quality);
            	break;
            case "png":
                return $this->png($destination);
            	break;
            default:
                throw new ImageException("Unknown file type '".$type."'.");
        }

        return $this;
    }

    public function output($quality = 80) {
        header("Content-Type: ".$this->mimetype());
        return $this->png();
    }

    public function preparecopy($old) {
        $this->alphablending(false);
        $this->savealpha(true);
        $this->palettecopy($old->handle());
        $transparent = $this->colorallocate(0, 0, 0);
        $this->fill(0, 0, $transparent);
        $this->colortransparent($transparent);

        return $this;
    }

    public function cut($left, $top, $width, $height) {
        $new = new self;
        $new->createtruecolor($width, $height);
        $new->preparecopy($this);
        $new->copyresampled(
            $this->handle(),
            0, 0, $left, $top,
            $width,
            $height,
            $width,
            $height
        );

        $this->sethandle($new->handler());
        return $this;
    }

    public function resize($maxwidth = 150, $maxheight = 150, $scale = true) {
    	if (!$scale) {
        	$width  = $maxwidth ;
            $height = $maxheight;
        } else {
            if ($this->width() < $maxwidth && $this->height() < $maxheight) {
                $width  = $this->width();
                $height = $this->height();
            } else if ($this->width() < $this->height()) {
                $height = $maxheight;
                $width  = round($this->width() * $height / $this->height());
            } else {
                $width  = $maxwidth;
                $height = round($this->height() * $width / $this->width());
            }
        }

        $new = new self;
        $new->createtruecolor($width, $height);
        $new->preparecopy($this);
        $new->copyresampled(
            $this->handle(),
            0, 0, 0, 0,
            $width,
            $height,
            $this->width(),
            $this->height()
        );

        $this->sethandle($new->handle());
        return $this;
    }

	/** extract one color channel and use it as a new image */
    public function filter() {
        return $this->algorithm(new imagealgorithm_function(
        		array($this, "filter_")));
    }
    public function filter_($x, $y, $image, $new,
    		$additionalparam) {
		/* used on every single pixel */
        return $this->getpixel($x, $y, false);
    }

	/** rotate image clockwise and automatically adjust height/width;
	 * beware: only 90°/-90°/180° are supported */
    public function rotate($angle) {
        switch ($angle) {
            case 90:
            case -90:
                $height = $this->width;
                $width = $this->height;
                break;
            case 180:
                $width = $this->width;
                $height = $this->height;
                break;
            default:
                throw new ImageException("angle must be 90, -90 or 180.");
        }

        $new = new self;
        $new->createtruecolor($width, $height);
        $new->preparecopy($this);

        for ($y = 0; $y < $this->height(); $y++) {
            for ($x = 0; $x < $this->width(); $x++) {
                $color = $this->colorat($x, $y);
                switch ($angle) {
                    case 90:
                        $new->setpixel($width - $y - 1, $x, $color);
                        break;
                    case -90:
                        $new->setpixel($y, $height - $x - 1, $color);
                        break;
                    case 180:
                        $new->setpixel($width - $x - 1,
                                $height - $y - 1, $color);
                        break;
                }
            }
        }

        $this->sethandle($new->handle());
        return $this;
    }

    public function pixel($channel = false) {
		for ($y = 0; $y < $this->height(); $y++) {
			for ($x = 0; $x < $this->width(); $x++) {
				$color = $this->getpixel($x, $y, $channel);
				$pixel[$x][$y] = $color;
			}
		}

		return $pixel;
    }

    public function initpixelcache() {
        if ($this->cacheinitialized())
            return $this;

        $this->pixel = array();
        for ($i = 0; $i < $this->width() * $this->height() * 3; $i++)
            $this->pixel[] = self::undefined;

        return $this;
    }

    public function resetpixelcache() {
        $this->deactivatepixelcache();
        $this->initpixelcache();
        return $this;
    }

    public function deactivatepixelcache() {
        $this->pixel = array();
        return $this;
    }

    public function getpixelcache() {
        return $this->pixel;
    }

    public function setpixelcache($pixel) {
        $this->pixel = $pixel;
        return $this;
    }

    public function cacheinitialized() {
        return ((count($this->pixel) == $this->width() * $this->height() *3) ?
                true : false);
    }

    public function getpixel($x, $y, $channel = self::all) {
        /* get color out of cache */
        if ($this->cacheinitialized()) {
            $offset = ($y * $this->width() + $x) * 3;

            $color = array("red" => $this->pixel[$offset + 0],
                    "green" => $this->pixel[$offset + 1],
                    "blue" => $this->pixel[$offset + 2]);
        }

        /* get color out of image */
        if (!isset($color) || $color["red"] == self::undefined)
            $color = $this->colorsforindex($this->colorat($x, $y));

        /* save color in cache */
        if ($this->cacheinitialized())
            for ($i = 0; $i < 3; $i++)
                $this->pixel[$offset + $i] = $color[$this->colornames[$i]];

        /* load default channel */
		if ($channel == false)
			$channel = $this->channel;

        /* get channel values */
		switch ($channel) {
			default:
                if (!is_array($channel))
                    throw new ImageException(
                            "No valid color channel was chosen.");
                $m = 1 / ($channel[0] + $channel[1] + $channel[2]);
				$intensity = $channel[0] * $m * $color["red"] +
						$channel[1] * $m * $color["green"] +
						$channel[2] * $m * $color["blue"];
                break;
			case self::all:
				return $color;
            case self::maxval:
                return max($color["red"], $color["green"], $color["blue"]);
			case self::black:
				$intensity = 0.3 * $color["red"] +
						0.59 * $color["green"] +
						0.11 * $color["blue"];
				break;
			case self::red:
				$intensity = $color["red"];
				break;
			case self::green:
				$intensity = $color["green"];
				break;
			case self::blue:
				$intensity = $color["blue"];
				break;
		}

        $intensity = round($intensity);

		return array("red" => $intensity,
				"green" => $intensity,
				"blue" => $intensity);
    }

    public function setpixelrgb($x, $y, array $color) {
        /* save new color in image */
        $this->setpixel($x, $y, $this->colorallocate($color["red"],
                $color["green"], $color["blue"]));

        /* save new color in cache */
        if ($this->pixel) {
            $this->pixel[($y * $this->width() + $x) * 3 + 0] = $color["red"];
            $this->pixel[($y * $this->width() + $x) * 3 + 1] = $color["green"];
            $this->pixel[($y * $this->width() + $x) * 3 + 2] = $color["blue"];
        }
    }

    public function algorithm($callback) {
        if (!($callback instanceof imagealgorithm)) {
            throw new ImageException("Callback is no class implementing ".
					"interface 'imagealgorithm'.");
        }

        if ($callback->option("newimg")) {
			$new = new self;
			$new->createtruecolor($this->width(), $this->height());
			$new->preparecopy($this);
    	}

		$callback->setoriginalimage($this);

		if ($callback->option("newimg") &&
				method_exists($callback, "setnewimage"))
			$callback->setnewimage($new);

		for ($x = 0; $x < $this->width(); $x++) {
		    for ($y = 0; $y < $this->height(); $y++) {
				$color = $callback->algorithm($x, $y);

		        if (!$callback->option("newimg") ||
                        $color == false || $color == self::undefined)
		        	continue;

				if (is_numeric($color)) {
					$color = min(255, max(0, $color));
					$new->setpixelrgb($x, $y, array("red" => $color,
							"green" => $color, "blue" => $color));
					continue;
				}

				foreach ($color as $key => $val)
					$color[$key] = min(255, max(0, $val));

				$new->setpixelrgb($x, $y, $color);
			}
		}

        if ($callback->option("newimg")) {
        	$this->sethandle($new->handle());
            $this->setpixelcache($new->getpixelcache());
            $new->close();
        }

		if ($callback->option("return"))
			return $callback->return();
        return $this;
    }

    public function close() {
        /*if (is_resource($this->handle))
            imagedestroy($this->handle);*/
        return $this;
    }

    public static function info($path, $cache = false) {
        return getimagesize($path);
    }

    public static function thumbnail($source, $destination,
    		$maxwidth = 150, $maxheight = 150, $scale = true) {
        if (file_exists($destination))
            return true;

        $img = new self;
        $img->createfromfile($source);
        $img->resize($maxwidth, $maxheight, $scale);
        $img->interlace();
        $ret = $img->save($destination);
        $img->close();

        return $ret;
    }
}