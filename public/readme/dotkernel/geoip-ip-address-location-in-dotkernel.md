---
title: "GeoIP: Ip Address Location In DotKernel"
description: "How DotKernel's getCountryByIp function in library/Dot/Geoip.php uses MaxMind's GeoIP technology and its .dat files to determine a visitor's country."
author: "Teo"
date_published: "2010-10-19"
canonical_url: "https://www.dotkernel.com/dotkernel/geoip-ip-address-location-in-dotkernel/"
category: "Dotkernel"
language: "en"
---

# GeoIP: Ip Address Location In DotKernel

## TL;DR

GeoIP is MaxMind's proprietary technology for IP geolocation. DotKernel uses it to get user statistics by country, determining a visitor's country, region, city, postal code, or area code in real time. The logic lives in `library/Dot/Geoip.php`, inside the `getCountryByIp` function, which branches over four cases depending on whether the `mod_geoip` PECL extension and its `.dat` files are available.

## What getCountryByIp does

The function first reads the session variable used to remember possible error messages, and initializes the country as 'unknown' in case it isn't found. It then runs through four if/else branches:

1. **`mod_geoip` PECL extension is not installed** — it uses the existing `externals/geoip/GeoIP.dat` file bundled with DotKernel (downloadable from MaxMind if you don't have it).
2. **`mod_geoip` is installed and `GeoIP.dat` exists** (`geoip_db_avail(GEOIP_COUNTRY_EDITION)`) — it uses the built-in PHP functions `geoip_country_code_by_name` and `geoip_country_name_by_name` to get the country code and name.
3. **`mod_geoip` is installed, `GeoIP.dat` doesn't exist, but `GeoIpCity.dat` exists** — it uses the PHP function `geoip_record_by_name` to get the country code and name.
4. **`mod_geoip` is installed, but neither `GeoIP.dat` nor `GeoIPCity.dat` exist** — same behavior as case 1: it uses `externals/geoip/GeoIP.dat` from the DotKernel framework.

```php
/**
 * Get the country by IP
 * Return an array with : short name, like 'us' and long name, like 'United States'
 * @access public
 * @param string $ip
 * @return array
 */
public function getCountryByIp($ip)
{
    $session = Zend_Registry::get('session');
    $country = array(0 => 'unknown',1 => 'NA');
    if(extension_loaded('geoip') == FALSE)
    {
        // GeoIp extension is not active
        $api = new Dot_Geoip_Country();
        $geoipPath = 'externals/geoip/GeoIP.dat';
        if(file_exists($geoipPath))
        {
            $country = $api->getCountryByAddr($geoipPath, $ip);
        }
        else
        {
            $session->message = $this->option->warningMessage->modGeoIp;
            $session->message = 'warning';
        }
    }
    elseif(geoip_db_avail(GEOIP_COUNTRY_EDITION))
    {
        //if GeoIP.dat file exists
        $country = geoip_country_code_by_name ($ip);
        $country = geoip_country_name_by_name($ip);
    }
    elseif(geoip_db_avail(GEOIP_CITY_EDITION_REV0))
    {
        //if GeoIPCity.dat file exists
        $record = geoip_record_by_name($ip);
        if(!empty($record))
        {
            $country = $record;
            $country = $record;
        }
    }
    else
    {
        // GeoIp extension is not active
        $api = new Dot_Geoip_Country();
        $geoipPath = 'externals/geoip/GeoIP.dat';
        if(file_exists($geoipPath))
        {
            $country = $api->getCountryByAddr($geoipPath, $ip);
        }
        else
        {
            $session->message = $this->option->warningMessage->modGeoIp;;
            $session->message = 'warning';
        }
    }
    return $country;
}
```

## FAQ

**Q: What is GeoIP and what does it let DotKernel do?**
A: GeoIP is MaxMind's proprietary technology for IP geolocation data. DotKernel uses it to get user statistics by country, determining a visitor's country, region, city, postal code, or area code in real time.

**Q: Where does the GeoIP logic live in DotKernel?**
A: In library/Dot/Geoip.php, inside the getCountryByIp function.

**Q: What does getCountryByIp do when the mod_geoip PECL extension isn't installed?**
A: It falls back to the existing externals/geoip/GeoIP.dat file bundled with DotKernel (downloadable from MaxMind if not present).

**Q: What happens when mod_geoip is installed and GeoIP.dat exists?**
A: It uses the built-in PHP functions geoip_country_code_by_name and geoip_country_name_by_name to get the country code and name.

**Q: What if GeoIP.dat is missing but GeoIPCity.dat exists, or neither file exists?**
A: If GeoIPCity.dat exists, it uses the PHP function geoip_record_by_name to get the country code and name. If neither .dat file exists, it behaves the same as when mod_geoip isn't installed, falling back to externals/geoip/GeoIP.dat.

## Resources

- [MaxMind's IP geolocation](http://www.maxmind.com/app/ip-location)
- [library/Dot/Geoip.php source](http://websvn.dotkernel.net/filedetails.php?repname=DotKernel&path=%2Ftrunk%2Flibrary%2FDot%2FGeoip.php)
- [GeoLite Country database](http://www.maxmind.com/app/geolitecountry)
- [PHP GeoIP functions](http://php.net/manual/en/book.geoip.php)
