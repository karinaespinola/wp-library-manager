<?php

namespace WLM\Services;

use WLM\Contracts\Bootable;

class ReportService implements Bootable
{
    private const CACHE_KEY = 'wlm_report_data';

    public function boot(): void
    {
        add_action(
            'save_post_book',
            [$this, 'clearCache']
        );
    }

    public function getData(): array
    {
        $data = get_transient(self::CACHE_KEY);

        if ($data !== false) {
            return $data;
        }

        $data = $this->calculate();

        set_transient(
            self::CACHE_KEY,
            $data,
            HOUR_IN_SECONDS
        );

        return $data;
    }

    private function calculate(): array
    {
        $counts = wp_count_posts('book');

        return [
            'published' => (int) ($counts->publish ?? 0),
            'drafts'    => (int) ($counts->draft ?? 0),
        ];
    }

    public function clearCache(): void
    {
        delete_transient(self::CACHE_KEY);
    }
}
