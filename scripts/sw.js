workbox.routing.registerRoute(/wp-admin(.*)|(.*)preview=true(.*)/,
    workbox.strategies.networkOnly()
);

// Stale while revalidate for JS and CSS that are not precache
workbox.routing.registerRoute(
    /\.(?:js|css)$/,
    workbox.strategies.staleWhileRevalidate(),
  );

// We want no more than 50 images in the cache. We check using a cache first strategy
workbox.routing.registerRoute(/\.(?:png|gif|jpg)$/,
    workbox.strategies.cacheFirst({
    cacheName: 'images-cache',
    cacheExpiration: {
            maxEntries: 50
        }
    })
);

// We need cache fonts if any
workbox.routing.registerRoute(/(.*)\.(?:woff|eot|woff2|ttf|svg)$/,
    workbox.strategies.cacheFirst({
    cacheExpiration: {
            maxEntries: 20
        },
    cacheableResponse: {
        statuses: [0, 200]
        }
    })
);

workbox.routing.registerRoute(/https:\/\/fonts.googleapis.com\/(.*)/,
workbox.strategies.cacheFirst({
    cacheExpiration: {
        maxEntries: 20
    },
    cacheableResponse: {statuses: [0, 200]}
    })
);


