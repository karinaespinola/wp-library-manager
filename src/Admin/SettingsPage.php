<?php

namespace WLM\Admin;

use WLM\Contracts\Bootable;

class SettingsPage implements Bootable
{
    public function boot(): void
    {
        add_action(
            'admin_menu',
            [$this, 'registerMenu']
        );

        add_action(
            'admin_init',
            [$this, 'registerSettings']
        );
    }

    public function registerMenu(): void
    {
        add_options_page(
            'Library Manager',
            'Library Manager',
            'manage_options',
            'wlm-settings',
            [$this, 'render']
        );
    }

    public function registerSettings(): void
    {
        register_setting(
            'wlm_settings',
            'wlm_books_per_page',
            [
                'type'              => 'integer',
                'default'           => 12,
                'sanitize_callback' => 'absint',
            ]
        );

        register_setting(
            'wlm_settings',
            'wlm_public_api',
            [
                'type'              => 'boolean',
                'default'           => true,
                'sanitize_callback' => function ($value) {
                    return (bool) $value;
                },
            ]
        );

        add_settings_section(
            'wlm_general_section',
            'General Settings',
            [$this, 'renderGeneralSection'],
            'wlm-settings'
        );

        add_settings_field(
            'wlm_books_per_page',
            'Books per page',
            [$this, 'renderBooksPerPageField'],
            'wlm-settings',
            'wlm_general_section'
        );

        add_settings_field(
            'wlm_public_api',
            'Public REST API',
            [$this, 'renderPublicApiField'],
            'wlm-settings',
            'wlm_general_section'
        );
    }

    public function renderBooksPerPageField(): void
    {
        $value = get_option(
            'wlm_books_per_page',
            12
        );

?>
        <input
            type="number"
            name="wlm_books_per_page"
            value="<?php echo esc_attr($value); ?>"
            min="1"
            max="100">
    <?php
    }

    public function renderGeneralSection(): void
    {
        echo '<p>Configure general Library Manager settings.</p>';
    }

    public function render(): void
    {
    ?>
        <div class="wrap">

            <h1>Library Manager Settings</h1>

            <form
                method="post"
                action="options.php">

                <?php
                settings_fields('wlm_settings');

                do_settings_sections(
                    'wlm-settings'
                );

                submit_button();
                ?>

            </form>

        </div>
    <?php
    }

    public function renderPublicApiField(): void
    {
        $enabled = (bool) get_option(
            'wlm_public_api',
            true
        );

    ?>
        <label>
            <input
                type="hidden"
                name="wlm_public_api"
                value="0">
            <input
                type="checkbox"
                name="wlm_public_api"
                value="1"
                <?php checked($enabled); ?>>

            Allow public read access to books
        </label>
<?php
    }
}
