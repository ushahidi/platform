<?php

namespace Symm\Gisconverter\Decoders;

use Symm\Gisconverter\Exceptions\UnavailableResource;
use Symm\Gisconverter\Exceptions\InvalidText;
use Symm\Gisconverter\Geometry\GeometryCollection;

abstract class XML extends Decoder
{
    public static function geomFromText($text)
    {
        if (!function_exists("simplexml_load_string") || !function_exists("libxml_use_internal_errors")) {
            throw new UnavailableResource("simpleXML");
        }

        libxml_use_internal_errors(true);

        $xmlobj = simplexml_load_string($text);
        if ($xmlobj === false) {
            throw new InvalidText(__CLASS__, $text);
        }

        try {
            $geom = static::geomFromXML($xmlobj);
        } catch (InvalidText $e) {
            throw new InvalidText(__CLASS__, $text);
        } catch (\Exception $e) {
            throw $e;
        }

        $PlaceAttributes=array();

        // Parses possible attributes using Children method
        foreach ($xmlobj->children() as $attribute) {
            $propvalue =$xmlobj->{$attribute->getName()};
            if ( $propvalue->children()->count()===0 ) {
                $PlaceAttributes[strval($attribute->getName())] = strval($propvalue->__toString());
            }
        }

        // Parses possible attributes using ExtendedData
        if ($xmlobj->ExtendedData) {
            foreach ( $xmlobj->ExtendedData->children() as $Element ) {
                foreach ( $Element->children() as $attribute ) {
                    if($attribute->attributes()->name) $PlaceAttributes[strval($attribute->attributes()->name)] =strval($attribute->__toString());
                }

                if ( empty( $Element->displayName ) ) {
                    if ( $Element->attributes()->name ) $PlaceAttributes[strval($Element->attributes()->name)] =strval($Element->value);
                } else {
                    $PlaceAttributes[strval($Element->displayName)] =strval($Element->value);
                }
            }
        }

        $geom->setAttributes($PlaceAttributes);

        return $geom;
    }

    protected static function childElements($xml, $nodename = "")
    {
        $nodename = strtolower($nodename);
        $res = array();

        foreach ($xml->children() as $child) {
            if ($nodename) {
                if (strtolower($child->getName()) == $nodename) {
                    array_push($res, $child);
                }
            } else {
                array_push($res, $child);
            }
        }

        return $res;
    }

    protected static function childsCollect($xml)
    {
        $components = array();

        foreach (static::childElements($xml) as $child) {
            try {
                $geom = static::geomFromXML($child);
                $components[] = $geom;
            } catch (InvalidText $e) {

            }
        }

        $ncomp = count($components);
        if ($ncomp == 0) {
            throw new InvalidText(__CLASS__);
        } elseif ($ncomp == 1) {
            return $components[0];
        } else {
            return new GeometryCollection($components);
        }
    }

    protected static function geomFromXML($xml)
    {

    }
}
