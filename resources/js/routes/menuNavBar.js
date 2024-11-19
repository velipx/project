import {
    mdiAccount,
    mdiCogOutline,
    mdiEmail,
    mdiLogout,
    mdiBell
} from '@mdi/js'

export default [
    {
        isCurrentUser: true,
        menu: [
            {
                icon: mdiAccount,
                label: 'My Profile',
                href: '/profile'
            },
            {
                icon: mdiCogOutline,
                label: 'Settings'
            },
            {
                icon: mdiEmail,
                label: 'Messages'
            },
            {
                isDivider: true
            },
            {
                icon: mdiLogout,
                label: 'Log Out',
                isLogout: true
            }
        ]
    },
    {
        icon: mdiBell,
        isNotificationBell: true,
        href: '#',
        menu: null // or any sub menu if needed
    }
]
