<?php

namespace WLM\Capabilities;

class BookCapabilities
{
    private const CAPABILITIES = [
        'edit_book',
        'read_book',
        'delete_book',

        'edit_books',
        'edit_others_books',
        'publish_books',
        'read_private_books',

        'delete_books',
        'delete_private_books',
        'delete_published_books',
        'delete_others_books',

        'edit_private_books',
        'edit_published_books',
    ];

    public static function addToAdministrator(): void
    {
        $role = get_role('administrator');

        if (! $role) {
            return;
        }

        foreach (self::CAPABILITIES as $capability) {
            $role->add_cap($capability);
        }
    }

    public static function createLibraryManagerRole(): void
    {
        add_role(
            'library_manager',
            'Library Manager',
            [
                'read' => true,

                'edit_books' => true,
                'edit_others_books' => true,

                'publish_books' => true,

                'read_private_books' => true,

                'delete_books' => true,
                'delete_others_books' => true,

                'edit_published_books' => true,
                'delete_published_books' => true,
            ]
        );
    }
}
