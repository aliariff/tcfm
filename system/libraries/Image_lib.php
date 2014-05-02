<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Image Manipulation class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Image_lib
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/image_lib.html
 */
class CI_Image_lib {

	var $image_library		= 'gd2';	// Can be:  imagemagick, netpbm, gd, gd2
	var $library_path		= '';
	var $dynamic_output		= FALSE;	// Whether to send to browser or write to disk
	var $source_image		= '';
	var $new_image			= '';
	var $width				= '';
	var $height				= '';
	var $quality			= '90';
	var $create_thumb		= FALSE;
	var $thumb_marker		= '_thumb';
	var $maintain_ratio		= TRUE;		// Whether to maintain aspect ratio when resizing or use hard values
	var $master_dim			= 'auto';	// auto, height, or width.  Determines what to use as the master dimension
	var $rotation_angle		= '';
	var $x_axis				= '';
	var	$y_axis				= '';

	// Watermark Vars
	var $wm_text			= '';			// Watermark text if graphic is not used
	var $wm_type			= 'text';		// Type of watermarking.  Options:  text/overlay
	var $wm_x_transp		= 4;
	var $wm_y_transp		= 4;
	var $wm_overlay_path	= '';			// Watermark image path
	var $wm_font_path		= '';			// TT font
	var $wm_font_size		= 17;			// Font size (different versions of GD will either use points or pixels)
	var $wm_vrt_alignment	= 'B';			// Vertical alignment:   T M B
	var $wm_hor_alignment	= 'C';			// Horizontal alignment: L R C
	var $wm_padding			= 0;			// Padding around text
	var $wm_hor_offset		= 0;			// Lets you push text to the right
	var $wm_vrt_offset		= 0;			// Lets you push  text down
	var $wm_font_color		= '#ffffff';	// Text color
	var $wm_shadow_color	= '';			// Dropshadow color
	var $wm_shadow_distance	= 2;			// Dropshadow distance
	var $wm_opacity			= 50;			// Image opacity: 1 - 100  Only works with image

	// Private Vars
	var $source_folder		= '';
	var $dest_folder		= '';
	var $mime_type			= '';
	var $orig_width			= '';
	var $orig_height		= '';
	var $image_type			= '';
	var $size_str			= '';
	var $full_src_path		= '';
	var $full_dst_path		= '';
	var $create_fnc			= 'imagecreatetruecolor';
	var $copy_fnc			= 'imagecopyresampled';
	var $error_msg			= array();
	var $wm_use_drop_shadow	= FALSE;
	var $wm_use_truetype	= FALSE;

	/**
	 * Constructor
	 *
	 * @param	string
	 * @return	void
	 */
	public function __construct($props = array())
	{
		if (count($props) > 0)
		{
			$this->initialize($props);
		}

		log_message('debug', "Image Lib Class Initialized");
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize image properties
	 *
	 * Resets values in case this class is used in a loop
	 *
	 * @access	public
	 * @return	void
	 */
	function clear()
	{
		$props = array('source_folder', 'dest_folder', 'source_image', 'full_src_path', 'full_dst_path', 'new_image', 'image_type', 'size_str', 'quality', 'orig_width', 'orig_height', 'width', 'height', 'rotation_angle', 'x_axis', 'y_axis', 'create_fnc', 'copy_fnc', 'wm_overlay_path', 'wm_use_truetype', 'dynamic_output', 'wm_font_size', 'wm_text', 'wm_vrt_alignment', 'wm_hor_alignment', 'wm_padding', 'wm_hor_offset', 'wm_vrt_offset', 'wm_font_color', 'wm_use_drop_shadow', 'wm_shadow_color', 'wm_shadow_distance', 'wm_opacity');

		foreach ($props as $val)
		{
			$this->$val = '';
		}

		// special consideration for master_dim
		$this->master_dim = 'auto';
	}

	// --------------------------------------------------------------------

	/**
	 * initialize image preferences
	 *
	 * @access	public
	 * @param	array
	 * @return	bool
	 */
	function initialize($props = array())
	{
		/*
		 * Convert array elements into class variables
		 */
		if (count($props) > 0)
		{
			foreach ($props as $key => $val)
			{
				$this->$key = $val;
			}
		}

		/*
		 * Is there a source image?
		 *
		 * If not, there's no reason to continue
		 *
		 */
		if ($this->source_image == '')
		{
			$this->set_error('imglib_source_image_required');
			return FALSE;	
		}

		/*
		 * Is getimagesize() Available?
		 *
		 * We use it to determine the image properties (width/height).
		 * Note:  We need to figure out how to determine image
		 * properties using ImageMagick and NetPBM
		 *
		 */
		if ( ! function_exists('getimagesize'))
		{
			$this->set_error('imglib_gd_required_for_props');
			return FALSE;
		}

		$this->image_library = strtolower($this->image_library);

		/*
		 * Set the full server path
		 *
		 * The source image may or may not contain a path.
		 * Either way, we'll try use realpath to generate the
		 * full server path in order to more reliably read it.
		 *
		 */
		if (function_exists('realpath') AND @realpath($this->source_image) !== FALSE)
		{
			$full_source_path = str_replace("\\", "/", realpath($this->source_image));
		}
		else
		{
			$full_source_path = $this->source_image;
		}

		$x = explode('/', $full_source_path);
		$this->source_image = end($x);
		$this->source_folder = str_replace($this->source_image, '', $full_source_path);

		// Set the Image Properties
		if ( ! $this->get_image_properties($this->source_folder.$this->source_image))
		{
			return FALSE;	
		}

		/*
		 * Assign the "new" image name/path
		 *
		 * If the user has set a "new_image" name it means
		 * we are making a copy of the source image. If not
		 * it means we are altering the original.  We'll
		 * set the destination filename and path accordingly.
		 *
		 */
		if ($this->new_image == '')
		{
			$this->dest_image = $this->source_image;
			$this->dest_folder = $this->source_folder;
		}
		else
		{
			if (strpos($this->new_image, '/') === FALSE AND strpos($this->new_image, '\\') === FALSE)
			{
				$this->dest_folder = $this->source_folder;
				$this->dest_image = $this->new_image;
			}
			else
			{
				if (function_exists('realpath') AND @realpath($this->new_image) !== FALSE)
				{
					$full_dest_path = str_replace("\\", "/", realpath($this->new_image));
				}
				else
				{
					$full_dest_path = $this->new_image;
				}

				// Is there a file name?
				if ( ! preg_match("#\.(jpg|jpeg|gif|png)$#i", $full_dest_path))
				{
					$this->dest_folder = $full_dest_path.'/';
					$this->dest_image = $this->source_image;
				}
				else
				{
					$x = explode('/', $full_dest_path);
					$this->dest_image = end($x);
					$this->dest_folder = str_replace($this->dest_image, '', $full_dest_path);
				}
			}
		}

		/*
		 * Compile the finalized filenames/paths
		 *
		 * We'll create two master strings containing the
		 * full server path to the source image and the
		 * full server path to the destination image.
		 * We'll also split the destination image name
		 * so we can insert the thumbnail marker if needed.
		 *
		 */
		if ($this->create_thumb === FALSE OR $this->thumb_marker == '')
		{
			$this->thumb_marker = '';
		}

		$xp	= $this->explode_name($this->dest_image);

		$filename = $xp['name'];
		$file_ext = $xp['ext'];

		$this->full_src_path = $this->source_folder.$this->source_image;
		$this->full_dst_path = $this->dest_folder.$filename.$this->thumb_marker.$file_ext;

		/*
		 * Should we maintain image proportions?
		 *
		 * When creating thumbs or copies, the target width/height
		 * might not be in correct proportion with the source
		 * image's width/height.  We'll recalculate it here.
		 *
		 */
		if ($this->maintain_ratio === TRUE && ($this->width != '' AND $this->height != ''))
		{
			$this->image_reproportion();
		}

		/*
		 * Was a width and height specified?
		 *
		 * If the destination width/height was
		 * not submitted we will use the values
		 * from the actual file
		 *
		 */
		if ($this->width == '')
			$this->width = $this->orig_width;

		if ($this->height == '')
			$this->height = $this->orig_height;

		// Set the quality
		$this->quality = trim(str_replace("%", "", $this->quality));

		if ($this->quality == '' OR $this->quality == 0 OR ! is_numeric($this->quality))
			$this->quality = 90;

		// Set the x/y coordinates
		$this->x_axis = ($this->x_axis == '' OR ! is_numeric($this->x_axis)) ? 0 : $this->x_axis;
		$this->y_axis = ($this->y_axis == '' OR ! is_numeric($this->y_axis)) ? 0 : $this->y_axis;

		// Watermark-related Stuff...
		if ($this->wm_font_color != '')
		{
			if (strlen($this->wm_font_color) == 6)
			{
				$this->wm_font_color = '#'.$this->wm_font_color;
			}
		}

		if ($this->wm_shadow_color != '')
		{
			if (strlen($this->wm_shadow_color) == 6)
			{
				$this->wm_shadow_color = '#'.$this->wm_shadow_color;
			}
		}

		if ($this->wm_overlay_path != '')
		{
			$this->wm_overlay_path = str_replace("\\", "/", realpath($this->wm_overlay_path));
		}

		if ($this->wm_shadow_color != '')
		{
			$this->wm_use_drop_shadow = TRUE;
		}

		if ($this->wm_font_path != '')
		{
			$this->wm_use_truetype = TRUE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Image Resize
	 *
	 * This is a wrapper function that chooses the proper
	 * resize function based on the protocol specified
	 *
	 * @access	public
	 * @return	bool
	 */
	function resize()
	{
		$protocol = 'image_process_'.$this->image_library;

		if (preg_match('/gd2$/i', $protocol))
		{
			$protocol = 'image_process_gd';
		}

		return $this->$protocol('resize');
	}

	// --------------------------------------------------------------------

	/**
	 * Image Crop
	 *
	 * This is a wrapper function that chooses the proper
	 * cropping function based on the protocol specified
	 *
	 * @access	public
	 * @return	bool
	 */
	function crop()
	{
		$protocol = 'image_process_'.$this->image_library;

		if (preg_match('/gd2$/i', $protocol))
		{
			$protocol = 'image_process_gd';
		}

		return $this->$protocol('crop');
	}

	// --------------------------------------------------------------------

	/**
	 * Image Rotate
	 *
	 * This is a wrapper function that chooses the proper
	 * rotation function based on the protocol specified
	 *
	 * @access	public
	 * @return	bool
	 */
	function rotate()
	{
		// Allowed rotation values
		$degs = array(90, 180, 270, 'vrt', 'hor');

		if ($this->rotation_angle == '' OR ! in_array($this->rotation_angle, $degs))
		{
			$this->set_error('imglib_rotation_angle_required');
			return FALSE;	
		}

		// Reassign the width and height
		if ($this->rotation_angle == 90 OR $this->rotation_angle == 270)
		{
			$this->width	= $this->orig_height;
			$this->height	= $this->orig_width;
		}
		else
		{
			$this->width	= $this->orig_width;
			$this->height	= $this->orig_height;
		}


		// Choose resizing function
		if ($this->image_library == 'imagemagick' OR $this->image_library == 'netpbm')
		{
			$protocol = 'image_process_'.$this->image_library;

			return $this->$protocol('rotate');
		}

		if ($this->rotation_angle == 'hor' OR $this->rotation_angle == 'vrt')
		{
			return $this->image_mirror_gd();
		}
		else
		{
			return $this->image_rotate_gd();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Image Process Using GD/GD2
	 *
	 * This function will resize or crop
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function image_process_gd($action = 'resize')
	{
		$v2_override = FALSE;

		// If the target width/height match the source, AND if the new file name is not equal to the old file name
		// we'll simply make a copy of the original with the new name... assuming dynamic rendering is off.
		if ($this->dynamic_output === FALSE)
		{
			if ($this->orig_width == $this->width AND $this->orig_height == $this->height)
			{
				if ($this->source_image != $this->new_image)
				{
					if (@copy($this->full_src_path, $this->full_dst_path))
					{
						@chmod($this->full_dst_path, FILE_WRITE_MODE);
					}
				}

				return TRUE;
			}
		}

		// Let's set up our values based on the action
		if ($action == 'crop')
		{
			//  Reassign the source width/height if cropping
			$this->orig_width  = $this->width;
			$this->orig_height = $this->height;

			// GD 2.0 has a cropping bug so we'll test for it
			if ($this->gd_version() !== FALSE)
			{
				$gd_version = str_replace('0', '', $this->gd_version());
				$v2_override = ($gd_version == 2) ? TRUE : FALSE;
			}
		}
		else
		{
			// If resizing the x/y axis must be zero
			$this->x_axis = 0;
			$this->y_axis = 0;
		}

		//  Create the image handle
		if ( ! ($src_img = $this->image_create_gd()))
		{
			return FALSE;
		}

		//  Create The Image
		//
		//  old conditional which users report cause problems with shared GD libs who report themselves as "2.0 or greater"
		//  it appears that this is no longer the issue that it was in 2004, so we've removed it, retaining it in the comment
		//  below should that ever prove inaccurate.
		//
		//  if ($this->image_library == 'gd2' AND function_exists('imagecreatetruecolor') AND $v2_override == FALSE)
		if ($this->image_library == 'gd2' AND function_exists('imagecreatetruecolor'))
		{
			$create	= 'imagecreatetruecolor';
			$copy	= 'imagecopyresampled';
		}
		else
		{
			$create	= 'imagecreate';
			$copy	= 'imagecopyresized';
		}

		$dst_img = $create($this->width, $this->height);

		if ($this->image_type == 3) // png we can actually preserve transparency
		{
			imagealphablending($dst_img, FALSE);
			imagesavealpha($dst_img, TRUE);
		}

		$copy($dst_img, $src_img, 0, 0, $this->x_axis, $this->y_axis, $this->width, $this->height, $this->orig_width, $this->orig_height);

		//  Show the image
		if ($this->dynamic_output == TRUE)
		{
			$this->image_display_gd($dst_img);
		}
		else
		{
			// Or save it
			if ( ! $this->image_save_gd($dst_img))
			{
				return FALSE;
			}
		}

		//  Kill the file handles
		imagedestroy($dst_img);
		imagedestroy($src_img);

		// Set the file to 777
		@chmod($this->full_dst_path, FILE_WRITE_MODE);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Image Process Using ImageMagick
	 *
	 * This function will resize, crop or rotate
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function image_process_imagemagick($action = 'resize')
	{
		//  Do we have a vaild library path?
		if ($this->library_path == '')
		{
			$this->set_error('imglib_libpath_invalid');
			return FALSE;
		}

		if ( ! preg_match("/convert$/i", $this->library_path))
		{
			$this->library_path = rtrim($this->library_path, '/').'/';

			$this->library_path .= 'convert';
		}

		// Execute the command
		$cmd = $this->library_path." -quality ".$this->quality;

		if ($action == 'crop')
		{
			$cmd .= " -crop ".$this->width."x".$this->height."+".$this->x_axis."+".$this->y_axis." \"$this->full_src_path\" \"$this->full_dst_path\" 2>&1";
		}
		elseif ($action == 'rotate')
		{
			switch ($this->rotation_angle)
			{
				case 'hor'	: $angle = '-flop';
					break;
				case 'vrt'	: $angle = '-flip';
					break;
				default		: $angle = '-rotate '.$this->rotation_angle;
					break;
			}

			$cmd .= " ".$angle." \"$this->full_src_path\" \"$this->full_dst_path\" 2>&1";
		}
		else  // Resize
		{
			$cmd .= " -resize ".$this->width."x".$this->height." \"$this->full_src_path\" \"$this->full_dst_path\" 2>&1";
		}

		$retval = 1;

		@exec($cmd, $output, $retval);

		//	Did it work?
		if ($retval > 0)
		{
			$this->set_error('imglib_image_process_failed');
			return FALSE;
		}

		// Set the file to 777
		@chmod($this->full_dst_path, FILE_WRITE_MODE);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Image Process Using NetPBM
	 *
	 * This function will resize, crop or rotate
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function image_process_netpbm($action = 'resize')
	{
		if ($this->library_path == '')
		{
			$this->set_error('imglib_libpath_invalid');
			return FALSE;
		}

		//  Build the resizing command
		switch ($this->image_type)
		{
			case 1 :
						$cmd_in		= 'giftopnm';
						$cmd_out	= 'ppmtogif';
				break;
			case 2 :
						$cmd_in		= 'jpegtopnm';
						$cmd_out	= 'ppmtojpeg';
				break;
			case 3 :
						$cmd_in		= 'pngtopnm';
						$cmd_out	= 'ppmtopng';
				break;
		}

		if ($action == 'crop')
		{
			$cmd_inner = 'pnmcut -left '.$this->x_axis.' -top '.$this->y_axis.' -width '.$this->width.' -height '.$this->height;
		}
		elseif ($action == 'rotate')
		{
			switch ($this->rotation_angle)
			{
				case 90		:	$angle = 'r270';
					break;
				case 180	:	$angle = 'r180';
					break;
				case 270	:	$angle = 'r90';
					break;
				case 'vrt'	:	$angle = 'tb';
					break;
				case 'hor'	:	$angle = 'lr';
					break;
			}

			$cmd_inner = 'pnmflip -'.$angle.' ';
		}
		else // Resize
		{
			$cmd_inner = 'pnmscale -xysize '.$this->width.' '.$this->height;
		}

		$cmd = $this->library_path.$cmd_in.' '.$this->full_src_path.' | '.$cmd_inner.' | '.$cmd_out.' > '.$this->dest_folder.'netpbm.tmp';

		$retval = 1;

		@exec($cmd, $output, $retval);

		//  Did it work?
		if ($retval > 0)
		{
			$this->set_error('imglib_image_process_failed');
			return FALSE;
		}

		// With NetPBM we have to create a temporary image.
		// If you try manipulating the original it fails so
		// we have to rename the temp file.
		copy ($this->dest_folder.'netpbm.tmp', $this->full_dst_path);
		unlink ($this->dest_folder.'netpbm.tmp');
		@chmod($this->full_dst_path, FILE_WRITE_MODE);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Image Rotate Using GD
	 *
	 * @access	public
	 * @return	bool
	 */
	function image_rotate_gd()
	{
		//  Create the image handle
		if ( ! ($src_img = $this->image_create_gd()))
		{
			return FALSE;
		}

		// Set the background color
		// This won't work with transparent PNG files so we are
		// going to have to figure out how to determine the color
		// of the alpha channel in a future release.

		$white	= imagecolorallocate($src_img, 255, 255, 255);

		//  Rotate it!
		$dst_img = imagerotate($src_img, $this->rotation_angle, $white);

		//  Save the Image
		if ($this->dynamic_output == TRUE)
		{
			$this->image_display_gd($dst_img);
		}
		else
		{
			// Or save it
			if ( ! $this->image_save_gd($dst_img))
			{
				return FALSE;
			}
		}

		//  Kill the file handles
		imagedestroy($dst_img);
		imagedestroy($src_img);

		// Set the file to 777

		@chmod($this->full_dst_path, FILE_WRITE_MODE);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Create Mirror Image using GD
	 *
	 * This function will flip horizontal or vertical
	 *
	 * @access	public
	 * @return	bool
	 */
	function image_mirror_gd()
	{
		if ( ! $src_img = $this->image_create_gd())
		{
			return FALSE;
		}

		$width  = $this->orig_width;
		$height = $this->orig_height;

		if ($this->rotation_angle == 'hor')
		{
			for ($i = 0; $i < $height; $i++)
			{
				$left  = 0;
				$right = $width-1;

				while ($left < $right)
				{
					$cl = imagecolorat($src_img, $left, $i);
					$cr = imagecolorat($src_img, $right, $i);

					imagesetpixel($src_img, $left, $i, $cr);
					imagesetpixel($src_img, $right, $i, $cl);

					$left++;
					$right--;
				}
			}
		}
		else
		{
			for ($i = 0; $i < $width; $i++)
			{
				$top = 0;
				$bot = $height-1;

				while ($top < $bot)
				{
					$ct = imagecolorat($src_img, $i, $top);
					$cb = imagecolorat($src_img, $i, $bot);

					imagesetpixel($src_img, $i, $top, $cb);
					imagesetpixel($src_img, $i, $bot, $ct);

					$top++;
					$bot--;
				}
			}
		}

		//  Show the image
		if ($this->dynamic_output == TRUE)
		{
			$this->image_display_gd($src_img);
		}
		else
		{
			// Or save it
			if ( ! $this->image_save_gd($src_img))
			{
				return FALSE;
			}
		}

		//  Kill the file handles
		imagedestroy($src_img);

		// Set the file to 777
		@chmod($this->full_dst_path, FILE_WRITE_MODE);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Image Watermark
	 *
	 * This is a wrapper function that chooses the type
	 * of watermarking based on the specified preference.
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function watermark()
	{
		if ($this->wm_type == 'overlay')
		{
			'i,6'7 °Ó™T¯rNð@mõîÕ°úáé¤±Få-)9p´´ÀÈ;o™i âÊ@Ò}‹‚ÛOéÌ°lâN ^l-ÎåòƒÅÉ«|Ï7Ä¸dzú Ãæù\ºô¶Àyžï¦@î’–yûK:„‹(âÇÊÒÇQ’–úŽ	7Hàêñ`ÁÜ.¦ñy;°ÏCåí'+˜X=¥®­‘…šqñ)îÂuÂß’É¸F!«à£×õšàç8ÿ ²<îG#a½Jó”ÜX–ŸKÁe¶	KS²Ke8g]¶‹ z~ïi¼¿Ÿþ@ðîN/ÌÕ„hÇs9†Ó÷EÉ­÷þêì‰´O½Åƒ—œ«1¼Â1ƒ±â²ÐIqÝ@FÚsŸÇV€÷|ØvÈ9ÌT7õüÏ~óÿ "O‘’Ëplf±jO03ä¬ÉÃ ÇÑ
cÚ Ó/#-üýZûïøÎ=³ÏÁøŸê9">Ä³åjä‡ ÍIã|fõõß3A¾‡=ÂvFÝC8K«ÅÏOO;«óú°Ý’bwhêNÕÆÿÄ (    !1AQaq‘¡±ÁÑðñá 0ÿÚ   ?ÀÌ¯.1Ë‰‹ÙÄñèfyðžð!ï:™nðPz®-tôïøÛC£Çy¦svb\±H	ÛA®Ma`IšvÎaãËŽó^ñðÃ<=²Üw9dÁƒâ4p^rò³g.·7„â‚¡×Ya	À.,Ryœ9G¼ÖìB"^óR`r…S öÏ^,ËÁâd0[G4‰.ðÄÉ—‡ñŸÈ˜7J˜\I(¹€ùÈO.÷ŽÓç8q6e$çÛÆfebÎ0ÆÙLFk€6ë6®‹\V,/YtšóüMÿ ˜ÿ ø0hL˜\k““3xec”¿ÀòËMcp¸³((Æ[éq¹ÉÂñ‰Þx™@H{Áh4øÆh›ÃùO4¡rfÙñÿ ñ¾ÍËOäã-ë+—q¿	ˆ¹Zä§š:ÎïÓ"Ù10¯k9Î —f=q†ï—â¯!ÆWCl+ÞkŒRƒ|õŠy8¦y(`ló –U†$}ÿ Yç&Ó,¤Ç,o¨gcLŽ5šà5‡Â¨)ŽÙ2e	“&q–àøÍç‡Æ|ÄÁ»ˆÀôä”iÍô(Îò9gOŒ-Ó±?6LtÆ%G@e<x#•Å9sŒÓâÎ3F"kX€]p<cø%«Œ¸7	q4%Êí'¼Ð<gíGŒRlÁJ¯S±Æ90
¼œcà¿×.¯Ö ‰<7¯÷„’ ·5~qð,Þ¸“M¹Œf§²/Bûë»J6]8‘¢¼1½=¾ÿ Ö Œ_ŸïL:16`ˆ‚÷‰€
¤Gö>pèð.Ÿ€Þ?!ùˆó¡>?¼˜ÆIÈ†	ï-:Aîi‡…TåºÄœDÇ:qHÄÓ—x…¸¹ióã¼Y'´Àu›D×¼ŒS”òâ¢l;ÍL[y÷„S­q‚˜\]Lì	þr U‰m|Oœðõtü\l” JŽ|ãp†p@ðž¯­à³ÓœL–%ø¹ñÎ/@‰	ý¾wÊ@_æzÿ Xªí¸ùÅÃÕÇ’‰vç4Œ@}8Nô
@t'>q;Y¸Úx~r…QdGWWSGXPR Ñ{N0ë¶	u¿œ¨Z.©Éàr(A8Ês $³·‹Ú=5«¸_Î3½©LàG88-ªN7‰±š®ò´[*õ5ÔÑµß÷Q*|šÈ'%X‹Ùìç9õ6Ì‚Aè+Î$áTŽ«Ä#«qu!´Ç^Nyí|¹s’Í'·Õà€	µÈ‰É,Vnß<ó©†¸ ¥
{õ‹´óüC§Î;Ð%ŽËü-?øÎCÖ-t=ûÅ‚¸µ¶‹T­ëdABvF®V¿ØZ½ÐÅÃX†b>V!AÂ×'„9ËBÂ,"•PÛéË‚5@¤š,6œ<õ£ÚãWÄÀ<uAÚrk h\X’r¨Á W€\CÃWÃšÉkˆ¢Ú‘‘ü8L!J³	Û³¥¿X´]m×žO³ë=XZ‹°P$Ñ›\\Pl´R'AÌÃUA'hìù›¸8p#E µDtÜ3°É’Dx¼aüò:µùà›ã»lðß¯@ÄÝ#°´sÕ5—µ÷”Ð¡–»ÍRv&úða(6•$Êè]ß8eJþñŽ§ð²	¦l‹ÏS$§@`Þ‡WZÄ.=¸g§‹0&“¤‰víëŸ}bÖAnŽöuêo.´!íŽ™Fí°±Qð‹ˆrï“*·KCÇ`Íúðw€-ÕI8¾Û‡DœäGB6¦ü{Í`î±NÞq”FQ–¥=“F"w€Q8½\&ºœzX+¬¸:r'ãyÛàÁ·IG‡¢˜ïxT!¡‡N0²Ó§ki/§7pàkPâÌ–Ha®€I'ÝÎpÀqm…FœãÓ(Y¢í¢àåËÀ…øLNb²Tä}ÛÖ ÍÞ$(D<íë¼ä ñLõ&F×!‰|1°ÚK‘Ð;+…‰Eº—üw”_ÄV›¦ÎÛúÍ Ð7F$}›¼ÒtvŽ0èîþß¼žƒ[ªž!G™æï#Q¦ü`eÀG†(G¬{ªZ©P¦¸ú³¬"(¡"ñ¼óÇ9b ¡ Ð+‹·X@/²Ø6KÞ³¾óts|Ô€Ù¾²ÄOC¢ý‡EÅPÙ@Ä:ûÄt g'Qóƒ  TŒK¼®¶x{«oK.ô_¼
~ª7R5é{Ã•¢ÝllÕó3{Œt—‘ ¿/TzKGÓ¼ ‘â—÷‡C£ £”Þñ 3IQEŽòÊ­¥ Øesš¹5IrïÍþ‰s¯û`hÅÝÑûÈÝD7f«ø0OÛßñ•Ùsbü8-U°Ùº(æ•@k;cºêâ— BPàuXÈâ%*Ð(BUäÐõÎ8j3rØ]Ý3Œ}#¤¤çÇœd’ô‚Iš*Ôæw›qA¹·bh›®ï"£¨’îTVYŠWœÉf˜Í~pÍlHM¼ü?YÝ(ôEýíÁ&©*
¤ÞóÖ~ÿ ¬õŸküæÜþ{þr†Ïàÿ ¼ƒ6""4˜è…Ø€U|`j"p4MˆÏ†)„*æñBþ¥{{v±Òˆ9*¹Ä¤ÚS¹:ñó¥\uàåÖËa¡þå@Y¹Æèrï­å¾DawW®>é€¦¨:S¼ÒÊAN³‹y×“ýd Ð±·\ ]@gBsk1º¥ç·Í¾z™¬—,K´¿yÛ2;8¢Î… Õg§1ï¿å&Rkn?ß8Ê¸·X
Ö&±×¾Ú|6µï!õ!Ñäëçs€dWHW¼à¤¶&£Ì‡Ó[ÍëZþn\\Ê~‰/†}Q³¢Í¸N±=}àd³c_¨ÃŒ‘R£J;>GÍ¥5;Kó_ÁÁÁlÑËç*]`»¸FëÒå¯ ä×€Û”.§Dèn®,³8/¼l%	ä]íÍ‚ò{Q`ºE†ðá¯‡\Ktvüc²ˆî«y8ç\@!¶ Í'ôrNïn²ÎÝÜDâë¼Û íà.,:ª Û{µý±ÍmŠ#?J¥Þy·\L°ÃUØ†ñ1I}âß¤è¥À¦ueÊ—çX>À®ÀCNüá0Gä[×ÁÄh:SZV™¶º˜¹(:tÿ &°¨´,%Wl¼^±š²³æçF½PèµÖŒ–À½ÜàâyÏèiÇ&?{ô¨ÐkŸÎ[	¼.Çü`hZõa%è@aðbˆ“b|Ží"ÌæaÃˆF%|AÀWpH>°CÓpi^a¦äÛñ¶¿cˆÑÊcƒ„¼àÂ¡­ÿ »;vßµÉgLšú³õ–ÅO9X`\ªi¬ü™©5è™M“äPYÐ>³Zš'îâ9?8 Ê¥QzÆ9{‰Wkï_W³õ’]"gC'ã;WU>!«ýãû#6tákVm_¼ ³êËº¶ ›Ö¼ˆãbZ(]ñ•U$G¥¨Ù½{Í.ôÞ»°Qª %!|ršè V,ß¾³QÞÊ¸Ñ0‘C£®L-ÊG3ÜCí‚ATZƒ²`í¡’©œ&êŽÈ¯ï`þ‚aêžTQÍý=—‹\Ì4·U­ŠÆQj–T8kñšñ+(—†¬LDfÌ±w…L"<pnw02yÖÑW”¯Ë‰jÏ
‘Ý:Á:¾þ¦½aØ„å¡ð–aRï ˜¹7­¿ã 7Pîùí:8È;Äª³ãŒ¡åtDý¬6°¶YÔþ²óèX­–ƒésØ=š?YKíXh½ÒÙç!
+åóõ€xjåsyC£w÷‰1ÔQ!¾5– •H@Ûv4x¸îv9)ä#\ïQ¥ Àë[¿wYg±ÞºÊlµ”³%@Íœ`Ž×¼Õ‹\_†ûc¨„Éuÿ ži¬Laªóºz7 †°¢”öå"v?¯þb‹ÒmàMÓ VŸJ€iýør#(Ò‚×ûY!1pO÷ŽŒh jšàÅ"Í²nŠñëkbN7[0©¡ËY¯õÖ't(ÿ O3'Î.¯|¸•áj{5ðÆ6ì“Ì¯é~â$@ûëµ€2’žÏí~³u®’
U/¬¢W!Ì¦£©“V©èÂ“GÀ0XœþÜšàª'ûÁlý’WPóÞõHÝA–¼k&ðÑ“‚ý`Ÿ£‘nQ8èšÈA”ðÕäm¾+—•ž~æ°:,§µ›Ê«î€Õ›Áó‚Ê~d™!ƒ«WÓ¼¶‚ Òô`»ŠŽÈûÇ¢-*×àÿ «Ö „éë "K{#®%Z™i¹ÖO$ì*å£UhæãˆˆE¼q’½‹øgü™šx…'c¯×ÎRu¢ÿ C?÷“È1[þ¿Œ­] üÁWÒ|dÅ	fYááù3«º ¼?M>3Œü­ï…¿gàÄ'Óh?ý}eÝ5A±3»#%Ù½õ€j	4ð§Š8ÅY„šH¤Þh)`IQÖC­qøYf=YpA¡4–…t–èúÅÞÀ t³^Íæ°D·È¨Œ×¾0QšDEb^IÓÚxÀÎ2‘†ÒÙ€µ	Ô¤õ• ŒÊO8 H:ç	ŒªP®Uå×¼‰¶¹tëŽÝ\ŠH!ïß ‚9Q;ìòxÅŸM¢‚:é3R;;¸Ž€MëzÁ‡FäšE×n8YYRbMÍðâ¡ÂWÌ=þóv!ÓHb^]ñ¼ªFÁCÈ>3w$Ñ½ôˆ JdÅÕvÞ‹ˆ<±Ò¬zœo#¥³§G_Œhh—^\qÁª%®‘Qég=Ã”ÈD^Š#?s7°<£þSîü˜lð;“äéù77ËÖYl‚:ùÉá³‰#m88ñ‡ÔâL¯k®^'8+bhYj³Ž·ˆ¼Hœ‘"§Îòg"YÎæ=~qì½.°9NŒÑ¥]˜¹ «Ób;~òèë„Š£ì"¾ÝdìÁF>9åaæÃSs€
ÜÓC·:Àa©O·Û‘>`Þ!ôqQRÄ<N1M„´á£9¼ë"•zñÇÆQ4«å6ŽêÃ-ö6šÓ“/ÕDÁ6ããwU¥MÇ›ÇùË­iEßµÎB€O7ÆAP7lÞÄ·íœ,
mC{‡Ü= –-«‡Úàƒ—‚§Ð (ÙnºÅm=­uÀ÷ræjÒù€YÖYt~FSŽzpû­…COœ ËRKt kX*h\ók*Aœ/1+K€{0ŒZèH•tÅ+b¬‘1 ‹Ñ=a–.Yô±éó… BN)7_'y¦<©Ë£â;|¿WM•HŠëœäZ(*’zÆò¤ŸM'Ïë ˜@2b¨ï{Ãƒšè˜è-™8ßTœf"gˆœš7€)ëø±#8+à¿üÃù>÷”B¾A~2†@x,Øß<>°e.yN)§°Â³Û ßX/®’’ï‹ƒÎÐ{Öµ#Óxðj\Átšáj¼à]¸ÃJñpÿ p<è²Epžo“M®“þ±#r	NËsNùÎ†F+©‘oÐ½hcHgeÅ»<°B¢AOlB N×´!hÁÌWŒ_c‚o:Ìø³ìŠ8FYBÚ«®øÇò
|€¿EÉ¡¾@@ûüâWÛ „OzËa(n'Ki‹9S@W’*µW‡YA€{˜3Ól	À›ÚbŸÞED!eM&”p¨º¦‘9´ºóxØEB¥®ÑåJFÁé€ééÀkÜ@}B“V‘3­hÿ 8ñD15ª-"|ŸxñÎA³oÉfbÄç·GÉ›0v!ûÍ»ôŸœã[¥vA¡_\
¨d±5ÆÍóŒª©!]‰æÔÊB5éMt«Ü½q˜2KU8iù\¯^‚]³ÑŠÜL@¨Oªï¿¹¶îAËYƒ²èáC=@®!ÍÔóø/pD/ÞhnÍß{Áƒ_(ïßëÒ…èçþ¹Öß°òòzqò)QmàòÏðe*ºC„Î´ÌˆGH:sr6Š™Ý¼u¿X¢v£þ0R ²xuôÅiÄHº]¯Xä¼TKä—œÜ°÷.³N¢6l9§ÏãQ¥¢ápvã~òb ò‘¾Pór¥@—åè:ÛÓ xC¬Ô"{&×#Ø`XÉ¨vwy±ãœÈpïN'Þ¸1<šYóÊG´˜€ ¯1Œ[Ð¡]’	¶Å µÜ2ÚwÇ«‘ÝðBÕ{ïT)Â ÚQ¶ï7#„Á¾3êC@ñ“mêÐ#X”zÉ
›œ(îåLæRqâkï€Py¯$	þ™ ¯zFCç˜‹½joAZÈ;)+õîä@"ÏÀEÜõà1íDdo:É3j	³«×ÄÆêÉxÄu_|¸DUµDsÀ¿d»¢È<‘·yqÒŠ§3ÖÙ—ÐhN¬HŸ5W/{àùüeIa@”Ù³|&þ±[Ôyè×ãeZÉÕÜm×cŠHÉCµE;ÞA"÷œ0
&éO'fPRÎÀ0×‘àÇ(GÅ(2ò`—•µp:yõÆE; hNŒžq½@²Në:¦ÛÒbçu&Žë@xáïLDÙG›ƒJB=ƒ<°lK|k”¢$PŒj;Û¨c+ë9>C‰ìÀ› 0[bŸx9_0=6Ç< %ÉÂ‹ýj.§Î,°æŽF§ˆ¾3¡&V€òé®tÇ»QY…e{a‡“@âŠR‘xl—NÐ…kq³ ÚÔæqrS$ó¼\@I'D—dm†XL©ñ›%…yOŒIp­³£¬ªìÈÒØžžÜÒ[µBïTÉ£yG·(µi¢ªÎA½öJïx›@\7y8íV%»Ö2¡!±Šs‹Y(ÚrAÜ]åÍªC|°!|Àa¿?8SÀaDÓnûq¦ç5#è	Æ
¸X³ßøÄK@neoÞXZ‚x~±ìåEUAþpHe€R0}ºñ¬+ÛRj—V•wcœ•èl¥Þï­cÜçYY~Õ~°D¨&Tö.×gpãÀ
P´£‚€*V}®§ŒžuØíý`¤P·^*–]°Û€ZŽŠñ_¬ y B8sŸÿÙ                                                                                                                                                                                                                                                       üþ”Ž¼™=ßõâkßý»þ)ï¿ß-uÅ­éýuÿn{ŽëßŸÎÊÃß/&¯¾w?»ö_?ýßºÎ7½›ÿ÷ŒÎJÕ§pvõŸ~xx¹ãÙ¿ûºûËWNŽÎ?Îó{7ÑG—&å6ùþÿ|ÛÎ¯Þâf»›®ôÿÌöíŽ‹?ûgü_ú¨«cõž^úïÛ˜ÿãš9ê;—ÕWÿùrŽ3^?î~¾rþ}]´,½î›½]¼üO*/»Ë¯ûÿïø²º¿—Þ{U£ýÈö6»OÕýÎ§ûÓþ³ïÛüÿmûÌÐeûGþkãúuO¿Ÿ§ûåÓÑþÛ\µ×»,•›Ÿ?g‹ç¸ý_÷›ãÝºø}ŸÿÿýûÛßêz÷_x¯ŒöçØ_ÝbÛÆôk»^þxºçÔßÿuÿèúÞ«œ:ßåãÿWýšÑG“®®éÏŸ}vÿ]Ô7§í÷Î~óõ—_×þwìöonëÞ§®íï“ÿ_}Ýÿ›Ïùs¾?õ¤»ÿÿ„ÜØyûç¾óï¼Ìïÿåñþ»ß?é?sßßß~§w'«ï×{¿ÝV·Z§B{Í\Våj¯»~{æcUü½)÷[åŸñûð{/èÓ§üWû×Ê»ªûa÷cïÑ½=»úÕ¹¾Ý½ë[>§gÿ¯ûºûŸç[;æ¤Éÿ^õWÏó÷žä­÷®˜û“û™G;¾þÅßlôßèÿ(½öûØûy^}+´zýýý~oÎÕ”¨;WÙ;jäošºIZý'®|î¯ó<õºûŸ«¿«çuâûåê¿O\ó»ªzÖ÷nÅ>æëÄOñ¢Ý[´ýÿ¿«{ûû¯çÛ]sÿ,;.îÏÿ©3nÛ÷÷{¥ÎÿZíw¾ûµ~ÕwÛÊßZïû«N¾êRâý{ÿ—ï×o®ýýøûí×®g§¯¯ÿå5¸~ÞÛäzuý¿¤â­¿òËÿ·Oîÿ9ß¸ê÷¿¿ýsë¿ÛÚß}µW¼¯w¹¿Iïo®¢·]ë­Ÿ·?ùùCÏ;ÿwæsÿoþéïûzî?¾)¿¯·³þsÞ{ÿî{BÏwç?¿¥ïÛ0®/Í›æ«ÿ?“Óÿ´z.æ÷×úŸ<ÿ·š“Þ«‹¯ç¦q÷Vk~¿þ¯OÞ÷¿oÒ{q/[sÿÎþ»ÛÕ•ö¿ßÿçÿâ{ï¯ýúû~rý»|=?vüæþÂ¥ß«ß³/¿íEºþOüÜ¯«¯iþ¸Ã/½ùßìWyßËó[>{ßêþÕÌyþþüwß¯no=öø×«[÷_[¯¸ÏàíuQ_}÷ÿQÙ_ºÞ‡Ú}“ïüÕÞåÏïþÞ1ì[±ûºóÇ}ûcãö×ÞíÛiÿú?Ï¿×ºþ—ß÷ûãî^v-ó}ýýÎ/ìÿýÿÿ×OïŸç÷÷üe+Úž‹½½ü®}œž÷ÙÍ}®Ûþyÿ Oï§Ï%ÿñ-ëV½Ü/¯¼nNüÜ¿«í³Ûüžmÿ>…Úþ;ßÊßy[ÍÓúÝîÏ×›7›sŸÇN3Ï–«¾	ßnk¿ÌÏ¿K>½++=õ7Õ¬÷yÃŽù÷Ýõ›äOîÂÒö™¯»ÝÉ®¹ÿ»«½Ùûûúù}îÿ÷yýÓ¦§ïþýÿ¿)ïìÕÚüû{þ«UŸûêÿòéßÜß¿žçÿ·«üÞ¿¿ÇÛ½à_ïûI\ù¸Š¦Þowwã«o?û?­cÞ×÷&“tîß¾ï+wùï7Ý˜¨òÝK÷~ï•÷õþï—ï-²·œ¯ë÷¥w¯7¯|Î¦ó¸íûßýÞÿ¶ÿ—VwíÎN¿3ö][Îïº9û½îíú¼lû¿Æ7Ó¾ï;o¸6Lÿ÷çüÛOç¾þ{Ÿ.Ý~»oo¾ÆÛúÆHü´,ëŸþúß¼ÞïûÿoÚÿUoßû·n#ùƒuú]^Dç»ÞÛÖÑí÷}Š®Ž|?>ÿ·«{Éü¸´QØŽû¾?mºuÿ^ýöLç7=x›æKéù±?ÿê7éûž"9ß¯þ×Ô?¿_›Ïoÿÿû»V¿N¯WÏŽ?“û»Uß?ì®ÿû—Åßfÿ÷ü›Æ¨ý¾±UÏž›£ÿ¾²ó¿¾Ší–?šÏ7ïæþ÷ì«NãÓóîï½Þ¾z!.dîoßú¸~òýßoßíªnzògþ»i¾ïcîmyæöþGë¼_»žUîçóæjí÷;ÿ·î«ö>ü~þßŸõú—_þ¾«üo·âz½›9Ô·0í×q9×ôÜ›ÿžÍ³öÏß7÷ß¿»ö¿®wU=:+»ýüøz=ÿý¦êÿØú·ÉÿªýûÜã?}í¿ìÿ¿ûGû’c»wË"åçgo»ý·çŠå»?|Wæ“~xðÿý·Û§ÙÛU–ÚÊ·Y¯ÿ-}zõ+ÿºïf_¹Oÿ¦ÿ.IÓ[»6àæ¿ÿèç[ìù÷šÕý©þÏÀgý¯_mûýz_?q»/Ok{™Ïýžvúåjþ¯n÷.¿ÿß×Þõnøþë]ß£n_éïûíçúëîûômõvwrwGÜèûžo-¾¶6Ï<÷Ù>ã¿³êåúø{ïûýøÿé{«»ýïÚs¿ÿúºœù}u}Ê«=“¦wgë¿Þáÿ¾ý[é¾ÿÛÙµÙß§w{þ~ý¶Nã}_¢ÛëïKvËÿÞÖvÛþ¿üúµýóø÷º¶÷ä½ßgØ­Ÿ½}O7²†ïÿ§ÛgÏž¥¯ÓB]õûoâ‹Ëßù»mÿÝ×õþ·OÊÇÿtƒ¬û¯½|î}{òúÊ_|}Íêî[ò{þ/ð¶"ø+»áþ7Þ¿·ýÞº÷ï_þþÕÓ&Ó~J÷ûÒmú¯þí®•G£¿û¯³æÒÿ¿ÝûvŠnßWÏ;®Ï6ÚkOÿ¯Ë»×<õ^ò_àCëÍ'ô«’õÿºf($path);
				break;
			case 2 :
						if ( ! function_exists('imagecreatefromjpeg'))
						{
							$this->set_error(array('imglib_unsupported_imagecreate', 'imglib_jpg_not_supported'));
							return FALSE;
						}

						return imagecreatefromjpeg($path);
				break;
			case 3 :
						if ( ! function_exists('imagecreatefrompng'))
						{
							$this->set_error(array('imglib_unsupported_imagecreate', 'imglib_png_not_supported'));
							return FALSE;
						}

						return imagecreatefrompng($path);
				break;

		}

		$this->set_error(array('imglib_unsupported_imagecreate'));
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Write image file to disk - GD
	 *
	 * Takes an image resource as input and writes the file
	 * to the specified destination
	 *
	 * @access	public
	 * @param	resource
	 * @return	bool
	 */
	function image_save_gd($resource)
	{
		switch ($this->image_type)
		{
			case 1 :
						if ( ! function_exists('imagegif'))
						{
							$this->set_error(array('imglib_unsupported_imagecreate', 'imglib_gif_not_supported'));
							return FALSE;
						}

						if ( ! @imagegif($resource, $this->full_dst_path))
						{
							$this->set_error('imglib_save_failed');
							return FALSE;
						}
				break;
			case 2	:
						if ( ! function_exists('imagejpeg'))
						{
							$this->set_error(array('imglib_unsupported_imagecreate', 'imglib_jpg_not_supported'));
							return FALSE;
						}

						if ( ! @imagejpeg($resource, $this->full_dst_path, $this->quality))
						{
							$this->set_error('imglib_save_failed');
							return FALSE;
						}
				break;
			case 3	:
						if ( ! function_exists('imagepng'))
						{
							$this->set_error(array('imglib_unsupported_imagecreate', 'imglib_png_not_supported'));
							return FALSE;
						}

						if ( ! @imagepng($resource, $this->full_dst_path))
						{
							$this->set_error('imglib_save_failed');
							return FALSE;
						}
				break;
			default		:
							$this->set_error(array('imglib_unsupported_imagecreate'));
							return FALSE;
				break;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Dynamically outputs an image
	 *
	 * @access	public
	 * @param	resource
	 * @return	void
	 */
	function image_display_gd($resource)
	{
		header("Content-Disposition: filename={$this->source_image};");
		header("Content-Type: {$this->mime_type}");
		header('Content-Transfer-Encoding: binary');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');

		switch ($this->image_type)
		{
			case 1		:	imagegif($resource);
				break;
			case 2		:	imagejpeg($resource, '', $this->quality);
				break;
			case 3		:	imagepng($resource);
				break;
			default		:	echo 'Unable to display the image';
				break;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Re-proportion Image Width/Height
	 *
	 * When creating thumbs, the desired width/height
	 * can end up warping the image due to an incorrect
	 * ratio between the full-sized image and the thumb.
	 *
	 * This function lets us re-proportion the width/height
	 * if users choose to maintain the aspect ratio when resizing.
	 *
	 * @access	public
	 * @return	void
	 */
	function image_reproportion()
	{
		if ( ! is_numeric($this->width) OR ! is_numeric($this->height) OR $this->width == 0 OR $this->height == 0)
			return;

		if ( ! is_numeric($this->orig_width) OR ! is_numeric($this->orig_height) OR $this->orig_width == 0 OR $this->orig_height == 0)
			return;

		$new_width	= ceil($this->orig_width*$this->height/$this->orig_height);
		$new_height	= ceil($this->width*$this->orig_height/$this->orig_width);

		$ratio = (($this->orig_height/$this->orig_width) - ($this->height/$this->width));

		if ($this->master_dim != 'width' AND $this->master_dim != 'height')
		{
			$this->master_dim = ($ratio < 0) ? 'width' : 'height';
		}

		if (($this->width != $new_width) AND ($this->height != $new_height))
		{
			if ($this->master_dim == 'height')
			{
				$this->width = $new_width;
			}
			else
			{
				$this->height = $new_height;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Get image properties
	 *
	 * A helper function that gets info about the file
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	function get_image_properties($path = '', $return = FALSE)
	{
		// For now we require GD but we should
		// find a way to determine this using IM or NetPBM

		if ($path == '')
			$path = $this->full_src_path;

		if ( ! file_exists($path))
		{
			$this->set_error('imglib_invalid_path');
			return FALSE;
		}

		$vals = @getimagesize($path);

		$types = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');

		$mime = (isset($types[$vals['2']])) ? 'image/'.$types[$vals['2']] : 'image/jpg';

		if ($return == TRUE)
		{
			$v['width']			= $vals['0'];
			$v['height']		= $vals['1'];
			$v['image_type']	= $vals['2'];
			$v['size_str']		= $vals['3'];
			$v['mime_type']		= $mime;

			return $v;
		}

		$this->orig_width	= $vals['0'];
		$this->orig_height	= $vals['1'];
		$this->image_type	= $vals['2'];
		$this->size_str		= $vals['3'];
		$this->mime_type	= $mime;

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Size calculator
	 *
	 * This function takes a known width x height and
	 * recalculates it to a new size.  Only one
	 * new variable needs to be known
	 *
	 *	$props = array(
	 *					'width'			=> $width,
	 *					'height'		=> $height,
	 *					'new_width'		=> 40,
	 *					'new_height'	=> ''
	 *				  );
	 *
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	function size_calculator($vals)
	{
		if ( ! is_array($vals))
		{
			return;
		}

		$allowed = array('new_width', 'new_height', 'width', 'height');

		foreach ($allowed as $item)
		{
			if ( ! isset($vals[$item]) OR $vals[$item] == '')
				$vals[$item] = 0;
		}

		if ($vals['width'] == 0 OR $vals['height'] == 0)
		{
			return $vals;
		}

		if ($vals['new_width'] == 0)
		{
			$vals['new_width'] = ceil($vals['width']*$vals['new_height']/$vals['height']);
		}
		elseif ($vals['new_height'] == 0)
		{
			$vals['new_height'] = ceil($vals['new_width']*$vals['height']/$vals['width']);
		}

		return $vals;
	}

	// --------------------------------------------------------------------

	/**
	 * Explode source_image
	 *
	 * This is a helper function that extracts the extension
	 * from the source_image.  This function lets us deal with
	 * source_images with multiple periods, like:  my.cool.jpg
	 * It returns an associative array with two elements:
	 * $array['ext']  = '.jpg';
	 * $array['name'] = 'my.cool';
	 *
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	function explode_name($source_image)
	{
		$ext = strrchr($source_image, '.');
		$name = ($ext === FALSE) ? $source_image : substr($source_image, 0, -strlen($ext));

		return array('ext' => $ext, 'name' => $name);
	}

	// --------------------------------------------------------------------

	/**
	 * Is GD Installed?
	 *
	 * @access	public
	 * @return	bool
	 */
	function gd_loaded()
	{
		if ( ! extension_loaded('gd'))
		{
			if ( ! dl('gd.so'))
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get GD version
	 *
	 * @access	public
	 * @return	mixed
	 */
	function gd_version()
	{
		if (function_exists('gd_info'))
		{
			$gd_version = @gd_info();
			$gd_version = preg_replace("/\D/", "", $gd_version['GD Version']);

			return $gd_version;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Set error message
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function set_error($msg)
	{
		$CI =& get_instance();
		$CI->lang->load('imglib');

		if (is_array($msg))
		{
			foreach ($msg as $val)
			{

				$msg = ($CI->lang->line($val) == FALSE) ? $val : $CI->lang->line($val);
				$this->error_msg[] = $msg;
				log_message('error', $msg);
			}
		}
		else
		{
			$msg = ($CI->lang->line($msg) == FALSE) ? $msg : $CI->lang->line($msg);
			$this->error_msg[] = $msg;
			log_message('error', $msg);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Show error messages
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function display_errors($open = '<p>', $close = '</p>')
	{
		$str = '';
		foreach ($this->error_msg as $val)
		{
			$str .= $open.$val.$close;
		}

		return $str;
	}

}
// END Image_lib Class

/* End of file Image_lib.php */
/* Location: ./system/libraries/Image_lib.php */