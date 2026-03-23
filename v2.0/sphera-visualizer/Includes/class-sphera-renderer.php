<?php

if (!defined('ABSPATH')) {
    exit;
}

class Sphera_Renderer {
    public static function get_default_markup(): string {
        return '<div class="sphera-renderer"></div>';
    }
}