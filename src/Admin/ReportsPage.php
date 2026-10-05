<?php

namespace WLM\Admin;

use WLM\Contracts\Bootable;
use WLM\Services\ReportService;

class ReportsPage implements Bootable
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function boot(): void
    {
        add_action(
            'admin_menu',
            [$this, 'registerMenu']
        );

        add_action(
            'admin_post_wlm_recalculate_reports',
            [$this, 'handleRecalculate']
        );
    }

    public function registerMenu(): void
    {
        add_menu_page(
            'Library Manager',
            'Library Manager',
            'edit_books',
            'wlm-reports',
            [$this, 'render'],
            'dashicons-book',
            25
        );
    }

    public function render(): void
    {
        if (! current_user_can('edit_books')) {
            wp_die('You are not allowed to access this page.');
        }

        $counts = $this->reportService->getData();

        $published = (int) ($counts['published'] ?? 0);
        $drafts = (int) ($counts['drafts'] ?? 0);

        $genre = isset($_GET['genre'])
            ? sanitize_key($_GET['genre'])
            : '';

        $genres = get_terms([
            'taxonomy'   => 'genre',
            'hide_empty' => false,
        ]);

        $query_args = [
            'post_type'      => 'book',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ];

        if ($genre !== '') {
            $query_args['tax_query'] = [
                [
                    'taxonomy' => 'genre',
                    'field'    => 'slug',
                    'terms'    => $genre,
                ],
            ];
        }

        $query = new \WP_Query($query_args);

?>
        <div class="wrap">

            <h1>Library Reports</h1>

            <?php
            if (
                isset($_GET['recalculated']) &&
                $_GET['recalculated'] === '1'
            ):
            ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        Statistics recalculated successfully.
                    </p>
                </div>
            <?php endif; ?>

            <h2>Overview</h2>

            <p>
                Published books:
                <strong>
                    <?php echo esc_html($published); ?>
                </strong>
            </p>

            <p>
                Draft books:
                <strong>
                    <?php echo esc_html($drafts); ?>
                </strong>
            </p>

            <hr>

            <h2>Filter by Genre</h2>

            <form method="get">

                <input
                    type="hidden"
                    name="page"
                    value="wlm-reports">

                <label for="wlm-genre">
                    Genre:
                </label>

                <select
                    id="wlm-genre"
                    name="genre">
                    <option value="">
                        All genres
                    </option>

                    <?php if (! is_wp_error($genres)): ?>

                        <?php foreach ($genres as $genre_term): ?>

                            <option
                                value="<?php echo esc_attr($genre_term->slug); ?>"
                                <?php selected($genre, $genre_term->slug); ?>>
                                <?php echo esc_html($genre_term->name); ?>
                            </option>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </select>

                <?php
                submit_button(
                    'Filter',
                    'secondary',
                    '',
                    false
                );
                ?>

            </form>

            <p>
                Matching books:
                <strong>
                    <?php echo esc_html($query->found_posts); ?>
                </strong>
            </p>

            <hr>

            <h2>Statistics</h2>

            <form
                method="post"
                action="<?php echo esc_url(
                            admin_url('admin-post.php')
                        ); ?>">

                <input
                    type="hidden"
                    name="action"
                    value="wlm_recalculate_reports">

                <?php
                wp_nonce_field(
                    'wlm_recalculate_reports',
                    'wlm_reports_nonce'
                );
                ?>

                <?php
                submit_button(
                    'Recalculate Statistics',
                    'secondary'
                );
                ?>

            </form>

        </div>
<?php
    }

    public function handleRecalculate(): void
    {
        check_admin_referer(
            'wlm_recalculate_reports',
            'wlm_reports_nonce'
        );

        if (! current_user_can('edit_books')) {
            wp_die('You are not allowed to do this.');
        }

        $this->reportService->clearCache();

        $this->reportService->getData();

        wp_safe_redirect(
            add_query_arg(
                [
                    'page'         => 'wlm-reports',
                    'recalculated' => '1',
                ],
                admin_url('admin.php')
            )
        );

        exit;
    }
}
