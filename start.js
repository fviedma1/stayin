const { exec } = require('child_process');

// Función para ejecutar un comando
const runCommand = (command, cwd) => {
    const process = exec(command, { cwd });

    process.stdout.on('data', (data) => console.log(data));
    process.stderr.on('data', (data) => console.error(data));
    process.on('close', (code) => console.log(`Proceso finalizado con código: ${code}`));
};

// Rutas de los proyectos
const backendPath = './src';
const frontendPath = './individual';

// Comandos a ejecutar
runCommand('php artisan serve', backendPath);
runCommand('npm run dev', backendPath);
runCommand('npm run dev', frontendPath);
