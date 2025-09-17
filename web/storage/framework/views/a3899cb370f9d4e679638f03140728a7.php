<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles / Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <div id="app" class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
            <!-- El componente App.vue se montará aquí -->
        </div>
    </body>
</html>

<?php /**PATH C:\Users\juanf\Desktop\gitRepository\grup07-hotels\web\resources\views\welcome.blade.php ENDPATH**/ ?>