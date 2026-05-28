import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            // ALL these files must be in the array for Vite to build them
            input: [
                'resources/css/app.css', 
                'resources/js/app.jsx', 
                'resources/js/fdaLogin.jsx', 
                'resources/js/fdaDashboard.jsx', 
                'resources/js/employeeView.jsx'
            ],
            refresh: true,
        }),
        react(),
    ],
});