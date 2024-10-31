import {
    mdiAccountMultiple,
    mdiMonitor,
    mdiPalette,
} from '@mdi/js'

export default [
    {
        route: 'admin.dashboard',
        icon: mdiMonitor,
        label: 'Dashboard'
    },
    {
        route: 'admin.users',
        icon: mdiAccountMultiple,
        label: 'Users'
    },
    {
        icon: mdiPalette,
        label: 'Roles & Permissions',
        menu: [
            {
                route: 'admin.roles',
                label: 'Roles'
            },
            {
                route: 'admin.permissions',
                label: 'Permissions'
            },
        ]
    },
]
