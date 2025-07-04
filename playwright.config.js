// playwright.config.js
const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
  timeout: 30000, // Tiempo máximo de cada test
  retries: 0,
  reporter: [
    ['list'],
    ['allure-playwright'] // Reporte en formato BDD
  ],
  projects: [
    {
      name: 'main-tests',
      testDir: './tests', // ✅ Ruta 1
    },
    {
      name: 'datafiller-tests',
      testDir: './DATAFILLER/tests/integration', // ✅ Ruta 2
    }
  ],
  use: {
    video: 'on',
    screenshot: 'only-on-failure',
    trace: 'retain-on-failure',
    baseURL: 'https://datafiller3.sytes.net'
  }
});
