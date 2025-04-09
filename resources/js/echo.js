import Echo from 'laravel-echo'

import Pusher from 'pusher-js'

// const options = {
//   broadcaster: 'pusher',
//   key: '1ac728eb26e770e7b87d',
//   cluster: 'ap1',
// }
window.Pusher = Pusher

// window.Pusher = new Pusher('1ac728eb26e770e7b87d', options)
Pusher.logToConsole = true

export const pusher = new Pusher('1ac728eb26e770e7b87d', {
  cluster: 'ap1',
})

// window.Pusher = require('pusher-js')

// window.Echo = new Echo({
//   ...options,
//   client: new Pusher(options.key, options),
// })

// export const echoPusher = new Echo({
//   ...options,
//   client: new Pusher('1ac728eb26e770e7b87d', options),
// })

// window.Echo = new Echo({
//   broadcaster: 'pusher',
//   // key: import.meta.env.VITE_PUSHER_APP_KEY,
//   key: '1ac728eb26e770e7b87d',
//   cluster: 'ap1',
//   wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//   wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//   wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//   forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//   enabledTransports: ['ws', 'wss'],
// })

// export const echoPusher = new Echo({
//   broadcaster: 'pusher',
//   // key: import.meta.env.VITE_PUSHER_APP_KEY,
//   key: '1ac728eb26e770e7b87d',
//   cluster: 'ap1',
//   wsHost: import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//   wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//   wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//   forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//   enabledTransports: ['ws', 'wss'],
// })

export const echoInstance = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: import.meta.env.VITE_REVERB_HOST,
  wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
  wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
  forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
  enabledTransports: ['ws', 'wss'],
})

// import.meta.env.VITE_FB_SUP_LINK
