<?php

class POC_API
{
    protected $api_url;

    public function __construct()
    {
        $this->api_url = 'https://api.hostletter.com/api';
    }

    public function call( $path, $method = 'GET', $data = [] )
    {
        $url = $this->build_path( $path ) ;

        $method = strtolower( $method );

        if($method === 'post') {
            return wp_remote_post( $url, array(
                'body' => $data
            ) );
        }

        if( ! empty( $data ) ) {
            $url = sprintf("%s?%s", $url, http_build_query( $data ) );
        }

        return wp_remote_get( $url );
    }

    public function get_pro_version()
    {
        return $this->parse_response(
            $this->call( 'site/poc_version' )
        );
    }

    protected function build_path( $path )
    {
        return rtrim( $this->api_url, '/' ) . '/' . $path;
    }

    protected function parse_response( $response )
    {
        if( is_wp_error( $response ) ) {
            return null;
        }

        $response = json_decode( $response['body'], true );

        if( $response['status'] != 1 ) {
            return null;
        }

        return $response['data'];
    }
}