<?php

/**
 * CollectPlus
 *
 * @category    CollectPlus
 * @package     Jjcommerce_CollectPlus
 * @version     2.0.0
 * @author      Jjcommerce Team
 *
 */

/**
 * PHPCoord
 * @package PHPCoord
 * @author Jonathan Stott
 * @author Doug Wright
 */
//namespace PHPCoord;
namespace Jjcommerce\CollectPlus\Model;
/**
 * Abstract class representing a Tranverse Mercator Projection
 * @author Doug Wright
 * @package PHPCoord
 */
abstract class TransverseMercator
{

    /**
     * X
     * @var float
     */
    protected $x;

    /**
     * Y
     * @var float
     */
    protected $y;

    /**
     * h
     * @var float
     */
    protected $h;

    /**
     * Reference ellipsoid used in this datum
     * @var RefEll
     */
    protected $refEll;

    /**
     * Cartesian constructor.
     * @param float $x
     * @param float $y
     * @param float $h
     * @param RefEll $refEll
     */
    public function __construct($x, $y, $h, RefEll $refEll)
    {
        $this->setX($x);
        $this->setY($y);
        $this->setH($h);
        $this->setRefEll($refEll);
    }

    /**
     * String version of coordinate.
     * @return string
     */
    public function __toString()
    {
        return "({$this->x}, {$this->y}, {$this->h})";
    }

    /**
     * @return float
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @param float $x
     */
    public function setX($x)
    {
        $this->x = $x;
    }

    /**
     * @return float
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param float $y
     */
    public function setY($y)
    {
        $this->y = $y;
    }

    /**
     * @return float
     */
    public function getH()
    {
        return $this->h;
    }

    /**
     * @param float $h
     */
    public function setH($h)
    {
        $this->h = $h;
    }

    /**
     * @return RefEll
     */
    public function getRefEll()
    {
        return $this->refEll;
    }

    /**
     * @param RefEll $refEll
     */
    public function setRefEll(RefEll $refEll)
    {
        $this->refEll = $refEll;
    }


    /**
     * Reference ellipsoid used by this projection
     * @return RefEll
     */
    abstract public function getReferenceEllipsoid();

    /**
     * Scale factor at central meridian
     * @return float
     */
    abstract public function getScaleFactor();

    /**
     * Northing of true origin
     * @return float
     */
    abstract public function getOriginNorthing();

    /**
     * Easting of true origin
     * @return float
     */
    abstract public function getOriginEasting();

    /**
     * Latitude of true origin
     * @return float
     */
    abstract public function getOriginLatitude();

    /**
     * Longitude of true origin
     * @return float
     */
    abstract public function getOriginLongitude();

    /**
     * Convert this grid reference into a latitude and longitude
     * Formula for transformation is taken from OS document
     * "A Guide to Coordinate Systems in Great Britain"
     *
     * @param float $N map coordinate (northing) of point to convert
     * @param float $E map coordinate (easting) of point to convert
     * @param float $N0 map coordinate (northing) of true origin
     * @param float $E0 map coordinate (easting) of true origin
     * @param float $phi0 map coordinate (latitude) of true origin
     * @param float $lambda0 map coordinate (longitude) of true origin and central meridian
     * @return LatLng
     */
    public function convertToLatitudeLongitude($N, $E, $N0, $E0, $phi0, $lambda0)
    {

        $phi0 = deg2rad($phi0);
        $lambda0 = deg2rad($lambda0);

        $refEll = $this->getReferenceEllipsoid();
        $F0 = $this->getScaleFactor();

        $a = $refEll->getMaj();
        $b = $refEll->getMin();
        $eSquared = $refEll->getEcc();
        $n = ($a - $b) / ($a + $b);
        $phiPrime = (($N - $N0) / ($a * $F0)) + $phi0;

        do {
            $M =
                ($b * $F0)
                * (((1 + $n + ((5 / 4) * $n * $n) + ((5 / 4) * $n * $n * $n))
                        * ($phiPrime - $phi0))
                    - (((3 * $n) + (3 * $n * $n) + ((21 / 8) * $n * $n * $n))
                        * sin($phiPrime - $phi0)
                        * cos($phiPrime + $phi0))
                    + ((((15 / 8) * $n * $n) + ((15 / 8) * $n * $n * $n))
                        * sin(2 * ($phiPrime - $phi0))
                        * cos(2 * ($phiPrime + $phi0)))
                    - (((35 / 24) * $n * $n * $n)
                        * sin(3 * ($phiPrime - $phi0))
                        * cos(3 * ($phiPrime + $phi0))));
            $phiPrime += ($N - $N0 - $M) / ($a * $F0);
        } while (($N - $N0 - $M) >= 0.00001);
        $v = $a * $F0 * pow(1 - $eSquared * pow(sin($phiPrime), 2), -0.5);
        $rho =
            $a
            * $F0
            * (1 - $eSquared)
            * pow(1 - $eSquared * pow(sin($phiPrime), 2), -1.5);
        $etaSquared = ($v / $rho) - 1;
        $VII = tan($phiPrime) / (2 * $rho * $v);
        $VIII =
            (tan($phiPrime) / (24 * $rho * pow($v, 3)))
            * (5
                + (3 * pow(tan($phiPrime), 2))
                + $etaSquared
                - (9 * pow(tan($phiPrime), 2) * $etaSquared));
        $IX =
            (tan($phiPrime) / (720 * $rho * pow($v, 5)))
            * (61
                + (90 * pow(tan($phiPrime), 2))
                + (45 * pow(tan($phiPrime), 2) * pow(tan($phiPrime), 2)));
        $X = (1 / cos($phiPrime)) / $v;
        $XI =
            ((1 / cos($phiPrime)) / (6 * $v * $v * $v))
            * (($v / $rho) + (2 * pow(tan($phiPrime), 2)));
        $XII =
            ((1 / cos($phiPrime)) / (120 * pow($v, 5)))
            * (5
                + (28 * pow(tan($phiPrime), 2))
                + (24 * pow(tan($phiPrime), 4)));
        $XIIA =
            ((1 / cos($phiPrime)) / (5040 * pow($v, 7)))
            * (61
                + (662 * pow(tan($phiPrime), 2))
                + (1320 * pow(tan($phiPrime), 4))
                + (720
                    * pow(tan($phiPrime), 6)));
        $phi =
            $phiPrime
            - ($VII * pow($E - $E0, 2))
            + ($VIII * pow($E - $E0, 4))
            - ($IX * pow($E - $E0, 6));
        $lambda =
            $lambda0
            + ($X * ($E - $E0))
            - ($XI * pow($E - $E0, 3))
            + ($XII * pow($E - $E0, 5))
            - ($XIIA * pow($E - $E0, 7));

        return new LatLng(rad2deg($phi), rad2deg($lambda), 0, $refEll);
    }
}


/**
 * Ordnance Survey grid reference
 * References are accurate to 1m
 * @author Jonathan Stott
 * @author Doug Wright
 * @package PHPCoord
 */
class OSRef extends TransverseMercator
{

    const GRID_LETTERS = "VWXYZQRSTULMNOPFGHJKABCDE";

    public function getReferenceEllipsoid()
    {
        return RefEll::airy1830();
    }

    public function getScaleFactor()
    {
        return 0.9996012717;
    }

    public function getOriginNorthing()
    {
        return -100000;
    }

    public function getOriginEasting()
    {
        return 400000;
    }

    public function getOriginLatitude()
    {
        return 49;
    }

    public function getOriginLongitude()
    {
        return -2;
    }

    /**
     * Create a new object representing a OSGB reference.
     *
     * @param int $x
     * @param int $y
     * @param int $z
     */
    public function __construct($x, $y, $z = 0)
    {
        parent::__construct($x, $y, $z, RefEll::airy1830());
    }

    /**
     * Take a string formatted as a six-figure OS grid reference (e.g.
     * "TG514131") and return a reference to an OSRef object that represents
     * that grid reference.
     *
     * @param string $ref
     * @return OSRef
     */
    public static function fromSixFigureReference($ref)
    {

        //first (major) letter is the 500km grid sq, origin at -1000000, -500000
        $majorEasting = strpos(self::GRID_LETTERS, $ref[0]) % 5  * 500000 - 1000000;
        $majorNorthing = (floor(strpos(self::GRID_LETTERS, $ref[0]) / 5)) * 500000 - 500000;

        //second (minor) letter is 100km grid sq, origin at 0,0 of this square
        $minorEasting = strpos(self::GRID_LETTERS, $ref[1]) % 5  * 100000;
        $minorNorthing = (floor(strpos(self::GRID_LETTERS, $ref[1]) / 5)) * 100000;

        $easting = $majorEasting + $minorEasting + (substr($ref, 2, 3) * 100);
        $northing = $majorNorthing + $minorNorthing + (substr($ref, 5, 3) * 100);

        return new OSRef($easting, $northing);
    }

    /**
     * Convert this grid reference into a grid reference string of a
     * given length (2, 4, 6, 8 or 10) including the two-character
     * designation for the 100km square. e.g. TG514131.
     * @return string
     */
    private function toGridReference($length)
    {

        $halfLength = $length / 2;

        $easting = str_pad($this->x, 6, 0, STR_PAD_LEFT);
        $northing = str_pad($this->y, 6, 0, STR_PAD_LEFT);


        $adjustedX = $this->x + 1000000;
        $adjustedY = $this->y + 500000;
        $majorSquaresEast = floor($adjustedX / 500000);
        $majorSquaresNorth = floor($adjustedY / 500000);
        $majorLetterIndex = (int)(5 * $majorSquaresNorth + $majorSquaresEast);
        $majorLetter = substr(self::GRID_LETTERS, $majorLetterIndex, 1);

        //second (minor) letter is 100km grid sq, origin at 0,0 of this square
        $minorSquaresEast = $easting[0] % 5;
        $minorSquaresNorth = $northing[0] % 5;
        $minorLetterIndex = (int)(5 * $minorSquaresNorth + $minorSquaresEast);
        $minorLetter = substr(self::GRID_LETTERS, $minorLetterIndex, 1);

        return $majorLetter . $minorLetter . substr($easting, 1, $halfLength) . substr($northing, 1, $halfLength);
    }

    /**
     * Convert this grid reference into a string using a standard two-figure
     * grid reference including the two-character designation for the 100km
     * square. e.g. TG51 (10km square).
     * @return string
     */
    public function toTwoFigureReference()
    {
        return $this->toGridReference(2);
    }

    /**
     * Convert this grid reference into a string using a standard four-figure
     * grid reference including the two-character designation for the 100km
     * square. e.g. TG5113 (1km square).
     * @return string
     */
    public function toFourFigureReference()
    {
        return $this->toGridReference(4);
    }

    /**
     * Convert this grid reference into a string using a standard six-figure
     * grid reference including the two-character designation for the 100km
     * square. e.g. TG514131 (100m square).
     * @return string
     */
    public function toSixFigureReference()
    {
        return $this->toGridReference(6);
    }

    /**
     * Convert this grid reference into a string using a standard eight-figure
     * grid reference including the two-character designation for the 100km
     * square. e.g. TG51431312 (10m square).
     * @return string
     */
    public function toEightFigureReference()
    {
        return $this->toGridReference(8);
    }

    /**
     * Convert this grid reference into a string using a standard ten-figure
     * grid reference including the two-character designation for the 100km
     * square. e.g. TG5143113121 (1m square).
     * @return string
     */
    public function toTenFigureReference()
    {
        return $this->toGridReference(10);
    }

    /**
     * Convert this grid reference into a latitude and longitude
     * @return LatLng
     */
    public function toLatLng()
    {
        $N = $this->y;
        $E = $this->x;
        $N0 = $this->getOriginNorthing();
        $E0 = $this->getOriginEasting();
        $phi0 = $this->getOriginLatitude();
        $lambda0 = $this->getOriginLongitude();

        return $this->convertToLatitudeLongitude($N, $E, $N0, $E0, $phi0, $lambda0);
    }

    /**
     * String version of coordinate.
     * @return string
     */
    public function __toString()
    {
        return "({$this->x}, {$this->y})";
    }
}


/**
 * Latitude/Longitude reference
 * @author Jonathan Stott
 * @author Doug Wright
 * @package PHPCoord
 */
class LatLng
{

    /**
     * Latitude
     * @var float
     */
    protected $lat;

    /**
     * Longitude
     * @var float
     */
    protected $lng;

    /**
     * Height
     * @var float
     */
    protected $h;

    /**
     * Reference ellipsoid the co-ordinates are from
     * @var RefEll
     */
    protected $refEll;

    /**
     * Create a new LatLng object from the given latitude and longitude
     * @param float $lat
     * @param float $lng
     * @param float $height
     * @param RefEll $refEll
     */
    public function __construct($lat, $lng, $height, RefEll $refEll)
    {
        $this->lat = round($lat, 8);
        $this->lng = round($lng, 8);
        $this->h = round($height);
        $this->refEll = $refEll;
    }

    /**
     * Return a string representation of this LatLng object
     * @return string
     */
    public function __toString()
    {
        return "({$this->lat}, {$this->lng})";
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param float $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param float $lng
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    }

    /**
     * @return float
     */
    public function getH()
    {
        return $this->h;
    }

    /**
     * @param float $h
     */
    public function setH($h)
    {
        $this->h = $h;
    }


    /**
     * @return RefEll
     */
    public function getRefEll()
    {
        return $this->refEll;
    }

    /**
     * @param RefEll $refEll
     */
    public function setRefEll(RefEll $refEll)
    {
        $this->refEll = $refEll;
    }



    /**
     * Calculate the surface distance between this LatLng object and the one
     * passed in as a parameter.
     *
     * @param LatLng $to a LatLng object to measure the surface distance to
     * @return float
     */
    public function distance(LatLng $to)
    {
        if ($this->refEll != $to->refEll) {
            throw new \RuntimeException('Source and destination co-ordinates are not using the same ellipsoid');
        }

        //Mean radius definition from taken from Wikipedia
        $er = ((2 * $this->refEll->getMaj()) + $this->refEll->getMin()) / 3;

        $latFrom = deg2rad($this->lat);
        $latTo = deg2rad($to->lat);
        $lngFrom = deg2rad($this->lng);
        $lngTo = deg2rad($to->lng);

        $d = acos(sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lngTo - $lngFrom)) * $er;

        return round($d, 5);
    }


    /**
     * Convert this LatLng object to OSGB36 datum.
     * Reference values for transformation are taken from OS document
     * "A Guide to Coordinate Systems in Great Britain"
     * @return void
     */
    public function toOSGB36()
    {
        if ($this->refEll == RefEll::airy1830()) {
            return;
        }

        $this->toWGS84();

        $tx = -446.448;
        $ty = 125.157;
        $tz = -542.060;
        $s = 0.0000204894;
        $rx = deg2rad(-0.1502 / 3600);
        $ry = deg2rad(-0.2470 / 3600);
        $rz = deg2rad(-0.8421 / 3600);

        $this->transformDatum(RefEll::airy1830(), $tx, $ty, $tz, $s, $rx, $ry, $rz);
    }

    /**
     * Convert this LatLng object to ED50 datum.
     * Reference values for transformation are taken from http://www.globalmapper.com/helpv9/datum_list.htm
     * @return void
     */
    public function toED50()
    {
        if ($this->refEll == RefEll::international1924()) {
            return;
        }

        $this->toWGS84();

        $tx = 87;
        $ty = 98;
        $tz = 121;
        $s = 0;
        $rx = deg2rad(0);
        $ry = deg2rad(0);
        $rz = deg2rad(0);

        $this->transformDatum(RefEll::international1924(), $tx, $ty, $tz, $s, $rx, $ry, $rz);
    }

    /**
     * Convert this LatLng object to NAD27 datum.
     * Reference values for transformation are taken from Wikipedia
     * @return void
     */
    public function toNAD27()
    {
        if ($this->refEll == RefEll::clarke1866()) {
            return;
        }

        $this->toWGS84();

        $tx = 8;
        $ty = -160;
        $tz = -176;
        $s = 0;
        $rx = deg2rad(0);
        $ry = deg2rad(0);
        $rz = deg2rad(0);

        $this->transformDatum(RefEll::clarke1866(), $tx, $ty, $tz, $s, $rx, $ry, $rz);
    }

    /**
     * Convert this LatLng object to Ireland 1975 datum.
     * Reference values for transformation are taken from OSI document
     * "Making maps compatible with GPS"
     * @return void
     */
    public function toIreland1975()
    {
        if ($this->refEll == RefEll::airyModified()) {
            return;
        }

        $this->toWGS84();

        $tx = -482.530;
        $ty = 130.596;
        $tz = -564.557;
        $s = -0.00000815;
        $rx = deg2rad(-1.042 / 3600);
        $ry = deg2rad(-0.214 / 3600);
        $rz = deg2rad(-0.631 / 3600);

        $this->transformDatum(RefEll::airyModified(), $tx, $ty, $tz, $s, $rx, $ry, $rz);
    }

    /**
     * Convert this LatLng object from WGS84 datum to Ireland 1975 datum.
     * Reference values for transformation are taken from OSI document
     * "Making maps compatible with GPS"
     * @return void
     */
    public function toWGS84() {

        switch ($this->refEll) {

            case RefEll::wgs84():
                return; //do nothing

            case RefEll::airy1830(): // values from OSGB document "A Guide to Coordinate Systems in Great Britain"
                $tx = 446.448;
                $ty = -125.157;
                $tz = 542.060;
                $s = -0.0000204894;
                $rx = deg2rad(0.1502 / 3600);
                $ry = deg2rad(0.2470 / 3600);
                $rz = deg2rad(0.8421 / 3600);
                break;

            case RefEll::airyModified(): // values from OSI document "Making maps compatible with GPS"
                $tx = 482.530;
                $ty = -130.596;
                $tz = 564.557;
                $s = 0.00000815;
                $rx = deg2rad(1.042 / 3600);
                $ry = deg2rad(0.214 / 3600);
                $rz = deg2rad(0.631 / 3600);
                break;

            case RefEll::clarke1866(): // assumes NAD27, values from Wikipedia
                $tx = -8;
                $ty = 160;
                $tz = 176;
                $s = 0;
                $rx = deg2rad(0);
                $ry = deg2rad(0);
                $rz = deg2rad(0);
                break;

            case RefEll::international1924(): // assumes ED50, values from http://www.globalmapper.com/helpv9/datum_list.htm
                $tx = -87;
                $ty = -98;
                $tz = -121;
                $s = 0;
                $rx = deg2rad(0);
                $ry = deg2rad(0);
                $rz = deg2rad(0);
                break;

            case RefEll::bessel1841(): // assumes Germany, values from Wikipedia
                $tx = 582;
                $ty = -105;
                $tz = -414;
                $s = 0.0000083;
                $rx = deg2rad(1.04 / 3600);
                $ry = deg2rad(0.35 / 3600);
                $rz = deg2rad(-3.08 / 3600);
                break;

            default:
                throw new \RuntimeException('Transform parameters not known for this ellipsoid');
        }

        $this->transformDatum(RefEll::wgs84(), $tx, $ty, $tz, $s, $rx, $ry, $rz);
    }

    /**
     * Transform co-ordinates from one datum to another using a Helmert transformation
     * @param RefEll $toRefEll
     * @param float $tranX
     * @param float $tranY
     * @param float $tranZ
     * @param float $scale
     * @param float $rotX  rotation about x-axis in seconds
     * @param float $rotY  rotation about y-axis in seconds
     * @param float $rotZ  rotation about z-axis in seconds
     * @return mixed
     */
    public function transformDatum(RefEll $toRefEll, $tranX, $tranY, $tranZ, $scale, $rotX, $rotY, $rotZ)
    {

        if ($this->refEll == $toRefEll) {
            return;
        }

        $cartesian = Cartesian::fromLatLong($this);
        $cartesian->transformDatum($toRefEll, $tranX, $tranY, $tranZ, $scale, $rotX, $rotY, $rotZ);
        $newLatLng = $cartesian->toLatitudeLongitude();

        $this->lat = $newLatLng->getLat();
        $this->lng = $newLatLng->getLng();
        $this->h = $newLatLng->getH();
        $this->refEll = $newLatLng->getRefEll();
    }


    /**
     * Convert this LatLng object into an OSGB grid reference. Note that this
     * function does not take into account the bounds of the OSGB grid -
     * beyond the bounds of the OSGB grid, the resulting OSRef object has no
     * meaning
     *
     * Reference values for transformation are taken from OS document
     * "A Guide to Coordinate Systems in Great Britain"
     *
     * @return OSRef
     */
    public function toOSRef()
    {
        $this->toOSGB36();

        $OSGB = new OSRef(0, 0); //dummy to get reference data
        $scale = $OSGB->getScaleFactor();
        $N0 = $OSGB->getOriginNorthing();
        $E0 = $OSGB->getOriginEasting();
        $phi0 = $OSGB->getOriginLatitude();
        $lambda0 = $OSGB->getOriginLongitude();

        $coords = $this->toTransverseMercatorEastingNorthing($scale, $E0, $N0, $phi0, $lambda0);

        return new OSRef(round($coords['E']), round($coords['N']), $this->h);
    }

    /**
     * Convert this LatLng object into an ITM grid reference
     *
     * @return ITMRef
     */
    public function toITMRef()
    {
        $this->toWGS84();

        $ITM = new ITMRef(0, 0); //dummy to get reference data
        $scale = $ITM->getScaleFactor();
        $N0 = $ITM->getOriginNorthing();
        $E0 = $ITM->getOriginEasting();
        $phi0 = $ITM->getOriginLatitude();
        $lambda0 = $ITM->getOriginLongitude();

        $coords = $this->toTransverseMercatorEastingNorthing($scale, $E0, $N0, $phi0, $lambda0);

        return new ITMRef(round($coords['E']), round($coords['N']), $this->h);
    }


    /**
     * Convert a WGS84 latitude and longitude to an UTM reference
     *
     * Reference values for transformation are taken from OS document
     * "A Guide to Coordinate Systems in Great Britain"
     * @return UTMRef
     */
    public function toUTMRef()
    {
        $this->toWGS84();

        $longitudeZone = (int)(($this->lng + 180) / 6) + 1;

        // Special zone for Norway
        if ($this->lat >= 56 && $this->lat < 64 && $this->lng >= 3 && $this->lng < 12) {
            $longitudeZone = 32;
        } elseif ($this->lat >= 72 && $this->lat < 84) { // Special zones for Svalbard
            if ($this->lng >= 0 && $this->lng < 9) {
                $longitudeZone = 31;
            } elseif ($this->lng >= 9 && $this->lng < 21) {
                $longitudeZone = 33;
            } elseif ($this->lng >= 21 && $this->lng < 33) {
                $longitudeZone = 35;
            } elseif ($this->lng >= 33 && $this->lng < 42) {
                $longitudeZone = 37;
            }
        }

        $UTMZone = $this->getUTMLatitudeZoneLetter($this->lat);

        $UTM = new UTMRef(0, 0, 0, $UTMZone, $longitudeZone); //dummy to get reference data
        $scale = $UTM->getScaleFactor();
        $N0 = $UTM->getOriginNorthing();
        $E0 = $UTM->getOriginEasting();
        $phi0 = $UTM->getOriginLatitude();
        $lambda0 = $UTM->getOriginLongitude();

        $coords = $this->toTransverseMercatorEastingNorthing($scale, $E0, $N0, $phi0, $lambda0);

        if ($this->lat < 0) {
            $coords['N'] += 10000000;
        }

        return new UTMRef(round($coords['E']), round($coords['N']), $this->h, $UTMZone, $longitudeZone);
    }

    /**
     * Work out the UTM latitude zone from the latitude
     * @param float $latitude
     * @return string
     */
    private function getUTMLatitudeZoneLetter($latitude)
    {

        if ($latitude < -80 || $latitude > 84) {
            throw new \OutOfRangeException('UTM zones do not apply in polar regions');
        }

        $zones = "CDEFGHJKLMNPQRSTUVWXX";
        $zoneIndex = (int)(($latitude + 80) / 8);
        return $zones[$zoneIndex];
    }


    /**
     * Convert a latitude and longitude to easting and northing using a Transverse Mercator projection
     * Formula for transformation is taken from OS document
     * "A Guide to Coordinate Systems in Great Britain"
     *
     * @param float $scale scale factor on central meridian
     * @param float $originEasting easting of true origin
     * @param float $originNorthing northing of true origin
     * @param float $originLat latitude of true origin
     * @param float $originLong longitude of true origin
     * @return array
     */
    public function toTransverseMercatorEastingNorthing(
        $scale,
        $originEasting,
        $originNorthing,
        $originLat,
        $originLong
    ) {

        $originLat = deg2rad($originLat);
        $originLong = deg2rad($originLong);

        $lat = deg2rad($this->lat);
        $sinLat = sin($lat);
        $cosLat = cos($lat);
        $tanLat = tan($lat);
        $tanLatSq = pow($tanLat, 2);
        $long = deg2rad($this->lng);

        $n = ($this->refEll->getMaj() - $this->refEll->getMin()) / ($this->refEll->getMaj() + $this->refEll->getMin());
        $nSq = pow($n, 2);
        $nCu = pow($n, 3);

        $v = $this->refEll->getMaj() * $scale * pow(1 - $this->refEll->getEcc() * pow($sinLat, 2), -0.5);
        $p = $this->refEll->getMaj() * $scale * (1 - $this->refEll->getEcc()) * pow(1 - $this->refEll->getEcc() * pow($sinLat, 2), -1.5);
        $hSq = (($v / $p) - 1);

        $latPlusOrigin = $lat + $originLat;
        $latMinusOrigin = $lat - $originLat;

        $longMinusOrigin = $long - $originLong;

        $M = $this->refEll->getMin() * $scale
            * ((1 + $n + 1.25 * ($nSq + $nCu)) * $latMinusOrigin
                - (3 * ($n + $nSq) + 2.625 * $nCu) * sin($latMinusOrigin) * cos($latPlusOrigin)
                + 1.875 * ($nSq + $nCu) * sin(2 * $latMinusOrigin) * cos(2 * $latPlusOrigin)
                - (35 / 24 * $nCu * sin(3 * $latMinusOrigin) * cos(3 * $latPlusOrigin)));

        $I = $M + $originNorthing;
        $II = $v / 2 * $sinLat * $cosLat;
        $III = $v / 24 * $sinLat * pow($cosLat, 3) * (5 - $tanLatSq + 9 * $hSq);
        $IIIA = $v / 720 * $sinLat * pow($cosLat, 5) * (61 - 58 * $tanLatSq + pow($tanLatSq, 2));
        $IV = $v * $cosLat;
        $V = $v / 6 * pow($cosLat, 3) * ($v / $p - $tanLatSq);
        $VI = $v / 120 * pow($cosLat, 5) * (5 - 18 * $tanLatSq + pow($tanLatSq, 2) + 14 * $hSq - 58 * $tanLatSq * $hSq);

        $E = $originEasting + $IV * $longMinusOrigin + $V * pow($longMinusOrigin, 3) + $VI * pow($longMinusOrigin, 5);
        $N = $I + $II * pow($longMinusOrigin, 2) + $III * pow($longMinusOrigin, 4) + $IIIA * pow($longMinusOrigin, 6);

        return array('E' => $E, 'N' => $N);
    }
}


/**
 * ECEF Cartesian coordinate
 * @author Doug Wright
 * @package PHPCoord
 */
class Cartesian
{

    /**
     * X
     * @var float
     */
    protected $x;

    /**
     * Y
     * @var float
     */
    protected $y;

    /**
     * Z
     * @var float
     */
    protected $z;

    /**
     * Reference ellipsoid used in this datum
     * @var RefEll
     */
    protected $refEll;

    /**
     * Cartesian constructor.
     * @param float $x
     * @param float $y
     * @param float $z
     * @param RefEll $refEll
     */
    public function __construct($x, $y, $z, RefEll $refEll)
    {
        $this->setX($x);
        $this->setY($y);
        $this->setZ($z);
        $this->setRefEll($refEll);
    }

    /**
     * String version of coordinate.
     * @return string
     */
    public function __toString()
    {
        return "({$this->x}, {$this->y}, {$this->z})";
    }

    /**
     * @return float
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @param float $x
     */
    public function setX($x)
    {
        $this->x = $x;
    }

    /**
     * @return float
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param float $y
     */
    public function setY($y)
    {
        $this->y = $y;
    }

    /**
     * @return float
     */
    public function getZ()
    {
        return $this->z;
    }

    /**
     * @param float $z
     */
    public function setZ($z)
    {
        $this->z = $z;
    }

    /**
     * @return RefEll
     */
    public function getRefEll()
    {
        return $this->refEll;
    }

    /**
     * @param RefEll $refEll
     */
    public function setRefEll($refEll)
    {
        $this->refEll = $refEll;
    }

    /**
     * Convert these coordinates into a latitude, longitude
     * Formula for transformation is taken from OS document
     * "A Guide to Coordinate Systems in Great Britain"
     *
     * @return LatLng
     */
    public function toLatitudeLongitude()
    {

        $lambda = rad2deg(atan2($this->y, $this->x));

        $p = sqrt(pow($this->x, 2) + pow($this->y, 2));

        $phi = atan($this->z / ($p * (1 - $this->refEll->getEcc())));

        do {
            $phi1 = $phi;
            $v = $this->refEll->getMaj() / (sqrt(1 - $this->refEll->getEcc() * pow(sin($phi), 2)));
            $phi = atan(($this->z + ($this->refEll->getEcc() * $v * sin($phi))) / $p);
        } while (abs($phi - $phi1) >= 0.00001);

        $h = $p / cos($phi) - $v;

        $phi = rad2deg($phi);

        return new LatLng($phi, $lambda, $h, $this->refEll);
    }

    /**
     * Convert a latitude, longitude height to x, y, z
     * Formula for transformation is taken from OS document
     * "A Guide to Coordinate Systems in Great Britain"
     *
     * @param LatLng $latLng
     * @return Cartesian
     */
    public static function fromLatLong(LatLng $latLng)
    {

        $a = $latLng->getRefEll()->getMaj();
        $eSquared = $latLng->getRefEll()->getEcc();
        $phi = deg2rad($latLng->getLat());
        $lambda = deg2rad($latLng->getLng());

        $v = $a / (sqrt(1 - $eSquared * pow(sin($phi), 2)));
        $x = ($v + $latLng->getH()) * cos($phi) * cos($lambda);
        $y = ($v + $latLng->getH()) * cos($phi) * sin($lambda);
        $z = ((1 - $eSquared) * $v + $latLng->getH()) * sin($phi);

        return new static($x, $y, $z, $latLng->getRefEll());
    }

    /**
     * Transform the datum used for these coordinates by using a Helmert Transform
     * @param RefEll $toRefEll
     * @param float $tranX
     * @param float $tranY
     * @param float $tranZ
     * @param float $scale
     * @param float $rotX  rotation about x-axis in radians
     * @param float $rotY  rotation about y-axis in radians
     * @param float $rotZ  rotation about z-axis in radians
     * @return mixed
     */
    public function transformDatum(RefEll $toRefEll, $tranX, $tranY, $tranZ, $scale, $rotX, $rotY, $rotZ)
    {

        $x = $tranX + ($this->getX() * (1 + $scale)) - ($this->getY() * $rotZ) + ($this->getZ() * $rotY);
        $y = $tranY + ($this->getX() * $rotZ) + ($this->getY() * (1 + $scale)) - ($this->getZ() * $rotX);
        $z = $tranZ - ($this->getX() * $rotY) + ($this->getY() * $rotX) + ($this->getZ() * (1 + $scale));

        $this->setX($x);
        $this->setY($y);
        $this->setZ($z);
        $this->setRefEll($toRefEll);
    }
}


/**
 * Reference ellipsoid
 * @author Jonathan Stott
 * @author Doug Wright
 * @package PHPCoord
 */
class RefEll
{

    /**
     * Major axis
     * @var float
     */
    protected $maj;

    /**
     * Minor axis
     * @var float
     */
    protected $min;

    /**
     * Eccentricity
     * @var float
     */
    protected $ecc;

    /**
     * Create a new RefEll object to represent a reference ellipsoid
     *
     * @param float $maj the major axis
     * @param float $min the minor axis
     */
    public function __construct($maj, $min)
    {
        $this->maj = $maj;
        $this->min = $min;
        $this->ecc = (($maj * $maj) - ($min * $min)) / ($maj * $maj);
    }

    /**
     * @return float
     */
    public function getMaj()
    {
        return $this->maj;
    }

    /**
     * @return float
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return float
     */
    public function getEcc()
    {
        return $this->ecc;
    }


    /**
     * Helper function to create Airy1830 ellipsoid used in GB
     * @return RefEll
     */
    public static function airy1830()
    {
        return new RefEll(6377563.396, 6356256.909);
    }

    /**
     * Helper function to create Airy Modified ellipsoid used by Ireland
     * @return RefEll
     */
    public static function airyModified()
    {
        return new RefEll(6377340.189, 6356034.447);
    }

    /**
     * Helper function to create WGS84 ellipsoid
     * @return RefEll
     */
    public static function wgs84()
    {
        return new RefEll(6378137, 6356752.314245);
    }

    /**
     * Helper function to create GRS80 ellipsoid
     * @return RefEll
     */
    public static function grs80()
    {
        return new RefEll(6378137, 6356752.314140);
    }

    /**
     * Helper function to create Clarke1866 ellipsoid
     * @return RefEll
     */
    public static function clarke1866()
    {
        return new RefEll(6378206.4, 6356583.8);
    }

    /**
     * Helper function to create International 1924 (Hayford) ellipsoid
     * @return RefEll
     */
    public static function international1924()
    {
        return new RefEll(6378388.0, 6356911.9);
    }

    /**
     * Helper function to create Bessel 1841 ellipsoid
     * @return RefEll
     */
    public static function bessel1841()
    {
        return new RefEll(6377397.155, 6356078.963);
    }
}