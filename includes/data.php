<?php
class ShabbatData extends ShabbatClose
{
    private $wpdb;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        if (!defined('SHABBAT_CLOSE_SETTINGS')) {
            define('SHABBAT_CLOSE_SETTINGS', self::table_name('shabbat_close_settings'));
        }
    }

    public function table_name($table)
    {
        $result = $this->wpdb->base_prefix.$table;
        return $result;
    }

    public function get_results($sql)
    {
        $result = $this->wpdb->get_results($sql);
        return $result;
    }
    
    public function insert($data)
    {
        $this->wpdb->insert(SHABBAT_CLOSE_SETTINGS, $data);
    }

    public function update($data, $where)
    {
        $this->wpdb->update(SHABBAT_CLOSE_SETTINGS, $data, $where);
    }
}