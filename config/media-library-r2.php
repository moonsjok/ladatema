<?php

return [
    /*
     * The disk where to store the media.
     */
    'disk_name' => 'r2',

    /*
     * The maximum file size of an item in KB.
     */
    'max_file_size' => 1024 * 1024, // 1GB

    /*
     * When urls to files are generated, this class will be called.
     */
    'url_generator_class' => Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,

    /*
     * The class that contains the logic for converting media.
     */
    'path_generator' => Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator::class,

    /*
     * Here you can specify which path generator should be used for a given model class.
     */
    'custom_path_generators' => [
        // App\Models\YourModel::class => App\MediaLibrary\YourCustomPathGenerator::class,
    ],

    /*
     * When media are generated, this class will be called to move the file to the right location.
     */
    'file_mover' => Spatie\MediaLibrary\Conversions\DefaultFileMover::class,

    /*
     * The class that contains the logic for extracting remote file headers.
     */
    'remote_file_header_extractor' => Spatie\MediaLibrary\Support\RemoteFileHeaderExtractor\DefaultRemoteFileHeaderExtractor::class,

    /*
     * The class that contains the logic for making responsive images.
     */
    'responsive_images' => [
        'width_calculator' => Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\DefaultWidthCalculator::class,
        'tiny_placeholder_generator' => Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\DefaultTinyPlaceholderGenerator::class,
    ],

    /*
     * The names of the queues that will be used to process conversions and generate responsive images.
     */
    'queue_names' => [
        'perform_conversions' => 'media-convert',
        'generate_responsive_images' => 'media-responsive',
    ],

    /*
     * The class that contains the logic for determining the media type.
     */
    'type_detector' => Spatie\MediaLibrary\MediaTypeDetector\DefaultMediaTypeDetector::class,

    /*
     * The logger that will be used.
     */
    'logger' => null,

    /*
     * Whether to convert media on queue.
     */
    'queue_conversions' => false,

    /*
     * Whether to generate responsive images on queue.
     */
    'queue_responsive_images' => false,

    /*
     * The class that contains the logic for handling file system operations.
     */
    'file_system' => Spatie\MediaLibrary\Support\FileSystem\DefaultFileSystem::class,

    /*
     * The class that contains the logic for handling image manipulations.
     */
    'image_driver' => Spatie\MediaLibrary\Conversions\ImageGenerators\DefaultImageGenerator::class,

    /*
     * The class that contains the logic for handling video manipulations.
     */
    'video_driver' => Spatie\MediaLibrary\Conversions\ImageGenerators\Video::class,

    /*
     * The class that contains the logic for handling pdf manipulations.
     */
    'pdf_driver' => Spatie\MediaLibrary\Conversions\ImageGenerators\Pdf::class,

    /*
     * The class that contains the logic for handling svg manipulations.
     */
    'svg_driver' => Spatie\MediaLibrary\Conversions\ImageGenerators\Svg::class,

    /*
     * The class that contains the logic for handling image optimizations.
     */
    'image_optimization' => [
        'optimizer' => Spatie\MediaLibrary\Conversions\ImageOptimizers\NullOptimizer::class,
        'chain' => [
            Spatie\MediaLibrary\Conversions\ImageOptimizers\JpegOptimizer::class,
            Spatie\MediaLibrary\Conversions\ImageOptimizers\PngOptimizer::class,
            Spatie\MediaLibrary\Conversions\ImageOptimizers\GifOptimizer::class,
            Spatie\MediaLibrary\Conversions\ImageOptimizers\SvgoOptimizer::class,
        ],
    ],

    /*
     * The class that contains the logic for handling image conversions.
     */
    'image_conversion' => [
        'driver' => Spatie\MediaLibrary\Conversions\ImageDrivers\Gd::class,
    ],

    /*
     * The class that contains the logic for handling video conversions.
     */
    'video_conversion' => [
        'driver' => Spatie\MediaLibrary\Conversions\VideoDrivers\DefaultVideoDriver::class,
    ],

    /*
     * The class that contains the logic for handling pdf conversions.
     */
    'pdf_conversion' => [
        'driver' => Spatie\MediaLibrary\Conversions\PdfDrivers\DefaultPdfDriver::class,
    ],

    /*
     * The class that contains the logic for handling svg conversions.
     */
    'svg_conversion' => [
        'driver' => Spatie\MediaLibrary\Conversions\SvgDrivers\DefaultSvgDriver::class,
    ],

    /*
     * The class that contains the logic for handling image optimizations.
     */
    'image_optimization' => [
        'optimizer' => Spatie\MediaLibrary\Conversions\ImageOptimizers\NullOptimizer::class,
        'chain' => [
            Spatie\MediaLibrary\Conversions\ImageOptimizers\JpegOptimizer::class,
            Spatie\MediaLibrary\Conversions\ImageOptimizers\PngOptimizer::class,
            Spatie\MediaLibrary\Conversions\ImageOptimizers\GifOptimizer::class,
            Spatie\MediaLibrary\Conversions\ImageOptimizers\SvgoOptimizer::class,
        ],
    ],

    /*
     * The class that contains the logic for handling image manipulations.
     */
    'image_manipulation' => [
        'driver' => Spatie\MediaLibrary\Conversions\ImageDrivers\Gd::class,
    ],

    /*
     * The class that contains the logic for handling video manipulations.
     */
    'video_manipulation' => [
        'driver' => Spatie\MediaLibrary\Conversions\VideoDrivers\DefaultVideoDriver::class,
    ],

    /*
     * The class that contains the logic for handling pdf manipulations.
     */
    'pdf_manipulation' => [
        'driver' => Spatie\MediaLibrary\Conversions\PdfDrivers\DefaultPdfDriver::class,
    ],

    /*
     * The class that contains the logic for handling svg manipulations.
     */
    'svg_manipulation' => [
        'driver' => Spatie\MediaLibrary\Conversions\SvgDrivers\DefaultSvgDriver::class,
    ],

    /*
     * The class that contains the logic for handling image optimizations.
     */
    'image_optimization' => [
        'optimizer' => Spatie\MediaLibrary\Conversions\ImageOptimizers\NullOptimizer::class,
        'chain' => [
            Spatie\MediaLibrary\Conversions\ImageOptimizers\JpegOptimizer::class,
            Spatie\MediaLibrary\Conversions\ImageOptimizers\PngOptimizer::class,
            Spatie\MediaLibrary\Conversions\ImageOptimizers\GifOptimizer::class,
            Spatie\MediaLibrary\Conversions\ImageOptimizers\SvgoOptimizer::class,
        ],
    ],
];
