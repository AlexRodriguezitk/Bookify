import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'node:url'

// No usamos una base fija, sino que se ajusta dinámicamente en el servidor
export default defineConfig({
  plugins: [vue()],
  base: '/bookify', // Base vacía para que use rutas relativas
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  build: {
    outDir: '../App', // Exporta directamente a la carpeta superior
    emptyOutDir: false, // Evita que se eliminen archivos existentes en ../
  },
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost/bookify/api',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, ''),
        configure: (proxy) => {
          proxy.on('proxyReq', (proxyReq, req) => {
            const token = req.headers['authorization']
            if (token) {
              proxyReq.setHeader('Authorization', token) // Forward JWT token
            }
          })
        },
      },
    },
  },
})
