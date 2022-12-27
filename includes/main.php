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

        // for testing
        // $this->day = 'Friday';
        // $this->time = '20:00';

        // Allow admin section to be used
        if (strpos($_SERVER['REQUEST_URI'], 'wp-admin') || strpos($_SERVER['REQUEST_URI'], 'wp-login')) {
            return true;    
        }

        // Set settings form DB
        $this->get_settings();
        
        // Start date check
        $check_day = $this->check_day();
        $this->is_site_closed($check_day);
    }

    private function get_settings() {
        $settings = $this->data->get_results("SELECT * FROM ". SHABBAT_CLOSE_SETTINGS ." WHERE `id` = 1");
        if (!empty($settings)) {
            $this->setting = $settings[0];
        }
    }

    private function check_day()
    {
        if (in_array($this->day, $this->day_arr)) {
            return $this->check_time();
        }
        return false;
    }

    private function check_time()
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

    public function is_site_closed($closed)
    {
        if ($closed === true) {
            $page = get_post($this->setting->redirect_url);
            // echo "<pre>";
            // print_r($page);
            // die;
            $this->render_page($page);
        } else if ($closed === 'default') {
            $this->render_default();
        }
    }
    
    public function render_default()
    {
        load_template(plugin_dir_path(__DIR__)."/views/closed.php", true, $this->setting);
        exit;
    }
    
    public function render_page($page)
    {
        load_template(plugin_dir_path(__DIR__)."/views/closed_page.php", true, [$this->setting, $page]);
        exit;
    }
}
