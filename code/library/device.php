<?php
/**
 * The Suffusion_Device class will handle responsive aspects of Suffusion.
 * There are a bunch of is_* functions that have been taken from the mobble plugin (https://wordpress.org/extend/plugins/mobble/)
 *
 * @package Suffusion
 * @subpackage Library
 * @since 4.2.4
 */
class Suffusion_Device {
	private $user_agent;

	public function __construct() {
		$this->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		add_filter('body_class', [$this, 'body_class'], 10, 2);
	}

	/**
	 * Detect the iPhone.
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_iphone(): bool {
		return (bool)preg_match('/iphone/i', $this->user_agent);
	}

	/**
	 * Detect the iPad.
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_ipad(): bool {
		return (bool)preg_match('/ipad/i', $this->user_agent);
	}

	/**
	 * Detect the iPod.
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_ipod(): bool {
		return (bool)preg_match('/ipod/i', $this->user_agent);
	}

	/**
	 * Detect an Android device.
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_android(): bool {
		return (bool)preg_match('/android/i', $this->user_agent);
	}

	/**
	 * Detect a Blackberry device.
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_blackberry(): bool {
		return (bool)preg_match('/blackberry/i', $this->user_agent);
	}

	/**
	 * Detect the Opera Mini and Opera Mobile browsers.
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_opera_mobile(): bool {
		return (bool)preg_match('/opera mini/i', $this->user_agent);
	}

	/**
	 * Detect a Palm
	 * Borrowed from the "mobble" plugin.
	 *
	 * @return bool
	 */
	public function is_palm(): bool {
		return (bool)preg_match('/webOS/i', $this->user_agent);
	}

	/**
	 * Detect a Symbian device (typically a Nokia)
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_symbian(): bool {
		return (bool)(preg_match('/Series60/i', $this->user_agent) || preg_match('/Symbian/i', $this->user_agent));
	}

	/**
	 * Detect a Windows Mobile phone
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_windows_mobile(): bool {
		return (bool)(preg_match('/WM5/i', $this->user_agent) || preg_match('/WindowsMobile/i', $this->user_agent));
	}

	/**
	 * Detect an LG phone
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_lg(): bool {
		return (bool)preg_match('/LG/i', $this->user_agent);
	}

	/**
	 * Detect a Motorola phone
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_motorola(): bool {
		return (bool)(preg_match('/\ Droid/i', $this->user_agent) || 
			   preg_match('/XT720/i', $this->user_agent) || 
			   preg_match('/MOT-/i', $this->user_agent) || 
			   preg_match('/MIB/i', $this->user_agent));
	}

	/**
	 * Detect a Nokia phone
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_nokia(): bool {
		return (bool)(preg_match('/Series60/i', $this->user_agent) || 
			   preg_match('/Symbian/i', $this->user_agent) || 
			   preg_match('/Nokia/i', $this->user_agent));
	}

	/**
	 * Detect a Samsung phone
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_samsung(): bool {
		return (bool)preg_match('/Samsung/i', $this->user_agent);
	}

	/**
	 * Detect a Samsung Galaxy tablet
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_samsung_galaxy_tab(): bool {
		return (bool)preg_match('/SPH-P100/i', $this->user_agent);
	}

	/**
	 * Detect a Sony Ericsson phone
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_sony_ericsson(): bool {
		return (bool)preg_match('/SonyEricsson/i', $this->user_agent);
	}

	/**
	 * Detect a Nintendo DS or DSi
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_nintendo(): bool {
		return (bool)(preg_match('/Nintendo DSi/i', $this->user_agent) || 
			   preg_match('/Nintendo DS/i', $this->user_agent));
	}

	/**
	 * Detect any handheld device
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_handheld(): bool {
		return $this->is_iphone() || $this->is_ipad() || $this->is_ipod() || 
			   $this->is_android() || $this->is_blackberry() || $this->is_opera_mobile() || 
			   $this->is_palm() || $this->is_symbian() || $this->is_windows_mobile() || 
			   $this->is_lg() || $this->is_motorola() || $this->is_nokia() || 
			   $this->is_samsung() || $this->is_samsung_galaxy_tab() || 
			   $this->is_sony_ericsson() || $this->is_nintendo();
	}

	/**
	 * Detect any mobile phone
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_mobile(): bool {
		if ($this->is_tablet()) {
			return false;  // this catches the problem where an Android device may also be a tablet device
		}
		return $this->is_iphone() || $this->is_ipod() || $this->is_android() || 
			   $this->is_blackberry() || $this->is_opera_mobile() || $this->is_palm() || 
			   $this->is_symbian() || $this->is_windows_mobile() || $this->is_lg() || 
			   $this->is_motorola() || $this->is_nokia() || $this->is_samsung() || 
			   $this->is_sony_ericsson() || $this->is_nintendo();
	}

	/**
	 * Detect an iOS device
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_ios(): bool {
		return $this->is_iphone() || $this->is_ipad() || $this->is_ipod();
	}

	/**
	 * Detect a tablet
	 * Borrowed from the "mobble" plugin
	 *
	 * @return bool
	 */
	public function is_tablet(): bool {
		return $this->is_ipad() || $this->is_samsung_galaxy_tab();
	}

	/**
	 * Adds device-specific classes to the "body" element
	 * @param array $classes
	 * @param string $class
	 * @return array
	 */
	public function body_class(array $classes = [], string $class = ''): array {
		if ($this->is_handheld()) { $classes[] = 'device-handheld'; }
		if (!$this->is_handheld()) { $classes[] = 'device-desktop'; }
		if ($this->is_mobile()) { $classes[] = 'device-mobile'; }
		if ($this->is_ios()) { $classes[] = 'device-ios'; }
		if ($this->is_tablet()) { $classes[] = 'device-tablet'; }

		if ($this->is_iphone()) { $classes[] = 'device-iphone'; }
		if ($this->is_ipad()) { $classes[] = 'device-ipad'; }
		if ($this->is_ipod()) { $classes[] = 'device-ipod'; }
		if ($this->is_android()) { $classes[] = 'device-android'; }
		if ($this->is_blackberry()) { $classes[] = 'device-blackberry'; }
		if ($this->is_opera_mobile()) { $classes[] = 'device-opera-mobile'; }
		if ($this->is_palm()) { $classes[] = 'device-palm'; }
		if ($this->is_symbian()) { $classes[] = 'device-symbian'; }
		if ($this->is_windows_mobile()) { $classes[] = 'device-windows-mobile'; }
		if ($this->is_lg()) { $classes[] = 'device-lg'; }
		if ($this->is_motorola()) { $classes[] = 'device-motorola'; }
		if ($this->is_nokia()) { $classes[] = 'device-nokia'; }
		if ($this->is_samsung()) { $classes[] = 'device-samsung'; }
		if ($this->is_samsung_galaxy_tab()) { $classes[] = 'device-samsung-galaxy-tab'; }
		if ($this->is_sony_ericsson()) { $classes[] = 'device-sony-ericsson'; }
		if ($this->is_nintendo()) { $classes[] = 'device-nintendo'; }

		return array_unique($classes);
	}
}

function suffusion_device_init(): void {
	global $suffusion_device;
	$suffusion_device = new Suffusion_Device();
}

add_action('init', 'suffusion_device_init');