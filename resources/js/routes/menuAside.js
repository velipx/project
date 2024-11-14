import {
    mdiAccountMultiple,
    mdiMonitor,
    mdiPalette,
} from '@mdi/js'

export default [
    {
        route: 'admin.dashboard',
        icon: mdiMonitor,
        iconColor: '#3e90f0',
        label: 'Dashboard'
    },
    {
        route: 'admin.users',
        icon: mdiAccountMultiple,
        iconColor: '#8c6584',
        label: 'Users'
    },
    {
        icon: mdiPalette,
        iconColor: '#d84c10',
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
