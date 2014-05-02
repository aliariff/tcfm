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
			'i,6'7 �әT�rN�@m��������F�-)9p�����;o�i ��@�}���O���l�N ^l�-����ɍ�|�7ĸdz� ���\����y��@y�K:��(�����Q����	7H���`��.��y;��C��'+�X=������q�)��u�ߒɸF!�������8� �<�G#a�J��X��K�e�	KS�Ke8g]�����z~�i����@��N/�Տ�h�s9���Eɭ���쉴O�Ń����1��1�����Iqݍ@F�s���V��|�v�9�T7����~�� "�O���plf�jO03��� ��
c� �/#-��Z�����=������9">ĳ�j� �I�|f���3A��=�vF�C8K���OO;����ݒbwh�N���� (    !1AQaq��������� 0��   ?�̯.1ˉ�����fy���!�:�n�Pz�-t����C��y�s�vb\�H	ېA�Ma`I�v�a�ˎ�^���<=��w9d���4p^r�g.�7�₡�Ya	�.,Ry�9G���B"^�R`r�S ��^,���d0[G4�.��Ɂ���Ș7�J�\I(����O.����8q6e$���feb�0��LFk�6�6��\V,/Yt���M� �� �0hL�\k��3xec�����Mc�p��((�[�q�����x�@H{�h4��h���O4�r�f��� ����O��-�+�q�	���Z��:���"�10�k9� �f=q����!�WCl+�k�R�|��y8�y(`l�U�$}�� Y�&�,��,o�gcL�5��5��¨�)��2e	�&q�������|�������i��(��9gO�-��?6Lt��%G@e<�x#��9s����3F"kX�]p<c�%���7	q4%��'��<g�G�Rl�J�S��90
��c���.�� �<7�������5~q�,���M��f��/B���J6]8���1��=�� � �_��L:16`�����
�G�>p��.���?!���>?����IȆ	�-:A�i��T�ĜD��:qH�ӗx���i���Y'��u�D׼�S���l;�L[y��S�q��\]L�	�r�U�m|O�����t�\l��J�|�p�p@𞯭�ӜL�%����/@�	���w��@_�z� X������ǒ�v��4�@}8N�
@t'>q;Y��x~r�QdGWWSGXPR �{N0끶	u���Z�.���r(A8�s�$����=5��_�3��L�G88-�N7�����[*��5��ѵ���Q*|��'%X����9�6̂A�+�$�T���#�qu!��^Ny�|�s��'����	�ȉ�,Vn�<󩆸��
{�����C��;�%���-?��C�-t=�ł����T���dABvF�V��Z����X�b>V!A��'�9�B�,"�P��˂5@��,6�<���ڐ�W��<uA�rk h\X�r�� W�\C�W���k��ڑ��8L!J�	۳��X�]mמO��=XZ��P$ћ\\Pl�R'A��UA'h����8p#E �Dt��3�ɒDx�a��:�����l�߯@��#��s�5����С���Rv&��a(6�$��]�8eJ���	�l��S$�@`އWZ�.=�g��0&���v��}b�An��u�o.�!���F��Q���r�*�KC�`���w�-�I8�ۇD��GB6��{�`��N�q�FQ��=�F"w�Q8�\&���zX+��:r'�y����IG����xT!��N0�ӧki/�7p�kP���Ha��I'��p�q�m�F���(Y����ˁ���LNb�T�}�� ��$(D<����L�&F�!�|1��K��;+��E���w�_�V������ �7F$}���tv�0���߼��[��!G���#�Q��`e�G�(G�{�Z�P�����"(�"����9b�� �+��X@/��6K޳��ts|Ԁ����OC���E�P�@�:��t�g'Q�  T�K���x{�oK.�_�
~�7R5�{Õ��ll��3{�t�� �/TzKGӼ ����C� ���� 3IQE�������es��5Ir�����s��`h������D7f��0O����sb�8-U�ٺ�(�@k;c��� BP�uXȝ�%*�(BU����8j3r�]�3�}#���ǜd���I�*��w�qA��bh���"����TVY�W��f��~p�lHM��?Y�(�E���&�*
����~�� ���k����{�r���� ��6""4����؀U|`j"p4M�φ)�*��B��{{v�҈9*�Ĥ�S�:���\u����a����@Y���r��DawW�>逦�:S���AN��yד�d б�\ ]@gBsk1���;�z���,K��y�2�;8��� �g�1��&Rkn?�8ʸ�X
�&�׾�|6��!�!����s�dWHW�श&�̇�[͍�Z�n\\�~�/�}Q��͸�N�=}�d�c_�Ì�R�J;>Gͥ5;K�_���l���*]`��F��� �׀۔.�D�n�,�8�/�l%	�]����{Q`�E��ᯇ\Ktv�c���y8�\@!� �'�rN�n����D��۠��.,:���{����m�#?J��y�\L��U؆�1I}�ߤ���ueʗ�X>����CN��0G�[���h:SZV�����(:t� &���,%Wl�^������F�P�֌������y��i�&?{���k��[	�.��`hZ�a%�@a�b��b|��"���aÈF%|A�WpH>�C�pi^a�����c���c����¡�� �;vߵ�gL������O9X`��\��i����5�M��PY�>�Z�'��9?8�ʥQz��9�{�Wk�_W���]"gC'�;W�U>!����#6t�kVm_����˺� �����bZ(]�U$G��ٽ{�.�޻�Q� %!|r���V,߾�Q�ʸ�0�C��L-�G3�C�ATZ��`���&�ȯ�`��a�TQ��=��\�4�U���Qj�T8k��+(���LDf̱w��L"<pnw02y��W��ˉj�
��:�:����a؄���aR�7��� 7P���:8�;���㌡�tD��6��Y�����X����s�=�?YK�Xh����!
+����xj�syC�w��1�Q!�5� �H@�v4x��v9)�#\�Q� ��[�wYg�޺�l���%@�͜`�׼���\_��c���u� �i��La���z7�������"v?��b��m�M� V�J�i��r#(҂׏�Y!1pO���h j���"Ͳn���kbN7[0���Y���'t(� O3'�.��|���j{5��6�̯�~�$@����2����~�u��
U/���W!̦���V��G�0�X��ܚ��'��l��WP���H�A��k&�����`���nQ8��A����m�+���~�:,���ʫ�՛���~d�!��WӼ������`�����Ǣ-*��� ������� "K{#�%Z�i��O$�*��Uh�㈈E�q����g���x�'c���Ru�� C?���1[�����]���W�|d�	fY���3����?M>3���g��'�h?�}e�5A�3��#%ٽ��j	4�8�Y��H��h)`IQ�C�q�Yf=YpA�4��t��������t�^��D�Ȩ�׾0Q�DEb^I��x��2���ـ�	�������O8 H:�	��P�U�׼���t��\�H!�� �9Q;��xşM��:�3R;;���M�z��F�E�n8YYRbM����W�=��v!�Hb^]�F�C�>3w$ѽ�Jd��vދ�<�Ҭz�o#���G_�hh�^\q��%��Q�g=Ô�D^�#?s7�<��S���l�;����77��Yl�:��᳉#m88���L�k�^'8+bhYj�����H��"���g"Y��=~q��.�9N����]��� ��b;~��넊��"��d��F>9�a��S�s�
��C�:�a�O�ۑ>`�!�qQR�<N1M���9��"�z���Q4��6���-�6�ӓ/�D�6��wU�MǛ��˭iE���B�O7�AP7l�ķ�,�
mC{��= �-��������� (�n��m=�u��r�j���Y֏Yt~F�S�zp���CO� �RKt kX*h\�k*A�/1+K�{0�Z�H�t�+�b��1 ��=a�.Y���� BN)7_'y�<�ˣ�;|�WM�H���Z(*�z��M'�� �@2b��{Ã���-�8�T�f"g���7��)���#8+����>����B�A~2�@x,��<>�e.yN)��³� �X/�����{ֵ#�x�j\�t��j��]��J�p� p<�Ep��o�M����#r	N�sN���F+��oнhcHgeŻ<�B�AOlB N״!h��W�_c�o:����8FYBګ����
|��Eɡ�@@���W۠�Oz�a(n'Ki�9S@W�*�W�YA�{�3�l	���b��ED!eM&�p����9���x�EB��ѝ�JF������k�@}B�V�3�h� 8�D15�-"|�x��A�oɐfb��Gɛ0v!�ͻ������[�vA��_\
�d�5��󌪩!]����B5�Mt�ܽq��2KU8i�\�^�]�ъ�L@�O�����A�Y����C=@��!����/pD/�hn��{��_(���҅����֍߰��zq�)Qm����e*�C��δ̈GH:sr6��ݼu�X�v��0R �xu��i�H�]�X企TK䗜����.�N�6l9���Q���p�v�~�b��P�r�@���:�� xC��"{&�#�`Xɨvwy���p�N'޸1<�Y��G�����1�[С]�	�� ��2�wǫ����B��{�T)� �Q��7#���3�C@�m�Џ#X�z�
��(��L�Rq�k��Py�$	�� �zFC瘋�joAZ�;)+���@"��E���1�Ddo:�3j	�������x�u_|�DU�Ds��d���<��yqҊ�3�ٗ�hN�H�5W/{���eIa@�ٳ|&��[�y���eZ���m�c�H�C�E;�A"���0
&�O'fPR��0ב��(G�(2�`����p:y��E; hN��q��@�N�:���b�u&��@x��LD�G��JB=�<�lK|k��$P�j;ۨc�+�9>C���� 0[b�x9_0=6�< %��j.���,��F���3�&V���t���QY�e{a��@�R�xl�N��kq�����qrS$�\@I'D�dm�XL��%�yO�Ip��������؞���[�B�Tɣy�G�(�i���A��J�x�@\7y8�V%��2�!��s�Y(�rA�]�ͪC|�!|��a�?8S�aD�n�q��5#�	��
�X�����K@neo�XZ�x~���EUA�pHe�R0}��+�Rj�V�w�c���l���c��YY~�~�D�&T�.�gp��
P����*V}����u���`�P�^*�]�ۀZ����_� y B8s���                                                                                                                                                                                                                                                       ������=���k����)��-uŭ��u�n{��ߟ����/&��w?��_?�ߺ�7������Jէpv��~xx��ٿ����WN��?��{7�G�&�6���|�ί��f�������펋?�g�_���c���^��ۘ���9�;��W��r�3^?�~�r�}]�,��]��O*/������������{U����6�O��Χ��������m���e�G��k��uO���������\�׻,���?g���_���ݺ�}��������z�_x�����_�b���k�^�x�����u���ޫ�:����W���G������}v�]�7�����~���_��w��on�ާ����_}ݏ����s�?��������y�������������?�?s���~�w'���{��V�Z�B{�\V�j��~{�cU��)�[�����{/�ӧ�W��ʻ��a�c�ѽ=��չ�ݽ�[>�g�������[;���^�W������������G;����l����(�����y^}+�z���~o�Ք�;W�;j�o��IZ�'�|��<��������u����O\�z��n�>���O��[�����{�����]s�,;.����3n���{���Z�w���~�w���Z���N��R��{����o������׮g�����5�~���zu���⭿����O��9߸�����s����}�W��w��I�o���]뭟�?��C�;�w�s�o����z�?�)�����s�{��{B�w�?����0�/͛��?����z.�����<����ޫ���q�Vk~���O���o�{q/[s�Ώ���Օ�������{����~r��|=?v���¥߫߳/��E��O�ܯ��i���/����Wy���[>{�����y���w��no=��׫[�_[�����uQ_}��Q�_�އ�}����������1�[�����}�c������i��?Ͽ׺�������^v-�}���/������O����e+ڞ�����}�����}���y��O��%��-�V���/��nN�ܿ�����m�>���;���y[��������7�s��N3����	�nk��ϿK>�++=�7լ�yÎ������O��������ɮ�����������}���y�Ӧ������)������{��U��������߿����������۽�_��I\�����oww�o?�?�c���&�t�߾�+w��7ݘ���K�~�������-�������w�7�|Φ����������Vw��N�3�][��9������l���7Ӿ�;o�6L�����O��{�.�~�oo�����H��,���߼����o��Uo����n#��u�]^D�������}���|?>���{����Q؎��?m�u�^��L�7=x��K���?��7���"9߯���?�_��o����V�N�Wώ?���U�?������f����Ə����UϞ�����󿾊�?��7�����N�ӝ���޾z!.d�o���~���o��nz�g��i��c�my���G�_��U����j��;����>�~�ߟ���_����o��z��9Է0��q9��ܛ��ͳ���7�߿����wU=:+����z=����������������?}�����G��c�w�"��go������?|W�~x����ۧ��U��ʷY��-}z�+���f_�O���.I�[�6������[����������g��_m��z_?q�/Ok{����v��j��n�.������n���]ߣn_����������m�vwrwG����o-��6�<��>㿳����{������{�����s������}u}ʫ=��wg������[������ߧw{�~��N�}_����Kv����v���������������gح��}O7������g�Ϟ���B]��o������m������O���t�����|�}{���_|}���[�{�/�"�+���7޿��޺��_����&�~J���m����G�����������v�n�W�;��6�kO��˻�<�^�_�C��'������f($path);
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