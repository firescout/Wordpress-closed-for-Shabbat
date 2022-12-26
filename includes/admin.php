<?php
class ShabbatAdmin extends ShabbatClose
{
	public $data;
    public $form_data;

	public function __construct()
    {
		add_action('admin_menu',  array( $this, 'add_menu_item' ));
		$this->data = new ShabbatData();
        $this->form_data = self::get_admin_settings();
    }
	
	public function add_menu_item()
    {
		//create new submenu item
		add_submenu_page(
			'options-general.php',
			__( 'Shabbat Settings' ),
			__( 'Shabbat Settings' ),
			'administrator',
			'shabbat-settings',
			array($this, 'settings_page')
		);
	}

	public function settings_page()
    {
        $json = file_get_contents(plugin_dir_path(__DIR__).'/dist/counties.json');
        $list = json_decode($json);

        if (isset($_POST['geo_name_id'])) {
            self::admin_form_post();
        }
	?>
	<div class="wrap">
		<h1>Shabbat Settings</h1>
		<hr />
        <p>
            <strong>Default Settings</strong><br />
            This plugin auto close the site on Friday at 17:00, and will reopen on Saturday at 18:00 using server time.
        </p>
        <p>
            <strong>For Dynamic Settings</strong><br />
            Set the Geo Name and the plugin will use the <a href="https://www.hebcal.com/shabbat" target="blank">Hebcal API</a>, site will close on candle lighting and reopen on Havdala times.<br />
            Set redirect page for the plugin to redirect user once closed.<br /><br />

            If start and end times are set the plugin will discard the API or server times and use these instead.
        </p>
        <form method="post">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Geo Name ID </th>
                    <td>
                        <select name="geo_name_id">
                            <option value="0">Select Location</option>
                            <?php foreach($list->data as $option) : ?>
                                <?php if ($this->form_data['geo_name_id'] == $option->value) : ?>
                                    <option value="<?= $option->value; ?>" selected="true"><?= $option->name; ?></option>
                                <?php else : ?>
                                    <option value="<?= $option->value; ?>"><?= $option->name; ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Start Time </th>
                    <td><input type="text" name="start_time" placeholder="17:00" value="<?= $this->form_data['start_time']; ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">End time</th>
                    <td><input type="text" name="end_time" placeholder="18:00" value="<?= $this->form_data['end_time']; ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        Load page content
                    </th>
                    <td><?php wp_dropdown_pages(array('name'=>'redirect_url', 'show_option_none'=>'Select Custom Page', 'option_none_value'=>'Select Custom Page', 'option_none_value'=>'0', 'selected'=>$this->form_data['redirect_url']));?></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="hidden" name="id" value="1" />
                        <?php submit_button(); ?>
                    </td>
                </tr>
            </table>
        </form>
	</div>
	<?php }

    private function admin_form_post() 
    {
        $data = [
            'id' => 1,
            'geo_name_id' => 0,
            'start_time' => NULL,
            'end_time' => NULL,
            'redirect_url' => 0
        ];
        if (isset($_POST["geo_name_id"])) {
            foreach ($data as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $key);
            }

            $setting = $this->data->get_results("SELECT * FROM ". SHABBAT_CLOSE_SETTINGS ." WHERE `id` = 1");
            if (!empty($setting)) {
                $this->data->update($data, array('id'=> $data['id']));
            } else {
                $this->data->insert($data);
            }
        }
        $this->form_data = $data;
    }

    private function get_admin_settings() {
        $data = [
            'id' => 1,
            'geo_name_id' => 0,
            'start_time' => NULL,
            'end_time' => NULL,
            'redirect_url' => 0
        ];
        $setting = $this->data->get_results("SELECT * FROM ". SHABBAT_CLOSE_SETTINGS ." WHERE `id` = 1");
        if (!empty($setting)) {
            $item = $setting[0];
            $data['geo_name_id'] = $item->geo_name_id;
            $data['start_time'] = $item->start_time;
            $data['end_time'] = $item->end_time;
            $data['redirect_url'] = $item->redirect_url;
        }
        return $data;
    }
}