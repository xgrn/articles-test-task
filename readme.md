Install dependencies
```shell
composer install
```

Run unit-tests
```shell
php vendor/bin/phpunit
```

Build and run the application in the container (podman required)
```shell
./build.sh
./run.sh
```

The Dockerfile runs all the unit-tests in a separate container before building the application one.

PNG and JPEG images are converted into WEBP on the container start.

I used Slim as the back-end framework with some libs to parse the markdown files.

The front-end is rather simple. I tried to apply some WYSIWYG editors for convenience, but they looked or worked aweful.
So I decided not to waste time on them.

On production I'd use front-end building sequence to handle E2E tests and maintain cached assets.
But in this case it's an overkill to me.

The overall time on this project was 7-8 hours. Spent mostly in attempts to apply a WYSIWYG editors to work
(I tried mditor and easymde), and I had some issues about stating all the services in the container.

The pure application coding time was about 5 hours in total including making all the test.