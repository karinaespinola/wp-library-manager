<?php

namespace WLM;

use WLM\PostTypes\BookPostType;
use WLM\Taxonomies\GenreTaxonomy;
use WLM\MetaBoxes\BookDetailsMetaBox;
use WLM\Taxonomies\LanguageTaxonomy;
use WLM\Filters\BookTitleFilter;
use WLM\Contracts\Bootable;
use WLM\Repositories\BookRepository;
use WLM\Shortcodes\BooksShortcode;
use WLM\Assets\FrontendAssets;
use WLM\Rest\BooksController;

defined('ABSPATH') || exit;

class Plugin
{
    /**
     * @var Bootable[]
     */    
    private array $services = [];

    public function __construct()
    {
        $this->services = [
            new BookPostType(),
            new GenreTaxonomy(),
            new LanguageTaxonomy(),
            new BookTitleFilter(),
            new BookDetailsMetaBox(),
            new BooksShortcode(new BookRepository()),
            new FrontendAssets(),
            new BooksController(new BookRepository()),
        ];
    }

    public function boot(): void
    {
        foreach ($this->services as $service) {
            $service->boot();
        }
    }

    public static function activate(): void
    {
        add_option(
        'wlm_books_per_page',
        12
        );

        $book_post_type = new PostTypes\BookPostType();
        $book_post_type->register();
        flush_rewrite_rules();
    }

    public static function deactivate(): void
    {
        flush_rewrite_rules();
    }
}