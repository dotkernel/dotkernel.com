---
title: "GeoIP: Ip Address Location In Dotkernel"
description: "How Dotkernel's getCountryByIp function in library/Dot/Geoip.php uses MaxMind's GeoIP technology and its .dat files to determine a visitor's country."
author: "Teo"
date_published: "2010-10-19"
canonical_url: "https://www.dotkernel.com/dotkernel/geoip-ip-address-location-in-dotkernel/"
category: "Dotkernel"
language: "en"
---

# GeoIP: Ip Address Location In Dotkernel

## TL;DR
GeoIP is MaxMind's proprietary technology for IP geolocation.
Dotkernel uses it to get user statistics by country, determining a visitor's country, region, city, postal code, or area code in real time.
The logic lives in `library/Dot/Geoip.php`, inside the `getCountryByIp` function, which branches over four cases depending on whether the `mod_geoip` PECL extension and its `.dat` files are available.

***GeoIP*** is the proprietary technology that drives [MaxMind's](http://www.maxmind.com/app/ip-location) IP geolocation data and services. It is a non-invasive way to determine geographical and other information about Internet visitors in real-time. When a person visits your website, ***GeoIP*** can determine which country, region, city, postal code, area code the visitor is coming from.

Dotkernel uses ***GeoIP*** to get user statistics by country. The main file for ***GeoIP*** is [library/Dot/Geoip.php](http://websvn.dotkernel.net/filedetails.php?repname=Dotkernel&path=%2Ftrunk%2Flibrary%2FDot%2FGeoip.php) Let’s explain what function ***getCountryByIp*** does:

- Line 41: get session variable to memorize the possible error messages later.
- Line 42: initialize country with ‘unknown’ value, in case the country isn’t found.

Next there are 4 ***if/else*** statements: **1.** Line 43 – 57: ***mod_geoip*** PECL extension is not installed

- In this case we use the already existing file **externals/geoip/GeoIP.dat** from Dotkernel (If you don’t have it, download it from [here](http://www.maxmind.com/app/geolitecountry)).

**2.** Line 58 – 63: ***mod_geoip*** is installed and ***GeoIP.dat*** file exists on the server: *geoip_db_avail(GEOIP_COUNTRY_EDITION)*

- In this case we simply use the default PHP functions ***[geoip_country_code_by_name](http://php.net/manual/en/book.geoip.php)*** and ***[geoip_country_name_by_name](http://php.net/manual/en/book.geoip.php)*** to get the country code and name.

**3.** Line 64 – 73 : ***mod_geoip*** is installed, ***GeoIP.dat*** file does not exist, but ***GeoIpCity.dat*** exists

- In this case we use the PHP function ***[geoip_record_by_name](http://php.net/manual/en/book.geoip.php)*** to get the country code and name.

**4.** Line 74 – 88 : ***mod_geoip*** is installed, but neither GeoIP.dat or GeoIPCity.dat exist

- This has the same behavior like item #1in this list –  it uses ***externals/geoip/GeoIP.dat*** from the Dotkernel framework

```
 line="32">/**
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
            $session->message['txt'] = $this->option->warningMessage->modGeoIp;;
            $session->message = 'warning';
        }
    }
    return $country;
}
}
```

## FAQ

**Q: What is GeoIP and what does it let Dotkernel do?**
A: GeoIP is MaxMind's proprietary technology for IP geolocation data. Dotkernel uses it to get user statistics by country, determining a visitor's country, region, city, postal code, or area code in real time.

**Q: Where does the GeoIP logic live in Dotkernel?**
A: In library/Dot/Geoip.php, inside the getCountryByIp function.

**Q: What does getCountryByIp do when the mod_geoip PECL extension isn't installed?**
A: It falls back to the existing externals/geoip/GeoIP.dat file bundled with Dotkernel (downloadable from MaxMind if not present).

**Q: What happens when mod_geoip is installed and GeoIP.dat exists?**
A: It uses the built-in PHP functions geoip_country_code_by_name and geoip_country_name_by_name to get the country code and name.

**Q: What if GeoIP.dat is missing but GeoIPCity.dat exists, or neither file exists?**
A: If GeoIPCity.dat exists, it uses the PHP function geoip_record_by_name to get the country code and name. If neither .dat file exists, it behaves the same as when mod_geoip isn't installed, falling back to externals/geoip/GeoIP.dat.
