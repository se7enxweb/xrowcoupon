<?php

class xrowCoupon
{
    public static $code = false;

    function __construct( $code = false )
	{
		self::$code = strtoupper( $code );
	}

    public static function fetchAttribute()
    {
        $db = eZDB::instance();
        $codeEscaped = $db->escapeString( self::$code );

        $sql = "SELECT * FROM ezcontentobject_attribute e, ezcontentobject e1
                            WHERE e1.current_version = e.version
                            AND e.contentobject_id = e1.id
                            AND e.data_type_string = 'ezcoupon' AND e1.status = 1
                            AND e.data_text like ( '$codeEscaped;%' );";
        $result = $db->arrayQuery( $sql );
        if ( isset( $result[0]['contentobject_id'] ) )
        {
            //#$obj = eZContentObject::fetch( $result[0]['contentobject_id'] );
            return new eZContentObjectAttribute( $result[0] );
        }
        else
        {
            return null;
        }
    }

    public static function isValid()
    {
        $attribute = self::fetchAttribute();
        if ( is_object( $attribute ) )
        {
            $data = $attribute->content();
            $time = new eZDate();

            if ( $time->isGreaterThan( $data['from'], true ) and !$time->isGreaterThan( $data['till'] ) )
            {
                return true;
            }
        }
        return false;
    }
}

?>