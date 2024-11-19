import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST.replace(/"/g, ''),
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: true,
    enabledTransports: ['wss', 'ws'],
});

window.Echo.connector.pusher.connection.bind('connected', function() {
    console.log('Successfully connected to Reverb server!');
});

window.Echo.connector.pusher.connection.bind('disconnected', function(reason) {
    console.error('Disconnected: ', reason);
});

window.Echo.connector.pusher.connection.bind('error', function(error) {
    console.error('Connection error: ', error);
});
