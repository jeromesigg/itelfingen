import { defineConfig } from 'vitest/config';
import vue from '@vitejs/plugin-vue';

// JS-Tests (npm test). Eigene Konfiguration ohne laravel-vite-plugin, das braucht nur der Build.
export default defineConfig({
    plugins: [vue()],
    test: {
        environment: 'happy-dom',
        include: ['tests/js/**/*.test.js'],
        restoreMocks: true,
    },
});
