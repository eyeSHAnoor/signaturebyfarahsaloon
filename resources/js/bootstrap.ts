import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const reverbKey = import.meta.env.VITE_REVERB_APP_KEY?.trim();

if (reverbKey) {
    const scheme = import.meta.env.VITE_REVERB_SCHEME;
    const port = Number(import.meta.env.VITE_REVERB_PORT || (scheme === 'https' ? 443 : 8080));

    window.Pusher = Pusher;
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
        wsPort: port,
        wssPort: port,
        forceTLS: scheme ? scheme === 'https' : window.location.protocol === 'https:',
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
    });
} else {
    window.Echo = undefined;
    console.info('Realtime broadcasting is disabled because VITE_REVERB_APP_KEY is not configured.');
}
