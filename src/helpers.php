<?php

/**
 * 基于 Latte 的字符串模版
 *
 * @param $template string 模版字符串
 * @param $params   array 模版数据
 * @param $string   boolean 是否渲染为字符串而不是直接输出
 */
if ( ! function_exists( 'render' ) ) {
    function render( $template, $params, $string = false ) {
        $latte = new Latte\Engine;
        $latte->setTempDirectory( WP_CONTENT_DIR . '/cache/' );

        $latte->setLoader( new Latte\Loaders\StringLoader( [
            'template' => $template,
        ] ) );

        if ( $string ) {
            return $latte->renderToString( 'template', $params );
        } else {
            $latte->render( 'template', $params );
        }

        return true;
    }
}


/**
 * 判断是否在微信中打开
 */
if ( ! function_exists( 'is_wechat' ) ) {
    function is_wechat() {
        if ( ! empty( $_SERVER[ 'HTTP_USER_AGENT' ] ) && strpos( $_SERVER[ 'HTTP_USER_AGENT' ], 'MicroMessenger' ) !== false ) {
            return true;
        }

        return false;
    }
}


/**
 * 判断是否为 Ajax 请求
 *
 * @return bool
 */
if ( ! function_exists( 'is_ajax' ) ) {
    function is_ajax() {
        if ( ! empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) == 'xmlhttprequest' ) {
            return true;
        }

        return false;
    }
}

/**
 * 判断当前语言是否为英文
 *
 * @return bool
 */
if ( ! function_exists( 'is_en' ) ) {
    function is_en() {

        $lang = get_bloginfo( 'language' );

        if ( $lang == 'en-US' ) {
            return true;
        }

        return false;
    }
}

/**
 * 获取用户的真实 IP
 *
 * @return mixed
 */
if ( ! function_exists( 'get_ip' ) ) {
    function get_ip() {
        $client  = @$_SERVER[ 'HTTP_CLIENT_IP' ];
        $forward = @$_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
        $remote  = $_SERVER[ 'REMOTE_ADDR' ];

        if ( filter_var( $client, FILTER_VALIDATE_IP ) ) {
            $ip = $client;
        } elseif ( filter_var( $forward, FILTER_VALIDATE_IP ) ) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }
}


/**
 * 获取前端资源路径
 */
class JsonManifest {
    private $manifest;

    public function __construct( $manifest_path ) {

        if ( file_exists( $manifest_path ) ) {
            $this->manifest = json_decode( file_get_contents( $manifest_path ), true );
        } else {
            $this->manifest = [];
        }

    }

    public function get() {
        return $this->manifest;
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return array|mixed|null
     */
    public function getPath( $key = '', $default = null ) {
        $collection = $this->manifest;

        if ( is_null( $key ) ) {
            return $collection;
        }

        if ( isset( $collection[ $key ] ) ) {
            return $collection[ $key ];
        }

        foreach ( explode( '.', $key ) as $segment ) {
            if ( ! isset( $collection[ $segment ] ) ) {
                return $default;
            } else {
                $collection = $collection[ $segment ];
            }
        }

        return $collection;
    }

}


/**
 * 获取前端资源
 *
 * @param $filename string 文件名
 *
 * @return string 文件路径
 */
if ( ! function_exists( 'assets' ) ) {
    function asset( $filename ) {
        $dist_path = get_theme_file_uri('/front/dist/');
        $directory = dirname( $filename ) . '/';
        $file      = basename( $filename );
        static $manifest;

        if ( empty( $manifest ) ) {
            $manifest_path = get_theme_file_path('/front/dist/' . 'assets.json');
            $manifest      = new JsonManifest( $manifest_path );
        }

        if ( array_key_exists( $file, $manifest->get() ) ) {
            return $dist_path . $directory . $manifest->get()[ $file ];
        } else {
            return $dist_path . $directory . $file;
        }
    }
}