<?php

    use Spatie\ImageOptimizer\Optimizers\Cwebp;
    use Spatie\ImageOptimizer\Optimizers\Gifsicle;
    use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
    use Spatie\ImageOptimizer\Optimizers\Optipng;
    use Spatie\ImageOptimizer\Optimizers\Pngquant;
    use Spatie\ImageOptimizer\Optimizers\Svgo;

    return [
        /*
         * When calling `optimize` the package will automatically determine which optimizers
         * should run for the given image.
         */
        'optimizers' => [

            Jpegoptim::class => [
                '-m80', // set maximum quality to 85%
                '--strip-all',
                '--all-progressive',
                '-o'
            ],

            Pngquant::class => [
                '--force'
                // required parameter for this package
            ],

            Optipng::class => [
                '-i0', // this will result in a non-interlaced, progressive scanned image
                '-o2',  // this set the optimization level to two (multiple IDAT compression trials)
                '-quiet' // required parameter for this package
            ],

            Svgo::class => [
                '--disable=cleanupIDs', // disabling because it is know to cause troubles
            ],

            Gifsicle::class => [
                '-b', // required parameter for this package
                '-O3', // this produces the slowest but best results
            ],

            Cwebp::class => [
                '-m 6', // for the slowest compression method in order to get the best compression.
                '-pass 10', // for maximizing the amount of analysis pass.
                '-mt', // multithreading for some speed improvements.
                '-q 90', // quality factor that brings the least noticeable changes.
            ],
        ],

        /*
        * The directory where your binaries are stored.
        * Only use this when you binaries are not accessible in the global environment.
        */
        'binary_path' => '',

        /*
         * The maximum time in seconds each optimizer is allowed to run separately.
         */
        'timeout' => 60,

        /*
         * If set to `true` all output of the optimizer binaries will be appended to the default log.
         * You can also set this to a class that implements `Psr\Log\LoggerInterface`.
         */
        'log_optimizer_activity' => false,
    ];