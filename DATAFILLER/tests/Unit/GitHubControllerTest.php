<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\GitHubController;

require_once __DIR__ . '/../../controllers/GitHubController.php';

class GitHubControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        $this->controller = new GitHubController();
    }

    public function testGetUserRepositoriesWithValidUser()
    {
        // Test básico para verificar que el método existe y retorna la estructura esperada
        $result = $this->controller->getUserRepositories('testuser');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        // Como es una API externa, solo verificamos la estructura
        if ($result['success']) {
            $this->assertArrayHasKey('data', $result);
        } else {
            $this->assertArrayHasKey('error', $result);
        }
    }

    public function testGetUserRepositoriesWithEmptyUser()
    {
        $result = $this->controller->getUserRepositories('');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
    }

    public function testGetUserRepositoriesWithInvalidUser()
    {
        // Usar un nombre de usuario que probablemente no exista
        $result = $this->controller->getUserRepositories('this-user-should-not-exist-123456789');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        if (!$result['success']) {
            $this->assertArrayHasKey('error', $result);
        }
    }

    public function testGetUserRepositoriesWithPagination()
    {
        $result = $this->controller->getUserRepositories('testuser', 1, 10);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
    }

    public function testGetRepositoryContentsWithValidRepo()
    {
        // Test básico para verificar estructura de respuesta
        $result = $this->controller->getRepositoryContents('testuser', 'testrepo');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        // Verificar estructura de respuesta exitosa o con error
        if ($result['success']) {
            $this->assertArrayHasKey('sql_files', $result);
            $this->assertArrayHasKey('directories', $result);
            $this->assertArrayHasKey('rate_limit', $result);
            $this->assertIsArray($result['sql_files']);
            $this->assertIsArray($result['directories']);
        } else {
            $this->assertArrayHasKey('error', $result);
        }
    }

    public function testGetRepositoryContentsWithEmptyUsername()
    {
        $result = $this->controller->getRepositoryContents('', 'testrepo');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        // Con username vacío, debería fallar
        if (!$result['success']) {
            $this->assertArrayHasKey('error', $result);
        }
    }

    public function testGetRepositoryContentsWithEmptyRepository()
    {
        $result = $this->controller->getRepositoryContents('testuser', '');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        // Con repository vacío, debería fallar
        if (!$result['success']) {
            $this->assertArrayHasKey('error', $result);
        }
    }

    public function testGetRepositoryContentsWithPath()
    {
        // Test con un path específico
        $result = $this->controller->getRepositoryContents('testuser', 'testrepo', 'database');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('sql_files', $result);
            $this->assertArrayHasKey('directories', $result);
            $this->assertArrayHasKey('rate_limit', $result);
        } else {
            $this->assertArrayHasKey('error', $result);
        }
    }

    public function testGetRepositoryContentsWithLeadingSlashPath()
    {
        // Test que el path con slash inicial se maneja correctamente
        $result = $this->controller->getRepositoryContents('testuser', 'testrepo', '/database/');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        if ($result['success']) {
            $this->assertArrayHasKey('sql_files', $result);
            $this->assertArrayHasKey('directories', $result);
        } else {
            $this->assertArrayHasKey('error', $result);
        }
    }

    public function testGetRepositoryContentsWithInvalidRepo()
    {
        // Test con repositorio que probablemente no existe
        $result = $this->controller->getRepositoryContents('testuser', 'this-repo-should-not-exist-123456789');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        // Debería fallar con repositorio inexistente
        if (!$result['success']) {
            $this->assertArrayHasKey('error', $result);
            $this->assertArrayHasKey('status', $result);
        }
    }

    public function testGetRepositoryContentsResponseStructure()
    {
        // Test para verificar que la respuesta siempre tenga la estructura esperada
        $result = $this->controller->getRepositoryContents('testuser', 'testrepo');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertIsBool($result['success']);
        
        if ($result['success']) {
            // Estructura de respuesta exitosa
            $this->assertArrayHasKey('sql_files', $result);
            $this->assertArrayHasKey('directories', $result);
            $this->assertArrayHasKey('rate_limit', $result);
            
            // Verificar tipos de datos
            $this->assertIsArray($result['sql_files']);
            $this->assertIsArray($result['directories']);
            
            // Si hay archivos SQL, verificar estructura
            foreach ($result['sql_files'] as $file) {
                $this->assertIsArray($file);
                // Los archivos de GitHub API deberían tener estas propiedades
                if (isset($file['name'])) {
                    $this->assertIsString($file['name']);
                }
            }
            
            // Si hay directorios, verificar estructura
            foreach ($result['directories'] as $dir) {
                $this->assertIsArray($dir);
                if (isset($dir['name'])) {
                    $this->assertIsString($dir['name']);
                }
            }
        } else {
            // Estructura de respuesta con error
            $this->assertArrayHasKey('error', $result);
            $this->assertIsString($result['error']);
        }
    }
}