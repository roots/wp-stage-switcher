<?php

class WP_Admin_Bar
{
    public array $menus = [];

    public function add_menu(array $args): void
    {
        $this->menus[$args['id']] = $args;
    }
}
