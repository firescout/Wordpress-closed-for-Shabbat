<?php
class ShabbatClose
{
    private $day;
    public $day_arr;
    private $time;
    public $data;
    public $admin;
    private $setting;

    public function __construct()
    {
        $this->data = new ShabbatData();
        $this->admin = new ShabbatAdmin();

        $this->day = date('l');
        $this->day_arr = array('Friday','Saturday');
        $this->time = date('H:i');

        // for testings
        $this->day = 'Friday';
        $this->time = '20:00';

        // Allow admin section to be used
        if (strpos($_SERVER['REQUEST_URI'], 'wp-admin') || strpos($_SERVER['REQUEST_URI'], 'wp-login')) {
            return true;    
        }

        // Set settings form DB
        $this->get_settings();
        
        // Start date check
        $check_day = $this->checkDay();
        $this->isSiteClosed($check_day);
    }

    private function get_settings() {
        $settings = $this->data->get_results("SELECT * FROM ". SHABBAT_CLOSE_SETTINGS ." WHERE `id` = 1");
        if (!empty($settings)) {
            $this->setting = $settings[0];
        }
    }

    private function checkDay()
    {
        if (in_array($this->day, $this->day_arr)) {
            return $this->checkTime();
        }
        return false;
    }

    private function checkTime()
    {
        if (empty($this->setting)) {
            if ($this->day == 'Friday' && $this->time > '17:00' || $this->day == 'Saturday' && $this->time < '18:00') {
                return 'default';
            } else {
                return false;
            }
        }
        if ($this->day == 'Friday' && $this->time > $this->setting->start_time || $this->day == 'Saturday' && $this->time < $this->setting->end_time) {
            if ($this->setting->redirect_url != 0) {
                return true;
            } else {
                return 'default';
            }
        } else {
            return false;
        }
    }

    public function isSiteClosed($closed)
    {
        if ($closed === true) {
            $page = get_post($this->setting->redirect_url);
            $this->renderPage($page);
        } else if ($closed === 'default') {
            $this->renderDefault();
        }
    }
    
    public function renderDefault()
    {
        load_template(plugin_dir_path(__DIR__)."/views/closed.php", true, $this->setting);
        exit;
    }

    public function renderPage($page)
    {
        load_template(plugin_dir_path(__DIR__)."/views/closed_page.php", true, [$this->setting, $page]);
        exit;
    }
}